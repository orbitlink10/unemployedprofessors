<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writers | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1d7bff;
            --dark: #14141a;
            --muted: #5c5c6a;
            --bg: #f7f8fb;
            --card: #ffffff;
            --border: #e6e8f0;
            --accent: #f7ad26;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
        }
        a { color: inherit; text-decoration: none; }
        header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(247,248,251,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(230,232,240,0.6);
        }
        .nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -0.2px;
        }
        .brand .mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            box-shadow: 0 12px 35px rgba(29,123,255,0.25);
        }
        .brand .text strong { color: var(--blue); }
        .menu {
            display: flex;
            align-items: center;
            gap: 26px;
            font-weight: 600;
            color: #202028;
        }
        .menu a {
            position: relative;
            padding: 6px 0;
            font-size: 15px;
        }
        .menu a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -6px;
            height: 3px;
            width: 0;
            background: var(--blue);
            border-radius: 12px;
            transition: width .18s ease;
        }
        .menu a:hover::after,
        .menu a.active::after { width: 18px; }
        .actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn {
            border-radius: 10px;
            padding: 12px 18px;
            font-weight: 700;
            border: 1px solid transparent;
            transition: transform .15s ease, box-shadow .15s ease, border .15s ease;
            font-size: 14px;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-ghost {
            background: #ffffff;
            border-color: var(--border);
            color: var(--dark);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            color: white;
            box-shadow: 0 12px 30px rgba(29,123,255,0.35);
        }
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 36px 24px 80px;
        }
        h1 {
            margin: 0 0 8px;
            font-size: clamp(30px, 3vw + 8px, 44px);
            letter-spacing: -0.4px;
        }
        .lead {
            margin: 0 0 24px;
            color: var(--muted);
            font-weight: 600;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 15px 35px rgba(17,24,39,0.06);
            display: grid;
            gap: 8px;
        }
        .name {
            font-size: 18px;
            font-weight: 800;
        }
        .meta {
            color: var(--muted);
            font-weight: 700;
            font-size: 14px;
        }
        .stats {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 6px;
        }
        .tag {
            background: #f2f7ff;
            color: #195bb8;
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 800;
        }
        .card a {
            margin-top: 8px;
            width: fit-content;
            color: #0f5951;
            font-weight: 800;
        }
        @media (max-width: 900px) {
            .menu { display: none; }
        }
    </style>
</head>
<body>
    <header>
        <div class="nav">
            <a class="brand" href="{{ url('/') }}">
                <span class="mark">WP</span>
                <span class="text">Boom<strong>Papers</strong></span>
            </a>
            <nav class="menu">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/') }}#how">How it Works</a>
                <a href="{{ url('/') }}#services">Services</a>
                <a href="{{ route('writers.index') }}" class="active">Writers</a>
                <a href="{{ url('/') }}#reviews">Reviews</a>
            </nav>
            <div class="actions">
                <a class="btn btn-ghost" href="{{ route('writer.auth', ['tab' => 'existing']) }}">Writer Sign In</a>
                <a class="btn btn-primary" href="{{ route('order', ['tab' => 'new']) }}">Order Now</a>
            </div>
        </div>
    </header>

    <main>
        <h1>Top Writers</h1>
        <p class="lead">Choose from experienced writers by specialty, rating, and completed orders.</p>

        <section class="grid">
            @foreach($writers as $writer)
                <article class="card">
                    <div class="name">{{ $writer['name'] }}</div>
                    <div class="meta">{{ $writer['specialty'] }}</div>
                    <div class="stats">
                        <span class="tag">Rating {{ $writer['rating'] }}/5</span>
                        <span class="tag">{{ $writer['orders'] }} orders</span>
                    </div>
                    <a href="{{ route('order', ['tab' => 'new']) }}">Hire Writer</a>
                </article>
            @endforeach
        </section>
    </main>
</body>
</html>
