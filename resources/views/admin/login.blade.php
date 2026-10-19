<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Writing Script</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #14786c;
            --accent-2: #f25c3c;
            --dark: #2f2f34;
            --muted: #6a6a74;
            --border: #e7e7ee;
            --bg: linear-gradient(135deg, #fff5f0 0%, #f9fbff 100%);
            --card: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }
        .shell {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.06);
            max-width: 1100px;
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            overflow: hidden;
        }
        .left {
            padding: 42px 48px;
        }
        .right {
            padding: 42px 48px;
            background: #fbf7f5;
            border-left: 1px solid var(--border);
            display: grid;
            align-content: center;
            gap: 20px;
            text-align: center;
        }
        h1 {
            font-size: 40px;
            margin: 0 0 8px 0;
            font-weight: 800;
            letter-spacing: -0.4px;
        }
        p.lead {
            margin: 0 0 26px 0;
            color: var(--muted);
            font-weight: 600;
        }
        form { display: grid; gap: 20px; }
        label {
            display: block;
            font-weight: 800;
            margin-bottom: 8px;
            color: #383840;
        }
        .field {
            display: grid;
            gap: 8px;
        }
        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 15px;
            font-weight: 700;
            color: var(--dark);
            background: #fff;
            outline: none;
            transition: border .12s ease, box-shadow .12s ease;
        }
        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(20,120,108,0.12);
        }
        .error {
            color: #d63b3b;
            font-size: 13px;
            font-weight: 700;
        }
        .btn {
            border: none;
            border-radius: 14px;
            padding: 14px 18px;
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
            transition: transform .1s ease, box-shadow .15s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary {
            background: linear-gradient(135deg, #14786c, #0c5c53);
            color: #fff;
            box-shadow: 0 16px 38px rgba(20,120,108,0.3);
        }
        .meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            color: var(--muted);
        }
        .meta a { color: var(--accent-2); }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #e8f6f3;
            color: #0c5c53;
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 800;
            width: fit-content;
        }
        .side-illustration {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }
        .credentials {
            background: #fff;
            border: 1px dashed var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            font-weight: 700;
            color: #41414b;
            display: grid;
            gap: 6px;
        }
        @media (max-width: 900px) {
            body { padding: 20px; }
            .left, .right { padding: 32px; }
            h1 { font-size: 34px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="left">
            <h1>Sign in</h1>
            <p class="lead">Access the admin workspace to manage orders, writers, and clients.</p>
            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="email">Enter Email *</label>
                    <input type="email" id="email" name="email" placeholder="admin@demo.com" value="{{ old('email', 'admin@demo.com') }}" required>
                    @error('email') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" value="admin123" required>
                    @error('password') <span class="error">{{ $message }}</span> @enderror
                </div>
                @error('credentials') <span class="error">{{ $message }}</span> @enderror
                <button class="btn btn-primary" type="submit">Login</button>
                <div class="meta">
                    <span>Forgot Password?</span>
                    <a href="#">Password</a>
                </div>
            </form>
        </div>
        <div class="right">
            <div class="badge">Effortlessly organize your workspace</div>
            <h2 style="margin:0;font-size:28px;font-weight:800;">Manage everything in one place.</h2>
            <p style="margin:0;color:var(--muted);font-weight:700;">Track orders, assignments, revisions, and approvals with a single login.</p>
            <img class="side-illustration" src="https://illustrations.popsy.co/gray/reading.svg" alt="Workspace illustration">
            <div class="credentials">
                <div>Admin email: admin@demo.com</div>
                <div>Password: admin123</div>
            </div>
        </div>
    </div>
</body>
</html>
