<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Dashboard | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f2f2f2;
            --panel: #ffffff;
            --panel-soft: #f7f7f7;
            --line: #dfdfdf;
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

        .sidebar {
            padding: 26px 14px;
            border-right: 1px solid #d8d8d8;
            background: rgba(255, 255, 255, 0.42);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 18px;
            border-bottom: 1px solid #d9d9d9;
        }

        .brand-mark {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(145deg, #1d72d8, #2947b7);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .brand-copy {
            display: grid;
            gap: 2px;
        }

        .brand-title {
            font-size: 27px;
            font-weight: 800;
            letter-spacing: -0.8px;
        }

        .brand-subtitle {
            font-size: 11px;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.4px;
        }

        .nav-card {
            margin-top: 18px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid #e5e5e5;
            border-radius: 18px;
            padding: 12px 10px;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.05);
        }

        .nav-section {
            display: grid;
            gap: 4px;
        }

        .nav-section + .nav-section {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #ececec;
        }

        .nav-label {
            padding: 0 10px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #8a8a8a;
            font-weight: 800;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 12px;
            border-radius: 14px;
            text-decoration: none;
            color: #666;
            font-weight: 700;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #fff;
            color: #2f2f2f;
            transform: translateX(2px);
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            color: #6b6b6b;
            flex-shrink: 0;
        }

        .nav-link.active .nav-icon,
        .nav-link:hover .nav-icon {
            color: #272727;
        }

        .nav-count {
            margin-left: auto;
            min-width: 30px;
            height: 30px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            padding: 0 8px;
            background: #fdeaea;
            border: 1px solid #f1b8b8;
            color: var(--danger);
            font-size: 12px;
            font-weight: 800;
        }

        .nav-link.active .nav-count,
        .nav-link:hover .nav-count {
            color: var(--danger);
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

        .workspace-panel {
            background: var(--panel);
            border: 1px solid #e5e5e5;
            border-radius: 22px;
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.05);
            min-width: 0;
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

        .panel-kicker {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #8a8a8a;
            font-weight: 800;
        }

        .panel-head h2 {
            margin: 6px 0 0;
            font-size: 30px;
            letter-spacing: -0.8px;
        }

        .panel-head p {
            margin: 8px 0 0;
            color: var(--muted);
            font-weight: 600;
        }

        .panel-meta {
            display: grid;
            gap: 8px;
            justify-items: end;
        }

        .panel-pill {
            border-radius: 999px;
            padding: 9px 12px;
            background: var(--panel-soft);
            border: 1px solid #e7e7e7;
            font-size: 12px;
            font-weight: 800;
            color: #666;
        }

        .table-wrap {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            min-width: 1280px;
            border-collapse: collapse;
        }

        thead {
            background: #fafafa;
        }

        th,
        td {
            padding: 14px 22px;
            border-bottom: 1px solid #ededed;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #7d7d7d;
            font-weight: 800;
        }

        tbody tr:hover {
            background: #fcfcfc;
        }

        .order-id {
            font-weight: 800;
            color: #2f2f2f;
            text-decoration: none;
        }

        .order-title {
            font-size: 12px;
            font-weight: 800;
            color: #232323;
            letter-spacing: -0.5px;
        }

        .order-link {
            color: inherit;
            text-decoration: none;
        }

        .order-link:hover .order-title,
        .order-id:hover {
            color: #4f61c2;
        }

        .meta {
            margin-top: 5px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        .poster-note {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .poster-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .poster-badge.admin {
            background: #eef3ff;
            color: #455cc7;
        }

        .poster-badge.customer {
            background: #edf9ef;
            color: #187a4b;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .status.private {
            background: #efefef;
            color: #767676;
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

        .deadline-live {
            font-weight: 800;
            color: #2d2d2d;
            white-space: nowrap;
        }

        .deadline-live.deadline-urgent {
            color: var(--warning);
        }

        .deadline-live.deadline-expired {
            color: var(--danger);
        }

        .writer-pay {
            font-weight: 800;
            color: var(--success);
            white-space: nowrap;
        }

        .order-actions {
            display: grid;
            gap: 10px;
            min-width: 180px;
        }

        .order-actions form {
            display: grid;
            gap: 8px;
            margin: 0;
        }

        .order-actions input[type="file"],
        .order-actions select {
            width: 100%;
            border: 1px solid #dddddd;
            border-radius: 12px;
            padding: 10px 12px;
            font: inherit;
            font-size: 13px;
            color: #555;
            background: #fff;
        }

        .btn {
            border: 0;
            border-radius: 12px;
            padding: 11px 14px;
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            text-align: center;
        }

        .btn-success {
            background: #232323;
            color: #fff;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-light {
            background: #f3f3f3;
            color: #4e4e4e;
            border: 1px solid #dfdfdf;
        }

        .file-note {
            font-size: 12px;
            font-weight: 800;
            color: #5d70c8;
        }

        .empty {
            text-align: center;
            padding: 32px 20px;
            color: var(--muted);
            font-weight: 700;
        }

        @media (max-width: 1080px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid #d8d8d8;
            }

            .content {
                padding: 18px;
            }
        }

        @media (max-width: 840px) {
            .hero,
            .panel-head {
                flex-direction: column;
            }

            th,
            td {
                padding: 12px 14px;
            }

            .order-title {
                font-size: 12px;
            }
        }
    @include('admin.partials.sidebar-styles')

        .nav-count {
            color: var(--danger);
        }
    </style>
</head>
<body>
    @php
        $availableItem = collect($menuItems ?? [])->firstWhere('key', 'available') ?? [];
        $assignedItem = collect($menuItems ?? [])->firstWhere('key', 'assigned') ?? [];
        $revisionItem = collect($menuItems ?? [])->firstWhere('key', 'revision') ?? [];
        $completedItem = collect($menuItems ?? [])->firstWhere('key', 'completed') ?? [];
        $approvedItem = collect($menuItems ?? [])->firstWhere('key', 'approved') ?? [];
        $availableCount = $availableItem['count'] ?? 0;
        $assignedCount = $assignedItem['count'] ?? 0;
        $revisionCount = $revisionItem['count'] ?? 0;
        $completedCount = $completedItem['count'] ?? 0;
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
                        <a class="nav-link {{ ($menu ?? 'available') === 'available' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['menu' => 'available']) }}">
                            <span>Available</span>
                            <span class="nav-count">{{ $availableCount }}</span>
                        </a>
                        <a class="nav-link {{ ($menu ?? '') === 'assigned' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['menu' => 'assigned']) }}">
                            <span>Assigned</span>
                            <span class="nav-count">{{ $assignedCount }}</span>
                        </a>
                        <a class="nav-link {{ ($menu ?? '') === 'completed' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['menu' => 'completed']) }}">
                            <span>Completed</span>
                            <span class="nav-count">{{ $completedCount }}</span>
                        </a>
                        <a class="nav-link {{ ($menu ?? '') === 'revision' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['menu' => 'revision']) }}">
                            <span>Revision</span>
                            <span class="nav-count">{{ $revisionCount }}</span>
                        </a>
                        <a class="nav-link {{ ($menu ?? '') === 'approved' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['menu' => 'approved']) }}">
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
                        <a class="nav-link" href="{{ route('writer.payments') }}">
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
                    <h1>{{ session('writer_name', 'Writer') }}</h1>
                    <p>Manage your queue from one place. Available jobs include work posted by both admin and customers, while assigned, revision, and completed work stay grouped in their own sections.</p>
                </div>
                <div class="hero-badge">{{ $activeMenu['label'] ?? 'Available' }} Queue</div>
            </section>

            @if(session('status'))
                <div class="flash success">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="flash error">{{ session('error') }}</div>
            @endif
            @if(session('uploaded'))
                <div class="flash success">{{ session('uploaded') }}</div>
            @endif

            <section class="workspace-panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Current Queue</div>
                        <h2>{{ $activeMenu['label'] ?? 'Available' }}</h2>
                        <p>{{ $activeMenu['description'] ?? 'Open orders ready to take.' }}</p>
                    </div>
                    <div class="panel-meta">
                        <div class="panel-pill">{{ count($orders ?? []) }} order(s)</div>
                        <div class="panel-pill">Writer pay rate: Ksh {{ number_format(writerRatePerPage(), 0) }}/page</div>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Pages</th>
                                <th>Writer Pay</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Assigned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <a class="order-id" href="{{ route('writer.order.show', ['id' => $order['id']]) }}">#{{ $order['id'] }}</a>
                                    </td>
                                    <td>
                                        <a class="order-link" href="{{ route('writer.order.show', ['id' => $order['id']]) }}">
                                            <div class="order-title">{{ $order['title'] }}</div>
                                            <div class="meta">{{ $order['subject'] }}</div>
                                            <div class="poster-note">
                                                <span class="poster-badge {{ $order['posted_by'] }}">{{ $order['posted_by_label'] }}</span>
                                                <span>Posted by {{ $order['posted_by_name'] }}</span>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $order['client_name'] }}</div>
                                    </td>
                                    <td>{{ $order['type'] }}</td>
                                    <td>{{ $order['pages'] }}</td>
                                    <td><span class="writer-pay">Ksh {{ number_format($order['writer_payout'] ?? writerPayoutForOrder($order), 0) }}</span></td>
                                    <td>
                                        <span class="deadline-live" data-deadline="{{ $order['writer_due_at'] ?? '' }}" data-fallback="{{ $order['writer_deadline_fallback'] }}">
                                            {{ $order['writer_deadline'] }}
                                        </span>
                                    </td>
                                    <td><span class="status {{ $order['status'] }}">{{ $order['status_label'] }}</span></td>
                                    <td>{{ $order['assigned_writer'] }}</td>
                                    <td>
                                        <div class="order-actions">
                                            @if($order['can_take'])
                                                <form action="{{ route('writer.order.claim', ['id' => $order['id']]) }}" method="POST">
                                                    @csrf
                                                    <button class="btn btn-success" type="submit">Take Order</button>
                                                </form>
                                            @elseif($order['is_assigned_to_current'])
                                                <form action="{{ route('writer.order.status', ['id' => $order['id']]) }}" method="POST">
                                                    @csrf
                                                    <select name="status" aria-label="Update order status">
                                                        @foreach($order['status_options'] as $key => $value)
                                                            <option value="{{ $key }}" {{ ($order['status'] === $key) ? 'selected' : '' }}>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-primary" type="submit">Update Status</button>
                                                </form>
                                                <form action="{{ route('writer.order.files', ['id' => $order['id']]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @include('partials.multi-file-upload', ['required' => true])
                                                    <button class="btn btn-light" type="submit">Upload Files</button>
                                                </form>
                                                @if(($order['files_count'] ?? 0) > 0)
                                                    <div class="file-note">{{ $order['files_count'] }} writer file(s)</div>
                                                @endif
                                            @else
                                                <div class="meta">Not available</div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="empty">No orders in this queue right now.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        const deadlineCells = document.querySelectorAll('.deadline-live');

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

        function refreshDeadlines() {
            deadlineCells.forEach(cell => {
                const rawDueAt = cell.dataset.deadline || '';
                const fallback = cell.dataset.fallback || 'N/A';
                const dueAtMs = Date.parse(rawDueAt);

                if (!Number.isFinite(dueAtMs)) {
                    cell.textContent = fallback;
                    cell.classList.remove('deadline-urgent', 'deadline-expired');
                    return;
                }

                const diffMs = dueAtMs - Date.now();
                cell.title = 'Writer due ' + new Date(dueAtMs).toLocaleString();

                if (diffMs <= 0) {
                    cell.textContent = 'Expired';
                    cell.classList.add('deadline-expired');
                    cell.classList.remove('deadline-urgent');
                    return;
                }

                cell.textContent = formatRemaining(diffMs);
                cell.classList.remove('deadline-expired');

                if (diffMs <= 60 * 60 * 1000) {
                    cell.classList.add('deadline-urgent');
                } else {
                    cell.classList.remove('deadline-urgent');
                }
            });
        }

        refreshDeadlines();
        setInterval(refreshDeadlines, 30000);
    </script>
</body>
</html>
