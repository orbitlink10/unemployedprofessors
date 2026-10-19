<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Order #{{ $order['id'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --accent:#f25c3c; --dark:#1c1c28; --muted:#6c737a; --border:#e5e8ed; --bg:#f7f8fb; --card:#ffffff; --green:#0f5951; }
        *{box-sizing:border-box;}
        body{margin:0;font-family:'Manrope',system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--dark);}
        .layout{display:grid;grid-template-columns:240px 1fr;min-height:100vh;}
        .sidebar{background:#fff;border-right:1px solid var(--border);padding:24px;display:grid;gap:26px;}
        .brand{display:flex;align-items:center;gap:12px;font-size:24px;font-weight:800;}
        .brand .icon{width:44px;height:44px;border-radius:14px;background:var(--accent);display:grid;place-items:center;color:#fff;font-size:22px;}
        .nav-group{display:grid;gap:8px;}
        .nav-title{font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:var(--muted);font-weight:800;}
        .nav-link{display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:12px;color:#2f3236;font-weight:800;text-decoration:none;}
        .nav-link.active,.nav-link:hover{background:#fff2ec;color:var(--accent);}
        .badge{margin-left:auto;background:#0f5951;color:#fff;border-radius:10px;padding:4px 9px;font-size:12px;font-weight:800;}
        .content{padding:24px 28px 40px;display:grid;gap:16px;}
        .topline{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;}
        h1{margin:0;font-size:32px;letter-spacing:-0.3px;}
        .pill-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap;color:#c47a00;font-weight:800;}
        .info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:10px;}
        .row{background:#fff;border:1px solid var(--border);border-radius:10px;padding:12px 14px;display:flex;flex-direction:column;gap:6px;}
        .row .label{font-weight:900;color:#2f3236;}
        .row .value{font-weight:700;color:#3a3d42;}
        .row .value.instructions{display:block;white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.55;}
        .files{background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px;}
        table{width:100%;border-collapse:collapse;}
        th,td{padding:10px 12px;border-bottom:1px solid var(--border);text-align:left;}
        th{font-weight:900;}
        .file-link{color:var(--green);font-weight:800;text-decoration:underline;text-underline-offset:2px;}
        .file-link:hover{color:#0b4a44;}
        .upload{margin-top:12px;display:grid;gap:10px;}
        .btn{border:none;border-radius:10px;padding:10px 12px;font-weight:900;cursor:pointer;}
        .btn-primary{background:var(--accent);color:#fff;}
        .btn-danger{background:#c53030;color:#fff;}
        .btn-danger:hover{background:#a61f1f;}
        .assign-form{display:flex;gap:8px;flex-wrap:wrap;}
        .header-actions{display:grid;gap:10px;justify-items:end;align-content:start;}
        .delete-form{display:flex;justify-content:flex-end;}
        select,input,button{font-family:inherit;font-weight:800;}
        select{padding:8px 10px;border-radius:10px;border:1px solid var(--border);}
        .badge-status{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:10px;font-weight:900;}
        .badge-status.pending{background:#fff3d9;color:#c27a00;}
        .badge-status.available{background:#e8f6ff;color:#0b6fb8;}
        .badge-status.assigned,.badge-status.inprogress{background:#e8f5ff;color:#0b6fb8;}
        .badge-status.editing{background:#f0f2ff;color:#3c4ad9;}
        .badge-status.revision{background:#fff0f2;color:#d62d50;}
        .badge-status.completed{background:#e7f8ee;color:#1f9b55;}
        .badge-status.approved{background:#eaf6ff;color:#1f6fb5;}
        .badge-status.cancelled{background:#fde9e9;color:#c53030;}
        @media(max-width:1000px){.layout{grid-template-columns:1fr;} .topline{flex-direction:column;align-items:flex-start;} }
    @include('admin.partials.sidebar-styles')
    </style>
</head>
<body>
<div class="layout">
    @include('admin.partials.sidebar', ['menuCounts' => $navCounts ?? []])

    <main class="content">
        <div class="topline">
            <div>
                <h1>#{{ $order['id'] }} {{ $order['title'] ?? 'Untitled' }}</h1>
                <div class="pill-row">
                    <span>{{ $order['level'] ?? 'College' }}</span>
                    <span>★ ★ ★ ★ ★</span>
                    <span>{{ $order['category'] ?? 'Standard' }} Writer</span>
                </div>
                <div class="pill-row" style="color:#4a4e55;">
                    <span>{{ $order['pages'] ?? 1 }} Page(s) 275 Words, {{ $order['slides'] ?? 0 }} PPTs</span>
                    <span>Due: {{ $order['deadline'] ?? '48 Hours' }}</span>
                    <span>USD {{ number_format($order['cost'] ?? 0,2) }}</span>
                    <span>Writer pay: Ksh {{ number_format($order['writer_payout'] ?? writerPayoutForOrder($order), 0) }}</span>
                </div>
            </div>
            <div class="header-actions">
                <form class="assign-form" action="{{ route('admin.orders.assign', ['id'=>$order['id']]) }}" method="POST">
                    @csrf
                    <select name="writer_id" required>
                        <option value="">Select writer</option>
                        @foreach(($writers ?? []) as $writer)
                            <option value="{{ $writer['id'] }}" {{ ($order['writer_id'] ?? null) == $writer['id'] ? 'selected' : '' }}>{{ $writer['name'] }}</option>
                        @endforeach
                    </select>
                    <select name="status">
                        @php $sts=['pending','available','assigned','inprogress','editing','revision','completed','approved','cancelled']; @endphp
                        @foreach($sts as $st)
                            <option value="{{ $st }}" {{ ($order['status'] ?? '')===$st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </form>
                <form class="delete-form" action="{{ route('admin.orders.delete', ['id' => $order['id']]) }}" method="POST" onsubmit="return confirm('Delete this order? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="redirect_to" value="{{ route('admin.orders') }}">
                    <button type="submit" class="btn btn-danger">Delete Order</button>
                </form>
            </div>
        </div>

        <div class="info-grid">
            <div class="row"><span class="label">Client</span><span class="value">{{ $order['customer_email'] ?? 'customer' }}</span></div>
            <div class="row"><span class="label">Writer</span><span class="value">{{ $order['writer_name'] ?? 'Unassigned' }}</span></div>
            <div class="row"><span class="label">Writer Pay</span><span class="value">Ksh {{ number_format($order['writer_payout'] ?? writerPayoutForOrder($order), 0) }}</span></div>
            <div class="row">
                <span class="label">Order Status</span>
                <span class="value">
                    <span id="statusBadge" class="badge-status {{ $order['status'] ?? 'pending' }}">{{ ucfirst($order['status'] ?? 'pending') }}</span>
                </span>
            </div>
            <div class="row"><span class="label">Pages</span><span class="value">{{ $order['pages'] ?? 1 }} Pages ({{ $order['spacing'] ?? 'Double' }}) , {{ $order['sources'] ?? 0 }} Sources</span></div>
            <div class="row"><span class="label">Powerpoints</span><span class="value">{{ $order['charts'] ?? 0 }} Charts , {{ $order['slides'] ?? 0 }} PPTS</span></div>
            <div class="row"><span class="label">Academic Level</span><span class="value">{{ $order['level'] ?? 'College' }}, {{ $order['category'] ?? 'Standard' }}</span></div>
            <div class="row"><span class="label">Subject</span><span class="value">{{ $order['subject'] ?? 'Other' }}, {{ $order['format'] ?? 'APA' }}</span></div>
            <div class="row"><span class="label">Instructions</span><div class="value instructions">{{ trim((string) ($order['instructions'] ?? '')) !== '' ? $order['instructions'] : '-' }}</div></div>
        </div>

        <div class="files">
            <h3 style="margin:0 0 6px 0;">Order File(s)</h3>
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

            <h3 style="margin:18px 0 6px 0;">Writer File(s)</h3>
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

            <form class="upload" action="{{ route('customer.order.files', ['id' => $order['id']]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label>Upload files (on behalf of client)</label>
                @include('partials.multi-file-upload')
                <button type="submit" class="btn btn-primary">Upload</button>
                @if(session('uploaded'))
                    <div style="color:var(--green); font-weight:800;">{{ session('uploaded') }}</div>
                @endif
            </form>
        </div>
    </main>
</div>
<script>
    (() => {
        const statusSel = document.querySelector('select[name="status"]');
        const badge = document.getElementById('statusBadge');
        const syncStatus = () => {
            if (!statusSel || !badge) return;
            const val = statusSel.value || 'pending';
            badge.textContent = val.charAt(0).toUpperCase() + val.slice(1);
            badge.className = 'badge-status ' + val;
        };
        statusSel?.addEventListener('change', syncStatus);
        syncStatus();
    })();
</script>
</body>
</html>
