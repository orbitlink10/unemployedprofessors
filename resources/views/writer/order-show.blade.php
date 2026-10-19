<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Order #{{ $order['id'] ?? '' }} | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f2f2f2;
            --panel: #ffffff;
            --line: #e4e4e4;
            --text: #2d2d2d;
            --muted: #717171;
            --accent: #5f70cb;
            --success: #0f9d69;
            --danger: #c53030;
            --warning: #c27a00;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(95, 112, 203, 0.12), transparent 30%),
                var(--bg);
            color: var(--text);
        }

        .page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 26px 18px 42px;
            display: grid;
            gap: 18px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            background: rgba(255,255,255,0.78);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 18px 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mark {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(145deg, #1d72d8, #2947b7);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 16px;
            font-weight: 800;
        }

        .brand-title {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.7px;
        }

        .brand-copy {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .nav-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-link,
        .btn {
            text-decoration: none;
            border-radius: 12px;
            padding: 11px 14px;
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            border: 1px solid #dfdfdf;
            background: #fff;
            color: #3b3b3b;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .btn-dark {
            background: #1f1f1f;
            border-color: #1f1f1f;
            color: #fff;
        }

        .hero {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.04);
        }

        h1 {
            margin: 0;
            max-width: 1000px;
            font-size: 14px;
            line-height: 1.18;
            letter-spacing: -0.8px;
        }

        .hero-meta {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 8px 12px;
            background: #f5f5f5;
            border: 1px solid #e8e8e8;
            color: #666;
            font-size: 12px;
            font-weight: 800;
        }

        .status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
        }

        .status.assigned,
        .status.inprogress {
            background: #edf2ff;
            color: #5365c4;
        }

        .status.available {
            background: #e9f8f0;
            color: #11885a;
        }

        .status.revision {
            background: #fff0f0;
            color: #c03b3b;
        }

        .status.completed,
        .status.approved {
            background: #edf9ef;
            color: #1d8f4d;
        }

        .status.editing {
            background: #f4f1ff;
            color: #7152d9;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr;
            gap: 18px;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.04);
        }

        .panel-title {
            margin: 0 0 16px;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -0.6px;
        }

        .details {
            display: grid;
            gap: 12px;
        }

        .detail-row {
            display: grid;
            gap: 4px;
            padding: 12px 14px;
            border: 1px solid #ececec;
            border-radius: 16px;
            background: #fafafa;
        }

        .detail-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8f8f8f;
            font-weight: 800;
        }

        .detail-value {
            font-size: 15px;
            font-weight: 700;
            color: #2f2f2f;
            word-break: break-word;
        }

        .detail-value.instructions {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            line-height: 1.55;
        }

        .actions {
            display: grid;
            gap: 12px;
        }

        .actions form {
            display: grid;
            gap: 10px;
            margin: 0;
        }

        select,
        input[type="file"] {
            width: 100%;
            border: 1px solid #dddddd;
            border-radius: 12px;
            padding: 11px 12px;
            font: inherit;
            background: #fff;
        }

        .file-list {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .file-item {
            display: grid;
            gap: 4px;
            padding: 12px 14px;
            border-radius: 14px;
            background: #fafafa;
            border: 1px solid #ececec;
        }

        .file-name {
            color: var(--accent);
            font-weight: 800;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .file-name:hover {
            color: #495bb8;
        }

        .file-date {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .empty {
            color: var(--muted);
            font-weight: 700;
        }

        .deadline {
            font-weight: 800;
            color: #2d2d2d;
        }

        .deadline.deadline-urgent {
            color: var(--warning);
        }

        .deadline.deadline-expired {
            color: var(--danger);
        }

        @media (max-width: 960px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .hero,
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            h1 {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="topbar">
            <div class="brand">
                <div class="mark">WR</div>
                <div>
                    <div class="brand-title">Writer Workspace</div>
                    <div class="brand-copy">Order Details</div>
                </div>
            </div>
            <div class="nav-actions">
                <a class="nav-link" href="{{ route('writer.dashboard') }}">Back to Dashboard</a>
                <a class="nav-link" href="{{ route('writer.logout') }}">Logout</a>
            </div>
        </header>

        <section class="hero">
            <div>
                <h1>#{{ $order['id'] }} {{ $order['title'] ?? 'Untitled' }}</h1>
                <div class="hero-meta">
                    <span class="pill">{{ $order['subject'] ?? 'Other' }}</span>
                    <span class="pill">{{ $order['type'] ?? 'Essay' }}</span>
                    <span class="pill">{{ $order['pages'] ?? 1 }} page(s)</span>
                    <span class="pill">Your pay: Ksh {{ number_format($order['writer_payout'] ?? writerPayoutForOrder($order), 0) }}</span>
                    <span class="pill">{{ $order['level'] ?? 'College' }}</span>
                    <span class="status {{ $order['status'] ?? 'pending' }}">{{ ($order['status'] ?? '') === 'inprogress' ? 'In Progress' : ucfirst($order['status'] ?? 'pending') }}</span>
                </div>
            </div>
        </section>

        <div class="grid">
            <section class="panel">
                <h2 class="panel-title">Order Details</h2>
                <div class="details">
                    <div class="detail-row">
                        <div class="detail-label">Client</div>
                        <div class="detail-value">
                            {{ trim((string) ($order['customer_name'] ?? '')) !== '' ? $order['customer_name'] : (orderPosterType($order) === 'admin' ? 'Admin Desk' : 'Client') }}
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Posted By</div>
                        <div class="detail-value">{{ orderPosterLabel($order) }}: {{ orderPosterName($order) }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Deadline</div>
                        <div class="detail-value">
                            <span class="deadline" data-deadline="{{ \writerDueAtForOrder($order) }}" data-fallback="{{ $order['deadline'] ?? 'N/A' }}">
                                {{ $order['deadline'] ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Writer Pay</div>
                        <div class="detail-value">Ksh {{ number_format($order['writer_payout'] ?? writerPayoutForOrder($order), 0) }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Instructions</div>
                        <div class="detail-value instructions">{{ trim((string) ($order['instructions'] ?? '')) !== '' ? $order['instructions'] : 'No instructions added.' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Formatting</div>
                        <div class="detail-value">{{ $order['format'] ?? 'APA' }}, {{ $order['spacing'] ?? 'Double' }}, {{ $order['sources'] ?? 0 }} source(s)</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Extras</div>
                        <div class="detail-value">
                            @php
                                $extras = [];
                                if (!empty($order['vip_support'])) {
                                    $extras[] = 'VIP support';
                                }
                                if (!empty($order['draft_outline'])) {
                                    $extras[] = 'Draft outline';
                                }
                            @endphp
                            {{ empty($extras) ? 'None' : implode(', ', $extras) }}
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Current Writer</div>
                        <div class="detail-value">{{ $order['writer_name'] ?? 'Unassigned' }}</div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h2 class="panel-title">Actions</h2>
                <div class="actions">
                    @if($canTake)
                        <form action="{{ route('writer.order.claim', ['id' => $order['id']]) }}" method="POST">
                            @csrf
                            <button class="btn btn-dark" type="submit">Take Order</button>
                        </form>
                    @elseif($isAssignedToCurrent)
                        <form action="{{ route('writer.order.files', ['id' => $order['id']]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @include('partials.multi-file-upload', ['required' => true])
                            <button class="btn" type="submit">Upload Files</button>
                        </form>
                    @else
                        <div class="empty">No actions available for this order.</div>
                    @endif
                </div>

                <h2 class="panel-title" style="margin-top:22px;">Order Files</h2>
                @if(!empty($orderFiles ?? []))
                    <div class="file-list">
                        @foreach(($orderFiles ?? []) as $file)
                            <div class="file-item">
                                <a class="file-name" href="{{ route('order.file.download', ['id' => $order['id'], 'file' => $file['path']]) }}">{{ $file['name'] }}</a>
                                <div class="file-date">{{ $file['date'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">No order files uploaded yet.</div>
                @endif

                <h2 class="panel-title" style="margin-top:22px;">Writer Files</h2>
                @if(!empty($writerFiles ?? []))
                    <div class="file-list">
                        @foreach(($writerFiles ?? []) as $file)
                            <div class="file-item">
                                <a class="file-name" href="{{ route('order.file.download', ['id' => $order['id'], 'file' => $file['path']]) }}">{{ $file['name'] }}</a>
                                <div class="file-date">{{ $file['date'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">No writer files uploaded yet.</div>
                @endif
            </section>
        </div>
    </div>

    <script>
        const deadlineCell = document.querySelector('.deadline');

        function formatRemaining(diffMs) {
            const totalMinutes = Math.max(Math.floor(diffMs / 60000), 0);
            const days = Math.floor(totalMinutes / 1440);
            const hours = Math.floor((totalMinutes % 1440) / 60);
            const minutes = totalMinutes % 60;

            if (days > 0) {
                return days + ' Day' + (days === 1 ? '' : 's') + ' ' + hours + 'h';
            }
            if (hours > 0) {
                return hours + 'h ' + minutes + 'm';
            }
            return minutes + 'm';
        }

        function refreshDeadline() {
            if (!deadlineCell) {
                return;
            }

            const rawDueAt = deadlineCell.dataset.deadline || '';
            const fallback = deadlineCell.dataset.fallback || 'N/A';
            const dueAtMs = Date.parse(rawDueAt);

            if (!Number.isFinite(dueAtMs)) {
                deadlineCell.textContent = fallback;
                deadlineCell.classList.remove('deadline-urgent', 'deadline-expired');
                return;
            }

            const diffMs = dueAtMs - Date.now();

            if (diffMs <= 0) {
                deadlineCell.textContent = 'Expired';
                deadlineCell.classList.add('deadline-expired');
                deadlineCell.classList.remove('deadline-urgent');
                return;
            }

            deadlineCell.textContent = formatRemaining(diffMs);
            deadlineCell.classList.remove('deadline-expired');

            if (diffMs <= 60 * 60 * 1000) {
                deadlineCell.classList.add('deadline-urgent');
            } else {
                deadlineCell.classList.remove('deadline-urgent');
            }
        }

        refreshDeadline();
        setInterval(refreshDeadline, 30000);
    </script>
</body>
</html>
