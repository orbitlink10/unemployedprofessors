<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

if (!function_exists('loadOrders')) {
    function loadOrders(): array
    {
        $file = storage_path('app/orders.json');
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            if (!is_array($data)) {
                return [];
            }

            $orders = collect($data)
                ->filter(fn ($order) => is_array($order))
                ->values()
                ->all();

            return normalizeOrderDeadlines($orders);
        }
        return [];
    }
}

if (!function_exists('saveOrders')) {
    function saveOrders(array $orders): void
    {
        $file = storage_path('app/orders.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        file_put_contents($file, json_encode(array_values($orders)));
    }
}

if (!function_exists('loadWriters')) {
    function loadWriters(): array
    {
        $file = storage_path('app/writers.json');
        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);

        return is_array($data) ? array_values($data) : [];
    }
}

if (!function_exists('saveWriters')) {
    function saveWriters(array $writers): void
    {
        $file = storage_path('app/writers.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }

        file_put_contents($file, json_encode(array_values($writers)));
    }
}

if (!function_exists('loadWriterPaymentRequests')) {
    function loadWriterPaymentRequests(): array
    {
        $file = storage_path('app/writer_payment_requests.json');
        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return [];
        }

        return normalizeWriterPaymentRequests(array_values($data));
    }
}

if (!function_exists('saveWriterPaymentRequests')) {
    function saveWriterPaymentRequests(array $requests): void
    {
        $file = storage_path('app/writer_payment_requests.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }

        file_put_contents($file, json_encode(array_values($requests)));
    }
}

if (!function_exists('normalizeWriterPaymentRequests')) {
    function normalizeWriterPaymentRequests(array $requests): array
    {
        $normalized = [];
        $usedNumbers = [];
        $seenNumbers = [];
        $changed = false;

        foreach ($requests as $request) {
            if (!is_array($request)) {
                continue;
            }

            $paymentId = strtoupper(trim((string) ($request['payment_id'] ?? '')));
            if (preg_match('/^PAY-(\d+)$/', $paymentId, $matches)) {
                $number = (int) $matches[1];
                if ($number > 0 && !isset($usedNumbers[$number])) {
                    $usedNumbers[$number] = true;
                }
            }
        }

        $nextNumber = empty($usedNumbers) ? 1 : (max(array_keys($usedNumbers)) + 1);

        foreach ($requests as $request) {
            if (!is_array($request)) {
                continue;
            }

            $item = $request;
            $paymentId = strtoupper(trim((string) ($item['payment_id'] ?? '')));
            $paymentNumber = preg_match('/^PAY-(\d+)$/', $paymentId, $matches) ? (int) $matches[1] : null;

            if ($paymentNumber !== null && $paymentNumber > 0 && !isset($seenNumbers[$paymentNumber])) {
                $normalizedPaymentId = sprintf('PAY-%04d', $paymentNumber);
                if ($paymentId !== $normalizedPaymentId) {
                    $item['payment_id'] = $normalizedPaymentId;
                    $changed = true;
                } else {
                    $item['payment_id'] = $paymentId;
                }
                $seenNumbers[$paymentNumber] = true;
            } else {
                while (isset($usedNumbers[$nextNumber]) || isset($seenNumbers[$nextNumber])) {
                    $nextNumber++;
                }

                $item['payment_id'] = sprintf('PAY-%04d', $nextNumber);
                $seenNumbers[$nextNumber] = true;
                $usedNumbers[$nextNumber] = true;
                $nextNumber++;
                $changed = true;
            }

            if (trim((string) ($item['id'] ?? '')) === '') {
                $item['id'] = (string) Str::uuid();
                $changed = true;
            }

            $normalized[] = $item;
        }

        if ($changed) {
            saveWriterPaymentRequests($normalized);
        }

        return $normalized;
    }
}

if (!function_exists('nextWriterPaymentId')) {
    function nextWriterPaymentId(array $requests): string
    {
        $maxNumber = 0;

        foreach ($requests as $request) {
            if (!is_array($request)) {
                continue;
            }

            $paymentId = strtoupper(trim((string) ($request['payment_id'] ?? '')));
            if (preg_match('/^PAY-(\d+)$/', $paymentId, $matches)) {
                $maxNumber = max($maxNumber, (int) $matches[1]);
            }
        }

        return sprintf('PAY-%04d', $maxNumber + 1);
    }
}

if (!function_exists('adminNavigationCounts')) {
    function adminNavigationCounts(): array
    {
        $orders = collect(loadOrders())
            ->filter(fn ($order) => is_array($order))
            ->map(function (array $order) {
                $order['status'] = normalizeAdminOrderStatus($order['status'] ?? 'pending');

                return $order;
            });
        $paymentRequests = collect(loadWriterPaymentRequests())->filter(fn ($request) => is_array($request));

        $distinctSubjects = $orders
            ->map(fn ($order) => trim((string) ($order['subject'] ?? '')))
            ->filter()
            ->map(fn ($subject) => strtolower($subject))
            ->unique()
            ->count();

        $distinctClients = $orders
            ->map(fn ($order) => trim((string) ($order['customer_email'] ?? '')))
            ->filter()
            ->map(fn ($email) => strtolower($email))
            ->unique()
            ->count();

        $distinctWriters = collect(loadWriters())
            ->filter(fn ($writer) => is_array($writer))
            ->map(fn ($writer) => trim((string) ($writer['name'] ?? '')))
            ->merge(
                $orders->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
            )
            ->filter()
            ->map(fn ($writer) => strtolower($writer))
            ->unique()
            ->count();

        return [
            'orders' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'available' => $orders->where('status', 'available')->count(),
            'assigned' => $orders->where('status', 'assigned')->count(),
            'editing' => $orders->where('status', 'editing')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'revision' => $orders->where('status', 'revision')->count(),
            'approved' => $orders->where('status', 'approved')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
            'courses' => $distinctSubjects,
            'clients' => $distinctClients,
            'writers' => $distinctWriters,
            'payment_requests' => $paymentRequests->count(),
        ];
    }
}

if (!function_exists('adminNotificationEmail')) {
    function adminNotificationEmail(): string
    {
        $candidates = [
            session('admin_email'),
            env('ADMIN_NOTIFICATION_EMAIL'),
            'admin@demo.com',
        ];

        foreach ($candidates as $candidate) {
            $email = strtolower(trim((string) $candidate));
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        return 'admin@demo.com';
    }
}

if (!function_exists('normalizeRecipientEmails')) {
    function normalizeRecipientEmails(array $emails): array
    {
        return collect($emails)
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter(fn ($email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }
}

if (!function_exists('allWriterNotificationEmails')) {
    function allWriterNotificationEmails(): array
    {
        return normalizeRecipientEmails(
            collect(loadWriters())
                ->map(fn ($writer) => $writer['email'] ?? null)
                ->all()
        );
    }
}

if (!function_exists('findWriterForOrder')) {
    function findWriterForOrder(array $order): ?array
    {
        $writers = collect(loadWriters())->filter(fn ($writer) => is_array($writer));

        $writerId = (int) ($order['writer_id'] ?? 0);
        if ($writerId > 0) {
            $writer = $writers->firstWhere('id', $writerId);
            if (is_array($writer)) {
                return $writer;
            }
        }

        $writerEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
        if ($writerEmail !== '') {
            $writer = $writers->first(function ($candidate) use ($writerEmail) {
                return strtolower(trim((string) ($candidate['email'] ?? ''))) === $writerEmail;
            });
            if (is_array($writer)) {
                return $writer;
            }
        }

        $writerName = trim((string) ($order['writer_name'] ?? ''));
        if ($writerName !== '') {
            $writer = $writers->first(function ($candidate) use ($writerName) {
                return strcasecmp(trim((string) ($candidate['name'] ?? '')), $writerName) === 0;
            });
            if (is_array($writer)) {
                return $writer;
            }
        }

        return null;
    }
}

if (!function_exists('assignedWriterEmailForOrder')) {
    function assignedWriterEmailForOrder(array $order): ?string
    {
        $writer = findWriterForOrder($order);
        $email = strtolower(trim((string) ($writer['email'] ?? ($order['writer_email'] ?? ''))));

        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }
}

if (!function_exists('orderEmailSubject')) {
    function orderEmailSubject(array $order, string $prefix): string
    {
        $orderId = (int) ($order['id'] ?? 0);
        $appName = config('app.name', 'Laravel');

        return $prefix . ' - Order #' . $orderId . ' | ' . $appName;
    }
}

if (!function_exists('orderEmailBody')) {
    function orderEmailBody(array $order, string $intro, array $lines = []): string
    {
        $orderId = (int) ($order['id'] ?? 0);
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
        $statusLabel = $status === 'inprogress' ? 'In Progress' : ucfirst($status);

        $detailLines = [
            'Order ID: #' . $orderId,
            'Title: ' . trim((string) ($order['title'] ?? 'Untitled')),
            'Customer: ' . trim((string) ($order['customer_name'] ?? 'Customer')),
            'Customer Email: ' . trim((string) ($order['customer_email'] ?? '')),
            'Writer: ' . trim((string) ($order['writer_name'] ?? 'Unassigned')),
            'Status: ' . $statusLabel,
            'Pages: ' . max(1, (int) ($order['pages'] ?? 1)),
            'Deadline: ' . trim((string) ($order['deadline'] ?? 'N/A')),
        ];

        if (array_key_exists('writer_payout', $order)) {
            $detailLines[] = 'Writer Pay: Ksh ' . number_format((float) ($order['writer_payout'] ?? 0), 0);
        }

        return trim(implode(PHP_EOL, array_filter([
            $intro,
            '',
            ...$detailLines,
            '',
            ...$lines,
        ])));
    }
}

if (!function_exists('sendEmailNotification')) {
    function sendEmailNotification(array $emails, string $subject, string $body): void
    {
        foreach (normalizeRecipientEmails($emails) as $email) {
            try {
                Mail::raw($body, function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}

if (!function_exists('notifyOrderCreatedByEmail')) {
    function notifyOrderCreatedByEmail(array $order): void
    {
        $order['writer_payout'] = writerPayoutForOrder($order);
        $subject = orderEmailSubject($order, 'New order posted');

        sendEmailNotification(
            [adminNotificationEmail()],
            $subject,
            orderEmailBody($order, 'A new order has been posted and is awaiting attention.')
        );

        sendEmailNotification(
            [$order['customer_email'] ?? null],
            $subject,
            orderEmailBody($order, 'Your order has been posted successfully.')
        );

        sendEmailNotification(
            allWriterNotificationEmails(),
            $subject,
            orderEmailBody(
                $order,
                'A new order is available in the system.',
                ['Visit the writer dashboard to review or claim the order if it is available.']
            )
        );
    }
}

if (!function_exists('notifyOrderStatusChangeByEmail')) {
    function notifyOrderStatusChangeByEmail(array $order, string $message): void
    {
        $order['writer_payout'] = writerPayoutForOrder($order);

        sendEmailNotification(
            [
                adminNotificationEmail(),
                strtolower(trim((string) ($order['customer_email'] ?? ''))),
                assignedWriterEmailForOrder($order),
            ],
            orderEmailSubject($order, 'Order status updated'),
            orderEmailBody($order, $message)
        );
    }
}

if (!function_exists('storeWriterProfilePicture')) {
    function storeWriterProfilePicture($file, int $writerId): ?string
    {
        if (!$file) {
            return null;
        }

        $dir = public_path('uploads/writers');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if ($extension === '') {
            $extension = 'jpg';
        }

        $name = 'writer_' . $writerId . '_' . uniqid('', true) . '.' . $extension;
        $file->move($dir, $name);

        return 'uploads/writers/' . $name;
    }
}

if (!function_exists('findWriterByEmail')) {
    function findWriterByEmail(array $writers, ?string $email): ?array
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return null;
        }

        foreach ($writers as $writer) {
            if (!is_array($writer)) {
                continue;
            }
            $writerEmail = strtolower(trim((string) ($writer['email'] ?? '')));
            if ($writerEmail === $email) {
                return $writer;
            }
        }

        return null;
    }
}

if (!function_exists('currentWriterProfile')) {
    function currentWriterProfile(): array
    {
        $writerId = (int) (session('writer_id') ?? 0);
        $writerName = trim((string) (session('writer_name') ?? ''));
        $writerEmail = strtolower(trim((string) (session('writer_email') ?? '')));
        $writers = loadWriters();
        $writerRecord = $writerEmail !== '' ? findWriterByEmail($writers, $writerEmail) : null;

        if (!$writerRecord && $writerId > 0) {
            $writerRecord = collect($writers)->firstWhere('id', $writerId);
        }

        return [
            'id' => $writerRecord['id'] ?? $writerId,
            'name' => trim((string) ($writerRecord['name'] ?? $writerName)) ?: 'Writer',
            'email' => trim((string) ($writerRecord['email'] ?? $writerEmail)) ?: 'writer@example.com',
            'qualification' => trim((string) ($writerRecord['qualification'] ?? session('writer_qualification', ''))),
            'profile_picture' => $writerRecord['profile_picture'] ?? session('writer_profile_picture'),
        ];
    }
}

if (!function_exists('durationLabelFromSeconds')) {
    function durationLabelFromSeconds(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $totalMinutes = (int) floor($seconds / 60);
        $days = (int) floor($totalMinutes / 1440);
        $hours = (int) floor(($totalMinutes % 1440) / 60);
        $minutes = $totalMinutes % 60;

        if ($days > 0) {
            return $days . ' Day' . ($days === 1 ? '' : 's') . ' ' . $hours . 'h';
        }

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }
}

if (!function_exists('remainingDeadlineLabelFor')) {
    function remainingDeadlineLabelFor(?string $dueAt, string $fallback = 'N/A'): string
    {
        $dueAtTs = strtotime((string) ($dueAt ?? ''));
        if ($dueAtTs === false || $dueAtTs <= 0) {
            return $fallback;
        }

        $diffSeconds = $dueAtTs - time();
        if ($diffSeconds <= 0) {
            return 'Expired';
        }

        return durationLabelFromSeconds($diffSeconds);
    }
}

if (!function_exists('deadlineSecondsFor')) {
    function deadlineSecondsFor(?string $deadline): ?int
    {
        $label = trim((string) $deadline);
        if ($label === '') {
            return null;
        }

        if (!preg_match('/^(\d+)\s*(hour|hours|day|days)$/i', $label, $matches)) {
            return null;
        }

        $amount = (int) $matches[1];
        $unit = strtolower($matches[2]);
        if ($amount <= 0) {
            return null;
        }

        if (in_array($unit, ['day', 'days'], true)) {
            return $amount * 24 * 60 * 60;
        }

        return $amount * 60 * 60;
    }
}

if (!function_exists('writerRatePerPage')) {
    function writerRatePerPage(): int
    {
        return 500;
    }
}

if (!function_exists('writerPayoutForPages')) {
    function writerPayoutForPages($pages): int
    {
        $pageCount = max(1, (int) $pages);

        return $pageCount * writerRatePerPage();
    }
}

if (!function_exists('writerPayoutForOrder')) {
    function writerPayoutForOrder(array $order): int
    {
        return writerPayoutForPages($order['pages'] ?? 1);
    }
}

if (!function_exists('writerDueAtForOrder')) {
    function writerDueAtForOrder(array $order): ?string
    {
        $baseDueTs = strtotime((string) ($order['due_at'] ?? ''));
        if ($baseDueTs === false || $baseDueTs <= 0) {
            $createdAtTs = strtotime((string) ($order['created_at'] ?? ''));
            if ($createdAtTs === false || $createdAtTs <= 0) {
                $createdAtTs = time();
            }

            $seconds = deadlineSecondsFor($order['deadline'] ?? null);
            if ($seconds === null) {
                return null;
            }
            $baseDueTs = $createdAtTs + $seconds;
        }

        $writerDueTs = $baseDueTs - (4 * 60 * 60);

        return date(DATE_ATOM, $writerDueTs);
    }
}

if (!function_exists('orderPosterType')) {
    function orderPosterType(array $order): string
    {
        $postedBy = strtolower(trim((string) ($order['posted_by'] ?? '')));
        if (in_array($postedBy, ['admin', 'customer'], true)) {
            return $postedBy;
        }

        $customerEmail = trim((string) ($order['customer_email'] ?? ''));
        $customerName = trim((string) ($order['customer_name'] ?? ''));

        return ($customerEmail !== '' || $customerName !== '') ? 'customer' : 'admin';
    }
}

if (!function_exists('normalizeAdminOrderStatus')) {
    function normalizeAdminOrderStatus($value): string
    {
        $status = strtolower(trim((string) $value));

        if ($status === '' || $status === 'bidding') {
            return 'pending';
        }

        if ($status === 'inprogress') {
            return 'editing';
        }

        return $status;
    }
}

if (!function_exists('orderPosterLabel')) {
    function orderPosterLabel(array $order): string
    {
        return orderPosterType($order) === 'admin' ? 'Admin' : 'Customer';
    }
}

if (!function_exists('orderPosterName')) {
    function orderPosterName(array $order): string
    {
        $posterName = trim((string) ($order['posted_by_name'] ?? ''));
        if ($posterName !== '') {
            return $posterName;
        }

        if (orderPosterType($order) === 'customer') {
            $customerName = trim((string) ($order['customer_name'] ?? ''));

            return $customerName !== '' ? $customerName : 'Customer';
        }

        return 'Admin';
    }
}

if (!function_exists('orderPosterEmail')) {
    function orderPosterEmail(array $order): string
    {
        $posterEmail = strtolower(trim((string) ($order['posted_by_email'] ?? '')));
        if ($posterEmail !== '') {
            return $posterEmail;
        }

        if (orderPosterType($order) === 'customer') {
            return strtolower(trim((string) ($order['customer_email'] ?? '')));
        }

        return '';
    }
}

if (!function_exists('normalizeOrderPaymentStatus')) {
    function normalizeOrderPaymentStatus($value): string
    {
        $status = strtolower(trim((string) $value));

        return match ($status) {
            '', 'unpaid', 'pending_checkout' => 'unpaid',
            'pending', 'processing', 'submitted' => 'pending',
            'completed', 'paid', 'success', 'successful' => 'completed',
            'failed' => 'failed',
            'invalid' => 'invalid',
            'reversed', 'refunded' => 'reversed',
            'cancelled', 'canceled' => 'cancelled',
            'not_required' => 'not_required',
            default => $status,
        };
    }
}

if (!function_exists('orderRequiresCustomerPayment')) {
    function orderRequiresCustomerPayment(array $order): bool
    {
        return orderPosterType($order) === 'customer';
    }
}

if (!function_exists('orderPaymentStatus')) {
    function orderPaymentStatus(array $order): string
    {
        $status = normalizeOrderPaymentStatus($order['payment_status'] ?? '');

        if (!orderRequiresCustomerPayment($order)) {
            return $status === 'unpaid' ? 'not_required' : ($status !== '' ? $status : 'not_required');
        }

        return $status !== '' ? $status : 'unpaid';
    }
}

if (!function_exists('orderIsPaid')) {
    function orderIsPaid(array $order): bool
    {
        return orderPaymentStatus($order) === 'completed' || !orderRequiresCustomerPayment($order);
    }
}

if (!function_exists('orderCanEnterWorkflow')) {
    function orderCanEnterWorkflow(array $order): bool
    {
        return orderIsPaid($order);
    }
}

if (!function_exists('orderPaymentStatusLabel')) {
    function orderPaymentStatusLabel(array $order): string
    {
        return match (orderPaymentStatus($order)) {
            'unpaid' => 'Pending Checkout',
            'pending' => 'Awaiting Confirmation',
            'completed' => 'Paid',
            'failed' => 'Payment Failed',
            'invalid' => 'Invalid Payment',
            'reversed' => 'Payment Reversed',
            'cancelled' => 'Payment Cancelled',
            default => 'Payment Status Unknown',
        };
    }
}

if (!function_exists('orderPaymentNotice')) {
    function orderPaymentNotice(array $order): ?string
    {
        if (!orderRequiresCustomerPayment($order)) {
            return null;
        }

        return match (orderPaymentStatus($order)) {
            'unpaid' => 'Payment pending. Complete checkout through Pesapal to activate this order.',
            'pending' => 'Your payment was submitted to Pesapal and is awaiting confirmation.',
            'completed' => 'Payment received successfully through Pesapal.',
            'failed' => 'The last Pesapal payment attempt failed. You can retry payment.',
            'invalid' => 'Pesapal marked the last payment attempt as invalid. Please retry payment.',
            'reversed' => 'This payment was reversed after processing. Contact support if this is unexpected.',
            'cancelled' => 'The Pesapal checkout was cancelled before payment was completed.',
            default => null,
        };
    }
}

if (!function_exists('pesapalSettings')) {
    function pesapalSettings(): array
    {
        return config('services.pesapal', []);
    }
}

if (!function_exists('pesapalBaseUrl')) {
    function pesapalBaseUrl(): string
    {
        $environment = strtolower(trim((string) (pesapalSettings()['environment'] ?? 'sandbox')));

        return in_array($environment, ['live', 'production'], true)
            ? 'https://pay.pesapal.com/v3'
            : 'https://cybqa.pesapal.com/pesapalv3';
    }
}

if (!function_exists('pesapalCallbackUrl')) {
    function pesapalCallbackUrl(): string
    {
        $configuredUrl = trim((string) (pesapalSettings()['callback_url'] ?? ''));

        if ($configuredUrl !== '') {
            return $configuredUrl;
        }

        return rtrim((string) config('app.url', 'http://localhost'), '/') . '/payments/pesapal/callback';
    }
}

if (!function_exists('pesapalIpnUrl')) {
    function pesapalIpnUrl(): string
    {
        $configuredUrl = trim((string) (pesapalSettings()['ipn_url'] ?? ''));

        if ($configuredUrl !== '') {
            return $configuredUrl;
        }

        return rtrim((string) config('app.url', 'http://localhost'), '/') . '/payments/pesapal/ipn';
    }
}

if (!function_exists('pesapalConfigured')) {
    function pesapalConfigured(): bool
    {
        $settings = pesapalSettings();

        return trim((string) ($settings['consumer_key'] ?? '')) !== ''
            && trim((string) ($settings['consumer_secret'] ?? '')) !== ''
            && pesapalCallbackUrl() !== ''
            && pesapalIpnUrl() !== '';
    }
}

if (!function_exists('generatePesapalMerchantReference')) {
    function generatePesapalMerchantReference(int $orderId): string
    {
        $rawReference = 'BP-ORDER-' . max(0, $orderId) . '-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
        $sanitizedReference = preg_replace('/[^A-Za-z0-9_.:-]/', '-', $rawReference) ?: 'BP-ORDER';

        return substr($sanitizedReference, 0, 50);
    }
}

if (!function_exists('pesapalMerchantReferenceForOrder')) {
    function pesapalMerchantReferenceForOrder(array $order): string
    {
        $existingReference = trim((string) ($order['payment_reference'] ?? ''));
        if ($existingReference !== '') {
            return $existingReference;
        }

        $rawReference = 'BP-ORDER-' . max(0, (int) ($order['id'] ?? 0));
        $sanitizedReference = preg_replace('/[^A-Za-z0-9_.:-]/', '-', $rawReference) ?: 'BP-ORDER';

        return substr($sanitizedReference, 0, 50);
    }
}

if (!function_exists('pesapalCustomerNameParts')) {
    function pesapalCustomerNameParts(?string $name): array
    {
        $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
        $parts = array_values(array_filter($parts, fn ($part) => $part !== ''));

        if ($parts === []) {
            return ['Customer', '', ''];
        }

        $firstName = array_shift($parts) ?: 'Customer';
        $lastName = $parts !== [] ? array_pop($parts) : '';
        $middleName = trim(implode(' ', $parts));

        return [$firstName, $middleName, $lastName];
    }
}

if (!function_exists('pesapalRequestToken')) {
    function pesapalRequestToken(): array
    {
        if (!pesapalConfigured()) {
            throw new \RuntimeException('Pesapal credentials are not configured.');
        }

        $settings = pesapalSettings();
        $response = Http::acceptJson()
            ->asJson()
            ->timeout(30)
            ->post(pesapalBaseUrl() . '/api/Auth/RequestToken', [
                'consumer_key' => trim((string) ($settings['consumer_key'] ?? '')),
                'consumer_secret' => trim((string) ($settings['consumer_secret'] ?? '')),
            ]);

        $payload = $response->json();
        $token = trim((string) ($payload['token'] ?? ''));

        if (!$response->successful() || !is_array($payload) || (string) ($payload['status'] ?? '') !== '200' || $token === '') {
            $message = trim((string) ($payload['message'] ?? 'Unable to authenticate with Pesapal.'));
            throw new \RuntimeException($message !== '' ? $message : 'Unable to authenticate with Pesapal.');
        }

        return $payload;
    }
}

if (!function_exists('pesapalRegisterIpn')) {
    function pesapalRegisterIpn(string $token): array
    {
        $response = Http::acceptJson()
            ->asJson()
            ->withToken($token)
            ->timeout(30)
            ->post(pesapalBaseUrl() . '/api/URLSetup/RegisterIPN', [
                'url' => pesapalIpnUrl(),
                'ipn_notification_type' => 'GET',
            ]);

        $payload = $response->json();
        $ipnId = trim((string) ($payload['ipn_id'] ?? ''));

        if (!$response->successful() || !is_array($payload) || (string) ($payload['status'] ?? '') !== '200' || $ipnId === '') {
            $message = trim((string) ($payload['message'] ?? 'Unable to register the Pesapal IPN URL.'));
            throw new \RuntimeException($message !== '' ? $message : 'Unable to register the Pesapal IPN URL.');
        }

        return $payload;
    }
}

if (!function_exists('pesapalSubmitOrder')) {
    function pesapalSubmitOrder(array $order): array
    {
        $tokenPayload = pesapalRequestToken();
        $token = trim((string) ($tokenPayload['token'] ?? ''));
        $ipnPayload = pesapalRegisterIpn($token);
        [$firstName, $middleName, $lastName] = pesapalCustomerNameParts($order['customer_name'] ?? $order['posted_by_name'] ?? 'Customer');
        $settings = pesapalSettings();
        $currency = strtoupper(trim((string) ($settings['currency'] ?? 'USD'))) ?: 'USD';
        $countryCode = strtoupper(trim((string) ($settings['country_code'] ?? 'KE'))) ?: 'KE';
        $description = Str::limit(
            trim('BoomPapers Order #' . (int) ($order['id'] ?? 0) . ' ' . trim((string) ($order['title'] ?? 'Payment'))),
            100,
            ''
        );

        $response = Http::acceptJson()
            ->asJson()
            ->withToken($token)
            ->timeout(30)
            ->post(pesapalBaseUrl() . '/api/Transactions/SubmitOrderRequest', [
                'id' => pesapalMerchantReferenceForOrder($order),
                'currency' => $currency,
                'amount' => round((float) ($order['cost'] ?? 0), 2),
                'description' => $description !== '' ? $description : 'BoomPapers payment',
                'callback_url' => pesapalCallbackUrl(),
                'redirect_mode' => 'TOP_WINDOW',
                'notification_id' => trim((string) ($ipnPayload['ipn_id'] ?? '')),
                'branch' => 'BoomPapers',
                'billing_address' => [
                    'email_address' => trim((string) ($order['customer_email'] ?? '')),
                    'phone_number' => trim((string) ($order['customer_phone'] ?? '')),
                    'country_code' => $countryCode,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'line_1' => '',
                    'line_2' => '',
                    'city' => '',
                    'state' => '',
                    'postal_code' => '',
                    'zip_code' => '',
                ],
            ]);

        $payload = $response->json();
        $redirectUrl = trim((string) ($payload['redirect_url'] ?? ''));
        $trackingId = trim((string) ($payload['order_tracking_id'] ?? ''));

        if (!$response->successful() || !is_array($payload) || (string) ($payload['status'] ?? '') !== '200' || $redirectUrl === '' || $trackingId === '') {
            $message = trim((string) ($payload['message'] ?? 'Unable to create a Pesapal payment request.'));
            throw new \RuntimeException($message !== '' ? $message : 'Unable to create a Pesapal payment request.');
        }

        return $payload;
    }
}

if (!function_exists('findOrderIdByMerchantReference')) {
    function findOrderIdByMerchantReference(string $merchantReference): ?int
    {
        $merchantReference = trim($merchantReference);
        if ($merchantReference === '') {
            return null;
        }

        if (preg_match('/^BP-ORDER-(\d+)(?:[-:.][A-Za-z0-9_.:-]+)?$/', $merchantReference, $matches)) {
            return (int) $matches[1];
        }

        $orders = loadOrders();

        foreach ($orders as $order) {
            if (!is_array($order)) {
                continue;
            }

            if (pesapalMerchantReferenceForOrder($order) === $merchantReference) {
                return (int) ($order['id'] ?? 0);
            }
        }

        return null;
    }
}

if (!function_exists('pesapalGetTransactionStatus')) {
    function pesapalGetTransactionStatus(string $trackingId): array
    {
        $trackingId = trim($trackingId);
        if ($trackingId === '') {
            throw new \RuntimeException('Missing Pesapal tracking ID.');
        }

        $tokenPayload = pesapalRequestToken();
        $token = trim((string) ($tokenPayload['token'] ?? ''));
        $response = Http::acceptJson()
            ->withToken($token)
            ->timeout(30)
            ->get(pesapalBaseUrl() . '/api/Transactions/GetTransactionStatus', [
                'orderTrackingId' => $trackingId,
            ]);

        $payload = $response->json();

        if (!$response->successful() || !is_array($payload) || (string) ($payload['status'] ?? '') !== '200') {
            $message = trim((string) ($payload['message'] ?? 'Unable to verify the Pesapal payment status.'));
            throw new \RuntimeException($message !== '' ? $message : 'Unable to verify the Pesapal payment status.');
        }

        return $payload;
    }
}

if (!function_exists('syncOrderPaymentStatusFromPesapal')) {
    function syncOrderPaymentStatusFromPesapal(int $orderId, array $transactionStatus, ?string $trackingId = null, ?string $merchantReference = null): ?array
    {
        $orders = loadOrders();
        $updatedOrder = null;
        $justMarkedPaid = false;
        $changed = false;

        foreach ($orders as &$order) {
            if (!is_array($order) || (int) ($order['id'] ?? 0) !== $orderId) {
                continue;
            }

            $currentPaymentStatus = orderPaymentStatus($order);
            $paymentStatusDescription = strtolower(trim((string) ($transactionStatus['payment_status_description'] ?? '')));
            $nextPaymentStatus = match ($paymentStatusDescription) {
                'completed' => 'completed',
                'failed' => 'failed',
                'invalid' => 'invalid',
                'reversed' => 'reversed',
                'cancelled', 'canceled' => 'cancelled',
                default => 'pending',
            };

            $order['payment_status'] = $nextPaymentStatus;
            $order['payment_reference'] = $merchantReference !== null && trim($merchantReference) !== ''
                ? trim($merchantReference)
                : pesapalMerchantReferenceForOrder($order);
            $order['payment_tracking_id'] = $trackingId !== null && trim($trackingId) !== ''
                ? trim($trackingId)
                : trim((string) ($order['payment_tracking_id'] ?? ''));
            $order['payment_method'] = trim((string) ($transactionStatus['payment_method'] ?? ($order['payment_method'] ?? '')));
            $order['payment_confirmation_code'] = trim((string) ($transactionStatus['confirmation_code'] ?? ($order['payment_confirmation_code'] ?? '')));
            $order['payment_account'] = trim((string) ($transactionStatus['payment_account'] ?? ($order['payment_account'] ?? '')));
            $order['payment_currency'] = strtoupper(trim((string) ($transactionStatus['currency'] ?? ($order['payment_currency'] ?? 'USD'))));
            $order['payment_amount'] = round((float) ($transactionStatus['amount'] ?? ($order['payment_amount'] ?? ($order['cost'] ?? 0))), 2);
            $order['payment_status_description'] = trim((string) ($transactionStatus['payment_status_description'] ?? ($order['payment_status_description'] ?? '')));
            $order['payment_message'] = trim((string) ($transactionStatus['description'] ?? ($transactionStatus['message'] ?? ($order['payment_message'] ?? ''))));
            $order['payment_checked_at'] = now()->toIso8601String();

            if ($nextPaymentStatus === 'completed') {
                if (trim((string) ($order['paid_at'] ?? '')) === '') {
                    $order['paid_at'] = now()->toIso8601String();
                }

                if ($currentPaymentStatus !== 'completed') {
                    $justMarkedPaid = true;
                }
            }

            $updatedOrder = $order;
            $changed = true;
            break;
        }
        unset($order);

        if (!$changed || !is_array($updatedOrder)) {
            return null;
        }

        if ($justMarkedPaid && trim((string) ($updatedOrder['payment_notified_at'] ?? '')) === '') {
            $updatedOrder['payment_notified_at'] = now()->toIso8601String();

            foreach ($orders as &$order) {
                if (!is_array($order) || (int) ($order['id'] ?? 0) !== $orderId) {
                    continue;
                }

                $order = $updatedOrder;
                break;
            }
            unset($order);
        }

        saveOrders($orders);

        if ($justMarkedPaid) {
            notifyOrderCreatedByEmail($updatedOrder);
        }

        return $updatedOrder;
    }
}

if (!function_exists('normalizeOrderDeadlines')) {
    function normalizeOrderDeadlines(array $orders): array
    {
        $normalized = [];
        $changed = false;

        foreach ($orders as $order) {
            if (!is_array($order)) {
                continue;
            }

            $item = $order;

            $createdAtTs = strtotime((string) ($item['created_at'] ?? ''));
            if ($createdAtTs === false || $createdAtTs <= 0) {
                $createdAtTs = time();
                $item['created_at'] = date(DATE_ATOM, $createdAtTs);
                $changed = true;
            }

            $dueAtTs = strtotime((string) ($item['due_at'] ?? ''));
            if ($dueAtTs === false || $dueAtTs <= 0) {
                $seconds = deadlineSecondsFor($item['deadline'] ?? null);
                if ($seconds !== null) {
                    $item['due_at'] = date(DATE_ATOM, $createdAtTs + $seconds);
                    $changed = true;
                }
            }

            $status = strtolower(trim((string) ($item['status'] ?? 'pending')));
            if (in_array($status, ['pending', 'available'], true)) {
                if ((int) ($item['writer_id'] ?? 0) !== 0) {
                    $item['writer_id'] = 0;
                    $changed = true;
                }

                if (trim((string) ($item['writer_name'] ?? '')) !== '') {
                    $item['writer_name'] = '';
                    $changed = true;
                }

                if (trim((string) ($item['writer_email'] ?? '')) !== '') {
                    $item['writer_email'] = '';
                    $changed = true;
                }
            }

            $writerPayout = writerPayoutForOrder($item);
            if ((int) ($item['writer_payout'] ?? 0) !== $writerPayout) {
                $item['writer_payout'] = $writerPayout;
                $changed = true;
            }

            $posterType = orderPosterType($item);
            if (($item['posted_by'] ?? null) !== $posterType) {
                $item['posted_by'] = $posterType;
                $changed = true;
            }

            $posterName = orderPosterName($item);
            if (trim((string) ($item['posted_by_name'] ?? '')) !== $posterName) {
                $item['posted_by_name'] = $posterName;
                $changed = true;
            }

            $posterEmail = orderPosterEmail($item);
            if ($posterEmail !== '' && strtolower(trim((string) ($item['posted_by_email'] ?? ''))) !== $posterEmail) {
                $item['posted_by_email'] = $posterEmail;
                $changed = true;
            }

            $paymentStatus = orderPaymentStatus($item);
            if (trim((string) ($item['payment_status'] ?? '')) !== $paymentStatus) {
                $item['payment_status'] = $paymentStatus;
                $changed = true;
            }

            $paymentReference = pesapalMerchantReferenceForOrder($item);
            if ($paymentReference !== '' && trim((string) ($item['payment_reference'] ?? '')) !== $paymentReference) {
                $item['payment_reference'] = $paymentReference;
                $changed = true;
            }

            $normalized[] = $item;
        }

        if ($changed) {
            saveOrders($normalized);
        }

        return $normalized;
    }
}

if (!function_exists('ordersForCustomer')) {
    function ordersForCustomer(array $orders, ?string $email): array
    {
        $email = trim((string) $email);
        if ($email === '') {
            return [];
        }

        return collect($orders)
            ->filter(function ($order) use ($email) {
                $orderEmail = trim((string) ($order['customer_email'] ?? ''));
                return $orderEmail !== '' && strcasecmp($orderEmail, $email) === 0;
            })
            ->values()
            ->all();
    }
}

if (!function_exists('normalizeOrderFileSource')) {
    function normalizeOrderFileSource(?string $source): string
    {
        $source = strtolower(trim((string) $source));

        return in_array($source, ['customer', 'writer'], true) ? $source : 'customer';
    }
}

if (!function_exists('uploadedFilesFromRequest')) {
    function uploadedFilesFromRequest($files): array
    {
        if (!is_array($files)) {
            return [];
        }

        return array_values(array_filter($files, fn ($file) => $file !== null));
    }
}

if (!function_exists('storeOrderFiles')) {
    function storeOrderFiles(array $files, int $orderId, string $source = 'customer'): void
    {
        if (empty($files)) {
            return;
        }

        $source = normalizeOrderFileSource($source);
        $stored = session('order_files', []);
        $dir = storage_path('app/uploads');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $name = uniqid('order_' . $orderId . '_', true) . '_' . $file->getClientOriginalName();
            $file->move($dir, $name);
            $stored[] = [
                'order_id' => $orderId,
                'name' => $file->getClientOriginalName(),
                'path' => $name,
                'date' => now()->toDateTimeString(),
                'source' => $source,
            ];
        }

        session(['order_files' => $stored]);
    }
}

if (!function_exists('orderFilesFor')) {
    function orderFilesFor(int $orderId, ?string $source = null): array
    {
        $files = collect(session('order_files', []))
            ->filter(fn ($file) => (int) ($file['order_id'] ?? 0) === $orderId)
            ->map(function ($file) {
                if (!is_array($file)) {
                    return null;
                }

                $file['source'] = normalizeOrderFileSource($file['source'] ?? 'customer');

                return $file;
            })
            ->filter()
            ->values();

        if ($source !== null) {
            $normalizedSource = normalizeOrderFileSource($source);
            $files = $files
                ->filter(fn ($file) => ($file['source'] ?? 'customer') === $normalizedSource)
                ->values();
        }

        return $files->all();
    }
}

if (!function_exists('findOrderFileFor')) {
    function findOrderFileFor(int $orderId, ?string $path): ?array
    {
        $path = basename(trim((string) $path));
        if ($path === '') {
            return null;
        }

        return collect(orderFilesFor($orderId))
            ->first(fn ($file) => basename((string) ($file['path'] ?? '')) === $path);
    }
}

if (!function_exists('pricingLevels')) {
    function pricingLevels(): array
    {
        return ['High School', 'College', 'Masters', 'PhD'];
    }
}

if (!function_exists('pricingDeadlines')) {
    function pricingDeadlines(): array
    {
        return ['8 Hours', '24 Hours', '48 Hours', '3 Days', '5 Days', '7 Days', '14 Days'];
    }
}

if (!function_exists('defaultPricingMatrix')) {
    function defaultPricingMatrix(): array
    {
        return [
            'High School' => [
                '8 Hours' => 29.6,
                '24 Hours' => 25.6,
                '48 Hours' => 19.6,
                '3 Days' => 17.6,
                '5 Days' => 15.6,
                '7 Days' => 14.6,
                '14 Days' => 12.6,
            ],
            'College' => [
                '8 Hours' => 32.6,
                '24 Hours' => 28.6,
                '48 Hours' => 21.6,
                '3 Days' => 19.6,
                '5 Days' => 17.6,
                '7 Days' => 16.6,
                '14 Days' => 14.6,
            ],
            'Masters' => [
                '8 Hours' => 36.6,
                '24 Hours' => 32.6,
                '48 Hours' => 25.6,
                '3 Days' => 23.6,
                '5 Days' => 21.6,
                '7 Days' => 20.6,
                '14 Days' => 18.6,
            ],
            'PhD' => [
                '8 Hours' => 40.6,
                '24 Hours' => 36.6,
                '48 Hours' => 29.6,
                '3 Days' => 27.6,
                '5 Days' => 25.6,
                '7 Days' => 24.6,
                '14 Days' => 22.6,
            ],
        ];
    }
}

if (!function_exists('loadPricing')) {
    function loadPricing(): array
    {
        $defaults = defaultPricingMatrix();
        $file = storage_path('app/pricing.json');
        if (!file_exists($file)) {
            return $defaults;
        }

        $json = file_get_contents($file);
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return $defaults;
        }

        $pricing = [];
        foreach (pricingLevels() as $level) {
            foreach (pricingDeadlines() as $deadline) {
                $value = $decoded[$level][$deadline] ?? $defaults[$level][$deadline];
                $pricing[$level][$deadline] = is_numeric($value) ? round((float) $value, 2) : $defaults[$level][$deadline];
            }
        }

        return $pricing;
    }
}

if (!function_exists('savePricing')) {
    function savePricing(array $pricing): void
    {
        $file = storage_path('app/pricing.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        file_put_contents($file, json_encode($pricing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

if (!function_exists('defaultHomepageContent')) {
    function defaultHomepageContent(): array
    {
        return [
            'logo_path' => null,
            'hero' => [
                'eyebrow' => 'Trusted by 25k+ students',
                'cta_pill' => 'Fast delivery · Free revisions',
                'title_prefix' => 'Professional',
                'title_highlight' => 'Paper Writing',
                'title_suffix' => 'Service that guarantees results',
                'description' => 'Hire a dedicated academic writer with subject expertise, 24/7 communication, and industry-leading turnaround times. Every paper is 100% original and tailored to your rubric.',
            ],
            'badges' => [
                ['value' => '4.4★', 'label' => 'Trustpilot', 'color' => '#00b67a'],
                ['value' => '4.2★', 'label' => 'Sitejabber', 'color' => '#ff3366'],
                ['value' => '4.9★', 'label' => 'Reviews.io', 'color' => '#000000'],
            ],
            'cards' => [
                ['title' => 'Business Plan', 'detail' => 'Growth · Strategy · Pitch'],
                ['title' => 'Problem Solving', 'detail' => 'Data · Finance · Math'],
                ['title' => 'Research Paper', 'detail' => 'Peer-reviewed · Structured'],
                ['title' => 'Essay', 'detail' => 'Creative · Argumentative'],
            ],
            'seo_html' => '<h2>Campus Management System Software in Kenya: Transforming Universities with Smart Technology</h2>',
        ];
    }
}

if (!function_exists('loadHomepageContent')) {
    function loadHomepageContent(): array
    {
        $defaults = defaultHomepageContent();
        $file = storage_path('app/homepage.json');
        if (!file_exists($file)) {
            return $defaults;
        }

        $json = file_get_contents($file);
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return $defaults;
        }

        return array_replace_recursive($defaults, $decoded);
    }
}

if (!function_exists('saveHomepageContent')) {
    function saveHomepageContent(array $content): void
    {
        $file = storage_path('app/homepage.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }

        file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

if (!function_exists('loadCustomPages')) {
    function loadCustomPages(): array
    {
        $file = storage_path('app/pages.json');
        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents($file);
        $decoded = json_decode($json, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }
}

if (!function_exists('saveCustomPages')) {
    function saveCustomPages(array $pages): void
    {
        $file = storage_path('app/pages.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }

        file_put_contents($file, json_encode(array_values($pages), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

if (!function_exists('publishedCustomPages')) {
    function publishedCustomPages(): array
    {
        return collect(loadCustomPages())
            ->filter(fn ($page) => is_array($page) && strtolower(trim((string) ($page['status'] ?? 'draft'))) === 'published')
            ->sortBy(fn ($page) => strtolower(trim((string) ($page['title'] ?? ''))))
            ->values()
            ->all();
    }
}

if (!function_exists('pricePerPageFor')) {
    function pricePerPageFor(?string $level, ?string $deadline): float
    {
        $pricing = loadPricing();
        $level = trim((string) ($level ?? 'College'));
        $deadline = trim((string) ($deadline ?? '48 Hours'));

        if (isset($pricing[$level][$deadline])) {
            return (float) $pricing[$level][$deadline];
        }

        foreach ($pricing as $savedLevel => $rows) {
            if (strcasecmp($savedLevel, $level) !== 0) {
                continue;
            }
            foreach ($rows as $savedDeadline => $value) {
                if (strcasecmp($savedDeadline, $deadline) === 0) {
                    return (float) $value;
                }
            }
        }

        return (float) ($pricing['College']['48 Hours'] ?? 21.6);
    }
}

if (!function_exists('categoryMultiplierFor')) {
    function categoryMultiplierFor(?string $category): float
    {
        $multipliers = [
            'standard' => 1.0,
            'premium' => 1.15,
            'platinum' => 1.30,
        ];

        $key = strtolower(trim((string) ($category ?? 'standard')));
        return $multipliers[$key] ?? 1.0;
    }
}

Route::get('/', function () {
    return view('welcome', [
        'homepageContent' => loadHomepageContent(),
        'navPages' => array_slice(publishedCustomPages(), 0, 2),
    ]);
});

Route::get('/writers', function () {
    $writers = [
        ['name' => 'Alice Writer', 'specialty' => 'Business, Management', 'rating' => '4.9', 'orders' => 312],
        ['name' => 'Brian Smith', 'specialty' => 'Nursing, Healthcare', 'rating' => '4.8', 'orders' => 284],
        ['name' => 'Carol Johnson', 'specialty' => 'Technology, IT', 'rating' => '4.9', 'orders' => 355],
        ['name' => 'David Lee', 'specialty' => 'Literature, History', 'rating' => '4.7', 'orders' => 241],
        ['name' => 'Eva Brown', 'specialty' => 'Economics, Finance', 'rating' => '4.8', 'orders' => 298],
    ];

    return view('writers', ['writers' => $writers]);
})->name('writers.index');

Route::get('/order', function () {
    return view('order');
})->name('order');

Route::get('/order/create', function () {
    return view('order-form', [
        'pricing' => loadPricing(),
    ]);
})->name('order.create');

Route::post('/customer/register', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'name' => 'required|string|max:255',
        'password' => 'required|string|min:6',
        'phone_country' => 'nullable|string|max:50',
        'phone_number' => 'nullable|string|max:30',
    ]);

    session([
        'customer_logged_in' => true,
        'customer_email' => $data['email'],
        'customer_name' => $data['name'],
    ]);

    return redirect()->route('customer.dashboard');
})->name('customer.register');

Route::post('/customer/login', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // For demo purposes we accept any credentials; in production verify against DB.
    session([
        'customer_logged_in' => true,
        'customer_email' => $data['email'],
        'customer_name' => strstr($data['email'], '@', true) ?: 'Customer',
    ]);

    return redirect()->route('customer.dashboard');
})->name('customer.login');

Route::get('/customer/logout', function () {
    session()->forget(['customer_logged_in', 'customer_email', 'customer_name']);
    return redirect()->route('order', ['tab' => 'existing']);
})->name('customer.logout');

Route::get('/writer', function (\Illuminate\Http\Request $request) {
    if (session('writer_logged_in')) {
        return redirect()->route('writer.dashboard');
    }

    return view('writer.auth', [
        'tab' => $request->query('tab', 'new'),
    ]);
})->name('writer.auth');

Route::post('/writer/register', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        'qualification' => 'nullable|string|max:255',
        'profile_picture' => 'nullable|image|max:5120',
        'tab' => 'nullable|string',
    ]);

    $writers = loadWriters();
    if (findWriterByEmail($writers, $data['email'])) {
        return back()
            ->withErrors(['email' => 'A writer account with this email already exists.'])
            ->withInput();
    }

    $id = (collect($writers)->max('id') ?? 0) + 1;
    $profilePicture = storeWriterProfilePicture($request->file('profile_picture'), $id);
    $writers[] = [
        'id' => $id,
        'name' => $data['name'],
        'email' => strtolower(trim($data['email'])),
        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        'qualification' => trim((string) ($data['qualification'] ?? '')),
        'profile_picture' => $profilePicture,
        'created_at' => now()->toIso8601String(),
    ];
    saveWriters($writers);

    session([
        'writer_logged_in' => true,
        'writer_id' => $id,
        'writer_name' => $data['name'],
        'writer_email' => strtolower(trim($data['email'])),
        'writer_qualification' => trim((string) ($data['qualification'] ?? '')),
        'writer_profile_picture' => $profilePicture,
    ]);

    return redirect()->route('writer.dashboard');
})->name('writer.register');

Route::post('/writer/login', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'tab' => 'nullable|string',
    ]);

    $writer = findWriterByEmail(loadWriters(), $data['email']);
    $passwordHash = (string) ($writer['password'] ?? '');
    $isValid = $writer && $passwordHash !== '' && password_verify($data['password'], $passwordHash);
    if (!$isValid) {
        return back()
            ->withErrors(['credentials' => 'Invalid email or password.'])
            ->withInput();
    }

    session([
        'writer_logged_in' => true,
        'writer_id' => $writer['id'] ?? null,
        'writer_name' => $writer['name'] ?? 'Writer',
        'writer_email' => $writer['email'] ?? strtolower(trim($data['email'])),
        'writer_qualification' => trim((string) ($writer['qualification'] ?? '')),
        'writer_profile_picture' => $writer['profile_picture'] ?? null,
    ]);

    return redirect()->route('writer.dashboard');
})->name('writer.login');

Route::get('/writer/logout', function () {
    session()->forget([
        'writer_logged_in',
        'writer_id',
        'writer_name',
        'writer_email',
        'writer_qualification',
        'writer_profile_picture',
    ]);
    return redirect()->route('writer.auth', ['tab' => 'existing']);
})->name('writer.logout');

Route::get('/writer/dashboard', function (\Illuminate\Http\Request $request) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $writerEmail = strtolower(trim((string) (session('writer_email') ?? '')));
    $menu = strtolower(trim((string) $request->query('menu', 'available')));
    if (!in_array($menu, ['available', 'assigned', 'revision', 'completed', 'approved'], true)) {
        $menu = 'available';
    }

    $activeAssignedStatuses = ['assigned', 'inprogress', 'editing'];
    $completedStatuses = ['completed'];
    $statusOptions = [
        'assigned' => 'Assigned',
        'inprogress' => 'In Progress',
        'editing' => 'Editing',
        'revision' => 'Revision',
        'completed' => 'Completed',
        'approved' => 'Approved',
    ];

    $ordersCollection = collect(loadOrders());

    $isMine = function (array $order) use ($writerId, $writerName, $writerEmail) {
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
        return $writerId > 0 && $assignedId === $writerId
            || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)
            || ($writerEmail !== '' && $assignedEmail !== '' && strcasecmp($assignedEmail, $writerEmail) === 0);
    };

    $isAvailable = function (array $order) {
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));

        return $assignedId <= 0
            && $assignedName === ''
            && $assignedEmail === ''
            && orderCanEnterWorkflow($order)
            && in_array($status, ['pending', 'available'], true);
    };

    $matchesMenu = function (array $order, string $selectedMenu) use ($isMine, $isAvailable, $activeAssignedStatuses, $completedStatuses) {
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));

        if ($selectedMenu === 'available') {
            return $isAvailable($order);
        }

        if (!$isMine($order)) {
            return false;
        }

        if ($selectedMenu === 'assigned') {
            return in_array($status, $activeAssignedStatuses, true);
        }

        if ($selectedMenu === 'revision') {
            return $status === 'revision';
        }

        if ($selectedMenu === 'completed') {
            return in_array($status, $completedStatuses, true);
        }

        return $status === 'approved';
    };

    $menuItems = [
        [
            'key' => 'available',
            'short' => 'AV',
            'label' => 'Available',
            'description' => 'Open jobs posted by admin or customers, ready to take.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'available'))->count(),
        ],
        [
            'key' => 'assigned',
            'short' => 'AS',
            'label' => 'Assigned',
            'description' => 'Orders currently on your desk.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'assigned'))->count(),
        ],
        [
            'key' => 'revision',
            'short' => 'RV',
            'label' => 'Revision',
            'description' => 'Orders waiting for changes.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'revision'))->count(),
        ],
        [
            'key' => 'completed',
            'short' => 'CM',
            'label' => 'Completed',
            'description' => 'Finished work awaiting final approval.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'completed'))->count(),
        ],
        [
            'key' => 'approved',
            'short' => 'AP',
            'label' => 'Approved',
            'description' => 'Orders fully approved and closed.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'approved'))->count(),
        ],
    ];

    $activeMenu = collect($menuItems)->firstWhere('key', $menu) ?? $menuItems[0];

    $orders = $ordersCollection
        ->filter(function ($order) use ($menu, $matchesMenu) {
            if (!is_array($order)) {
                return false;
            }

            return $matchesMenu($order, $menu);
        })
        ->sortByDesc(fn ($order) => (int) ($order['id'] ?? 0))
        ->map(function (array $order) use ($writerId, $writerName, $writerEmail, $statusOptions, $isAvailable) {
            $writerDueAt = writerDueAtForOrder($order);
            $postedDeadlineSeconds = deadlineSecondsFor($order['deadline'] ?? null);
            $adjustedSeconds = $postedDeadlineSeconds !== null ? max(0, $postedDeadlineSeconds - (4 * 60 * 60)) : null;
            $fallbackDeadline = $adjustedSeconds !== null
                ? durationLabelFromSeconds($adjustedSeconds)
                : (string) ($order['deadline'] ?? 'N/A');

            $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
            $orderWriterId = (int) ($order['writer_id'] ?? 0);
            $orderWriterName = trim((string) ($order['writer_name'] ?? ''));
            $orderWriterEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
            $posterType = orderPosterType($order);
            $clientName = trim((string) ($order['customer_name'] ?? ''));

            $isAssignedToCurrent = $writerId > 0 && $orderWriterId === $writerId
                || ($writerName !== '' && $orderWriterName !== '' && strcasecmp($orderWriterName, $writerName) === 0)
                || ($writerEmail !== '' && $orderWriterEmail !== '' && strcasecmp($orderWriterEmail, $writerEmail) === 0);
            $isTakeable = $isAvailable($order);
            $canViewOrderStatus = $isAssignedToCurrent || $isTakeable;

            $files = orderFilesFor((int) ($order['id'] ?? 0), 'writer');

            return [
                'id' => $order['id'] ?? null,
                'title' => $order['title'] ?? 'Untitled',
                'subject' => $order['subject'] ?? 'Other',
                'type' => $order['type'] ?? 'Essay',
                'pages' => (int) ($order['pages'] ?? 1),
                'writer_payout' => writerPayoutForOrder($order),
                'status' => $canViewOrderStatus ? $status : 'private',
                'client_name' => $clientName !== '' ? $clientName : ($posterType === 'admin' ? 'Admin Desk' : 'Client'),
                'client_email' => $order['customer_email'] ?? 'client@example.com',
                'posted_by' => $posterType,
                'posted_by_label' => orderPosterLabel($order),
                'posted_by_name' => orderPosterName($order),
                'assigned_writer' => $isAssignedToCurrent
                    ? ($orderWriterName !== '' ? $orderWriterName : 'Unassigned')
                    : ($isTakeable ? 'Unassigned' : 'Private'),
                'writer_due_at' => $writerDueAt,
                'writer_deadline' => remainingDeadlineLabelFor($writerDueAt, $fallbackDeadline),
                'writer_deadline_fallback' => $fallbackDeadline,
                'is_assigned_to_current' => $isAssignedToCurrent,
                'can_take' => $isTakeable,
                'status_options' => $statusOptions,
                'status_label' => !$canViewOrderStatus
                    ? 'Private'
                    : ($status === 'inprogress' ? 'In Progress' : ucfirst($status)),
                'files_count' => count($files),
            ];
        })
        ->values()
        ->all();

    return view('writer.dashboard', [
        'orders' => $orders,
        'menu' => $menu,
        'menuItems' => $menuItems,
        'activeMenu' => $activeMenu,
    ]);
})->name('writer.dashboard');

