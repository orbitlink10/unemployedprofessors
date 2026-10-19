@php
    $menuCounts = is_array($menuCounts ?? null) ? $menuCounts : [];
    $menuSections = [
        [
            'title' => 'Main',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'patterns' => ['admin.dashboard']],
                ['label' => 'Add Order', 'route' => 'order.create', 'patterns' => ['order.create']],
                [
                    'label' => 'Assigned',
                    'route' => 'admin.orders',
                    'route_params' => ['status' => 'assigned'],
                    'patterns' => ['admin.orders'],
                    'active_query' => ['status' => 'assigned'],
                    'count_key' => 'assigned',
                ],
                [
                    'label' => 'Completed',
                    'route' => 'admin.orders',
                    'route_params' => ['status' => 'completed'],
                    'patterns' => ['admin.orders'],
                    'active_query' => ['status' => 'completed'],
                    'count_key' => 'completed',
                ],
                [
                    'label' => 'Revision',
                    'route' => 'admin.orders',
                    'route_params' => ['status' => 'revision'],
                    'patterns' => ['admin.orders'],
                    'active_query' => ['status' => 'revision'],
                    'count_key' => 'revision',
                ],
                [
                    'label' => 'Approved',
                    'route' => 'admin.orders',
                    'route_params' => ['status' => 'approved'],
                    'patterns' => ['admin.orders'],
                    'active_query' => ['status' => 'approved'],
                    'count_key' => 'approved',
                ],
                ['label' => 'Payment Requested', 'route' => 'admin.payments', 'patterns' => ['admin.payments'], 'count_key' => 'payment_requests'],
                ['label' => 'Courses', 'route' => 'admin.courses', 'patterns' => ['admin.courses'], 'count_key' => 'courses'],
            ],
        ],
        [
            'title' => 'Manage Users',
            'items' => [
                ['label' => 'Clients', 'route' => 'admin.clients', 'patterns' => ['admin.clients'], 'count_key' => 'clients'],
                ['label' => 'Writers', 'route' => 'admin.writers', 'patterns' => ['admin.writers'], 'count_key' => 'writers'],
            ],
        ],
        [
            'title' => 'Content',
            'items' => [
                ['label' => 'Settings', 'route' => 'admin.settings', 'patterns' => ['admin.settings']],
                ['label' => 'Homepage Content', 'route' => 'admin.homepage', 'patterns' => ['admin.homepage']],
                ['label' => 'Pages', 'route' => 'admin.pages', 'patterns' => ['admin.pages']],
            ],
        ],
        [
            'title' => 'Account',
            'items' => [
                ['label' => 'Logout', 'route' => 'admin.logout', 'patterns' => ['admin.logout']],
            ],
        ],
    ];
@endphp
<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="icon">AD</span>
        <div class="label">
            <span class="eyebrow">Control Panel</span>
            <span class="title">Admin</span>
        </div>
    </div>

    <div class="sidebar-nav">
        @foreach($menuSections as $section)
            <section class="nav-group">
                <div class="nav-title">{{ $section['title'] }}</div>
                <div class="nav-links">
                    @foreach($section['items'] as $item)
                        @php
                            $patterns = $item['patterns'] ?? [$item['route']];
                            $routeParameters = $item['route_params'] ?? [];
                            $activeQuery = is_array($item['active_query'] ?? null) ? $item['active_query'] : [];
                            $isActive = false;
                            foreach ($patterns as $pattern) {
                                if (request()->routeIs($pattern)) {
                                    $isActive = true;
                                    break;
                                }
                            }

                            if ($isActive && $activeQuery !== []) {
                                foreach ($activeQuery as $queryKey => $expectedValue) {
                                    if ((string) request()->query($queryKey, '') !== (string) $expectedValue) {
                                        $isActive = false;
                                        break;
                                    }
                                }
                            }

                            $countKey = $item['count_key'] ?? null;
                            $count = $countKey !== null && array_key_exists($countKey, $menuCounts)
                                ? (int) $menuCounts[$countKey]
                                : null;
                        @endphp
                        <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ route($item['route'], $routeParameters) }}" @if($isActive) aria-current="page" @endif>
                            <span>{{ $item['label'] }}</span>
                            @if($count !== null)
                                <span class="nav-count">{{ $count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</aside>
