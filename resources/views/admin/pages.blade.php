<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pages | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --accent:#f25c3c; --dark:#1c1c28; --muted:#6b6b7a; --border:#e5e8ed; --bg:#f7f8fb; --card:#ffffff; --green:#0f5951; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:'Manrope',system-ui,-apple-system,sans-serif; background:var(--bg); color:var(--dark); }
        .layout { display:grid; grid-template-columns:240px 1fr; min-height:100vh; }
        .sidebar { background:#fff; border-right:1px solid var(--border); padding:24px; display:grid; gap:26px; }
        .brand { display:flex; align-items:center; gap:12px; font-size:24px; font-weight:800; }
        .brand .icon { width:44px; height:44px; border-radius:14px; background:var(--accent); display:grid; place-items:center; color:#fff; font-size:22px; }
        .nav-group { display:grid; gap:8px; }
        .nav-title { font-size:12px; letter-spacing:0.4px; text-transform:uppercase; color:var(--muted); font-weight:800; }
        .nav-link { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:12px; color:#2f3236; font-weight:800; text-decoration:none; }
        .nav-link.active, .nav-link:hover { background:#fff2ec; color:var(--accent); }
        .content { padding:24px 28px 40px; display:grid; gap:16px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; gap:12px; }
        .btn { border:none; border-radius:12px; padding:12px 16px; font-weight:900; cursor:pointer; text-decoration:none; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-light { background:#fff; border:1px solid var(--border); color:#2f3236; }
        .btn-danger { background:#c53030; color:#fff; }
        .hint { font-size:13px; color:var(--muted); font-weight:700; }
        .ok { background:#e7f8ee; color:#1f9b55; padding:10px 12px; border-radius:10px; font-weight:800; }
        .err { background:#fde9e9; color:#c53030; padding:10px 12px; border-radius:10px; font-weight:800; }
        .grid { display:grid; grid-template-columns:1.1fr 0.9fr; gap:16px; }
        .panel { background:#fff; border:1px solid var(--border); border-radius:18px; padding:20px; box-shadow:0 18px 40px rgba(17,42,72,0.05); }
        .panel h2 { margin:0 0 16px; font-size:20px; }
        .table-wrap { overflow:auto; }
        table { width:100%; border-collapse:collapse; min-width:680px; }
        th, td { padding:12px 14px; border-bottom:1px solid var(--border); text-align:left; vertical-align:top; }
        th { font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:#5c6476; }
        .field { display:grid; gap:8px; margin-bottom:14px; }
        label { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; color:#5c6476; }
        input, textarea, select { width:100%; border:1px solid var(--border); border-radius:12px; padding:12px 14px; font:inherit; }
        textarea { min-height:280px; resize:vertical; }
        .status { display:inline-flex; align-items:center; border-radius:999px; padding:6px 10px; font-size:12px; font-weight:800; }
        .status.published { background:#e7f8ee; color:#1f9b55; }
        .status.draft { background:#fff3d9; color:#c27a00; }
        .table-actions { display:flex; gap:8px; flex-wrap:wrap; }
        .link { color:var(--green); font-weight:800; text-decoration:none; }
        .muted { color:var(--muted); font-size:13px; }
        @media (max-width: 1100px) {
            .layout, .grid { grid-template-columns:1fr; }
            .topbar { flex-direction:column; align-items:flex-start; }
        }
    @include('admin.partials.sidebar-styles')
    </style>
</head>
<body>
@php $editing = $editingPage ?? null; @endphp
<div class="layout">
    @include('admin.partials.sidebar', ['menuCounts' => $navCounts ?? []])

    <main class="content">
        <div class="topbar">
            <div>
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Pages</div>
                <div style="font-size:28px; font-weight:900;">Custom Page Manager</div>
            </div>
            <a class="btn btn-light" href="{{ route('admin.pages') }}">New Page</a>
        </div>

        <div class="hint">Create landing pages, policy pages, or service pages. Published pages are available publicly at their saved slug.</div>

        @if(session('page_saved'))
            <div class="ok">{{ session('page_saved') }}</div>
        @endif
        @if(session('page_deleted'))
            <div class="ok">{{ session('page_deleted') }}</div>
        @endif
        @if($errors->any())
            <div class="err">{{ $errors->first() }}</div>
        @endif

        <div class="grid">
            <section class="panel">
                <h2>{{ $editing ? 'Edit Page' : 'Create Page' }}</h2>
                <form action="{{ route('admin.pages.save') }}" method="POST">
                    @csrf
                    @if($editing)
                        <input type="hidden" name="id" value="{{ $editing['id'] }}">
                    @endif

                    <div class="field">
                        <label for="title">Page Title</label>
                        <input id="title" name="title" value="{{ old('title', $editing['title'] ?? '') }}" required>
                    </div>

                    <div class="field">
                        <label for="slug">Slug</label>
                        <input id="slug" name="slug" value="{{ old('slug', $editing['slug'] ?? '') }}" placeholder="leave blank to auto-generate">
                    </div>

                    <div class="field">
                        <label for="summary">Summary</label>
                        <textarea id="summary" name="summary" style="min-height:110px;">{{ old('summary', $editing['summary'] ?? '') }}</textarea>
                    </div>

                    <div class="field">
                        <label for="content">Page Content</label>
                        <textarea id="content" name="content">{{ old('content', $editing['content'] ?? '') }}</textarea>
                    </div>

                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="draft" {{ old('status', $editing['status'] ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $editing['status'] ?? 'draft') === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>

                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">{{ $editing ? 'Update Page' : 'Create Page' }}</button>
                        @if($editing)
                            <a class="btn btn-light" href="{{ route('admin.pages') }}">Cancel</a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="panel">
                <h2>Saved Pages</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th>Status</th>
                                <th>Slug</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td>
                                        <div style="font-weight:800;">{{ $page['title'] }}</div>
                                        <div class="muted">{{ $page['summary'] ?? '' }}</div>
                                    </td>
                                    <td><span class="status {{ strtolower($page['status'] ?? 'draft') }}">{{ ucfirst($page['status'] ?? 'draft') }}</span></td>
                                    <td><span class="muted">/pages/{{ $page['slug'] }}</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <a class="link" href="{{ route('admin.pages', ['edit' => $page['id']]) }}">Edit</a>
                                            @if(($page['status'] ?? 'draft') === 'published')
                                                <a class="link" href="{{ route('page.show', ['slug' => $page['slug']]) }}" target="_blank" rel="noopener noreferrer">View</a>
                                            @endif
                                            <form action="{{ route('admin.pages.delete', ['id' => $page['id']]) }}" method="POST" onsubmit="return confirm('Delete this page?');">
                                                @csrf
                                                <button class="btn btn-danger" type="submit" style="padding:8px 10px;">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted" style="padding:20px 14px;">No custom pages created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