Route::get('/writer/profile', function () {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    return view('writer.profile', [
        'writerProfile' => currentWriterProfile(),
    ]);
})->name('writer.profile');

Route::get('/writer/payments', function () {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $writerProfile = currentWriterProfile();
    $writerId = (int) ($writerProfile['id'] ?? 0);
    $writerName = trim((string) ($writerProfile['name'] ?? ''));
    $writerEmail = strtolower(trim((string) ($writerProfile['email'] ?? '')));
    $activeAssignedStatuses = ['assigned', 'inprogress', 'editing'];
    $completedStatuses = ['completed'];
    $ordersCollection = collect(loadOrders())->filter(fn ($order) => is_array($order));

    $isMine = function (array $order) use ($writerId, $writerName, $writerEmail) {
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));

        return ($writerId > 0 && $assignedId === $writerId)
            || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)
            || ($writerEmail !== '' && $assignedEmail !== '' && strcasecmp($assignedEmail, $writerEmail) === 0);
    };

    $isAvailable = function (array $order) {
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));

        return $assignedId <= 0
            && $assignedName === ''
            && $assignedEmail === ''
            && orderCanEnterWorkflow($order)
            && in_array($status, ['pending', 'available'], true);
    };

    $matchesMenu = function (array $order, string $selectedMenu) use ($isMine, $isAvailable, $activeAssignedStatuses, $completedStatuses) {
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));

        if ($selectedMenu === 'available') {
            return $isAvailable($order);
        }

        if (!$isMine($order)) {
            return false;
        }

        if ($selectedMenu === 'assigned') {
            return in_array($status, $activeAssignedStatuses, true);
        }

        if ($selectedMenu === 'revision') {
            return $status === 'revision';
        }

        if ($selectedMenu === 'completed') {
            return in_array($status, $completedStatuses, true);
        }

        return $status === 'approved';
    };

    $menuItems = [
        [
            'key' => 'available',
            'label' => 'Available',
            'count' => $ordersCollection->filter(fn ($order) => $matchesMenu($order, 'available'))->count(),
        ],
        [
            'key' => 'assigned',
            'label' => 'Assigned',
            'count' => $ordersCollection->filter(fn ($order) => $matchesMenu($order, 'assigned'))->count(),
        ],
        [
            'key' => 'completed',
            'label' => 'Completed',
            'count' => $ordersCollection->filter(fn ($order) => $matchesMenu($order, 'completed'))->count(),
        ],
        [
            'key' => 'revision',
            'label' => 'Revision',
            'count' => $ordersCollection->filter(fn ($order) => $matchesMenu($order, 'revision'))->count(),
        ],
        [
            'key' => 'approved',
            'label' => 'Approved',
            'count' => $ordersCollection->filter(fn ($order) => $matchesMenu($order, 'approved'))->count(),
        ],
    ];

    $matchesPaymentRequestWriter = function (array $request) use ($writerId, $writerName, $writerEmail) {
        $requestWriterId = (int) ($request['writer_id'] ?? 0);
        $requestWriterName = trim((string) ($request['writer_name'] ?? ''));
        $requestWriterEmail = strtolower(trim((string) ($request['writer_email'] ?? '')));

        return ($writerId > 0 && $requestWriterId === $writerId)
            || ($writerName !== '' && $requestWriterName !== '' && strcasecmp($requestWriterName, $writerName) === 0)
            || ($writerEmail !== '' && $requestWriterEmail !== '' && strcasecmp($requestWriterEmail, $writerEmail) === 0);
    };

    $paymentRequests = collect(loadWriterPaymentRequests())
        ->filter(fn ($request) => is_array($request) && $matchesPaymentRequestWriter($request))
        ->sortByDesc(fn ($request) => strtotime((string) ($request['requested_at'] ?? '')) ?: 0)
        ->values();

    $requestedOrderIds = $paymentRequests
        ->map(fn ($request) => (int) ($request['order_id'] ?? 0))
        ->filter(fn ($orderId) => $orderId > 0)
        ->unique()
        ->values()
        ->all();

    $eligibleOrders = $ordersCollection
        ->filter(function ($order) use ($isMine, $requestedOrderIds) {
            $status = strtolower(trim((string) ($order['status'] ?? 'pending')));

            return $isMine($order)
                && $status === 'approved'
                && !in_array((int) ($order['id'] ?? 0), $requestedOrderIds, true);
        })
        ->sortByDesc(fn ($order) => (int) ($order['id'] ?? 0))
        ->map(function (array $order) {
            return [
                'id' => (int) ($order['id'] ?? 0),
                'title' => trim((string) ($order['title'] ?? 'Untitled')),
                'subject' => trim((string) ($order['subject'] ?? 'Other')),
                'pages' => max(1, (int) ($order['pages'] ?? 1)),
                'amount' => writerPayoutForOrder($order),
                'approved_at' => trim((string) ($order['client_notice_at'] ?? $order['writer_last_update_at'] ?? '')),
            ];
        })
        ->values()
        ->all();

    $paymentHistory = $paymentRequests
        ->map(function (array $request) {
            $requestedAt = strtotime((string) ($request['requested_at'] ?? ''));

            return [
                'id' => trim((string) ($request['id'] ?? '')),
                'payment_id' => trim((string) ($request['payment_id'] ?? '')),
                'order_id' => (int) ($request['order_id'] ?? 0),
                'order_title' => trim((string) ($request['order_title'] ?? 'Untitled')),
                'pages' => max(1, (int) ($request['pages'] ?? 1)),
                'amount' => (int) ($request['amount'] ?? 0),
                'status' => trim((string) ($request['status'] ?? 'requested')) ?: 'requested',
                'requested_at' => $requestedAt ? date('d M Y, h:i A', $requestedAt) : 'N/A',
            ];
        })
        ->values()
        ->all();

    return view('writer.payments', [
        'writerProfile' => $writerProfile,
        'menuItems' => $menuItems,
        'eligibleOrders' => $eligibleOrders,
        'paymentHistory' => $paymentHistory,
        'eligibleTotal' => collect($eligibleOrders)->sum('amount'),
        'requestedTotal' => collect($paymentHistory)->sum('amount'),
    ]);
})->name('writer.payments');

