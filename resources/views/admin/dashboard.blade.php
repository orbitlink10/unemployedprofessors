<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Writing Script</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #f25c3c;
            --accent-strong: #df4d2d;
            --accent-soft: #fff2ec;
            --primary: var(--accent);
            --primary-strong: var(--accent-strong);
            --primary-soft: var(--accent-soft);
            --sidebar-accent: var(--accent);
            --sidebar-accent-secondary: #ff8a65;
            --sidebar-soft: var(--accent-soft);
            --dark: #1c1c28;
            --muted: #6b6b7a;
            --card: #ffffff;
            --border: #e5e8ed;
            --bg: #f7f8fb;
            --green: #0f5951;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        .layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 24px 22px;
            display: flex;
            flex-direction: column;
            gap: 22px;
            box-shadow: none;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 22px;
        }
        .brand .mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), #ff8a65);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 16px;
            box-shadow: none;
        }
        .badge-label {
            background: var(--primary-soft);
            padding: 10px 12px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 13px;
            color: var(--primary-strong);
        }
        .nav-group { display: grid; gap: 10px; }
        .nav-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            color: #2d2d35;
            transition: background .12s ease, color .12s ease, transform .12s ease;
        }
        .nav-link.active,
        .nav-link:hover {
            background: var(--primary-soft);
            color: var(--primary-strong);
            transform: translateX(1px);
        }
        .nav-count {
            margin-left: auto;
            background: #f2f6ff;
            border-radius: 10px;
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 800;
            color: #2f4168;
        }
        .content {
            padding: 24px 28px 40px;
            display: grid;
            gap: 18px;
            align-content: start;
        }
        .topbar {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            justify-content: space-between;
        }
        .topline { display: grid; gap: 2px; }
        .eyebrow { font-size: 13px; color: var(--muted); font-weight: 700; }
        .page-title { font-size: 28px; line-height: 1.1; font-weight: 800; letter-spacing: -0.2px; }
        .topbar .actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn {
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            background: #fff;
            color: #2b2b34;
            transition: transform .12s ease, box-shadow .12s ease, border .12s ease;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        .btn:not(.btn-primary) { border-color: var(--border); }
        .btn-primary {
            background: var(--accent);
            color: #fff;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            display: grid;
            gap: 8px;
            transition: transform .14s ease, border-color .14s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            border-color: #f4cfc5;
        }
        .card .label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 15px;
            color: #2c2c36;
        }
        .label-icon {
            width: 28px;
            height: 28px;
            border-radius: 9px;
            background: var(--accent-soft);
            color: var(--accent);
            border: 1px solid #ffd7cd;
            display: inline-grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.2px;
        }
        .card .count { font-size: 14px; line-height: 1.2; font-weight: 800; letter-spacing: -0.1px; }
        .card small { color: var(--muted); font-size: 12px; }
        .card .trend {
            margin-left: auto;
            color: var(--green);
            font-size: 12px;
            font-weight: 800;
        }
        .chart-placeholder {
            background: linear-gradient(180deg, #fffaf7 0%, #ffffff 100%);
            border-radius: 12px;
            border: 1px dashed #f4cfc5;
            height: 180px;
            display: grid;
            place-items: center;
            color: #45506a;
            font-weight: 700;
            font-size: 14px;
        }
        .table-card {
            padding: 0;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead { background: #fff7f3; }
        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }
        th { font-weight: 800; color: #3a3a45; }
        tbody tr:hover { background: #fffaf7; }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 13px;
        }
        .status.pending { background: #fff4e5; color: #d66b00; }
        .status.available { background: #e7f8ee; color: #1f9b55; }
        .status.assigned { background: #e8f5ff; color: #0c7bca; }
        .status.editing { background: #f0f2ff; color: #3c4ad9; }
        .status.completed { background: #e7f8ee; color: #1f9b55; }
        .status.revision { background: #fff0f2; color: #d62d50; }
        .status.approved { background: #eaf6ff; color: #1f6fb5; }
        .status.cancelled { background: #fde9e9; color: #c53030; }
        .writer-pay { font-weight: 800; color: #0f5951; white-space: nowrap; }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--accent-soft);
            border: 1px solid #ffd7cd;
            display: grid; place-items: center;
            font-weight: 800; color: var(--accent);
        }
        @media (max-width: 1100px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { flex-direction: row; flex-wrap: wrap; align-items: center; }
            .nav-group { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); width: 100%; }
            .content { padding: 24px 20px 40px; }
            .page-title { font-size: 24px; }
            .card .count { font-size: 14px; }
        }
    @include('admin.partials.sidebar-styles')
    </style>
</head>
<body>
<div class="layout">
    @include('admin.partials.sidebar', ['menuCounts' => $navCounts ?? []])

    <main class="content">
        @php
            $orders = $orders ?? [];
            $count = function($status) use ($orders) { return collect($orders)->where('status', $status)->count(); };
        @endphp
        <div class="topbar">
            <div class="topline">
                <div class="eyebrow">Welcome back</div>
                <div class="page-title">{{ session('admin_name', 'Admin') }}</div>
            </div>
            <div class="actions">
                <a class="btn" href="{{ route('admin.orders') }}">Invoices</a>
                <a class="btn btn-primary" href="{{ route('order.create') }}">New Order</a>
            </div>
        </div>

        <section class="summary-grid">
            @php $statuses = [
                ['key'=>'pending','code'=>'PD','label'=>'Pending','sub'=>'Awaiting writer'],
                ['key'=>'available','code'=>'AV','label'=>'Available','sub'=>'Open to claim'],
                ['key'=>'assigned','code'=>'AS','label'=>'Assigned','sub'=>'In progress'],
                ['key'=>'editing','code'=>'ED','label'=>'Editing','sub'=>'QA review'],
                ['key'=>'completed','code'=>'CO','label'=>'Completed','sub'=>'Delivered'],
                ['key'=>'revision','code'=>'RV','label'=>'Revision','sub'=>'Awaiting fixes'],
                ['key'=>'approved','code'=>'AP','label'=>'Approved','sub'=>'Paid'],
                ['key'=>'cancelled','code'=>'CN','label'=>'Cancelled','sub'=>'Refunded'],
            ]; @endphp
            @foreach($statuses as $s)
                <a class="card" href="{{ route('admin.orders', ['status' => $s['key']]) }}" style="text-decoration:none; color:inherit; cursor:pointer;">
                    <div class="label"><span class="label-icon">{{ $s['code'] }}</span> {{ $s['label'] }}</div>
                    <div class="count">{{ $count($s['key']) }} orders</div>
                    <div style="display:flex; align-items:center; gap:10px;"><small>{{ $s['sub'] }}</small><span class="trend">Open</span></div>
                </a>
            @endforeach
        </section>

        <div class="card chart-placeholder">
            Performance chart placeholder (connect to your analytics or reporting data)
        </div>

        <div class="card table-card">
            <table>
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client</th>
                    <th>Writer</th>
                    <th>Topic</th>
                    <th>Due</th>
                    <th>Writer Pay</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order['id'] }}</td>
                        <td><span class="avatar">{{ strtoupper(substr($order['title'] ?? 'O',0,1)) }}</span> {{ $order['customer_name'] ?? 'Customer' }}</td>
                        <td>{{ $order['writer_name'] ?? 'Unassigned' }}</td>
                        <td>{{ $order['title'] ?? 'Untitled' }}</td>
                        <td>{{ $order['deadline'] ?? '48 Hours' }}</td>
                        <td><span class="writer-pay">Ksh {{ number_format($order['writer_payout'] ?? writerPayoutForOrder($order), 0) }}</span></td>
                        <td><span class="status {{ $order['status'] ?? 'pending' }}">{{ ucfirst($order['status'] ?? 'pending') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; padding:16px; color:#6b6b7a;">No orders yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
