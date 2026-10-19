<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page['title'] ?? 'Page' }} | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --blue:#1d7bff; --dark:#14141a; --muted:#5c5c6a; --bg:#f7f8fb; --border:#e6e8f0; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:'Manrope',system-ui,-apple-system,sans-serif; background:var(--bg); color:var(--dark); }
        a { color:inherit; text-decoration:none; }
        header { position:sticky; top:0; z-index:10; background:rgba(247,248,251,0.92); backdrop-filter:blur(12px); border-bottom:1px solid rgba(230,232,240,0.6); }
        .nav { max-width:1200px; margin:0 auto; padding:18px 24px; display:flex; align-items:center; justify-content:space-between; gap:24px; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:800; font-size:22px; }
        .mark { width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, #1d7bff, #25c7ff); display:grid; place-items:center; color:#fff; font-size:16px; font-weight:800; box-shadow:0 12px 35px rgba(29,123,255,0.25); }
        .brand strong { color:var(--blue); }
        .menu { display:flex; align-items:center; gap:26px; font-weight:600; }
        .actions { display:flex; align-items:center; gap:12px; }
        .btn { border-radius:10px; padding:12px 18px; font-weight:700; font-size:14px; }
        .btn-ghost { background:#fff; border:1px solid var(--border); }
        .btn-primary { background:linear-gradient(135deg, #1d7bff, #25c7ff); color:#fff; box-shadow:0 12px 30px rgba(29,123,255,0.35); }
        .shell { max-width:1000px; margin:0 auto; padding:56px 24px 80px; }
        .page-card { background:#fff; border:1px solid var(--border); border-radius:24px; padding:32px; box-shadow:0 24px 60px rgba(23,44,85,0.08); }
        h1 { margin:0 0 14px; font-size:46px; line-height:1.08; letter-spacing:-0.5px; }
        .summary { color:var(--muted); font-size:18px; line-height:1.7; margin-bottom:22px; }
        .content h2, .content h3 { margin:26px 0 12px; line-height:1.15; }
        .content p { color:var(--muted); line-height:1.8; margin:0 0 16px; }
        .content ul, .content ol { color:var(--muted); line-height:1.8; margin:0 0 16px 22px; }
        @media (max-width: 900px) {
            .menu { display:none; }
            .shell { padding:40px 20px 60px; }
            .page-card { padding:24px; }
            h1 { font-size:34px; }
        }
    </style>
</head>
<body>
<header>
    <div class="nav">
        <a class="brand" href="{{ url('/') }}">
            <span class="mark">BP</span>
            <span>Boom<strong>Papers</strong></span>
        </a>
        <nav class="menu">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ route('writers.index') }}">Writers</a>
            @foreach($navPages ?? [] as $navPage)
                <a href="{{ route('page.show', ['slug' => $navPage['slug']]) }}">{{ $navPage['title'] }}</a>
            @endforeach
        </nav>
        <div class="actions">
            <a class="btn btn-ghost" href="{{ route('order', ['tab' => 'existing']) }}">Sign In</a>
            <a class="btn btn-primary" href="{{ route('order', ['tab' => 'new']) }}">Order Now</a>
        </div>
    </div>
</header>

<main class="shell">
    <article class="page-card">
        <h1>{{ $page['title'] ?? 'Page' }}</h1>
        @if(trim((string) ($page['summary'] ?? '')) !== '')
            <div class="summary">{{ $page['summary'] }}</div>
        @endif
        <div class="content">
            {!! trim((string) ($page['content'] ?? '')) !== '' ? $page['content'] : '<p>No page content has been added yet.</p>' !!}
        </div>
    </article>
</main>
</body>
</html>