Route::post('/writer/payments/{id}/request', function ($id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $writerProfile = currentWriterProfile();
    $writerId = (int) ($writerProfile['id'] ?? 0);
    $writerName = trim((string) ($writerProfile['name'] ?? ''));
    $writerEmail = strtolower(trim((string) ($writerProfile['email'] ?? '')));
    $targetId = (int) $id;
    $orders = loadOrders();
    $order = collect($orders)->first(function ($candidate) use ($targetId) {
        return is_array($candidate) && (int) ($candidate['id'] ?? 0) === $targetId;
    });

    if (!is_array($order)) {
        return redirect()->route('writer.payments')->with('error', 'Order not found.');
    }

    $assignedId = (int) ($order['writer_id'] ?? 0);
    $assignedName = trim((string) ($order['writer_name'] ?? ''));
    $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
    $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
    $isMine = ($writerId > 0 && $assignedId === $writerId)
        || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)
        || ($writerEmail !== '' && $assignedEmail !== '' && strcasecmp($assignedEmail, $writerEmail) === 0);

    if (!$isMine) {
        return redirect()->route('writer.payments')->with('error', 'You can only request payment for your own approved orders.');
    }

    if ($status !== 'approved') {
        return redirect()->route('writer.payments')->with('error', 'Payment can only be requested for approved orders.');
    }

    $paymentRequests = loadWriterPaymentRequests();
    $alreadyRequested = collect($paymentRequests)->contains(function ($request) use ($targetId, $writerId, $writerName, $writerEmail) {
        if (!is_array($request) || (int) ($request['order_id'] ?? 0) !== $targetId) {
            return false;
        }

        $requestWriterId = (int) ($request['writer_id'] ?? 0);
        $requestWriterName = trim((string) ($request['writer_name'] ?? ''));
        $requestWriterEmail = strtolower(trim((string) ($request['writer_email'] ?? '')));

        return ($writerId > 0 && $requestWriterId === $writerId)
            || ($writerName !== '' && $requestWriterName !== '' && strcasecmp($requestWriterName, $writerName) === 0)
            || ($writerEmail !== '' && $requestWriterEmail !== '' && strcasecmp($requestWriterEmail, $writerEmail) === 0);
    });

    if ($alreadyRequested) {
        return redirect()->route('writer.payments')->with('error', 'Payment for this order has already been requested.');
    }

    $amount = writerPayoutForOrder($order);
    $paymentRequest = [
        'id' => (string) Str::uuid(),
        'payment_id' => nextWriterPaymentId($paymentRequests),
        'order_id' => $targetId,
        'order_title' => trim((string) ($order['title'] ?? 'Untitled')),
        'pages' => max(1, (int) ($order['pages'] ?? 1)),
        'amount' => $amount,
        'status' => 'requested',
        'writer_id' => $writerId,
        'writer_name' => $writerName !== '' ? $writerName : trim((string) ($order['writer_name'] ?? 'Writer')),
        'writer_email' => $writerEmail !== '' ? $writerEmail : strtolower(trim((string) ($order['writer_email'] ?? ''))),
        'requested_at' => now()->toIso8601String(),
    ];

    $paymentRequests[] = $paymentRequest;
    saveWriterPaymentRequests($paymentRequests);

    $adminEmail = adminNotificationEmail();
    if ($adminEmail !== '') {
        sendEmailNotification(
            [$adminEmail],
            'Writer Payment Request - Order #' . $targetId,
            implode("\n", [
                'A writer has requested payment for an approved order.',
                'Writer: ' . ($paymentRequest['writer_name'] ?: 'Writer'),
                'Email: ' . ($paymentRequest['writer_email'] ?: 'N/A'),
                'Order: #' . $targetId . ' - ' . $paymentRequest['order_title'],
                'Pages: ' . $paymentRequest['pages'],
                'Amount: Ksh ' . number_format($amount, 0),
                'Requested At: ' . now()->toDateTimeString(),
            ])
        );
    }

    return redirect()->route('writer.payments')->with('status', 'Payment request submitted successfully.');
})->name('writer.payments.request');

