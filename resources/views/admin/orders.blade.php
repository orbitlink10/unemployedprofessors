<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders | BoomPapers</title>
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
        .badge{margin-left:auto;background:#0f5951;color:#fff;border-radius:10px;padding:4px 9px;font-size:12px;font-weight:800;}
        .content{padding:24px 28px 40px;display:grid;gap:16px;align-content:start;}
        .topbar{display:flex;justify-content:space-between;align-items:center;gap:12px;}
        .filters{display:flex;gap:8px;flex-wrap:wrap;align-items:flex-start;}
        .chip{display:inline-flex;align-items:center;align-self:flex-start;padding:10px 12px;border-radius:10px;border:1px solid var(--border);background:#fff;font-weight:800;color:#2f3236;text-decoration:none;}
        .chip.active{background:#0f5951;color:#fff;border-color:#0f5951;}
        table{width:100%;border-collapse:collapse;}
        th,td{padding:12px 14px;border-bottom:1px solid var(--border);text-align:left;font-size:14px;}
        th{font-weight:900;color:#2c2f33;}
        tr:hover{background:#f9fbfc;}
        .status{padding:6px 10px;border-radius:10px;font-weight:800;display:inline-flex;align-items:center;gap:6px;}
        .status.pending{background:#fff3d9;color:#c27a00;}
        .status.available{background:#e7f8ee;color:#1f9b55;}
        .status.assigned{background:#e8f5ff;color:#0b6fb8;}
        .status.inprogress{background:#e8f5ff;color:#0b6fb8;}
        .status.completed{background:#e7f8ee;color:#1f9b55;}
        .status.revision{background:#fff0f2;color:#d62d50;}
        .status.editing{background:#f0f2ff;color:#3c4ad9;}
        .status.approved{background:#eaf6ff;color:#1f6fb5;}
        .status.cancelled{background:#fde9e9;color:#c53030;}
        .deadline-live{font-weight:800;color:#2f3236;white-space:nowrap;}
        .deadline-live.deadline-urgent{color:#c27a00;}
        .deadline-live.deadline-expired{color:#c53030;}
        .writer-pay{font-weight:900;color:#0f5951;white-space:nowrap;}
        .assign-form{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
        select,button{font-family:inherit;font-weight:800;}
        select{padding:8px 10px;border-radius:10px;border:1px solid var(--border);}
        .btn{border:none;border-radius:10px;padding:8px 12px;font-weight:900;cursor:pointer;}
        .btn-primary{background:var(--accent);color:#fff;}
        .action-stack{display:grid;gap:8px;}
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
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Orders</div>
                <div style="font-size:24px; font-weight:900;">Manage & Assign</div>
            </div>
            <a class="btn btn-primary" href="{{ route('order.create') }}">New Order</a>
        </div>

        <div class="filters">
            @php $statuses = ['pending','available','assigned','editing','completed','revision','approved','cancelled']; @endphp
            <a class="chip {{ $status ? '' : 'active' }}" data-status="all" href="{{ route('admin.orders') }}">All</a>
            @foreach($statuses as $st)
                <a class="chip {{ $status === $st ? 'active' : '' }}" data-status="{{ $st }}" href="{{ route('admin.orders', ['status'=>$st]) }}">{{ ucfirst($st) }}</a>
            @endforeach
        </div>

        @if(session('assigned'))
            <div style="background:#e7f8ee; color:#1f9b55; padding:10px 12px; border-radius:10px; font-weight:800;">
                {{ session('assigned') }}
            </div>
        @endif
        @if(session('deleted'))
            <div style="background:#fde9e9; color:#9b1c1c; padding:10px 12px; border-radius:10px; font-weight:800;">
                {{ session('deleted') }}
            </div>
        @endif

        <div class="card table-card" style="background:#fff; border:1px solid var(--border); border-radius:12px; overflow:hidden;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Writer</th>
                    <th>Deadline</th>
                    <th>Writer Pay</th>
                    <th>Status</th>
                    <th>Assign</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr data-order="{{ $order['id'] }}" data-status="{{ $order['status'] ?? 'pending' }}">
                        <td><a href="{{ route('admin.order.show', ['id' => $order['id']]) }}" style="color:var(--green); font-weight:900; text-decoration:none;">#{{ $order['id'] }}</a></td>
                        <td>
                            <a href="{{ route('admin.order.show', ['id' => $order['id']]) }}" style="color:#2c2f33; font-weight:800; text-decoration:none;">
                                {{ $order['title'] ?? 'Untitled' }}
                            </a>
                        </td>
                        <td>{{ trim((string) ($order['customer_email'] ?? '')) !== '' ? $order['customer_email'] : (orderPosterType($order) === 'admin' ? 'Admin panel' : 'customer') }}</td>
                        <td>{{ $order['writer_name'] ?? 'Unassigned' }}</td>
                        @php
                            $fallbackDeadline = $order['deadline'] ?? '48 Hours';
                            $dueAt = $order['due_at'] ?? null;
                        @endphp
                        <td>
                            <span class="deadline-live" data-deadline="{{ $dueAt }}" data-fallback="{{ $fallbackDeadline }}">
                                {{ $fallbackDeadline }}
                            </span>
                        </td>
                        <td><span class="writer-pay">Ksh {{ number_format($order['writer_payout'] ?? writerPayoutForOrder($order), 0) }}</span></td>
                        <td><span class="status {{ $order['status'] ?? 'pending' }} status-pill" data-order="{{ $order['id'] }}">{{ ucfirst($order['status'] ?? 'pending') }}</span></td>
                        <td>
                            <div class="action-stack">
                                <form class="assign-form" action="{{ route('admin.orders.assign', ['id'=>$order['id']]) }}" method="POST">
                                    @csrf
                                    <select name="writer_id" required>
                                        <option value="">Select writer</option>
                                        @foreach($writers as $w)
                                            <option value="{{ $w['id'] }}" {{ ($order['writer_id'] ?? null) == $w['id'] ? 'selected' : '' }}>{{ $w['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="writer_name" id="writer_name_{{ $order['id'] }}" value="{{ $order['writer_name'] ?? '' }}">
                                    <select name="status" class="status-select" data-order="{{ $order['id'] }}">
                                        <option value="pending" {{ ($order['status'] ?? '')==='pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="available" {{ ($order['status'] ?? '')==='available' ? 'selected' : '' }}>Available</option>
                                        <option value="assigned" {{ ($order['status'] ?? '')==='assigned' ? 'selected' : '' }}>Assigned</option>
                                        <option value="inprogress" {{ ($order['status'] ?? '')==='inprogress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="editing" {{ ($order['status'] ?? '')==='editing' ? 'selected' : '' }}>Editing</option>
                                        <option value="revision" {{ ($order['status'] ?? '')==='revision' ? 'selected' : '' }}>Revision</option>
                                        <option value="completed" {{ ($order['status'] ?? '')==='completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="approved" {{ ($order['status'] ?? '')==='approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="cancelled" {{ ($order['status'] ?? '')==='cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Assign</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <script>
                        (() => {
                            const row = document.currentScript.previousElementSibling;
                            const form = row ? row.querySelector('form.assign-form') : null;
                            const sel = form ? form.querySelector('select[name="writer_id"]') : null;
                            const hidden = form ? form.querySelector('input[name="writer_name"]') : null;
                            sel?.addEventListener('change', () => {
                                const selected = sel.options[sel.selectedIndex];
                                if (hidden) {
                                    hidden.value = selected ? selected.textContent : '';
                                }
                            });
                        })();
                    </script>
                @empty
                    <tr><td colspan="8" style="text-align:center; padding:16px; color:var(--muted); font-weight:800;">No orders yet</td></tr>
@endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
<script>
    const initialFilter = new URLSearchParams(window.location.search).get('status') || "{{ $status ?? '' }}";
    const chips = document.querySelectorAll('.chip');
    const rows = document.querySelectorAll('tr[data-status]');

    const setActiveChip = (filter) => {
        chips.forEach(c => c.classList.remove('active'));
        const match = Array.from(chips).find(c => (c.dataset.status || '') === (filter || ''));
        if (match) match.classList.add('active');
    };

    const applyFilter = (filter) => {
        rows.forEach(row => {
            const s = row.dataset.status || 'pending';
            row.style.display = (!filter || filter === 'all' || filter === s) ? '' : 'none';
        });
        setActiveChip(filter || '');
    };

    // on load
    applyFilter(initialFilter || '');

    const formatStatusLabel = (value) => {
        if (value === 'inprogress') {
            return 'In Progress';
        }

        return value
            .split('_')
            .map(part => part.charAt(0).toUpperCase() + part.slice(1))
            .join(' ');
    };

    const submitAssignForm = (select) => {
        const form = select.closest('form.assign-form');
        if (!form) {
            return;
        }

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
            return;
        }

        form.submit();
    };

    // Update status pill live when status select changes and submit immediately
    document.querySelectorAll('.status-select').forEach(sel => {
        sel.addEventListener('change', () => {
            const orderId = sel.dataset.order;
            const val = sel.value || 'pending';
            const pill = document.querySelector('.status-pill[data-order=\"' + orderId + '\"]');
            if (pill) {
                pill.textContent = formatStatusLabel(val);
                pill.className = 'status status-pill ' + val;
            }
            const row = document.querySelector('tr[data-order=\"' + orderId + '\"]');
            if (row) {
                row.dataset.status = val;
            }
            submitAssignForm(sel);
        });
    });

    const deadlineCells = document.querySelectorAll('.deadline-live');

    const formatDuration = (totalMinutes) => {
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
    };

    const formatRemainingDeadline = (diffMs) => formatDuration(Math.max(Math.floor(diffMs / 60000), 0));

    const formatLateDeadline = (diffMs) => {
        const totalMinutes = Math.max(Math.floor(Math.abs(diffMs) / 60000), 1);
        return '-' + formatDuration(totalMinutes);
    };

    const updateDeadlineCell = (cell) => {
        const dueAtRaw = cell.dataset.deadline || '';
        const fallback = cell.dataset.fallback || 'N/A';
        const dueAtMs = Date.parse(dueAtRaw);

        if (!Number.isFinite(dueAtMs)) {
            cell.textContent = fallback;
            cell.classList.remove('deadline-urgent', 'deadline-expired');
            return;
        }

        const now = Date.now();
        const diffMs = dueAtMs - now;
        cell.title = 'Due ' + new Date(dueAtMs).toLocaleString();

        if (diffMs <= 0) {
            cell.textContent = formatLateDeadline(diffMs);
            cell.classList.remove('deadline-urgent');
            cell.classList.add('deadline-expired');
            return;
        }

        cell.textContent = formatRemainingDeadline(diffMs);
        cell.classList.remove('deadline-expired');
        if (diffMs <= 60 * 60 * 1000) {
            cell.classList.add('deadline-urgent');
        } else {
            cell.classList.remove('deadline-urgent');
        }
    };

    const refreshDeadlines = () => {
        deadlineCells.forEach(updateDeadlineCell);
    };

    refreshDeadlines();
    setInterval(refreshDeadlines, 30000);
</script>
</body>
</html>
