<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
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
        .hint { font-size:13px; color:var(--muted); font-weight:700; }
        .ok { background:#e7f8ee; color:#1f9b55; padding:10px 12px; border-radius:10px; font-weight:800; }
        .err { background:#fde9e9; color:#c53030; padding:10px 12px; border-radius:10px; font-weight:800; }
        .panel { background:#fff; border:1px solid var(--border); border-radius:18px; padding:20px; box-shadow:0 18px 40px rgba(17,42,72,0.05); }
        .panel-head { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:18px; }
        .panel-head h2 { margin:0; font-size:16px; letter-spacing:0.3px; text-transform:uppercase; }
        .panel-note { color:var(--muted); font-weight:700; }
        .grid-3 { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
        .grid-2 { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px; }
        .field { display:grid; gap:8px; }
        label { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px; color:#5c6476; }
        input, textarea { width:100%; border:1px solid var(--border); border-radius:12px; padding:12px 14px; font:inherit; }
        textarea { min-height:120px; resize:vertical; }
        .color-row { display:flex; gap:10px; align-items:center; }
        .color-row input[type="color"] { width:56px; padding:4px; height:44px; }
        .footer-actions { display:flex; justify-content:flex-end; }
        .editor-label {
            font-size: 18px;
            font-weight: 800;
            text-transform: none;
            letter-spacing: 0;
            color: var(--dark);
        }
        .homepage-editor {
            min-height: 440px;
            resize: vertical;
        }
        .tox-tinymce {
            border: 1px solid var(--border) !important;
            border-radius: 18px !important;
            overflow: hidden !important;
            box-shadow: 0 18px 40px rgba(17,42,72,0.05);
        }
        .tox .tox-editor-header {
            box-shadow: none !important;
            border-bottom: 1px solid var(--border) !important;
        }
        .tox .tox-menubar,
        .tox .tox-toolbar-overlord {
            background: #fff !important;
        }
        .tox .tox-mbtn,
        .tox .tox-tbtn,
        .tox .tox-statusbar__path-item,
        .tox .tox-statusbar__wordcount {
            font-family: 'Manrope', system-ui, -apple-system, sans-serif !important;
        }
        .tox .tox-edit-area__iframe {
            background: #fff !important;
        }
        .tox .tox-statusbar {
            border-top: 1px solid var(--border) !important;
        }
        @media (max-width: 1100px) {
            .layout { grid-template-columns:1fr; }
            .topbar, .panel-head, .grid-3, .grid-2 { grid-template-columns:1fr; display:grid; }
        }
    @include('admin.partials.sidebar-styles')
    </style>
</head>
<body>
@php
    $homepage = $homepageContent ?? \defaultHomepageContent();
    $hero = $homepage['hero'] ?? [];
    $badges = collect($homepage['badges'] ?? [])->pad(3, ['value' => '', 'label' => '', 'color' => '#1d7bff'])->values();
    $cards = collect($homepage['cards'] ?? [])->pad(4, ['title' => '', 'detail' => ''])->values();
@endphp
<div class="layout">
    @include('admin.partials.sidebar', ['menuCounts' => $navCounts ?? []])

    <main class="content">
        <div class="topbar">
            <div>
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Homepage Content</div>
                <div style="font-size:28px; font-weight:900;">Homepage Content Editor</div>
            </div>
            <button form="homepageForm" type="submit" class="btn btn-primary">Save Homepage</button>
        </div>

        <div class="hint">Update hero copy, trust badges, highlight cards, and the long-form homepage content shown on the public site.</div>

        @if(session('homepage_saved'))
            <div class="ok">{{ session('homepage_saved') }}</div>
        @endif
        @if($errors->any())
            <div class="err">{{ $errors->first() }}</div>
        @endif

        <form id="homepageForm" action="{{ route('admin.homepage.update') }}" method="POST" style="display:grid; gap:16px;">
            @csrf

            <section class="panel">
                <div class="panel-head">
                    <h2>Hero Section</h2>
                    <div class="panel-note">Main copy shown above the fold.</div>
                </div>
                <div class="grid-3">
                    <div class="field">
                        <label for="hero_eyebrow">Eyebrow</label>
                        <input id="hero_eyebrow" name="hero_eyebrow" value="{{ old('hero_eyebrow', $hero['eyebrow'] ?? '') }}" required>
                    </div>
                    <div class="field">
                        <label for="hero_cta_pill">CTA Pill</label>
                        <input id="hero_cta_pill" name="hero_cta_pill" value="{{ old('hero_cta_pill', $hero['cta_pill'] ?? '') }}" required>
                    </div>
                    <div class="field">
                        <label for="hero_title_prefix">Hero Title Prefix</label>
                        <input id="hero_title_prefix" name="hero_title_prefix" value="{{ old('hero_title_prefix', $hero['title_prefix'] ?? '') }}" required>
                    </div>
                </div>
                <div class="grid-2" style="margin-top:14px;">
                    <div class="field">
                        <label for="hero_title_highlight">Hero Title Highlight</label>
                        <input id="hero_title_highlight" name="hero_title_highlight" value="{{ old('hero_title_highlight', $hero['title_highlight'] ?? '') }}" required>
                    </div>
                    <div class="field">
                        <label for="hero_title_suffix">Hero Title Suffix</label>
                        <input id="hero_title_suffix" name="hero_title_suffix" value="{{ old('hero_title_suffix', $hero['title_suffix'] ?? '') }}" required>
                    </div>
                </div>
                <div class="field" style="margin-top:14px;">
                    <label for="hero_description">Hero Description</label>
                    <textarea id="hero_description" name="hero_description" required>{{ old('hero_description', $hero['description'] ?? '') }}</textarea>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Trust Badges</h2>
                    <div class="panel-note">Three badges shown under the hero call to action.</div>
                </div>
                <div class="grid-3">
                    @foreach($badges as $index => $badge)
                        <div class="field">
                            <label>Badge {{ $index + 1 }}</label>
                            <input name="badge_value_{{ $index + 1 }}" value="{{ old('badge_value_'.($index + 1), $badge['value'] ?? '') }}" placeholder="4.9*" required>
                            <input name="badge_label_{{ $index + 1 }}" value="{{ old('badge_label_'.($index + 1), $badge['label'] ?? '') }}" placeholder="Trustpilot" required>
                            <div class="color-row">
                                <input type="color" name="badge_color_{{ $index + 1 }}" value="{{ old('badge_color_'.($index + 1), $badge['color'] ?? '#1d7bff') }}" required>
                                <input value="{{ old('badge_color_'.($index + 1), $badge['color'] ?? '#1d7bff') }}" disabled>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Highlight Cards</h2>
                    <div class="panel-note">Floating cards displayed on the right side of the hero.</div>
                </div>
                <div class="grid-2">
                    @foreach($cards as $index => $card)
                        <div class="field">
                            <label for="card_title_{{ $index + 1 }}">Card {{ $index + 1 }} Title</label>
                            <input id="card_title_{{ $index + 1 }}" name="card_title_{{ $index + 1 }}" value="{{ old('card_title_'.($index + 1), $card['title'] ?? '') }}" required>
                            <label for="card_detail_{{ $index + 1 }}">Card {{ $index + 1 }} Detail</label>
                            <input id="card_detail_{{ $index + 1 }}" name="card_detail_{{ $index + 1 }}" value="{{ old('card_detail_'.($index + 1), $card['detail'] ?? '') }}" required>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h2>Home Page Content (SEO)</h2>
                    <div class="panel-note">Page description editor with menus, toolbar actions, code view, media tools, and fullscreen.</div>
                </div>
                <div class="field">
                    <label class="editor-label" for="seo_html">Page Description:</label>
                    <textarea id="seo_html" name="seo_html" class="homepage-editor">{{ old('seo_html', $homepage['seo_html'] ?? '') }}</textarea>
                </div>
            </section>

            <div class="footer-actions">
                <button type="submit" class="btn btn-primary">Save Homepage</button>
            </div>
        </form>
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editorField = document.getElementById('seo_html');
        const homepageForm = document.getElementById('homepageForm');

        if (!editorField || typeof tinymce === 'undefined') {
            return;
        }

        tinymce.init({
            selector: '#seo_html',
            license_key: 'gpl',
            menubar: 'file edit view insert format tools table',
            plugins: 'advlist lists link image media table code fullscreen autoresize',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link image media | code fullscreen',
            toolbar_mode: 'sliding',
            min_height: 440,
            autoresize_bottom_margin: 24,
            branding: false,
            promotion: false,
            statusbar: false,
            elementpath: false,
            resize: true,
            browser_spellcheck: true,
            convert_urls: false,
            relative_urls: false,
            block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Blockquote=blockquote',
            content_style: "body { font-family: Manrope, system-ui, -apple-system, sans-serif; font-size: 18px; line-height: 1.75; padding: 18px; } p { margin: 0 0 18px; } h2, h3, h4 { margin: 0 0 14px; line-height: 1.2; } img, iframe, video { max-width: 100%; height: auto; border-radius: 12px; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #e5e8ed; padding: 10px 12px; } blockquote { border-left: 4px solid #f25c3c; padding-left: 16px; margin-left: 0; color: #5c6476; }",
            setup: (editor) => {
                editor.on('change input undo redo', () => {
                    editor.save();
                });
            }
        });

        homepageForm?.addEventListener('submit', () => {
            tinymce.triggerSave();
        });
    });
</script>
</body>
</html>
