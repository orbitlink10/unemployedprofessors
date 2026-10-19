<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place an Order | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #0f5951;
            --green-soft: #0b4841;
            --dark: #1c1c1c;
            --muted: #565e64;
            --border: #dce3e6;
            --bg: #f8fafb;
            --card: #ffffff;
            --yellow: #ffb300;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            font-size: 14px;
        }
        header {
            padding: 18px 22px 8px;
        }
        h1 { margin: 0; font-size: 26px; letter-spacing: -0.2px; }
        .lead { margin: 4px 0 0 0; color: var(--muted); font-weight: 600; font-size: 13px; }
        .layout {
            max-width: 1200px;
            margin: 0 auto 28px;
            padding: 0 14px 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 14px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px;
            box-shadow: 0 12px 28px rgba(17, 42, 72, 0.07);
        }
        .form-grid { display: grid; gap: 12px; }
        label { font-weight: 800; margin-bottom: 6px; display: block; color: #2c2f33; font-size: 13px; }
        select, input, textarea {
            width: 100%;
            padding: 10px 10px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: #fdfefe;
            font-weight: 600;
            font-size: 14px;
            outline: none;
            transition: border .12s ease, box-shadow .12s ease;
        }
        select:focus, input:focus, textarea:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(15, 89, 81, 0.12);
        }
        textarea { min-height: 110px; resize: vertical; }
        .choices {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 8px;
        }
        .pill {
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 9px 10px;
            text-align: center;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
            background: #f5f7f8;
            transition: all .12s ease;
        }
        .pill.active {
            background: var(--green);
            color: #fff;
            border-color: var(--green);
        }
        .flex-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px; align-items: center; }
        .quantity { display: grid; grid-template-columns: 48px 1fr 48px; align-items: center; }
        .quantity button {
            height: 36px;
            border: 1px solid var(--border);
            background: #fff;
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
        }
        .quantity input { text-align: center; font-weight: 800; }
        .btn {
            border: none;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 900;
            font-size: 14px;
            cursor: pointer;
            transition: transform .1s ease, box-shadow .15s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-green {
            background: var(--green);
            color: #fff;
            box-shadow: 0 10px 22px rgba(15, 89, 81, 0.25);
            width: 100%;
        }
        .coupon {
            background: #ffa400;
            border-radius: 10px;
            padding: 10px;
            display: grid;
            grid-template-columns: 1fr 120px;
            gap: 8px;
            align-items: center;
        }
        .coupon input {
            background: #fff6df;
            border: 1px solid #f7d26b;
        }
        .coupon button {
            background: #fff;
            color: #d17a00;
            border: none;
            height: 36px;
            border-radius: 9px;
            font-weight: 900;
            cursor: pointer;
        }
        .sidebar {
            position: sticky;
            top: 14px;
            display: grid;
            gap: 8px;
        }
        .summary-row { display: flex; justify-content: space-between; align-items: center; margin: 4px 0; }
        .summary-label { color: var(--muted); font-weight: 700; font-size: 13px; }
        .summary-value { font-weight: 800; color: var(--dark); font-size: 13px; }
        .total {
            font-size: 18px;
            font-weight: 900;
            color: var(--green);
        }
        .cards { display: flex; gap: 8px; flex-wrap: wrap; }
        .cards img { height: 22px; }
        .section-title { font-weight: 900; margin: 6px 0 2px; font-size: 14px; }
        .helper { color: var(--muted); font-weight: 600; font-size: 12px; margin-top: 4px; }
        .addon-list {
            border: 1px dashed var(--border);
            border-radius: 10px;
            overflow: hidden;
        }
        .addon-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            cursor: pointer;
            transition: background .12s ease;
        }
        .addon-item + .addon-item {
            border-top: 1px solid var(--border);
        }
        .addon-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--green);
            cursor: pointer;
            flex: 0 0 auto;
        }
        .addon-item.active {
            background: #ecf3f1;
        }
        @media (max-width: 950px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { position: static; }
        }
    </style>