Route::get('/writer/orders/{id}', function ($id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $writerEmail = strtolower(trim((string) (session('writer_email') ?? '')));
    $order = collect(loadOrders())->firstWhere('id', (int) $id);

    if (!$order || !is_array($order)) {
        return redirect()->route('writer.dashboard')->with('error', 'Order not found.');
    }

    $assignedId = (int) ($order['writer_id'] ?? 0);
    $assignedName = trim((string) ($order['writer_name'] ?? ''));
    $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
    $status = strtolower(trim((string) ($order['status'] ?? 'pending')));

    $isMine = $writerId > 0 && $assignedId === $writerId
        || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)
        || ($writerEmail !== '' && $assignedEmail !== '' && strcasecmp($assignedEmail, $writerEmail) === 0);

    $isAvailable = $assignedId <= 0
        && $assignedName === ''
        && $assignedEmail === ''
        && orderCanEnterWorkflow($order)
        && in_array($status, ['pending', 'available'], true);

    if (!$isMine && !$isAvailable) {
        return redirect()->route('writer.dashboard')->with('error', 'You cannot view that order.');
    }

    $orderFiles = orderFilesFor((int) $id, 'customer');
    $writerFiles = orderFilesFor((int) $id, 'writer');

    $order['writer_payout'] = writerPayoutForOrder($order);

    return view('writer.order-show', [
        'order' => $order,
        'orderFiles' => $orderFiles,
        'writerFiles' => $writerFiles,
        'canTake' => $isAvailable,
        'isAssignedToCurrent' => $isMine,
    ]);
})->name('writer.order.show');

