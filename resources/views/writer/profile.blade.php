<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Profile | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f2f2f2;
            --panel: #ffffff;
            --panel-soft: #f7f7f7;
            --line: #dfdfdf;
            --text: #2d2d2d;
            --muted: #727272;
            --accent: #5f70cb;
            --accent-soft: #eef1ff;
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
            grid-template-columns: 320px minmax(0, 1fr);
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

        .content {
            padding: 28px;
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

        .profile-section {
            display: grid;
            grid-template-columns: minmax(320px, 430px) minmax(0, 1fr);
            gap: 16px;
        }

        .profile-card,
        .profile-copy-card {
            background: var(--panel);
            border: 1px solid #e5e5e5;
            border-radius: 22px;
            padding: 22px;
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.05);
        }

        .profile-card {
            display: grid;
            gap: 18px;
        }

        .profile-card-head {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            object-fit: cover;
            border: 1px solid #e6e6e6;
            background: #eceff9;
            flex-shrink: 0;
        }

        .profile-avatar-fallback {
            display: grid;
            place-items: center;
            font-size: 28px;
            font-weight: 800;
            color: #4e60bf;
        }

        .profile-name {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.6px;
        }

        .profile-tag {
            margin-top: 6px;
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 8px 12px;
            background: var(--accent-soft);
            color: #5b69c4;
            font-size: 12px;
            font-weight: 800;
        }

        .profile-list {
            display: grid;
            gap: 10px;
        }

        .profile-row {
            display: grid;
            gap: 4px;
            padding: 12px 14px;
            border-radius: 16px;
            background: #fafafa;
            border: 1px solid #ececec;
        }

        .profile-row-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            color: #8f8f8f;
            font-weight: 800;
        }

        .profile-row-value {
            font-size: 15px;
            font-weight: 700;
            color: #2f2f2f;
            word-break: break-word;
        }

        .profile-copy-card {
            display: grid;
            align-content: center;
            gap: 12px;
        }

        .panel-kicker {
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #6781a7;
            font-weight: 800;
        }

        .profile-copy-card h2 {
            margin: 0;
            font-size: 30px;
            letter-spacing: -0.8px;
        }

        .profile-copy-card p {
            margin: 0;
            color: var(--muted);
            font-weight: 600;
            max-width: 560px;
        }

        .panel-pill {
            width: max-content;
            border-radius: 999px;
            padding: 9px 12px;
            background: var(--panel-soft);
            border: 1px solid #e7e7e7;
            font-size: 12px;
            font-weight: 800;
            color: #666;
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

        @media (max-width: 980px) {
            .profile-section {
                grid-template-columns: 1fr;
            }

            .hero {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @php
        $profile = $writerProfile ?? [];
        $profileName = trim((string) ($profile['name'] ?? 'Writer'));
        $profileEmail = trim((string) ($profile['email'] ?? 'writer@example.com'));
        $profileQualification = trim((string) ($profile['qualification'] ?? ''));
        $profilePicture = trim((string) ($profile['profile_picture'] ?? ''));
        $profileId = $profile['id'] ?? session('writer_id');
        $initialParts = preg_split('/\s+/', $profileName) ?: [];
        $initials = collect($initialParts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
        if ($initials === '') {
            $initials = 'WR';
        }
    @endphp

    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">BP</div>
                <div class="brand-copy">
                    <div class="brand-title">boompapers</div>
                    <div class="brand-subtitle">Writer Workspace</div>
                </div>
            </div>

            <div class="nav-card">
                <div class="nav-section">
                    <div class="nav-label">Workspace</div>
                    <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'available']) }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 11.5 12 4l9 7.5"></path>
                            <path d="M5 10.5V20h14v-9.5"></path>
                        </svg>
                        <span>Available</span>
                    </a>
                    <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'assigned']) }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                            <path d="M8 12h8"></path>
                            <path d="M8 8h8"></path>
                            <path d="M8 16h5"></path>
                        </svg>
                        <span>Assigned</span>
                    </a>
                    <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'revision']) }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 12a9 9 0 0 1 15.4-6.4L21 8"></path>
                            <path d="M21 3v5h-5"></path>
                            <path d="M21 12a9 9 0 0 1-15.4 6.4L3 16"></path>
                            <path d="M8 16H3v5"></path>
                        </svg>
                        <span>Revision</span>
                    </a>
                    <a class="nav-link" href="{{ route('writer.dashboard', ['menu' => 'completed']) }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m5 12 4.2 4.2L19 6.5"></path>
                        </svg>
                        <span>Completed</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Profile</div>
                    <a class="nav-link active" href="{{ route('writer.profile') }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 20a8 8 0 0 1 16 0"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                    <a class="nav-link" href="{{ route('writer.payments') }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="M3 10h18"></path>
                            <path d="M7 15h3"></path>
                        </svg>
                        <span>Payment</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Links</div>
                    <a class="nav-link" href="{{ route('writers.index') }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9.5" cy="7" r="4"></circle>
                            <path d="M20 8v6"></path>
                            <path d="M23 11h-6"></path>
                        </svg>
                        <span>Public Writers</span>
                    </a>
                    <a class="nav-link" href="{{ route('writer.logout') }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <path d="M16 17l5-5-5-5"></path>
                            <path d="M21 12H9"></path>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </aside>

        <main class="content">
            <section class="hero">
                <div>
                    <h1>{{ $profileName }}</h1>
                    <p>This page contains the writer account details stored in the workspace. Use the left menu to go back to your order queues.</p>
                </div>
                <div class="hero-badge">My Profile</div>
            </section>

            <section class="profile-section">
                <article class="profile-card">
                    <div class="profile-card-head">
                        @if($profilePicture !== '')
                            <img class="profile-avatar" src="{{ asset($profilePicture) }}" alt="{{ $profileName }}">
                        @else
                            <div class="profile-avatar profile-avatar-fallback">{{ $initials }}</div>
                        @endif
                        <div>
                            <div class="profile-name">{{ $profileName }}</div>
                            <div class="profile-tag">Writer Profile</div>
                        </div>
                    </div>

                    <div class="profile-list">
                        <div class="profile-row">
                            <div class="profile-row-label">Name</div>
                            <div class="profile-row-value">{{ $profileName }}</div>
                        </div>
                        <div class="profile-row">
                            <div class="profile-row-label">Email</div>
                            <div class="profile-row-value">{{ $profileEmail }}</div>
                        </div>
                        <div class="profile-row">
                            <div class="profile-row-label">Writer ID</div>
                            <div class="profile-row-value">#{{ $profileId ?: 'N/A' }}</div>
                        </div>
                        <div class="profile-row">
                            <div class="profile-row-label">Profile Pic</div>
                            <div class="profile-row-value">{{ $profilePicture !== '' ? 'Uploaded' : 'Not uploaded yet' }}</div>
                        </div>
                        <div class="profile-row">
                            <div class="profile-row-label">Writer Qualification</div>
                            <div class="profile-row-value">{{ $profileQualification !== '' ? $profileQualification : 'Not added yet' }}</div>
                        </div>
                    </div>
                </article>

                <article class="profile-copy-card">
                    <div class="panel-kicker">Profile Section</div>
                    <h2>Writer details at a glance</h2>
                    <p>This page shows the writer name, email, writer ID, profile photo status, and qualification pulled from the writer account data.</p>
                    <div class="panel-pill">Profile picture is shown here when uploaded during registration</div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
