<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Writers | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --accent:#f25c3c; --dark:#1c1c28; --muted:#6b6b7a; --border:#e5e8ed; --bg:#f7f8fb; --card:#ffffff; --green:#0f5951; }
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
        .content{padding:24px 28px 40px;display:grid;gap:16px;}
        .topbar{display:flex;justify-content:space-between;align-items:center;gap:12px;}
        .btn{border:none;border-radius:10px;padding:10px 12px;font-weight:900;cursor:pointer;text-decoration:none;}
        .btn-primary{background:var(--accent);color:#fff;}
        table{width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden;}
        th,td{padding:12px 14px;border-bottom:1px solid var(--border);text-align:left;font-size:14px;}
        th{font-weight:900;color:#2c2f33;background:#fff7f3;}
        tr:hover{background:#f9fbfc;}
        .status{display:inline-block;padding:6px 10px;border-radius:10px;font-weight:800;font-size:12px;}
        .status.active{background:#e7f8ee;color:#1f9b55;}
        .status.available{background:#e8f5ff;color:#0b6fb8;}
        @media(max-width:1000px){.layout{grid-template-columns:1fr;} .topbar{flex-direction:column;align-items:flex-start;} }
    @include('admin.partials.sidebar-styles')
    </style>
</head>
<body>
<div class="layout">
    @include('admin.partials.sidebar', ['menuCounts' => $navCounts ?? []])

    <main class="content">
        <div class="topbar">
            <div>
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Writers</div>
                <div style="font-size:24px; font-weight:900;">Writer Directory</div>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.orders') }}">Assign Orders</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Assigned Orders</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($writers as $writer)
                    <tr>
                        <td>#{{ $writer['id'] }}</td>
                        <td>{{ $writer['name'] }}</td>
                        <td>{{ $writer['orders'] }}</td>
                        <td>
                            <span class="status {{ strtolower($writer['status']) }}">
                                {{ $writer['status'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
