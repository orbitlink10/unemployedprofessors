<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Payments | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f2f2f2;
            --panel: #ffffff;
            --line: #e2e2e2;
            --border: #e5e8ed;
            --dark: #1c1c28;
            --text: #2d2d2d;
            --muted: #727272;
            --accent: #5f70cb;
            --accent-soft: #eef1ff;
            --sidebar-accent: #f25c3c;
            --sidebar-accent-secondary: #ff8a65;
            --sidebar-soft: #fff2ec;
            --success: #0f9d69;
            --danger: #c53030;
            --warning: #c27a00;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(95, 112, 203, 0.12), transparent 28%),
                var(--bg);
            color: var(--text);
        }

        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
        }

        .content {
            padding: 28px;
            min-width: 0;
            display: grid;
            gap: 20px;
        }

        .hero {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid #e3e3e3;
            border-radius: 22px;
            padding: 24px;
        }

        .hero h1 {
            margin: 0;
            font-size: 34px;
            letter-spacing: -1px;
        }

        .hero p {
            margin: 8px 0 0;
            color: var(--muted);
            font-weight: 600;
            max-width: 760px;
        }

        .hero-badge {
            border-radius: 999px;
            padding: 10px 14px;
            background: #fff;
            border: 1px solid #e2e2e2;
            font-size: 12px;
            font-weight: 800;
            color: #575757;
            white-space: nowrap;
        }

        .flash {
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 800;
            border: 1px solid transparent;
        }

        .flash.success {
            background: #eaf8f0;
            border-color: #bce7cb;
            color: #0f7b4d;
        }

        .flash.error {
            background: #fdeaea;
            border-color: #f5c3c3;
            color: #b42318;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .summary-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.05);
            display: grid;
            gap: 8px;
        }

        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #8a8a8a;
            font-weight: 800;
        }

        .summary-value {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--dark);
        }

        .summary-copy {
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        .panel {
            background: var(--panel);
            border: 1px solid #e5e5e5;
            border-radius: 22px;
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .panel-head {
            padding: 22px 22px 16px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            border-bottom: 1px solid #ececec;
        }

        .panel-title {
            margin: 0;
            font-size: 22px;
            letter-spacing: -0.6px;
            color: var(--dark);
        }

        .panel-copy {
            margin: 6px 0 0;
            font-size: 14px;
            color: var(--muted);
            font-weight: 600;
        }

        .panel-badge {
            border-radius: 999px;
            padding: 10px 12px;
            background: #fff7ea;
            border: 1px solid #f4d49a;
            color: #a96e00;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 16px 22px;
            border-bottom: 1px solid #efefef;
            text-align: left;
            vertical-align: middle;
        }

        th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #8a8a8a;
            font-weight: 800;
        }

        td {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .order-title {
            display: grid;
            gap: 4px;
        }

        .order-title strong {
            font-size: 15px;
            color: var(--dark);
        }

        .order-meta {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .amount {
            font-weight: 800;
            color: var(--success);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
            text-transform: capitalize;
            border: 1px solid transparent;
        }

        .status-pill.requested {
            background: #fff7ea;
            border-color: #f4d49a;
            color: #a96e00;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 16px;
            border-radius: 14px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 16px 30px rgba(95, 112, 203, 0.24);
        }

        .empty-state {
            padding: 28px 22px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 1180px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .content {
                padding: 18px;
            }

            .hero,
            .panel-head {
                flex-direction: column;
            }

            th,
            td {
                padding: 14px 16px;
            }
        }

        @media (max-width: 540px) {
            .hero h1 {
                font-size: 28px;
            }

            .summary-value {
                font-size: 28px;
            }
        }

        @include('admin.partials.sidebar-styles')
    </style>
</head>
<body>
    @php
        $availableItem = collect($menuItems ?? [])->firstWhere('key', 'available') ?? [];
        $assignedItem = collect($menuItems ?? [])->firstWhere('key', 'assigned') ?? [];
        $completedItem = collect($menuItems ?? [])->firstWhere('key', 'completed') ?? [];
        $revisionItem = collect($menuItems ?? [])->firstWhere('key', 'revision') ?? [];
        $approvedItem = collect($menuItems ?? [])->firstWhere('key', 'approved') ?? [];
        $availableCount = $availableItem['count'] ?? 0;
        $assignedCount = $assignedItem['count'] ?? 0;
        $completedCount = $completedItem['count'] ?? 0;
        $revisionCount = $revisionItem['count'] ?? 0;
        $approvedCount = $approvedItem['count'] ?? 0;
    @endphp

    <div class="app-shell">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <span class="icon">WR</span>
                <div class="label">
                    <span class="eyebrow">Control Panel</span>
                    <span class="title">Writer</span>
                </div>
            </div>

            <div class="sidebar-nav">
                <section class="nav-group">
                    <div class="nav-title">Main</div>
                    <div class="nav-links">
                        <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'available']) }}">
                            <span>Dashboard</span>
                            <span class="nav-count">{{ $availableCount }}</span>
                        </a>
                        <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'assigned']) }}">
                            <span>Assigned</span>
                            <span class="nav-count">{{ $assignedCount }}</span>
                        </a>
                        <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'completed']) }}">
                            <span>Completed</span>
                            <span class="nav-count">{{ $completedCount }}</span>
                        </a>
                        <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'revision']) }}">
                            <span>Revision</span>
                            <span class="nav-count">{{ $revisionCount }}</span>
                        </a>
                        <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'approved']) }}">
                            <span>Approved</span>
                            <span class="nav-count">{{ $approvedCount }}</span>
                        </a>
                    </div>
                </section>

                <section class="nav-group">
                    <div class="nav-title">Account</div>
                    <div class="nav-links">
                        <a class="nav-link" href="{{ route('writer.profile') }}">
                            <span>Profile</span>
                        </a>
                        <a class="nav-link active" href="{{ route('writer.payments') }}">
                            <span>Payment</span>
                        </a>
                        <a class="nav-link" href="{{ route('writers.index') }}">
                            <span>Writers</span>
                        </a>
                        <a class="nav-link" href="{{ route('writer.logout') }}">
                            <span>Logout</span>
                        </a>
                    </div>
                </section>
            </div>
        </aside>

        <main class="content">
            <section class="hero">
                <div>
                    <h1>{{ $writerProfile['name'] ?? session('writer_name', 'Writer') }}</h1>
                    <p>Request payment only for approved orders. The page keeps your request history so previously submitted payouts remain visible in one place.</p>
                </div>
                <div class="hero-badge">Payment Center</div>
            </section>

            @if(session('status'))
                <div class="flash success">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="flash error">{{ session('error') }}</div>
            @endif

            <section class="summary-grid">
                <article class="summary-card">
                    <div class="summary-label">Ready to Request</div>
                    <div class="summary-value">{{ count($eligibleOrders ?? []) }}</div>
                    <div class="summary-copy">Approved orders that can be sent for payment right now.</div>
                </article>
                <article class="summary-card">
                    <div class="summary-label">Eligible Amount</div>
                    <div class="summary-value">Ksh {{ number_format($eligibleTotal ?? 0, 0) }}</div>
                    <div class="summary-copy">Total payout waiting on new payment requests.</div>
                </article>
                <article class="summary-card">
                    <div class="summary-label">Requested History</div>
                    <div class="summary-value">Ksh {{ number_format($requestedTotal ?? 0, 0) }}</div>
                    <div class="summary-copy">Total amount already requested from approved orders.</div>
                </article>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">Approved Orders</h2>
                        <p class="panel-copy">Use the request button to submit payout for each approved order that has not been requested before.</p>
                    </div>
                    <div class="panel-badge">{{ count($eligibleOrders ?? []) }} open request{{ count($eligibleOrders ?? []) === 1 ? '' : 's' }}</div>
                </div>

                @if(!empty($eligibleOrders))
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Pages</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($eligibleOrders as $order)
                                    <tr>
                                        <td>
                                            <div class="order-title">
                                                <strong>#{{ $order['id'] }} {{ $order['title'] }}</strong>
                                                <span class="order-meta">{{ $order['subject'] }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $order['pages'] }}</td>
                                        <td><span class="amount">Ksh {{ number_format($order['amount'] ?? 0, 0) }}</span></td>
                                        <td>
                                            <form method="POST" action="{{ route('writer.payments.request', ['id' => $order['id']]) }}">
                                                @csrf
                                                <button class="button button-primary" type="submit">Request Payment</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">No approved orders are waiting for a payment request right now.</div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2 class="panel-title">Payment History</h2>
                        <p class="panel-copy">Every payment request you have submitted stays listed here for reference.</p>
                    </div>
                    <div class="panel-badge">{{ count($paymentHistory ?? []) }} total request{{ count($paymentHistory ?? []) === 1 ? '' : 's' }}</div>
                </div>

                @if(!empty($paymentHistory))
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Pages</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentHistory as $payment)
                                    <tr>
                                        <td>
                                            <div class="order-title">
                                                <strong>#{{ $payment['order_id'] }} {{ $payment['order_title'] }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $payment['pages'] }}</td>
                                        <td><span class="amount">Ksh {{ number_format($payment['amount'] ?? 0, 0) }}</span></td>
                                        <td><span class="status-pill {{ strtolower($payment['status'] ?? 'requested') }}">{{ $payment['status'] ?? 'requested' }}</span></td>
                                        <td>{{ $payment['requested_at'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">No payment requests have been submitted yet.</div>
                @endif
            </section>
        </main>
    </div>
</body>
</html>
