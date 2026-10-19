<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1d7bff;
            --accent: #f7ad26;
            --dark: #1c1c28;
            --muted: #606070;
            --border: #e6e8f0;
            --card: #ffffff;
            --bg: #f8f9fc;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 18px;
        }
        .shell {
            width: 100%;
            max-width: 960px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 30px 70px rgba(19, 43, 77, 0.08);
            overflow: hidden;
        }
        header {
            padding: 22px 26px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .brand { font-weight: 800; font-size: 20px; display: flex; align-items: center; gap: 10px; }
        .brand .mark { width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg,#1d7bff,#25c7ff); display:grid;place-items:center; color:#fff; font-weight: 800; }
        .tabs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            border-bottom: 1px solid var(--border);
        }
        .tab {
            padding: 16px;
            text-align: center;
            font-weight: 800;
            cursor: pointer;
            color: var(--muted);
            background: #fafbff;
            border-right: 1px solid var(--border);
            transition: background .12s ease, color .12s ease;
        }
        .tab:last-child { border-right: none; }
        .tab.active {
            background: #fff;
            color: var(--blue);
            box-shadow: inset 0 -3px 0 var(--blue);
        }
        .pane { display: none; padding: 28px 26px 30px; }
        .pane.active { display: grid; gap: 18px; }
        label { font-weight: 800; margin-bottom: 8px; display: block; color: #2d2d35; }
        .field { display: grid; gap: 8px; }
        input, select {
            width: 100%;
            padding: 14px 15px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 15px;
            font-weight: 600;
            background: #f8fbff;
            color: var(--dark);
            outline: none;
            transition: border .12s ease, box-shadow .12s ease;
        }
        input:focus, select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(29, 123, 255, 0.12);
        }
        .split { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
        .btn {
            border: none;
            border-radius: 14px;
            padding: 14px 16px;
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
            transition: transform .1s ease, box-shadow .15s ease;
            width: 100%;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: var(--accent);
            color: #0f172a;
            box-shadow: 0 16px 38px rgba(247, 173, 38, 0.28);
        }
        .helper { color: var(--muted); font-weight: 600; }
        .alt { text-align: center; padding: 18px; border-top: 1px solid var(--border); background:#fafbff; }
        .alt a { color: var(--blue); font-weight: 800; }
        @media (max-width: 700px) {
            header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header>
            <div class="brand">
                <span class="mark">✍</span>
                <span>Boom<strong>Papers</strong></span>
            </div>
            <div class="helper">Secure checkout · 100% private</div>
        </header>

        <div class="tabs">
            <div class="tab" data-tab="new">New Customer</div>
            <div class="tab" data-tab="existing">Existing Customer</div>
        </div>

        <div class="pane" id="pane-new">
            <form action="{{ route('customer.register') }}" method="POST">
                @csrf
                <div class="field">
                    <label>Email address</label>
                    <input type="email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="field">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Jane Doe" required>
                </div>
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="split">
                    <div class="field">
                        <label>Phone</label>
                        <select name="phone_country">
                            <option>United States +1</option>
                            <option>Kenya +254</option>
                            <option>United Kingdom +44</option>
                            <option>Canada +1</option>
                            <option>Australia +61</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>&nbsp;</label>
                        <input type="text" name="phone_number" placeholder="(eg 2015555555)" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>
        </div>

        <div class="pane" id="pane-existing">
            <form action="{{ route('customer.login') }}" method="POST">
                @csrf
                <div class="field">
                    <label>Email address</label>
                    <input type="email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>
        </div>

        <div class="alt">
            Need help? <a href="{{ route('order', ['tab' => 'existing']) }}">Chat with support</a>
        </div>
    </div>

    <script>
        const queryParams = new URLSearchParams(window.location.search);
        const defaultTab = queryParams.get('tab') || 'new';
        const tabs = document.querySelectorAll('.tab');
        const panes = {
            new: document.getElementById('pane-new'),
            existing: document.getElementById('pane-existing')
        };

        function activate(tab) {
            tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
            Object.keys(panes).forEach(key => panes[key].classList.toggle('active', key === tab));
        }

        tabs.forEach(t => t.addEventListener('click', () => activate(t.dataset.tab)));
        activate(defaultTab === 'existing' ? 'existing' : 'new');
    </script>
</body>
</html>
