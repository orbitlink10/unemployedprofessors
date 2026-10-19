<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order['id'] }} | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #0f5951;
            --accent: #f8b23d;
            --muted: #6c737a;
            --border: #e5e8ed;
            --bg: #f7f8fb;
            --card: #ffffff;
        }
        * { box-sizing: border-box; }
        body { margin:0; font-family:'Manrope', system-ui, -apple-system, sans-serif; background: var(--bg); color:#1d1f22; }
        .layout { display:grid; grid-template-columns: 240px 1fr; min-height:100vh; }
        .sidebar { background:#fff; border-right:1px solid var(--border); padding:24px; display:flex; flex-direction:column; gap:18px; }
        .brand { display:flex; align-items:center; gap:12px; font-size:24px; font-weight:800; }
        .brand .icon { width:44px; height:44px; border-radius:14px; background:#0f5951; display:grid; place-items:center; color:#fff; font-size:22px; }
        .nav-group { display:flex; flex-direction:column; gap:8px; }
        .nav-title { font-size:12px; letter-spacing:0.4px; text-transform:uppercase; color:var(--muted); font-weight:800; }
        .nav-link { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:12px; color:#2f3236; font-weight:800; text-decoration:none; }
        .nav-link.active, .nav-link:hover { background:#ecf3f1; color:var(--green); }
        .badge { margin-left:auto; background:#0f5951; color:#fff; border-radius:10px; padding:4px 9px; font-size:12px; font-weight:800; }
        .content { padding:24px 28px 40px; display:grid; gap:18px; }
        .topline { display:flex; justify-content:space-between; align-items:center; gap:12px; }
        h1 { margin:0; font-size:34px; letter-spacing:-0.3px; }
        .pill-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; color:#c47a00; font-weight:800; }
        .summary-grid { display:grid; gap:12px; }
        .row { display:flex; align-items:center; gap:12px; padding:12px 14px; background:#fff; border:1px solid var(--border); border-radius:10px; }
        .row .label { width:160px; font-weight:900; color:#2f3236; }
        .row .value { font-weight:700; color:#3a3d42; }
        .files-section { background:#fff; border:1px solid var(--border); border-radius:12px; padding:16px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:10px 12px; border-bottom:1px solid var(--border); text-align:left; }
        th { font-weight:900; }
        .file-link { color: var(--green); font-weight: 800; text-decoration: underline; text-underline-offset: 2px; }
        .file-link:hover { color: #0b4a44; }
        .upload { margin-top:12px; display:grid; gap:10px; }
        .btn { border:none; border-radius:12px; padding:12px 14px; font-weight:900; cursor:pointer; }
        .btn-primary { background: var(--accent); color:#0f172a; }
        .btn-secondary { background: var(--green); color:#fff; }
        .pay { align-self:flex-start; padding:14px 18px; background:var(--accent); color:#0f172a; border-radius:12px; font-weight:900; border:none; cursor:pointer; }
        .right-panel { background:#fff; border:1px solid var(--border); border-radius:12px; padding:16px; display:grid; gap:10px; }
        textarea { width:100%; min-height:90px; border:1px solid var(--border); border-radius:10px; padding:10px; font-weight:700; }
        @media (max-width: 1100px) {
            .layout { grid-template-columns:1fr; }
            .sidebar { gap:14px; padding:18px; }
            .topline { flex-direction:column; align-items:flex-start; }
        }
    </style>
</head>
<body>
@php
    $paymentStatus = orderPaymentStatus($order);
    $paymentNotice = orderPaymentNotice($order);
@endphp
<div class="layout">
    <aside class="sidebar">
        <div class="brand"><span class="icon">◎</span><span>MyAccount</span></div>
        <div class="nav-group">
            <div class="nav-title">Main Menu</div>
            <a class="nav-link" href="{{ route('order.create') }}">New Order</a>
            <a class="nav-link {{ strtolower(trim((string) ($order['status'] ?? ''))) === 'assigned' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'assigned']) }}">Assigned</a>
            <a class="nav-link {{ strtolower(trim((string) ($order['status'] ?? ''))) === 'completed' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'completed']) }}">Completed</a>
            <a class="nav-link {{ strtolower(trim((string) ($order['status'] ?? ''))) === 'revision' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'revision']) }}">Revision</a>
            <a class="nav-link {{ strtolower(trim((string) ($order['status'] ?? ''))) === 'approved' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'approved']) }}">Approved</a>
        </div>
        <div class="nav-group">
            <div class="nav-title">Listing</div>
            <a class="nav-link" href="#">Courses</a>
            <a class="nav-link" href="#">Top Writers</a>
        </div>
        <div class="nav-group">
            <div class="nav-title">Account</div>
            <a class="nav-link" href="#">Profile</a>
            <a class="nav-link" href="{{ route('customer.logout') }}">Logout</a>
        </div>
    </aside>

    <main class="content">
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

        <div class="topline">
            <div>
                <h1>#{{ $order['id'] }} {{ $order['title'] }}</h1>
                <div class="pill-row">
                    <span>{{ $order['level'] ?? 'College' }}</span>
                    <span>★ ★ ★ ★ ★</span>
                    <span>{{ $order['category'] ?? 'Standard' }} Writer</span>
                </div>
                <div class="pill-row" style="color:#4a4e55;">
                    <span>{{ $order['pages'] ?? 1 }} Page(s) 275 Words, {{ $order['slides'] ?? 0 }} PPTs</span>
                    <span>Due: {{ $order['deadline'] ?? '48 Hours' }}</span>
                    <span>USD {{ number_format($order['cost'] ?? 0,2) }}</span>
                </div>
                @if($paymentNotice)
                    <div style="margin-top:10px; padding:10px 12px; border-radius:12px; background:{{ $paymentStatus === 'completed' ? '#e7f8ee' : ($paymentStatus === 'pending' ? '#edf7ff' : '#fff3d9') }}; color:{{ $paymentStatus === 'completed' ? '#1f9b55' : ($paymentStatus === 'pending' ? '#145da0' : '#c27a00') }}; font-size:13px; font-weight:800;">
                        {{ $paymentNotice }}
                    </div>
                @endif
                @if(!empty($order['client_notice']))
                    <div style="margin-top:10px; padding:10px 12px; border-radius:12px; background:#edf7ff; color:#145da0; font-size:13px; font-weight:800;">
                        {{ $order['client_notice'] }}
                    </div>
                @endif
            </div>
            @if(orderRequiresCustomerPayment($order) && $paymentStatus !== 'completed')
                <form action="{{ route('customer.order.pay', ['id' => $order['id']]) }}" method="POST">
                    @csrf
                    <button type="submit" class="pay">Pay with Pesapal (USD {{ number_format($order['cost'] ?? 0,2) }})</button>
                </form>
            @elseif($paymentStatus === 'completed')
                <div style="padding:14px 18px; background:#e7f8ee; color:#1f9b55; border-radius:12px; font-weight:900;">
                    Payment Received
                </div>
            @endif
        </div>

        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px;">
            <div class="summary-grid">
                @php
                    $upsells = [];
                    if (!empty($order['vip_support'])) {
                        $upsells[] = 'VIP support';
                    }
                    if (!empty($order['draft_outline'])) {
                        $upsells[] = 'Draft/outline';
                    }
                @endphp
                <div class="row"><span class="label">Deadline</span><span class="value">{{ $order['deadline'] ?? '48 Hours' }}</span></div>
                <div class="row"><span class="label">Order Status</span><span class="value" style="color:#c27a00;">Order is "{{ ucwords(str_replace('inprogress', 'in progress', $order['status'] ?? 'pending')) }}"</span></div>
                <div class="row"><span class="label">Payment Status</span><span class="value">{{ orderPaymentStatusLabel($order) }}</span></div>
                <div class="row"><span class="label">Pages</span><span class="value">{{ $order['pages'] ?? 1 }} Pages 275 Words ({{ $order['spacing'] ?? 'Double' }}) , {{ $order['sources'] ?? 0 }} Sources</span></div>
                <div class="row"><span class="label">Powerpoints</span><span class="value">{{ $order['charts'] ?? 0 }} Charts , {{ $order['slides'] ?? 0 }} PPTS</span></div>
                <div class="row"><span class="label">Academic Level</span><span class="value">{{ $order['level'] ?? 'College' }}, {{ $order['category'] ?? 'Standard' }}</span></div>
                <div class="row"><span class="label">Writer</span><span class="value">{{ $order['writer_name'] ?? 'Unassigned' }}</span></div>
                <div class="row"><span class="label">Upsells</span><span class="value">{{ empty($upsells) ? 'None' : implode(', ', $upsells) }}</span></div>
                <div class="row"><span class="label">Subject</span><span class="value">{{ $order['subject'] ?? 'Other' }}, {{ $order['format'] ?? 'APA' }}</span></div>
            </div>

            <div class="right-panel">
                <label>Message to</label>
                <select style="padding:10px; border:1px solid var(--border); border-radius:10px; font-weight:700;">
                    <option>Support</option>
                    <option>Writer</option>
                </select>
                <label>Message</label>
                <textarea placeholder="Write your message"></textarea>
                <button class="btn btn-secondary">Send Message</button>
                <button class="btn btn-primary" style="margin-top:6px; background: #000; color:#fff;">Delete Order</button>
            </div>
        </div>

        <div class="files-section">
            <h3 style="margin:0 0 6px 0;">Writer File(s)</h3>
            <hr style="border:none; border-top:1px solid var(--border); margin:8px 0 14px;">
            <table>
                <thead><tr><th>#</th><th>File</th><th>Date</th></tr></thead>
                <tbody>
                @forelse(($writerFiles ?? []) as $idx => $file)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><a class="file-link" href="{{ route('order.file.download', ['id' => $order['id'], 'file' => $file['path']]) }}">{{ $file['name'] }}</a></td>
                        <td>{{ $file['date'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:var(--muted); font-weight:800;">No writer files uploaded</td></tr>
                @endforelse
                </tbody>
            </table>

            <h3 style="margin:10px 0 6px 0;">Order File(s)</h3>
            <hr style="border:none; border-top:1px solid var(--border); margin:8px 0 14px;">
            <table>
                <thead><tr><th>#</th><th>File</th><th>Date</th></tr></thead>
                <tbody>
                @forelse(($orderFiles ?? []) as $idx => $file)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><a class="file-link" href="{{ route('order.file.download', ['id' => $order['id'], 'file' => $file['path']]) }}">{{ $file['name'] }}</a></td>
                        <td>{{ $file['date'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:var(--muted); font-weight:800;">No order files uploaded</td></tr>
                @endforelse
                </tbody>
            </table>

            <form class="upload" action="{{ route('customer.order.files', ['id' => $order['id']]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label>Upload any additional files here</label>
                @include('partials.multi-file-upload')
                <button type="submit" class="btn btn-secondary">Submit files</button>
                @if(session('uploaded'))
                    <div style="color:var(--green); font-weight:800;">{{ session('uploaded') }}</div>
                @endif
            </form>
        </div>
    </main>
</div>
</body>
</html>