Route::post('/writer/orders/{id}/claim', function (\Illuminate\Http\Request $request, $id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $orders = loadOrders();
    $targetId = (int) $id;
    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $writerEmail = strtolower(trim((string) (session('writer_email') ?? '')));
    $found = false;
    $updated = false;
    $claimedOrder = null;

    foreach ($orders as &$order) {
        if (!is_array($order) || (int) ($order['id'] ?? 0) !== $targetId) {
            continue;
        }

        $found = true;
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));

        if (($assignedId > 0 && $writerId > 0 && $assignedId !== $writerId)
            || ($assignedName !== '' && $writerName !== '' && strcasecmp($assignedName, $writerName) !== 0)
            || ($assignedEmail !== '' && $writerEmail !== '' && strcasecmp($assignedEmail, $writerEmail) !== 0)) {
            return back()->with('error', 'This order is already assigned.');
        }

        if ($assignedId === $writerId || ($assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)) {
            return back()->with('status', 'This order is already assigned to you.');
        }

        if (!in_array($status, ['pending', 'available'], true)) {
            return back()->with('error', 'Only available orders can be claimed right now.');
        }

        if (!orderCanEnterWorkflow($order)) {
            return back()->with('error', 'This order is awaiting payment confirmation and cannot be claimed yet.');
        }

        $noticeTime = now()->toIso8601String();
        $displayWriter = $writerName !== '' ? $writerName : 'A writer';
        $order['writer_id'] = $writerId;
        $order['writer_name'] = $writerName;
        $order['writer_email'] = $writerEmail;
        $order['status'] = 'assigned';
        $order['writer_taken_at'] = $noticeTime;
        $order['client_notice'] = $displayWriter . ' has taken your order. Work is now in progress.';
        $order['client_notice_at'] = $noticeTime;
        $updated = true;
        $claimedOrder = $order;
        break;
    }

    if (!$found) {
        return back()->with('error', 'Order not found.');
    }

    if (!$updated) {
        return back()->with('error', 'Could not claim this order.');
    }

    saveOrders($orders);
    if (is_array($claimedOrder)) {
        notifyOrderStatusChangeByEmail(
            $claimedOrder,
            'A writer has been assigned to this order. Current status: Assigned.'
        );
    }

    return redirect()->route('writer.dashboard', ['menu' => 'assigned'])
        ->with('status', 'Order taken successfully. Client notified immediately.');
})->name('writer.order.claim');

Route::post('/writer/orders/{id}/status', function (\Illuminate\Http\Request $request, $id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $data = $request->validate([
        'status' => 'required|string|max:50',
    ]);

    $allowedStatuses = ['assigned', 'inprogress', 'editing', 'revision', 'completed', 'approved'];
    $newStatus = strtolower(trim((string) $data['status']));
    if (!in_array($newStatus, $allowedStatuses, true)) {
        return back()->with('error', 'Invalid status selected.');
    }

    $orders = loadOrders();
    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $targetId = (int) $id;
    $found = false;
    $updatedOrder = null;

    foreach ($orders as &$order) {
        if (!is_array($order) || (int) ($order['id'] ?? 0) !== $targetId) {
            continue;
        }

        $found = true;
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));

        if (!($assignedId === $writerId || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0))) {
            return back()->with('error', 'You can only update orders assigned to you.');
        }

        $displayWriter = $assignedName !== '' ? $assignedName : ($writerName !== '' ? $writerName : 'Your writer');
        $noticeTime = now()->toIso8601String();
        $order['status'] = $newStatus;
        $order['writer_last_update_at'] = $noticeTime;

        if ($newStatus === 'revision') {
            $order['client_notice'] = $displayWriter . ' moved your order to revision.';
        } elseif (in_array($newStatus, ['completed', 'approved'], true)) {
            $order['client_notice'] = $displayWriter . ' marked your order as completed.';
        } else {
            $order['client_notice'] = $displayWriter . ' updated your order status to ' . ($newStatus === 'inprogress' ? 'in progress' : $newStatus) . '.';
        }
        $order['client_notice_at'] = $noticeTime;
        $updatedOrder = $order;
        break;
    }

    if (!$found) {
        return back()->with('error', 'Order not found.');
    }

    saveOrders($orders);
    $targetMenu = 'assigned';
    if ($newStatus === 'revision') {
        $targetMenu = 'revision';
    } elseif ($newStatus === 'completed') {
        $targetMenu = 'completed';
    } elseif ($newStatus === 'approved') {
        $targetMenu = 'approved';
    }

    if (is_array($updatedOrder)) {
        $statusLabel = $newStatus === 'inprogress' ? 'In Progress' : ucfirst($newStatus);
        notifyOrderStatusChangeByEmail(
            $updatedOrder,
            'The order status was updated to ' . $statusLabel . '.'
        );
    }

    return redirect()->route('writer.dashboard', ['menu' => $targetMenu])
        ->with('status', 'Order status updated.');
})->name('writer.order.status');

