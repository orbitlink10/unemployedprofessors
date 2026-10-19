<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Payment Requests | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #f25c3c;
            --dark: #1c1c28;
            --muted: #6b6b7a;
            --border: #e5e8ed;
            --bg: #f7f8fb;
            --card: #ffffff;
            --green: #0f5951;
            --warning: #c27a00;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
        }

        .layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        .content {
            padding: 24px 28px 40px;
            display: grid;
            gap: 16px;
            align-content: start;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 16px 30px rgba(23, 44, 85, 0.06);
            display: grid;
            gap: 8px;
        }

        .summary-label {
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 800;
        }

        .summary-value {
            font-size: 30px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .summary-copy {
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .table-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 16px 30px rgba(23, 44, 85, 0.06);
        }

        .table-head {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }

        th {
            font-weight: 900;
            color: #2c2f33;
            background: #fff7f3;
        }

        tbody tr:hover {
            background: #f9fbfc;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .request-order {
            display: grid;
            gap: 4px;
        }

        .request-order strong {
            color: var(--green);
            font-weight: 900;
        }

        .request-meta,
        .request-meta-link {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .request-meta-link {
            text-decoration: none;
        }

        .request-meta-link:hover {
            color: var(--accent);
        }

        .writer-name {
            font-weight: 800;
            color: var(--dark);
        }

        .writer-email {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            word-break: break-word;
        }

        .amount {
            font-weight: 900;
            color: var(--green);
            white-space: nowrap;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 12px;
            text-transform: capitalize;
        }

        .status.requested {
            background: #fff3d9;
            color: var(--warning);
        }

        .status.paid {
            background: #e7f8ee;
            color: #1f9b55;
        }

        .status.rejected {
            background: #fde9e9;
            color: #c53030;
        }

        .order-status {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            text-transform: capitalize;
            background: #eef4ff;
            color: #4164a8;
        }

        .empty-state {
            padding: 24px 20px 28px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        @media (max-width: 1000px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .topbar,
            .table-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @include('admin.partials.sidebar-styles')
    </style>
</head>
<body>
<div class="layout">
    @include('admin.partials.sidebar', ['menuCounts' => $navCounts ?? []])

    <main class="content">
        <div class="topbar">
            <div>
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Payments</div>
                <div style="font-size:24px; font-weight:900;">Payment Requested</div>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.orders', ['status' => 'approved']) }}">View Approved Orders</a>
        </div>

        <section class="summary-grid">
            <article class="card">
                <div class="summary-label">Total Requests</div>
                <div class="summary-value">{{ count($paymentRequests ?? []) }}</div>
                <div class="summary-copy">All writer payment requests submitted so far.</div>
            </article>
            <article class="card">
                <div class="summary-label">Total Amount</div>
                <div class="summary-value">Ksh {{ number_format($requestedTotal ?? 0, 0) }}</div>
                <div class="summary-copy">Combined payout value requested by writers.</div>
            </article>
            <article class="card">
                <div class="summary-label">Orders</div>
                <div class="summary-value">{{ $requestedOrders ?? 0 }}</div>
                <div class="summary-copy">Approved orders that already have payment requests.</div>
            </article>
            <article class="card">
                <div class="summary-label">Writers</div>
                <div class="summary-value">{{ $requestingWriters ?? 0 }}</div>
                <div class="summary-copy">Writers who have submitted at least one request.</div>
            </article>
        </section>

        <section class="table-card">
            <div class="table-head">
                <div>
                    <div style="font-size:14px; color:var(--muted); font-weight:700;">History</div>
                    <div style="font-size:22px; font-weight:900;">Writer Payment Requests</div>
                </div>
                <div style="font-size:13px; color:var(--muted); font-weight:800;">Newest requests appear first.</div>
            </div>

            @if(!empty($paymentRequests))
                <table>
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Writer</th>
                            <th>Pages</th>
                            <th>Amount</th>
                            <th>Request Status</th>
                            <th>Order Status</th>
                            <th>Requested At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentRequests as $payment)
                            <tr>
                                <td>
                                    <div class="request-order">
                                        <strong>{{ $payment['payment_id'] ?: 'PAY-0000' }}</strong>
                                        @if(($payment['order_id'] ?? 0) > 0)
                                            <a class="request-meta-link" href="{{ route('admin.order.show', ['id' => $payment['order_id']]) }}">Order #{{ $payment['order_id'] }}</a>
                                        @else
                                            <span class="request-meta">Order unavailable</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="writer-name">{{ $payment['writer_name'] ?: 'Writer' }}</div>
                                    <div class="writer-email">{{ $payment['writer_email'] ?: 'No email stored' }}</div>
                                </td>
                                <td>{{ $payment['pages'] ?? 1 }}</td>
                                <td><span class="amount">Ksh {{ number_format($payment['amount'] ?? 0, 0) }}</span></td>
                                <td><span class="status {{ strtolower($payment['status'] ?? 'requested') }}">{{ $payment['status'] ?? 'requested' }}</span></td>
                                <td><span class="order-status">{{ $payment['order_status'] ?? 'unknown' }}</span></td>
                                <td>{{ $payment['requested_at'] ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">No writer payment requests have been submitted yet.</div>
            @endif
        </section>
    </main>
</div>
</body>
</html>