</head>
<body>
    <header>
        <h1>Place an Order</h1>
        <p class="lead">It’s fast, secure, and confidential.</p>
    </header>

    <div class="layout">
        <div class="card">
            <form class="form-grid" action="{{ route('order.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div>
                    <label>Type of Paper</label>
                    <select name="type">
                        <option>Essay (Any)</option>
                        <option>Research Paper</option>
                        <option>Business Plan</option>
                        <option>Case Study</option>
                        <option>Presentation</option>
                    </select>
                </div>

                <div>
                    <label>Academic Level</label>
                    <input type="hidden" name="level" id="levelInput" value="High School">
                    <div class="choices" data-group="level">
                        <div class="pill active">High School</div>
                        <div class="pill">College</div>
                        <div class="pill">Masters</div>
                        <div class="pill">PhD</div>
                    </div>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Subject</label>
                        <select name="subject">
                            <option>Other</option>
                            <option>Business</option>
                            <option>Nursing</option>
                            <option>Technology</option>
                            <option>Literature</option>
                        </select>
                    </div>
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Enter paper title">
                    </div>
                </div>

                <div>
                    <label>Instructions</label>
                    <textarea name="instructions" placeholder="Please paste all your paper instructions here"></textarea>
                </div>

                <div>
                    <label>Upload Files (Optional)</label>
                    @include('partials.multi-file-upload')
                    <div class="helper">Attach rubrics, references, or any supporting documents (up to 5MB each).</div>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Paper format</label>
                        <input type="hidden" name="format" id="formatInput" value="APA">
                        <div class="choices" data-group="format">
                            <div class="pill">MLA</div>
                            <div class="pill active">APA</div>
                            <div class="pill">Harvard</div>
                            <div class="pill">Chicago</div>
                            <div class="pill">Other</div>
                        </div>
                    </div>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Number of Pages</label>
                        <div class="quantity">
                            <button type="button" onclick="step('pages', -1)">−</button>
                            <input id="pages" name="pages" type="number" value="1" min="1">
                            <button type="button" onclick="step('pages', 1)">+</button>
                        </div>
                        <div class="helper">Approx 275 words</div>
                    </div>
                    <div>
                        <label>Spacing</label>
                        <input type="hidden" name="spacing" id="spacingInput" value="Double">
                        <div class="choices" data-group="spacing">
                            <div class="pill active">Double</div>
                            <div class="pill">Single</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label>Currency</label>
                    <input type="hidden" name="currency" id="currencyInput" value="USD">
                    <div class="choices" data-group="currency">
                        <div class="pill active">USD</div>
                        <div class="pill">GBP</div>
                        <div class="pill">EUR</div>
                        <div class="pill">AUD</div>
                    </div>
                </div>

                <div class="coupon">
                    <input type="text" placeholder="Have coupon? Enter here">
                    <button type="button">APPLY</button>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Number of Sources</label>
                        <div class="quantity">
                            <button type="button" onclick="step('sources', -1)">−</button>
                            <input id="sources" name="sources" type="number" value="0" min="0">
                            <button type="button" onclick="step('sources', 1)">+</button>
                        </div>
                    </div>
                    <div>
                        <label>PowerPoint Slides</label>
                        <div class="quantity">
                            <button type="button" onclick="step('slides', -1)">−</button>
                            <input id="slides" name="slides" type="number" value="0" min="0">
                            <button type="button" onclick="step('slides', 1)">+</button>
                        </div>
                    </div>
                    <div>
                        <label>Charts</label>
                        <div class="quantity">
                            <button type="button" onclick="step('charts', -1)">−</button>
                            <input id="charts" name="charts" type="number" value="0" min="0">
                            <button type="button" onclick="step('charts', 1)">+</button>
                        </div>
                    </div>
                </div>

                <div>
                    <label>Deadline</label>
                    <input type="hidden" name="deadline" id="deadlineInput" value="48 Hours">
                    <div class="choices" data-group="deadline">
                        <div class="pill">8 Hours</div>
                        <div class="pill">24 Hours</div>
                        <div class="pill active">48 Hours</div>
                        <div class="pill">3 Days</div>
                        <div class="pill">5 Days</div>
                        <div class="pill">7 Days</div>
                        <div class="pill">14 Days</div>
                    </div>
                </div>

                <div>
                    <label>Category</label>
                    <input type="hidden" name="category" id="categoryInput" value="Standard">
                    <div class="choices" data-group="category">
                        <div class="pill active">Standard</div>
                        <div class="pill">Premium</div>
                        <div class="pill">Platinum</div>
                    </div>
                </div>

                <div>
                    <label>Additional Services</label>
                    <div class="addon-list">
                        <label class="addon-item" id="vipOption">
                            <input type="checkbox" id="vip" name="vip_support" value="1">
                            <div>
                                <div class="section-title">VIP support <span style="color:var(--green);">$25</span></div>
                                <div class="helper">24/7 VIP manager and priority communication.</div>
                            </div>
                        </label>
                        <label class="addon-item" id="draftOption">
                            <input type="checkbox" id="draft" name="draft_outline" value="1">
                            <div>
                                <div class="section-title">Draft/outline <span style="color:var(--green);">$20</span></div>
                                <div class="helper">Receive a draft outlining structure before final paper.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-green">Submit Order</button>
            </form>
        </div>

        <aside class="sidebar">
            <div class="card">
                <div class="section-title">Summary</div>
                <div class="summary-row"><span class="summary-label">Level</span><span id="summaryLevel" class="summary-value">High School</span></div>
                <div class="summary-row"><span class="summary-label">Type</span><span id="summaryType" class="summary-value">Essay (Any)</span></div>
                <div class="summary-row"><span class="summary-label">Deadline</span><span id="summaryDeadline" class="summary-value">48 Hours</span></div>
                <div class="summary-row"><span class="summary-label">Category</span><span id="summaryCategory" class="summary-value">Standard</span></div>
                <div class="summary-row"><span class="summary-label">Pages</span><span id="summaryPages" class="summary-value">1 page x $19.60</span></div>
                <div class="summary-row"><span class="summary-label">Extras</span><span id="summaryExtras" class="summary-value">$0.00</span></div>
                <hr style="border:none; border-top:1px solid var(--border); margin:12px 0;">
                <div class="summary-row"><span class="section-title">Total Price</span><span id="summaryTotal" class="total">$19.60</span></div>
                <div class="cards">
                    <img src="{{ asset('images/cards/visa.svg') }}" alt="Visa">
                    <img src="{{ asset('images/cards/mastercard.svg') }}" alt="Mastercard">
                    <img src="{{ asset('images/cards/amex.svg') }}" alt="Amex">
                    <img src="{{ asset('images/cards/discover.svg') }}" alt="Discover">
                </div>
            </div>
        </aside>
    </div>

    <script>
        const pricingMatrix = @json($pricing ?? []);
        const defaultLevel = 'High School';
        const defaultDeadline = '48 Hours';
        const fallbackPrice = Number(
            (pricingMatrix.College && pricingMatrix.College['48 Hours']) ?? 21.6
        );

        const levelInput = document.getElementById('levelInput');
        const deadlineInput = document.getElementById('deadlineInput');
        const categoryInput = document.getElementById('categoryInput');
        const pagesInput = document.getElementById('pages');
        const typeSelect = document.querySelector('select[name="type"]');
        const vipCheckbox = document.getElementById('vip');
        const draftCheckbox = document.getElementById('draft');
        const categoryMultipliers = {
            Standard: 1,
            Premium: 1.15,
            Platinum: 1.3,
        };
        const extraPrices = {
            vip: 25,
            draft: 20,
        };

        const summaryLevel = document.getElementById('summaryLevel');
        const summaryType = document.getElementById('summaryType');
        const summaryDeadline = document.getElementById('summaryDeadline');
        const summaryCategory = document.getElementById('summaryCategory');
        const summaryPages = document.getElementById('summaryPages');
        const summaryExtras = document.getElementById('summaryExtras');
        const summaryTotal = document.getElementById('summaryTotal');

        function toMoney(value) {
            return '$' + Number(value).toFixed(2);
        }

        function resolvePrice(level, deadline) {
            if (pricingMatrix[level] && pricingMatrix[level][deadline] != null) {
                return Number(pricingMatrix[level][deadline]);
            }

            const matchedLevel = Object.keys(pricingMatrix).find(
                key => key.toLowerCase() === String(level).toLowerCase()
            );
            if (matchedLevel) {
                const row = pricingMatrix[matchedLevel];
                const matchedDeadline = Object.keys(row).find(
                    key => key.toLowerCase() === String(deadline).toLowerCase()
                );
                if (matchedDeadline) {
                    return Number(row[matchedDeadline]);
                }
            }

            return fallbackPrice;
        }

        function resolveCategoryMultiplier(category) {
            const matchedCategory = Object.keys(categoryMultipliers).find(
                key => key.toLowerCase() === String(category).toLowerCase()
            );

            return matchedCategory ? Number(categoryMultipliers[matchedCategory]) : 1;
        }

        function syncAddonStates() {
            document.getElementById('vipOption')?.classList.toggle('active', Boolean(vipCheckbox?.checked));
            document.getElementById('draftOption')?.classList.toggle('active', Boolean(draftCheckbox?.checked));
        }

        function updateSummary() {
            const level = (levelInput?.value || defaultLevel).trim();
            const deadline = (deadlineInput?.value || defaultDeadline).trim();
            const category = (categoryInput?.value || 'Standard').trim();
            const type = (typeSelect?.value || 'Essay (Any)').trim();
            const pages = Math.max(parseInt(pagesInput?.value || 1, 10) || 1, 1);
            const pricePerPage = resolvePrice(level, deadline);
            const categoryMultiplier = resolveCategoryMultiplier(category);
            const categoryTotal = pages * pricePerPage * categoryMultiplier;
            const extrasTotal = (vipCheckbox?.checked ? extraPrices.vip : 0) + (draftCheckbox?.checked ? extraPrices.draft : 0);
            const total = categoryTotal + extrasTotal;
            const pageLabel = pages === 1 ? 'page' : 'pages';

            if (pagesInput) {
                pagesInput.value = pages;
            }
            if (summaryLevel) {
                summaryLevel.textContent = level;
            }
            if (summaryType) {
                summaryType.textContent = type;
            }
            if (summaryDeadline) {
                summaryDeadline.textContent = deadline;
            }
            if (summaryCategory) {
                summaryCategory.textContent = category;
            }
            if (summaryPages) {
                const multiplierLabel = categoryMultiplier > 1 ? ' (' + category + ' x' + categoryMultiplier.toFixed(2) + ')' : '';
                summaryPages.textContent = pages + ' ' + pageLabel + ' x ' + toMoney(pricePerPage) + multiplierLabel;
            }
            if (summaryExtras) {
                summaryExtras.textContent = toMoney(extrasTotal);
            }
            if (summaryTotal) {
                summaryTotal.textContent = toMoney(total);
            }
        }

        function step(id, delta) {
            const input = document.getElementById(id);
            const next = Math.max(parseInt(input.value || 0) + delta, parseInt(input.min || 0));
            input.value = next;
            updateSummary();
        }
        // pill toggles within each data-group container
        document.querySelectorAll('[data-group]').forEach(group => {
            group.addEventListener('click', e => {
                const pill = e.target.closest('.pill');
                if (!pill) return;
                [...group.children].forEach(el => el.classList.toggle('active', el === pill));
                const hidden = document.getElementById(group.dataset.group + 'Input');
                if (hidden) hidden.value = pill.textContent.trim();
                updateSummary();
            });
        });
        // set initial hidden values based on default actives
        document.querySelectorAll('[data-group]').forEach(group => {
            const active = group.querySelector('.pill.active') || group.querySelector('.pill');
            if (active) {
                const hidden = document.getElementById(group.dataset.group + 'Input');
                if (hidden) hidden.value = active.textContent.trim();
            }
        });

        typeSelect?.addEventListener('change', updateSummary);
        pagesInput?.addEventListener('input', updateSummary);
        pagesInput?.addEventListener('change', updateSummary);
        vipCheckbox?.addEventListener('change', () => {
            syncAddonStates();
            updateSummary();
        });
        draftCheckbox?.addEventListener('change', () => {
            syncAddonStates();
            updateSummary();
        });
        syncAddonStates();
        updateSummary();
    </script>
</body>
</html>