Route::post('/writer/orders/{id}/files', function (\Illuminate\Http\Request $request, $id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $orders = loadOrders();
    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));

    $request->validate([
        'files' => 'nullable|array',
        'files.*' => 'nullable|file|max:5120',
    ]);

    $uploadedFiles = uploadedFilesFromRequest($request->file('files', []));
    if ($uploadedFiles === []) {
        return back()->with('error', 'Please select at least one file.');
    }

    $targetId = (int) $id;
    $found = false;
    $updatedMenu = 'completed';
    $uploadMessage = 'Files uploaded successfully.';
    $statusEmailMessage = null;
    $updatedOrder = null;

    foreach ($orders as &$order) {
        if (!is_array($order) || (int) ($order['id'] ?? 0) !== $targetId) {
            continue;
        }

        $found = true;
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));

        if (!($assignedId === $writerId || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0))) {
            return back()->with('error', 'You can only upload files to orders assigned to you.');
        }

        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
        if (in_array($status, ['assigned', 'inprogress', 'editing', 'revision'], true)) {
            $displayWriter = $assignedName !== '' ? $assignedName : ($writerName !== '' ? $writerName : 'Your writer');
            $noticeTime = now()->toIso8601String();
            $order['status'] = 'completed';
            $order['writer_last_update_at'] = $noticeTime;
            $order['client_notice'] = $displayWriter . ' uploaded files and marked your order as completed.';
            $order['client_notice_at'] = $noticeTime;
            $updatedMenu = 'completed';
            $uploadMessage = 'Files uploaded successfully. Order moved to completed.';
            $statusEmailMessage = 'Uploaded files changed the order status to Completed.';
        } elseif ($status === 'approved') {
            $updatedMenu = 'approved';
        } elseif ($status === 'completed') {
            $updatedMenu = 'completed';
        } else {
            $updatedMenu = 'assigned';
        }

        $updatedOrder = $order;
        break;
    }

    if (!$found) {
        return redirect()->route('writer.dashboard')->with('error', 'Order not found.');
    }

    storeOrderFiles($uploadedFiles, (int) $id, 'writer');
    saveOrders($orders);
    if ($statusEmailMessage !== null && is_array($updatedOrder)) {
        notifyOrderStatusChangeByEmail($updatedOrder, $statusEmailMessage);
    }

    return redirect()->route('writer.dashboard', ['menu' => $updatedMenu])
        ->with('uploaded', $uploadMessage);
})->name('writer.order.files');

Route::get('/customer/dashboard', function (\Illuminate\Http\Request $request) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }
    $orders = loadOrders();
    $customerOrders = collect(ordersForCustomer($orders, session('customer_email')))
        ->sortByDesc(fn ($order) => (int) ($order['id'] ?? 0))
        ->values();
    $statusFilter = strtolower(trim((string) $request->query('status', 'all')));
    $statusCards = [
        ['key' => 'assigned', 'label' => 'Assigned'],
        ['key' => 'pending', 'label' => 'Pending'],
        ['key' => 'bidding', 'label' => 'Bidding'],
        ['key' => 'inprogress', 'label' => 'In Progress'],
        ['key' => 'editing', 'label' => 'Editing'],
        ['key' => 'completed', 'label' => 'Completed'],
        ['key' => 'revision', 'label' => 'Revision'],
        ['key' => 'approved', 'label' => 'Approved'],
        ['key' => 'cancelled', 'label' => 'Cancelled'],
    ];
    $allowedStatuses = collect($statusCards)->pluck('key')->all();

    if ($statusFilter !== 'all' && !in_array($statusFilter, $allowedStatuses, true)) {
        $statusFilter = 'all';
    }

    $statusCards = collect($statusCards)
        ->map(function (array $card) use ($customerOrders, $statusFilter) {
            $matchingOrders = $customerOrders
                ->filter(fn ($order) => strtolower(trim((string) ($order['status'] ?? 'pending'))) === $card['key'])
                ->values();
            $count = $matchingOrders->count();
            $targetUrl = $count === 1
                ? route('customer.order.show', ['id' => $matchingOrders->first()['id']])
                : route('customer.dashboard', ['status' => $card['key']]);

            return [
                ...$card,
                'count' => $count,
                'url' => $targetUrl,
                'active' => $statusFilter === $card['key'],
            ];
        })
        ->all();

    $visibleOrders = $statusFilter === 'all'
        ? $customerOrders->all()
        : $customerOrders
            ->filter(fn ($order) => strtolower(trim((string) ($order['status'] ?? 'pending'))) === $statusFilter)
            ->values()
            ->all();
    $selectedStatusLabel = collect($statusCards)->firstWhere('active', true)['label'] ?? null;

    return view('customer.dashboard', [
        'orders' => $visibleOrders,
        'statusCards' => $statusCards,
        'statusFilter' => $statusFilter,
        'selectedStatusLabel' => $selectedStatusLabel,
        'orderCount' => $customerOrders->count(),
    ]);
})->name('customer.dashboard');

Route::post('/order/submit', function (\Illuminate\Http\Request $request) {
    if (!session('customer_logged_in') && !session('admin_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }

    $data = $request->validate([
        'title' => 'nullable|string|max:255',
        'type' => 'nullable|string|max:100',
        'level' => 'nullable|string|max:50',
        'format' => 'nullable|string|max:50',
        'spacing' => 'nullable|string|max:20',
        'deadline' => 'nullable|string|max:50',
        'category' => 'nullable|string|max:50',
        'subject' => 'nullable|string|max:100',
        'instructions' => 'nullable|string',
        'pages' => 'nullable|integer|min:1',
        'sources' => 'nullable|integer|min:0',
        'slides' => 'nullable|integer|min:0',
        'charts' => 'nullable|integer|min:0',
        'vip_support' => 'nullable|boolean',
        'draft_outline' => 'nullable|boolean',
        'files' => 'nullable|array',
        'files.*' => 'nullable|file|max:5120',
    ]);

    $orders = loadOrders();
    $id = (collect($orders)->max('id') ?? 802) + 1;
    $pages = $data['pages'] ?? 1;
    $selectedLevel = $data['level'] ?? 'College';
    $selectedDeadline = $data['deadline'] ?? '48 Hours';
    $selectedCategory = $data['category'] ?? 'Standard';
    $pricePerPage = pricePerPageFor($selectedLevel, $selectedDeadline);
    $categoryMultiplier = categoryMultiplierFor($selectedCategory);
    $createdAt = now();
    $deadlineSeconds = deadlineSecondsFor($selectedDeadline);
    $dueAt = $deadlineSeconds !== null
        ? $createdAt->copy()->addSeconds($deadlineSeconds)->toIso8601String()
        : null;
    $vipSupport = $request->boolean('vip_support');
    $draftOutline = $request->boolean('draft_outline');
    $instructions = trim((string) ($data['instructions'] ?? ''));
    $extrasCost = ($vipSupport ? 25 : 0) + ($draftOutline ? 20 : 0);
    $baseCost = $pages * $pricePerPage;
    $totalCost = round(($baseCost * $categoryMultiplier) + $extrasCost, 2);
    $isAdminPosting = session('admin_logged_in') && !session('customer_logged_in');
    $postedBy = $isAdminPosting ? 'admin' : 'customer';
    $posterName = $isAdminPosting
        ? trim((string) session('admin_name', 'Admin'))
        : trim((string) session('customer_name', 'Customer'));
    $posterEmail = $isAdminPosting
        ? strtolower(trim((string) session('admin_email', adminNotificationEmail())))
        : strtolower(trim((string) session('customer_email', 'customer')));
    $orderRecord = [
        'id' => $id,
        'title' => $data['title'] ?? 'Untitled Paper',
        'pages' => $pages,
        'cost' => $totalCost,
        'status' => 'pending',
        'deadline' => $selectedDeadline,
        'level' => $selectedLevel,
        'type' => $data['type'] ?? 'Essay',
        'format' => $data['format'] ?? 'APA',
        'spacing' => $data['spacing'] ?? 'Double',
        'category' => $selectedCategory,
        'subject' => $data['subject'] ?? 'Other',
        'instructions' => $instructions !== '' ? $instructions : null,
        'sources' => $data['sources'] ?? 0,
        'slides' => $data['slides'] ?? 0,
        'charts' => $data['charts'] ?? 0,
        'vip_support' => $vipSupport,
        'draft_outline' => $draftOutline,
        'created_at' => $createdAt->toIso8601String(),
        'due_at' => $dueAt,
        'writer_payout' => writerPayoutForPages($pages),
        'posted_by' => $postedBy,
        'posted_by_name' => $posterName !== '' ? $posterName : ($isAdminPosting ? 'Admin' : 'Customer'),
        'posted_by_email' => $posterEmail,
        'customer_email' => $isAdminPosting ? null : $posterEmail,
        'customer_name' => $isAdminPosting ? null : ($posterName !== '' ? $posterName : 'Customer'),
        'payment_status' => $isAdminPosting ? 'not_required' : 'unpaid',
        'payment_reference' => $isAdminPosting ? '' : generatePesapalMerchantReference($id),
        'payment_currency' => strtoupper(trim((string) (pesapalSettings()['currency'] ?? 'USD'))) ?: 'USD',
        'payment_amount' => $totalCost,
    ];
    $orders[] = $orderRecord;
    saveOrders($orders);
    storeOrderFiles(uploadedFilesFromRequest($request->file('files', [])), $id);

    if ($isAdminPosting) {
        notifyOrderCreatedByEmail($orderRecord);

        return redirect()->route('admin.orders')->with('assigned', 'Order posted successfully.');
    }

    try {
        $pesapalOrder = $orderRecord;
        $pesapalOrder['payment_reference'] = generatePesapalMerchantReference($id);
        $checkout = pesapalSubmitOrder($pesapalOrder);
        $orders = loadOrders();

        foreach ($orders as &$order) {
            if (!is_array($order) || (int) ($order['id'] ?? 0) !== $id) {
                continue;
            }

            $order['payment_reference'] = trim((string) ($checkout['merchant_reference'] ?? $pesapalOrder['payment_reference']));
            $order['payment_tracking_id'] = trim((string) ($checkout['order_tracking_id'] ?? ''));
            $order['payment_checkout_url'] = trim((string) ($checkout['redirect_url'] ?? ''));
            $order['payment_last_started_at'] = now()->toIso8601String();
            break;
        }
        unset($order);

        saveOrders($orders);

        return redirect()->away(trim((string) ($checkout['redirect_url'] ?? '')));
    } catch (\Throwable $e) {
        report($e);

        return redirect()
            ->route('customer.order.show', ['id' => $id])
            ->with('error', 'Your order was saved, but Pesapal checkout could not be started. Click Pay Now to retry.');
    }
})->name('order.submit');

Route::post('/customer/orders/{id}/pay', function ($id) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }

    $targetId = (int) $id;
    $customerOrder = collect(ordersForCustomer(loadOrders(), session('customer_email')))
        ->firstWhere('id', $targetId);

    if (!$customerOrder || !is_array($customerOrder)) {
        return redirect()->route('customer.dashboard')->with('error', 'Order not found.');
    }

    if (!orderRequiresCustomerPayment($customerOrder)) {
        return redirect()->route('customer.order.show', ['id' => $targetId])->with('status', 'This order does not require online payment.');
    }

    if (orderIsPaid($customerOrder)) {
        return redirect()->route('customer.order.show', ['id' => $targetId])->with('status', 'This order is already marked as paid.');
    }

    try {
        $orders = loadOrders();
        $paymentReference = generatePesapalMerchantReference($targetId);

        foreach ($orders as &$order) {
            if (!is_array($order) || (int) ($order['id'] ?? 0) !== $targetId) {
                continue;
            }

            $order['payment_reference'] = $paymentReference;
            $customerOrder = $order;
            break;
        }
        unset($order);

        saveOrders($orders);

        $checkout = pesapalSubmitOrder($customerOrder);
        $orders = loadOrders();

        foreach ($orders as &$order) {
            if (!is_array($order) || (int) ($order['id'] ?? 0) !== $targetId) {
                continue;
            }

            $order['payment_reference'] = trim((string) ($checkout['merchant_reference'] ?? $paymentReference));
            $order['payment_tracking_id'] = trim((string) ($checkout['order_tracking_id'] ?? ''));
            $order['payment_checkout_url'] = trim((string) ($checkout['redirect_url'] ?? ''));
            $order['payment_last_started_at'] = now()->toIso8601String();
            break;
        }
        unset($order);

        saveOrders($orders);

        return redirect()->away(trim((string) ($checkout['redirect_url'] ?? '')));
    } catch (\Throwable $e) {
        report($e);

        return redirect()
            ->route('customer.order.show', ['id' => $targetId])
            ->with('error', 'Unable to start the Pesapal checkout right now. Please try again shortly.');
    }
})->name('customer.order.pay');

Route::get('/payments/pesapal/callback', function (\Illuminate\Http\Request $request) {
    $trackingId = trim((string) $request->query('OrderTrackingId', ''));
    $merchantReference = trim((string) $request->query('OrderMerchantReference', ''));
    $orderId = findOrderIdByMerchantReference($merchantReference);

    if ($orderId === null || $orderId <= 0) {
        return redirect()->route('customer.dashboard')->with('error', 'We could not match that Pesapal payment to an order.');
    }

    try {
        $transactionStatus = pesapalGetTransactionStatus($trackingId);
        $updatedOrder = syncOrderPaymentStatusFromPesapal($orderId, $transactionStatus, $trackingId, $merchantReference);

        if (!$updatedOrder) {
            return redirect()->route('customer.dashboard')->with('error', 'Order not found while updating the Pesapal payment.');
        }

        $paymentStatus = orderPaymentStatus($updatedOrder);
        $flashKey = $paymentStatus === 'completed' ? 'status' : 'error';
        $flashMessage = $paymentStatus === 'completed'
            ? 'Pesapal payment confirmed successfully.'
            : 'Pesapal payment status: ' . orderPaymentStatusLabel($updatedOrder) . '.';

        if (session('customer_logged_in')) {
            return redirect()
                ->route('customer.order.show', ['id' => $orderId])
                ->with($flashKey, $flashMessage);
        }

        return redirect()->route('order', ['tab' => 'existing'])->with($flashKey, $flashMessage);
    } catch (\Throwable $e) {
        report($e);

        if (session('customer_logged_in')) {
            return redirect()
                ->route('customer.order.show', ['id' => $orderId])
                ->with('error', 'We could not verify the Pesapal payment yet. Please refresh this order shortly.');
        }

        return redirect()->route('order', ['tab' => 'existing'])
            ->with('error', 'We could not verify the Pesapal payment yet. Please log in and check your order shortly.');
    }
})->name('payments.pesapal.callback');

