<aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-[calc(100vh-2rem)] bg-white rounded-xl m-5 border border-gray-100 overflow-y-auto shadow-sm transition-all duration-300">
    <div class="flex flex-col h-full">
        <!-- Top Section: Announcements + Dynamic Menu Items -->
        <div class="flex-1 px-4 py-4 space-y-8">
            <!-- Logo + Toggle Button -->
            <div class="flex items-center justify-between mt-4">
                <a href="/dashboard_new" class="flex items-center sidebar-logo">
                    <img src="{{ asset('logo/jez_pro.png') }}" class="h-6 w-auto transition-all duration-300" alt="JEZ PRO" />
                    <div class="w-9 h-9 bg-red-500 rounded-lg flex items-center justify-center text-white font-bold text-lg sidebar-logo-icon hidden">
                        J
                    </div>
                </a>
                <button id="sidebar-toggle" class="w-9 h-9 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left text-gray-500 text-sm sidebar-toggle-icon"></i>
                </button>
            </div>

            <!-- Announcements Button (Fixed - Always visible) -->
            <div class="mb-4 sidebar-fixed">
                <a href="{{ url('/announcements') }}" class="w-full flex items-center justify-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium text-sm transition-colors shadow-lg shadow-red-400/50 group">
                    <i class="cft-standard-stroke cft-mansory-grid text-white sidebar-icon-only hidden"></i>
                    <div class="flex items-center justify-between w-full sidebar-text-visible">
                        <span>Announcements</span>
                        <i class="cft-standard-stroke cft-mansory-grid text-white text-lg"></i>
                    </div>
                </a>
            </div>
            
            <!-- Dynamic Menu Items from Database -->
            <div id="sidebar-menu-container" class="space-y-2">
                <!-- Special Dashboard Section (Always visible when Dashboard tab clicked) -->
                <div class="menu-section" data-category="dashboard" data-original-title="Dashboard">
                    <!-- Menu Title (Section Header) -->
                    <div class="mb-2 sidebar-section-title">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Dashboard
                        </h3>
                    </div>
                    
                    <!-- Dashboard Menu Items -->
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/dashboard_new') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('dashboards*') || request()->is('dashboard_new*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Dashboard">
                            <i class="cft-standard-stroke cft-dashboard text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Dashboard</span>
                        </a>
                    </div>
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/dashboard_v2') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('dashboard_v2*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Dashboard V2">
                            <i class="cft-standard-stroke cft-chart-pie text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Dashboard V2</span>
                        </a>
                    </div>
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/asset_detail') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('asset_detail*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Asset Detail">
                            <i class="cft-standard-stroke cft-file text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Asset Detail</span>
                        </a>
                    </div>
                    <div class="sidebar-menu-item mb-1">
                        <a href="{{ url('/power_bi_dashboard') }}" 
                           class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('power_bi_dashboard*') ? 'bg-gray-100 font-semibold' : '' }}"
                           title="Power BI Dashboard">
                            <i class="cft-standard-stroke cft-chart-bar text-gray-400 flex-shrink-0 text-lg"></i>
                            <span class="sidebar-menu-text">Power BI Dashboard</span>
                        </a>
                    </div>
                </div>
                
                @if (!empty($data['sidebar']))
                    @foreach ($data['sidebar'] as $menuTitle)
                        @php
                            // Skip Dashboard menu title as it's already hardcoded above
                            $isDashboard = strtolower($menuTitle->mt_title) === 'dashboard';
                        @endphp
                        
                        @if (!$isDashboard)
                            <!-- Menu Section (with data-category for filtering) -->
                            <div class="menu-section" data-category="{{ strtolower(str_replace(' ', '-', $menuTitle->mt_title)) }}" data-original-title="{{ $menuTitle->mt_title }}">
                                <!-- Menu Title (Section Header) -->
                                <div class="mb-2 mt-4 sidebar-section-title">
                                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                        {{ $menuTitle->mt_title }}
                                    </h3>
                                </div>
                                
                                <!-- Menu Items -->
                                @if (!empty($menuTitle->ma))
                                    @foreach ($menuTitle->ma as $menuItem)
                                        <div class="sidebar-menu-item mb-1">
                                            <a href="{{ url('/') }}/{{ $menuItem->ma_slug }}" 
                                               class="sidebar-item flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is($menuItem->ma_slug . '*') ? 'bg-gray-100 font-semibold' : '' }}"
                                               title="{{ $menuItem->ma_title }}">
                                                <i class="cft-standard-stroke cft-{{ $menuItem->ma_slug == 'dashboard' ? 'home' : ($menuItem->ma_slug == 'pos_v2' ? 'wallet' : 'file') }} text-gray-400 flex-shrink-0"></i>
                                                <span class="sidebar-menu-text">{{ $menuItem->ma_title }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    @endforeach
                @else
                    <!-- Fallback: No menu data available -->
                    <div class="px-3 py-4 text-sm text-gray-500 text-center">
                        No menu items available
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Bottom Section: User, Konfigurasi, Logout (Fixed - Always visible) -->
        <div class="border-t border-gray-200 p-3 space-y-1 sidebar-fixed">
            <!-- User Profile -->
            <a href="{{ url('/user') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('user*') ? 'bg-gray-100 font-semibold' : '' }}" title="User">
                <i class="cft-standard-stroke cft-user text-gray-400 flex-shrink-0 text-lg"></i>
                <span class="sidebar-menu-text">User</span>
            </a>
            
            <!-- Konfigurasi -->
            <a href="{{ url('/konfigurasi') }}" class="sidebar-item flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors {{ request()->is('konfigurasi*') ? 'bg-gray-100 font-semibold' : '' }}" title="Konfigurasi">
                <i class="cft-standard-stroke cft-settings text-gray-400 flex-shrink-0 text-lg"></i>
                <span class="sidebar-menu-text">Konfigurasi</span>
            </a>
            
            <!-- Logout -->
            <a href="{{ route('logout') }}" class="flex items-center justify-center gap-3 px-4 py-2.5 mt-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg font-medium text-sm transition-colors" title="Logout">
                <i class="cft-standard-stroke cft-logout flex-shrink-0 text-lg"></i>
                <span class="sidebar-menu-text">Logout</span>
            </a>
        </div>
    </div>
</aside>

<script>
// Sidebar Toggle & Filter Logic
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const topbar = document.getElementById('topbar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mainContent = document.querySelector('main');
    const topbarTabs = document.querySelectorAll('.topbar-tab');
    const menuSections = document.querySelectorAll('.menu-section');
    
    // Load saved collapsed state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        toggleSidebar(true);
    }
    
    // Toggle Sidebar
    sidebarToggle.addEventListener('click', function() {
        const willCollapse = !sidebar.classList.contains('sidebar-collapsed');
        toggleSidebar(willCollapse);
        localStorage.setItem('sidebarCollapsed', willCollapse);
    });
    
    function toggleSidebar(collapse) {
        if (collapse) {
            // Collapse sidebar
            sidebar.classList.add('sidebar-collapsed');
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-24');
            
            // Adjust main content margin
            if (mainContent) {
                mainContent.classList.remove('ml-72');
                mainContent.classList.add('ml-32');
            }
            if (topbar) {
                topbar.classList.remove('ml-72-plus');
                topbar.classList.add('ml-32-plus');
            }

            // Hide all text elements
            document.querySelectorAll('.sidebar-menu-text').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.sidebar-section-title').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.sidebar-text-visible').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.sidebar-icon-only').forEach(el => el.classList.remove('hidden'));
            
            // Switch logo
            document.querySelector('.sidebar-logo img').classList.add('hidden');
            document.querySelector('.sidebar-logo-icon').classList.remove('hidden');
            
            // Rotate toggle icon
            document.querySelector('.sidebar-toggle-icon').classList.add('rotate-180');
            
            // Center menu items
            document.querySelectorAll('.sidebar-item').forEach(el => {
                el.classList.add('justify-center');
                el.classList.remove('gap-3');
            });
            
        } else {
            // Expand sidebar
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.remove('w-24');
            sidebar.classList.add('w-64');
            
            // Adjust main content margin
            if (mainContent) {
                mainContent.classList.remove('ml-32');
                mainContent.classList.add('ml-72');
            }
            if (topbar) {
                topbar.classList.remove('ml-32-plus');
                topbar.classList.add('ml-72-plus');
            }
            
            // Show all text elements
            document.querySelectorAll('.sidebar-menu-text').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.sidebar-section-title').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.sidebar-text-visible').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.sidebar-icon-only').forEach(el => el.classList.add('hidden'));
            
            // Switch logo
            document.querySelector('.sidebar-logo img').classList.remove('hidden');
            document.querySelector('.sidebar-logo-icon').classList.add('hidden');
            
            // Rotate toggle icon back
            document.querySelector('.sidebar-toggle-icon').classList.remove('rotate-180');
            
            // Reset menu items alignment
            document.querySelectorAll('.sidebar-item').forEach(el => {
                el.classList.remove('justify-center');
                el.classList.add('gap-3');
            });
        }
    }
    
    // Category mapping between top bar and sidebar mt_title
    const categoryMapping = {
        'all': [], // Show all
        'dashboard': ['dashboard'],
        'master-data': ['master-data', 'data-master', 'master', 'data'],
        'stock': ['stock', 'inventory', 'warehouse', 'gudang'],
        'sales': ['sales', 'point-of-sales', 'penjualan', 'pos', 'sale'],
        'human-resource': ['human-resource', 'hr', 'sdm', 'human', 'resource']
    };
    
    // Filter sidebar menu based on category
    function filterSidebarMenu(category) {
        console.log('Filtering sidebar for category:', category);
        
        if (!category || category === 'all') {
            // Show all menus
            menuSections.forEach(section => {
                section.style.display = 'block';
            });
            return;
        }
        
        const allowedCategories = categoryMapping[category] || [category];
        console.log('Allowed categories:', allowedCategories);
        
        menuSections.forEach(section => {
            const sectionCategory = section.getAttribute('data-category');
            const originalTitle = section.getAttribute('data-original-title').toLowerCase();
            
            console.log('Checking section:', originalTitle, '(', sectionCategory, ')');
            
            // Check if section matches any of the allowed categories
            const isMatch = allowedCategories.some(cat => {
                return sectionCategory.includes(cat) || originalTitle.includes(cat.replace('-', ' '));
            });
            
            section.style.display = isMatch ? 'block' : 'none';
            console.log('Section', originalTitle, 'match:', isMatch);
        });
    }
    
    // Add click event to top bar tabs
    topbarTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent navigation
            
            // Get category from data-category attribute
            const category = this.getAttribute('data-category') || 'all';
            
            // Filter sidebar
            filterSidebarMenu(category);
            
            // Save selected category to localStorage
            localStorage.setItem('selectedCategory', category);
            
            // Update active state
            topbarTabs.forEach(t => {
                t.classList.remove('bg-gray-900', 'text-white');
                t.classList.add('text-gray-700', 'hover:bg-gray-100');
            });
            this.classList.add('bg-gray-900', 'text-white');
            this.classList.remove('text-gray-700', 'hover:bg-gray-100');
        });
    });
    
    // Load saved category from localStorage or show all by default
    const savedCategory = localStorage.getItem('selectedCategory') || 'all';
    filterSidebarMenu(savedCategory);
    
    // Set active tab based on saved category
    topbarTabs.forEach(tab => {
        if (tab.getAttribute('data-category') === savedCategory) {
            tab.classList.add('bg-gray-900', 'text-white');
            tab.classList.remove('text-gray-700', 'hover:bg-gray-100');
        }
    });
});
</script>
