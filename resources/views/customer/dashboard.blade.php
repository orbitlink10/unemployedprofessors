<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #0f5951;
            --accent: #f8b23d;
            --muted: #6c737a;
            --border: #e5e8ed;
            --dark: #1c1c28;
            --bg: #f7f8fb;
            --card: #ffffff;
            --sidebar-accent: #f25c3c;
            --sidebar-accent-secondary: #ff8a65;
            --sidebar-soft: #fff2ec;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: #1d1f22;
        }
        .layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background:#fff;
            border-right:1px solid var(--border);
            padding:24px;
            display:flex;
            flex-direction:column;
            gap:18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            color: #1d1f22;
        }
        .brand .icon {
            width:44px;
            height:44px;
            border-radius:14px;
            background:#0f5951;
            display: grid;
            place-items: center;
            color:#fff;
            font-size:22px;
        }
        .nav-group {
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .nav-title {
            font-size:12px;
            letter-spacing:0.4px;
            text-transform:uppercase;
            color:var(--muted);
            font-weight:800;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            text-decoration: none;
            color:#2f3236;
            font-weight:800;
            transition: background .12s ease, color .12s ease;
        }
        .nav-link.active,
        .nav-link:hover {
            background:#ecf3f1;
            color:var(--green);
        }
        .badge {
            margin-left:auto;
            background:#0f5951;
            color:#fff;
            border-radius: 10px;
            padding:4px 9px;
            font-size:12px;
            font-weight:800;
        }
        .content {
            padding: 24px 28px 40px;
            display: grid;
            gap: 18px;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .topbar .user {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }
        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #ecf3f1;
            display: grid;
            place-items: center;
            color: var(--green);
            font-weight: 900;
        }
        .chips {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        .chip {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            box-shadow: 0 12px 28px rgba(0,0,0,0.04);
            color: inherit;
            text-decoration: none;
            transition: transform .12s ease, border-color .12s ease, box-shadow .12s ease;
        }
        .chip:hover {
            transform: translateY(-1px);
            border-color: rgba(15, 89, 81, 0.3);
            box-shadow: 0 16px 30px rgba(17, 42, 72, 0.08);
        }
        .chip.active {
            border-color: rgba(15, 89, 81, 0.45);
            box-shadow: 0 0 0 1px rgba(15, 89, 81, 0.1), 0 16px 30px rgba(17, 42, 72, 0.08);
        }
        .chip .count { margin-left: auto; color: var(--green); }
        .filter-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 12px 28px rgba(0,0,0,0.04);
        }
        .filter-banner a {
            color: var(--green);
            font-weight: 800;
            text-decoration: none;
        }
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(17, 42, 72, 0.06);
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #fff8f0; }
        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }
        th { font-weight: 800; color: #2c2f33; }
        tr:hover { background: #f9fbfc; }
        .status {
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status.pending { background: #fff3d9; color: #c27a00; }
        .status.inprogress { background: #e9f6ff; color: #0b6fb8; }
        .status.completed { background: #e7f8ee; color: #1f9b55; }
        .search-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 6px;
        }
        .search-bar input {
            flex: 1;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-weight: 700;
        }
        .search-bar button {
            padding: 12px 16px;
            border-radius: 10px;
            border: none;
            background: var(--green);
            color: #fff;
            font-weight: 900;
            cursor: pointer;
        }
        @media (max-width: 1000px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { gap:14px; padding:18px; }
        }
    @include('admin.partials.sidebar-styles')

        .nav-count {
            color: #c53030;
        }
</style>
</head>
<body>
@php
    $orders = $orders ?? [];
    $statusCards = $statusCards ?? [];
    $statusFilter = $statusFilter ?? 'all';
    $selectedStatusLabel = $selectedStatusLabel ?? null;
    $orderCount = $orderCount ?? count($orders);
    $assignedCount = collect($statusCards)->firstWhere('key', 'assigned')['count'] ?? 0;
    $completedCount = collect($statusCards)->firstWhere('key', 'completed')['count'] ?? 0;
    $revisionCount = collect($statusCards)->firstWhere('key', 'revision')['count'] ?? 0;
    $approvedCount = collect($statusCards)->firstWhere('key', 'approved')['count'] ?? 0;
@endphp
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="icon">CU</span>
            <div class="label">
                <span class="eyebrow">Control Panel</span>
                <span class="title">Customer</span>
            </div>
        </div>
        <div class="sidebar-nav">
            <section class="nav-group">
                <div class="nav-title">Main</div>
                <div class="nav-links">
                    <a class="nav-link {{ $statusFilter === 'all' ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                        <span>Dashboard</span>
                        <span class="nav-count">{{ $orderCount }}</span>
                    </a>
                    <a class="nav-link" href="{{ route('order.create') }}">
                        <span>Add Order</span>
                    </a>
                    <a class="nav-link {{ $statusFilter === 'assigned' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'assigned']) }}">
                        <span>Assigned</span>
                        <span class="nav-count">{{ $assignedCount }}</span>
                    </a>
                    <a class="nav-link {{ $statusFilter === 'completed' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'completed']) }}">
                        <span>Completed</span>
                        <span class="nav-count">{{ $completedCount }}</span>
                    </a>
                    <a class="nav-link {{ $statusFilter === 'revision' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'revision']) }}">
                        <span>Revision</span>
                        <span class="nav-count">{{ $revisionCount }}</span>
                    </a>
                    <a class="nav-link {{ $statusFilter === 'approved' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'approved']) }}">
                        <span>Approved</span>
                        <span class="nav-count">{{ $approvedCount }}</span>
                    </a>
                </div>
            </section>
            <section class="nav-group">
                <div class="nav-title">Listing</div>
                <div class="nav-links">
                    <a class="nav-link" href="#">
                        <span>Courses</span>
                    </a>
                    <a class="nav-link" href="#">
                        <span>Top Writers</span>
                    </a>
                </div>
            </section>
            <section class="nav-group">
                <div class="nav-title">Account</div>
                <div class="nav-links">
                    <a class="nav-link" href="#">
                        <span>Profile</span>
                    </a>
                    <a class="nav-link" href="{{ route('customer.logout') }}">
                        <span>Logout</span>
                    </a>
                </div>
            </section>
        </div>
    </aside>

    <main class="content">
        <div class="topbar">
            <div>
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Welcome back</div>
                <div style="font-size:26px; font-weight:900;">{{ session('customer_name','Customer') }}</div>
            </div>
            <div class="user">
                <div class="avatar">{{ strtoupper(substr(session('customer_name','C'),0,1)) }}</div>
                <div>
                    <div style="font-weight:900;">{{ session('customer_name','Customer') }}</div>
                    <div style="color:var(--muted); font-weight:700; font-size:13px;">{{ session('customer_email','you@example.com') }}</div>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div style="padding:12px 14px; border-radius:12px; background:#e7f8ee; color:#1f9b55; font-weight:800;">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div style="padding:12px 14px; border-radius:12px; background:#fde9e9; color:#c53030; font-weight:800;">
                {{ session('error') }}
            </div>
        @endif

        @if(($statusFilter ?? 'all') === 'all')
            <div class="search-bar">
                <input type="text" placeholder="Search orders">
                <button>Search</button>
            </div>
        @endif
        @if($statusFilter === 'all')
            <div class="chips">
                @foreach($statusCards as $card)
                    <a
                        class="chip {{ !empty($card['active']) ? 'active' : '' }}"
                        href="{{ $card['url'] }}"
                        title="{{ ($card['count'] ?? 0) === 1 ? 'Open this order' : 'Show '.$card['label'].' orders' }}"
                    >
                        <span>{{ $card['label'] }}</span>
                        <span class="count">({{ $card['count'] ?? 0 }})</span>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="table-card">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Deadline</th>
                    <th>Cost</th>
                    <th>Writer</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('customer.order.show', ['id' => $order['id']]) }}" style="color:var(--green); font-weight:900; text-decoration:none;">#{{ $order['id'] }}</a></td>
                        <td>
                            <a href="{{ route('customer.order.show', ['id' => $order['id']]) }}" style="color:#2c2f33; font-weight:800; text-decoration:none;">
                                {{ $order['title'] }}
                            </a>
                            <br><small style="color:var(--muted); font-weight:700;">{{ $order['pages'] }} Pg(s)</small>
                            @if(orderPaymentNotice($order))
                                @php $paymentStatus = orderPaymentStatus($order); @endphp
                                <div style="margin-top:8px; padding:8px 10px; border-radius:10px; background:{{ $paymentStatus === 'completed' ? '#e7f8ee' : ($paymentStatus === 'pending' ? '#edf7ff' : '#fff3d9') }}; color:{{ $paymentStatus === 'completed' ? '#1f9b55' : ($paymentStatus === 'pending' ? '#145da0' : '#c27a00') }}; font-size:12px; font-weight:800;">
                                    {{ orderPaymentNotice($order) }}
                                </div>
                            @endif
                            @if(!empty($order['client_notice']))
                                <div style="margin-top:8px; padding:8px 10px; border-radius:10px; background:#edf7ff; color:#145da0; font-size:12px; font-weight:800;">
                                    {{ $order['client_notice'] }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $order['deadline'] ?? '48 Hours' }}</td>
                        <td>USD {{ number_format($order['cost'] ?? 0, 2) }}</td>
                        <td>{{ $order['writer_name'] ?? 'Unassigned' }}</td>
                        <td><span class="status {{ $order['status'] }}">{{ ucwords(str_replace('inprogress', 'in progress', $order['status'] ?? 'pending')) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:20px; color:var(--muted); font-weight:800;">{{ $statusFilter === 'all' ? 'No orders yet' : 'No orders in this status' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