Route::get('/payments/pesapal/ipn', function (\Illuminate\Http\Request $request) {
    $trackingId = trim((string) $request->query('OrderTrackingId', ''));
    $merchantReference = trim((string) $request->query('OrderMerchantReference', ''));
    $notificationType = trim((string) $request->query('OrderNotificationType', 'IPNCHANGE'));
    $orderId = findOrderIdByMerchantReference($merchantReference);

    if ($orderId === null || $orderId <= 0) {
        return response()->json([
            'status' => 404,
            'message' => 'Order not found.',
        ], 404);
    }

    try {
        $transactionStatus = pesapalGetTransactionStatus($trackingId);
        $updatedOrder = syncOrderPaymentStatusFromPesapal($orderId, $transactionStatus, $trackingId, $merchantReference);

        return response()->json([
            'status' => 200,
            'orderNotificationType' => $notificationType,
            'orderTrackingId' => $trackingId,
            'orderMerchantReference' => $merchantReference,
            'paymentStatus' => is_array($updatedOrder) ? orderPaymentStatus($updatedOrder) : 'unknown',
        ]);
    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'status' => 500,
            'orderTrackingId' => $trackingId,
            'orderMerchantReference' => $merchantReference,
            'message' => 'Unable to verify payment status.',
        ], 500);
    }
})->name('payments.pesapal.ipn');

Route::get('/customer/orders/{id}', function ($id) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }
    $orders = ordersForCustomer(loadOrders(), session('customer_email'));
    $order = collect($orders)->firstWhere('id', (int)$id);
    if (!$order) {
        return redirect()->route('customer.dashboard');
    }
    $orderFiles = orderFilesFor((int) $id, 'customer');
    $writerFiles = orderFilesFor((int) $id, 'writer');
    return view('customer.order', [
        'order' => $order,
        'orderFiles' => $orderFiles,
        'writerFiles' => $writerFiles,
        'orderCount' => count($orders),
    ]);
})->name('customer.order.show');

Route::post('/customer/orders/{id}/files', function (\Illuminate\Http\Request $request, $id) {
    if (!session('customer_logged_in') && !session('admin_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }

    $orders = session('admin_logged_in')
        ? loadOrders()
        : ordersForCustomer(loadOrders(), session('customer_email'));
    $order = collect($orders)->firstWhere('id', (int) $id);
    if (!$order) {
        return session('admin_logged_in')
            ? redirect()->route('admin.orders')
            : redirect()->route('customer.dashboard');
    }

    $request->validate([
        'files' => 'nullable|array',
        'files.*' => 'nullable|file|max:5120', // 5MB each for demo
    ]);

    storeOrderFiles(uploadedFilesFromRequest($request->file('files', [])), (int) $id);

    return back()->with('uploaded', 'Files uploaded successfully.');
})->name('customer.order.files');

Route::get('/orders/{id}/files/{file}', function ($id, $file) {
    $targetId = (int) $id;
    $order = collect(loadOrders())->firstWhere('id', $targetId);
    if (!$order || !is_array($order)) {
        abort(404);
    }

    $canAccess = false;

    if (session('admin_logged_in')) {
        $canAccess = true;
    } elseif (session('customer_logged_in')) {
        $customerEmail = trim((string) session('customer_email', ''));
        $orderCustomerEmail = trim((string) ($order['customer_email'] ?? ''));
        $canAccess = $customerEmail !== ''
            && $orderCustomerEmail !== ''
            && strcasecmp($customerEmail, $orderCustomerEmail) === 0;
    } elseif (session('writer_logged_in')) {
        $writerId = (int) (session('writer_id') ?? 0);
        $writerName = trim((string) (session('writer_name') ?? ''));
        $writerEmail = strtolower(trim((string) (session('writer_email') ?? '')));
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
        $isMine = $writerId > 0 && $assignedId === $writerId
            || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)
            || ($writerEmail !== '' && $assignedEmail !== '' && strcasecmp($assignedEmail, $writerEmail) === 0);
        $isAvailable = $assignedId <= 0
            && $assignedName === ''
            && $assignedEmail === ''
            && in_array($status, ['pending', 'available'], true);
        $canAccess = $isMine || $isAvailable;
    }

    if (!$canAccess) {
        abort(404);
    }

    $storedFile = findOrderFileFor($targetId, (string) $file);
    if (!$storedFile) {
        abort(404);
    }

    $storedPath = basename((string) ($storedFile['path'] ?? ''));
    if ($storedPath === '') {
        abort(404);
    }

    $fullPath = storage_path('app/uploads/' . $storedPath);
    if (!is_file($fullPath)) {
        abort(404);
    }

    return response()->download($fullPath, (string) ($storedFile['name'] ?? $storedPath));
})->where('file', '.*')->name('order.file.download');

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $email = $request->input('email');
    $password = $request->input('password');

    if ($email === 'admin@demo.com' && $password === 'admin123') {
        session([
            'admin_logged_in' => true,
            'admin_name' => 'Admin',
            'admin_email' => $email,
        ]);
        return redirect('/admin');
    }

    return back()->withErrors([
        'credentials' => 'Invalid email or password.',
    ])->withInput();
})->name('admin.login.submit');

Route::get('/admin/logout', function () {
    session()->forget(['admin_logged_in', 'admin_name', 'admin_email']);
    return redirect()->route('admin.login');
})->name('admin.logout');

