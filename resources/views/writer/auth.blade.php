<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Portal | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1d7bff;
            --dark: #14141a;
            --muted: #5c5c6a;
            --bg: #f7f8fb;
            --card: #ffffff;
            --border: #e6e8f0;
            --danger: #c53030;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 80% 10%, rgba(29,123,255,0.12), rgba(29,123,255,0) 28%), var(--bg);
            color: var(--dark);
            display: grid;
            place-items: center;
            padding: 22px;
        }
        .shell {
            width: 100%;
            max-width: 760px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 22px 42px rgba(18, 34, 63, 0.1);
        }
        .top {
            padding: 20px 24px 14px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            font-weight: 800;
        }
        .mark {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 14px;
            font-weight: 800;
        }
        .home {
            text-decoration: none;
            color: var(--blue);
            font-weight: 700;
            font-size: 14px;
        }
        .tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid var(--border);
            background: #fbfcff;
        }
        .tab {
            padding: 14px;
            text-align: center;
            font-weight: 800;
            color: var(--muted);
            cursor: pointer;
            border-right: 1px solid var(--border);
        }
        .tab:last-child { border-right: none; }
        .tab.active {
            color: var(--blue);
            background: #fff;
            box-shadow: inset 0 -3px 0 var(--blue);
        }
        .pane { display: none; padding: 20px 24px 24px; }
        .pane.active { display: grid; gap: 14px; }
        label { font-weight: 800; font-size: 14px; margin-bottom: 6px; display: block; }
        input {
            width: 100%;
            padding: 12px 13px;
            border-radius: 11px;
            border: 1px solid var(--border);
            background: #fcfdff;
            font-weight: 700;
            outline: none;
        }
        input[type="file"] {
            padding: 10px 13px;
            background: #fff;
        }
        input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(29,123,255,0.15);
        }
        .btn {
            border: none;
            border-radius: 11px;
            padding: 12px 14px;
            font-weight: 800;
            cursor: pointer;
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            color: #fff;
        }
        .helper { color: var(--muted); font-size: 13px; font-weight: 600; }
        .alert {
            margin: 14px 24px 0;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 700;
            font-size: 14px;
            background: #fff2f2;
            color: var(--danger);
            border: 1px solid #ffd0d0;
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="top">
            <div class="brand"><span class="mark">WR</span><span>Writer Portal</span></div>
            <a class="home" href="{{ route('writers.index') }}">View Writers</a>
        </div>

        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <div class="tabs">
            <div class="tab" data-tab="new">Create Writer Account</div>
            <div class="tab" data-tab="existing">Writer Sign In</div>
        </div>

        <div class="pane" id="pane-new">
            <form action="{{ route('writer.register') }}" method="POST" enctype="multipart/form-data" style="display:grid; gap:14px;">
                @csrf
                <input type="hidden" name="tab" value="new">
                <div>
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Writer" required>
                </div>
                <div>
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="writer@example.com" required>
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Minimum 6 characters" required>
                </div>
                <div>
                    <label>Writer Qualification</label>
                    <input type="text" name="qualification" value="{{ old('qualification') }}" placeholder="MBA, BSN, PhD, CPA">
                </div>
                <div>
                    <label>Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/*">
                </div>
                <button class="btn" type="submit">Create Account</button>
            </form>
            <div class="helper">After signup, you will see all client-posted orders. Prices are hidden from writers.</div>
        </div>

        <div class="pane" id="pane-existing">
            <form action="{{ route('writer.login') }}" method="POST" style="display:grid; gap:14px;">
                @csrf
                <input type="hidden" name="tab" value="existing">
                <div>
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="writer@example.com" required>
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Your password" required>
                </div>
                <button class="btn" type="submit">Sign In</button>
            </form>
            <div class="helper">Deadlines in writer view are automatically reduced by 4 hours from client deadlines.</div>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll('.tab');
        const panes = {
            new: document.getElementById('pane-new'),
            existing: document.getElementById('pane-existing')
        };

        const queryTab = new URLSearchParams(window.location.search).get('tab');
        const fallbackTab = @json(old('tab', $tab ?? 'new'));

        function activate(tab) {
            const chosen = tab === 'existing' ? 'existing' : 'new';
            tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === chosen));
            Object.keys(panes).forEach(key => panes[key].classList.toggle('active', key === chosen));
        }

        tabs.forEach(t => t.addEventListener('click', () => activate(t.dataset.tab)));
        activate(queryTab || fallbackTab || 'new');
    </script>
</body>
</html>