Route::get('/admin', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $orders = collect(loadOrders())
        ->filter(fn ($order) => is_array($order))
        ->map(function (array $order) {
            $order['status'] = normalizeAdminOrderStatus($order['status'] ?? 'pending');
            $order['writer_payout'] = writerPayoutForOrder($order);

            return $order;
        })
        ->sortByDesc(fn ($order) => (int) ($order['id'] ?? 0))
        ->values()
        ->all();

    return view('admin.dashboard', [
        'orders' => $orders,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.dashboard');

Route::get('/admin/orders', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $statusOrder = array_flip([
        'pending',
        'available',
        'assigned',
        'editing',
        'completed',
        'revision',
        'approved',
        'cancelled',
    ]);

    $status = strtolower(trim((string) $request->query('status', '')));
    if ($status !== '' && !array_key_exists($status, $statusOrder)) {
        $status = '';
    }

    $orders = collect(loadOrders())
        ->filter(fn ($order) => is_array($order))
        ->map(function (array $order) {
            $order['status'] = normalizeAdminOrderStatus($order['status'] ?? 'pending');
            $order['writer_payout'] = writerPayoutForOrder($order);

            return $order;
        });
    $allOrders = $orders;

    if ($status !== '') {
        $orders = $orders->filter(
            fn (array $order) => ($order['status'] ?? 'pending') === $status
        );
    }

    $orders = $orders
        ->sort(function (array $left, array $right) {
            $leftCreatedAt = strtotime((string) ($left['created_at'] ?? ''));
            $rightCreatedAt = strtotime((string) ($right['created_at'] ?? ''));

            if ($leftCreatedAt !== false && $rightCreatedAt !== false && $leftCreatedAt !== $rightCreatedAt) {
                return $rightCreatedAt <=> $leftCreatedAt;
            }

            if ($leftCreatedAt === false && $rightCreatedAt !== false) {
                return 1;
            }

            if ($leftCreatedAt !== false && $rightCreatedAt === false) {
                return -1;
            }

            return ((int) ($right['id'] ?? 0)) <=> ((int) ($left['id'] ?? 0));
        })
        ->values()
        ->all();
    $registeredWriters = collect(loadWriters())
        ->map(function ($writer) {
            $name = trim((string) ($writer['name'] ?? ''));
            if ($name === '') {
                return null;
            }

            return [
                'id' => is_numeric($writer['id'] ?? null) ? (int) $writer['id'] : null,
                'name' => $name,
            ];
        })
        ->filter()
        ->values();

    $maxWriterId = (int) ($registeredWriters->max('id') ?? 0);
    $registeredByKey = $registeredWriters->keyBy(
        fn ($writer) => strtolower(trim((string) $writer['name']))
    );

    $historicalNames = $allOrders
        ->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
        ->filter()
        ->groupBy(fn ($name) => strtolower($name))
        ->values()
        ->map(fn ($rows) => $rows->first());

    foreach ($historicalNames as $historicalName) {
        $key = strtolower(trim((string) $historicalName));
        if ($key === '' || $registeredByKey->has($key)) {
            continue;
        }
        $maxWriterId++;
        $registeredWriters->push([
            'id' => $maxWriterId,
            'name' => $historicalName,
        ]);
        $registeredByKey->put($key, true);
    }

    $writers = $registeredWriters
        ->values()
        ->all();
    return view('admin.orders', [
        'orders' => $orders,
        'status' => $status,
        'writers' => $writers,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.orders');

Route::post('/admin/orders/{id}/assign', function (\Illuminate\Http\Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $data = $request->validate([
        'writer_id' => 'required',
        'writer_name' => 'nullable|string|max:255',
        'status' => 'nullable|string|max:50',
    ]);

    $selectedWriter = collect(loadWriters())
        ->filter(fn ($writer) => is_array($writer))
        ->first(function ($writer) use ($data) {
            return (string) ($writer['id'] ?? '') === (string) $data['writer_id'];
        });

    $fallbackWriterName = trim((string) ($data['writer_name'] ?? ''));
    if ((!$selectedWriter || trim((string) ($selectedWriter['name'] ?? '')) === '') && $fallbackWriterName === '') {
        return back()->with('assigned', 'Selected writer was not found.');
    }

    $selectedWriterId = is_numeric($selectedWriter['id'] ?? null) ? (int) $selectedWriter['id'] : $data['writer_id'];
    $selectedWriterName = $selectedWriter
        ? trim((string) ($selectedWriter['name'] ?? ''))
        : $fallbackWriterName;
    $selectedWriterEmail = $selectedWriter
        ? strtolower(trim((string) ($selectedWriter['email'] ?? '')))
        : '';
    $newStatus = strtolower(trim((string) ($data['status'] ?? 'assigned'))) ?: 'assigned';
    $clearAssignment = in_array($newStatus, ['pending', 'available'], true);
    $orders = loadOrders();
    $updatedOrder = null;
    $statusChanged = false;
    $writerChanged = false;

    foreach ($orders as &$order) {
        if ((int) ($order['id'] ?? 0) === (int) $id) {
            $previousStatus = strtolower(trim((string) ($order['status'] ?? 'pending')));
            $previousWriterId = (string) ($order['writer_id'] ?? '');
            $previousWriterEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));

            if (orderRequiresCustomerPayment($order) && !orderIsPaid($order)) {
                $allowedWhileUnpaid = ['pending', 'available', 'cancelled'];
                if (!in_array($newStatus, $allowedWhileUnpaid, true)) {
                    return back()->with('assigned', 'This customer order is still awaiting Pesapal payment confirmation.');
                }
            }

            $order['writer_id'] = $clearAssignment ? 0 : $selectedWriterId;
            $order['writer_name'] = $clearAssignment ? '' : $selectedWriterName;
            $order['writer_email'] = $clearAssignment ? '' : $selectedWriterEmail;
            $order['status'] = $newStatus;

            $statusChanged = $previousStatus !== $newStatus;
            $writerChanged = $previousWriterId !== (string) ($order['writer_id'] ?? '')
                || $previousWriterEmail !== strtolower(trim((string) ($order['writer_email'] ?? '')));
            $updatedOrder = $order;
            break;
        }
    }

    if (!is_array($updatedOrder)) {
        return back()->with('assigned', 'Order not found.');
    }

    saveOrders($orders);

    if ($statusChanged || $writerChanged) {
        $statusLabel = $newStatus === 'inprogress' ? 'In Progress' : ucfirst($newStatus);
        $message = ($clearAssignment && $writerChanged)
            ? 'An administrator returned this order to the available queue. Current status: ' . $statusLabel . '.'
            : ($writerChanged
            ? 'A writer has been assigned to this order. Current status: ' . $statusLabel . '.'
            : 'The order status was updated to ' . $statusLabel . '.');
        notifyOrderStatusChangeByEmail($updatedOrder, $message);
    }

    return redirect()
        ->route('admin.orders', ['status' => normalizeAdminOrderStatus($newStatus)])
        ->with('assigned', $clearAssignment ? 'Order moved to available successfully.' : 'Order assigned successfully.');
})->name('admin.orders.assign');

Route::delete('/admin/orders/{id}', function (\Illuminate\Http\Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $targetId = (int) $id;
    $orders = loadOrders();
    $exists = collect($orders)->contains(
        fn ($order) => (int) ($order['id'] ?? 0) === $targetId
    );

    if (!$exists) {
        return back()->with('deleted', 'Order not found.');
    }

    $remainingOrders = collect($orders)
        ->reject(fn ($order) => (int) ($order['id'] ?? 0) === $targetId)
        ->values()
        ->all();

    saveOrders($remainingOrders);

    $storedFiles = collect(session('order_files', []));
    $filesToDelete = $storedFiles->filter(
        fn ($file) => (int) ($file['order_id'] ?? 0) === $targetId
    );
    $keptFiles = $storedFiles->reject(
        fn ($file) => (int) ($file['order_id'] ?? 0) === $targetId
    )->values()->all();

    foreach ($filesToDelete as $file) {
        $name = basename((string) ($file['path'] ?? ''));
        if ($name === '') {
            continue;
        }
        $fullPath = storage_path('app/uploads/' . $name);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    session(['order_files' => $keptFiles]);

    $redirectTo = trim((string) $request->input('redirect_to', ''));
    if ($redirectTo !== '' && str_starts_with($redirectTo, url('/'))) {
        return redirect($redirectTo)->with('deleted', 'Order deleted successfully.');
    }

    return back()->with('deleted', 'Order deleted successfully.');
})->name('admin.orders.delete');

Route::get('/admin/orders/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $orders = loadOrders();
    $order = collect($orders)->firstWhere('id', (int)$id);
    if (!$order) {
        return redirect()->route('admin.orders');
    }
    $order['writer_payout'] = writerPayoutForOrder($order);
    $writers = collect(loadWriters())
        ->filter(fn ($writer) => is_array($writer) && trim((string) ($writer['name'] ?? '')) !== '')
        ->map(function (array $writer) {
            return [
                'id' => is_numeric($writer['id'] ?? null) ? (int) $writer['id'] : null,
                'name' => trim((string) ($writer['name'] ?? '')),
            ];
        })
        ->values();
    $currentWriterName = trim((string) ($order['writer_name'] ?? ''));
    if ($currentWriterName !== '' && !$writers->contains(fn ($writer) => strcasecmp((string) ($writer['name'] ?? ''), $currentWriterName) === 0)) {
        $writers->push([
            'id' => is_numeric($order['writer_id'] ?? null) ? (int) $order['writer_id'] : null,
            'name' => $currentWriterName,
        ]);
    }
    $writers = $writers->all();
    $orderFiles = orderFilesFor((int) $id, 'customer');
    $writerFiles = orderFilesFor((int) $id, 'writer');
    return view('admin.order-show', [
        'order' => $order,
        'orderFiles' => $orderFiles,
        'writerFiles' => $writerFiles,
        'writers' => $writers,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.order.show');

Route::get('/admin/payments', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $ordersById = collect(loadOrders())
        ->filter(fn ($order) => is_array($order))
        ->keyBy(fn ($order) => (int) ($order['id'] ?? 0));

    $paymentRequests = collect(loadWriterPaymentRequests())
        ->filter(fn ($request) => is_array($request))
        ->sortByDesc(fn ($request) => strtotime((string) ($request['requested_at'] ?? '')) ?: 0)
        ->map(function (array $request) use ($ordersById) {
            $orderId = (int) ($request['order_id'] ?? 0);
            $order = $ordersById->get($orderId);
            $requestedAtTimestamp = strtotime((string) ($request['requested_at'] ?? ''));
            $requestStatus = strtolower(trim((string) ($request['status'] ?? 'requested'))) ?: 'requested';

            return [
                'id' => trim((string) ($request['id'] ?? '')),
                'payment_id' => trim((string) ($request['payment_id'] ?? '')),
                'order_id' => $orderId,
                'order_title' => trim((string) ($request['order_title'] ?? ($order['title'] ?? 'Untitled'))),
                'writer_name' => trim((string) ($request['writer_name'] ?? ($order['writer_name'] ?? 'Writer'))),
                'writer_email' => trim((string) ($request['writer_email'] ?? ($order['writer_email'] ?? ''))),
                'pages' => max(1, (int) ($request['pages'] ?? ($order['pages'] ?? 1))),
                'amount' => is_array($order)
                    ? writerPayoutForOrder($order)
                    : (int) ($request['amount'] ?? 0),
                'status' => $requestStatus,
                'order_status' => is_array($order)
                    ? strtolower(trim((string) ($order['status'] ?? 'pending')))
                    : 'unknown',
                'requested_at' => $requestedAtTimestamp
                    ? date('d M Y, h:i A', $requestedAtTimestamp)
                    : 'N/A',
            ];
        })
        ->values();

    $activeWriters = $paymentRequests
        ->map(fn ($request) => strtolower(trim((string) ($request['writer_email'] ?: $request['writer_name']))))
        ->filter()
        ->unique()
        ->count();

    return view('admin.payments', [
        'paymentRequests' => $paymentRequests->all(),
        'requestedTotal' => $paymentRequests->sum('amount'),
        'requestedOrders' => $paymentRequests->pluck('order_id')->filter()->unique()->count(),
        'requestingWriters' => $activeWriters,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.payments');

Route::get('/admin/courses', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $orders = collect(loadOrders());
    $courses = $orders
        ->map(function ($order) {
            return [
                'subject' => trim((string) ($order['subject'] ?? '')),
                'writer' => trim((string) ($order['writer_name'] ?? '')),
            ];
        })
        ->filter(fn ($row) => $row['subject'] !== '')
        ->groupBy(fn ($row) => strtolower($row['subject']))
        ->values()
        ->map(function ($rows, $index) {
            $subject = $rows->first()['subject'];
            $activeWriters = collect($rows)
                ->map(fn ($row) => strtolower($row['writer']))
                ->filter()
                ->unique()
                ->count();

            return [
                'id' => $index + 1,
                'name' => $subject,
                'active_writers' => $activeWriters,
            ];
        })
        ->all();

    return view('admin.courses', [
        'courses' => $courses,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.courses');

Route::get('/admin/clients', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $orders = loadOrders();
    $clients = collect($orders)
        ->groupBy(fn ($order) => trim((string) ($order['customer_email'] ?? '')))
        ->filter(fn ($rows, $email) => $email !== '')
        ->map(function ($rows, $email) {
            $first = $rows->first();
            return [
                'name' => $first['customer_name'] ?? (strstr((string) $email, '@', true) ?: 'Client'),
                'email' => $email,
                'orders' => $rows->count(),
                'spent' => round($rows->sum(fn ($row) => (float) ($row['cost'] ?? 0)), 2),
            ];
        })
        ->values()
        ->all();

    return view('admin.clients', [
        'clients' => $clients,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.clients');

Route::get('/admin/writers', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $orders = loadOrders();
    $registeredWriters = collect(loadWriters())
        ->map(function ($writer) {
            $name = trim((string) ($writer['name'] ?? ''));
            if ($name === '') {
                return null;
            }

            return [
                'id' => is_numeric($writer['id'] ?? null) ? (int) $writer['id'] : null,
                'name' => $name,
            ];
        })
        ->filter()
        ->values();

    $maxWriterId = (int) ($registeredWriters->max('id') ?? 0);
    $registeredByKey = $registeredWriters->keyBy(
        fn ($writer) => strtolower(trim((string) ($writer['name'] ?? '')))
    );

    $legacyWriterNames = collect($orders)
        ->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
        ->filter()
        ->groupBy(fn ($name) => strtolower($name))
        ->values()
        ->map(fn ($rows) => $rows->first());

    foreach ($legacyWriterNames as $legacyName) {
        $key = strtolower(trim((string) $legacyName));
        if ($key === '' || $registeredByKey->has($key)) {
            continue;
        }
        $maxWriterId++;
        $registeredWriters->push([
            'id' => $maxWriterId,
            'name' => $legacyName,
        ]);
        $registeredByKey->put($key, true);
    }

    $writers = $registeredWriters->values()->map(function ($writer) use ($orders) {
        $writerName = $writer['name'];
        $assigned = collect($orders)->filter(
            fn ($order) => strcasecmp(trim((string) ($order['writer_name'] ?? '')), $writerName) === 0
        );

        return [
            'id' => $writer['id'],
            'name' => $writerName,
            'orders' => $assigned->count(),
            'status' => $assigned->isEmpty() ? 'Available' : 'Active',
        ];
    })->all();

    return view('admin.writers', [
        'writers' => $writers,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.writers');

Route::get('/admin/settings', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    return view('admin.settings', [
        'levels' => pricingLevels(),
        'deadlines' => pricingDeadlines(),
        'pricing' => loadPricing(),
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.settings');

Route::post('/admin/settings', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $levels = pricingLevels();
    $deadlines = pricingDeadlines();
    $current = loadPricing();
    $submitted = $request->input('prices', []);
    $updated = [];

    foreach ($levels as $level) {
        foreach ($deadlines as $deadline) {
            $raw = $submitted[$level][$deadline] ?? $current[$level][$deadline] ?? null;

            if (!is_numeric($raw)) {
                return back()->withErrors([
                    'prices' => "Invalid price for {$level} / {$deadline}.",
                ])->withInput();
            }

            $updated[$level][$deadline] = round(max(0, (float) $raw), 2);
        }
    }

    savePricing($updated);

    return back()->with('settings_saved', 'Pricing settings updated successfully.');
})->name('admin.settings.update');

Route::get('/admin/homepage', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    return view('admin.homepage', [
        'homepageContent' => loadHomepageContent(),
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.homepage');

Route::post('/admin/homepage', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $data = $request->validate([
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048|dimensions:max_width=4000,max_height=4000',
        'remove_logo' => 'nullable|boolean',
        'hero_eyebrow' => 'required|string|max:255',
        'hero_cta_pill' => 'required|string|max:255',
        'hero_title_prefix' => 'required|string|max:255',
        'hero_title_highlight' => 'required|string|max:255',
        'hero_title_suffix' => 'required|string|max:255',
        'hero_description' => 'required|string',
        'badge_value_1' => 'required|string|max:60',
        'badge_label_1' => 'required|string|max:120',
        'badge_color_1' => 'required|string|max:20',
        'badge_value_2' => 'required|string|max:60',
        'badge_label_2' => 'required|string|max:120',
        'badge_color_2' => 'required|string|max:20',
        'badge_value_3' => 'required|string|max:60',
        'badge_label_3' => 'required|string|max:120',
        'badge_color_3' => 'required|string|max:20',
        'card_title_1' => 'required|string|max:255',
        'card_detail_1' => 'required|string|max:255',
        'card_title_2' => 'required|string|max:255',
        'card_detail_2' => 'required|string|max:255',
        'card_title_3' => 'required|string|max:255',
        'card_detail_3' => 'required|string|max:255',
        'card_title_4' => 'required|string|max:255',
        'card_detail_4' => 'required|string|max:255',
        'seo_html' => 'nullable|string',
    ]);

    $content = [
        'hero' => [
            'eyebrow' => trim((string) $data['hero_eyebrow']),
            'cta_pill' => trim((string) $data['hero_cta_pill']),
            'title_prefix' => trim((string) $data['hero_title_prefix']),
            'title_highlight' => trim((string) $data['hero_title_highlight']),
            'title_suffix' => trim((string) $data['hero_title_suffix']),
            'description' => trim((string) $data['hero_description']),
        ],
        'badges' => [
            [
                'value' => trim((string) $data['badge_value_1']),
                'label' => trim((string) $data['badge_label_1']),
                'color' => trim((string) $data['badge_color_1']),
            ],
            [
                'value' => trim((string) $data['badge_value_2']),
                'label' => trim((string) $data['badge_label_2']),
                'color' => trim((string) $data['badge_color_2']),
            ],
            [
                'value' => trim((string) $data['badge_value_3']),
                'label' => trim((string) $data['badge_label_3']),
                'color' => trim((string) $data['badge_color_3']),
            ],
        ],
        'cards' => [
            [
                'title' => trim((string) $data['card_title_1']),
                'detail' => trim((string) $data['card_detail_1']),
            ],
            [
                'title' => trim((string) $data['card_title_2']),
                'detail' => trim((string) $data['card_detail_2']),
            ],
            [
                'title' => trim((string) $data['card_title_3']),
                'detail' => trim((string) $data['card_detail_3']),
            ],
            [
                'title' => trim((string) $data['card_title_4']),
                'detail' => trim((string) $data['card_detail_4']),
            ],
        ],
        'seo_html' => trim((string) ($data['seo_html'] ?? '')),
    ];

    $previousLogo = loadHomepageContent()['logo_path'] ?? null;
    $content['logo_path'] = $previousLogo;
    if ($request->hasFile('logo')) {
        $logo = $request->file('logo');
        $directory = public_path('uploads/branding');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $filename = (string) \Illuminate\Support\Str::uuid().'.'.$logo->extension();
        $logo->move($directory, $filename);
        $content['logo_path'] = 'uploads/branding/'.$filename;
    } elseif ($request->boolean('remove_logo')) {
        $content['logo_path'] = null;
    }

    saveHomepageContent($content);

    if ($previousLogo && $previousLogo !== $content['logo_path']
        && preg_match('~^uploads/branding/[a-f0-9-]+\.(jpg|jpeg|png|webp|gif)$~', $previousLogo)
        && is_file(public_path($previousLogo))) {
        unlink(public_path($previousLogo));
    }

    return back()->with('homepage_saved', 'Homepage content updated successfully.');
})->name('admin.homepage.update');

Route::get('/admin/pages', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $pages = collect(loadCustomPages())
        ->sortByDesc(fn ($page) => (int) ($page['id'] ?? 0))
        ->values()
        ->all();
    $editId = (int) $request->query('edit', 0);
    $editingPage = collect($pages)->firstWhere('id', $editId);

    return view('admin.pages', [
        'pages' => $pages,
        'editingPage' => is_array($editingPage) ? $editingPage : null,
        'navCounts' => adminNavigationCounts(),
    ]);
})->name('admin.pages');

Route::post('/admin/pages', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $data = $request->validate([
        'id' => 'nullable|integer',
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'summary' => 'nullable|string|max:500',
        'content' => 'nullable|string',
        'status' => 'required|string|in:draft,published',
    ]);

    $pages = loadCustomPages();
    $targetId = (int) ($data['id'] ?? 0);
    $title = trim((string) $data['title']);
    $slug = Str::slug(trim((string) ($data['slug'] ?? '')));
    if ($slug === '') {
        $slug = Str::slug($title);
    }

    if ($slug === '') {
        return back()->withErrors(['slug' => 'Please provide a valid page title or slug.'])->withInput();
    }

    $slugExists = collect($pages)->contains(function ($page) use ($slug, $targetId) {
        if (!is_array($page)) {
            return false;
        }

        return strtolower(trim((string) ($page['slug'] ?? ''))) === strtolower($slug)
            && (int) ($page['id'] ?? 0) !== $targetId;
    });

    if ($slugExists) {
        return back()->withErrors(['slug' => 'That page slug already exists.'])->withInput();
    }

    $updated = false;
    foreach ($pages as &$page) {
        if (!is_array($page) || (int) ($page['id'] ?? 0) !== $targetId) {
            continue;
        }

        $page['title'] = $title;
        $page['slug'] = $slug;
        $page['summary'] = trim((string) ($data['summary'] ?? ''));
        $page['content'] = trim((string) ($data['content'] ?? ''));
        $page['status'] = trim((string) $data['status']);
        $page['updated_at'] = now()->toIso8601String();
        $updated = true;
        break;
    }
    unset($page);

    if (!$updated) {
        $newId = (collect($pages)->max('id') ?? 0) + 1;
        $pages[] = [
            'id' => $newId,
            'title' => $title,
            'slug' => $slug,
            'summary' => trim((string) ($data['summary'] ?? '')),
            'content' => trim((string) ($data['content'] ?? '')),
            'status' => trim((string) $data['status']),
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];
        $targetId = $newId;
    }

    saveCustomPages($pages);

    return redirect()->route('admin.pages', ['edit' => $targetId])
        ->with('page_saved', 'Page saved successfully.');
})->name('admin.pages.save');

Route::post('/admin/pages/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $targetId = (int) $id;
    $pages = collect(loadCustomPages());
    $exists = $pages->contains(fn ($page) => is_array($page) && (int) ($page['id'] ?? 0) === $targetId);
    if (!$exists) {
        return back()->with('page_deleted', 'Page not found.');
    }

    saveCustomPages(
        $pages
            ->reject(fn ($page) => is_array($page) && (int) ($page['id'] ?? 0) === $targetId)
            ->values()
            ->all()
    );

    return redirect()->route('admin.pages')->with('page_deleted', 'Page deleted successfully.');
})->name('admin.pages.delete');

Route::get('/pages/{slug}', function ($slug) {
    $page = collect(loadCustomPages())->first(function ($item) use ($slug) {
        if (!is_array($item)) {
            return false;
        }

        return strtolower(trim((string) ($item['slug'] ?? ''))) === strtolower(trim((string) $slug))
            && strtolower(trim((string) ($item['status'] ?? 'draft'))) === 'published';
    });

    if (!$page) {
        abort(404);
    }

    return view('page', [
        'page' => $page,
        'navPages' => array_slice(publishedCustomPages(), 0, 2),
    ]);
})->name('page.show');
