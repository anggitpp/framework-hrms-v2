<div id="kt_app_header" class="app-header">
    @if(!Str::contains(url()->current(), 'dashboard'))
        <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
            <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show sidebar menu">
                <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <span class="svg-icon svg-icon-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor" />
                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor" />
                    </svg>
                </span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                <a class="d-lg-none">
                    <img alt="Logo" src="assets/media/logos/logo-2.png" class="h-40px" />
                </a>
            </div>
        @else
            <div class="app-container container-xxl d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-0">
                    <div>
                        <img alt="Logo" src="assets/media/logos/sidebar-logo.png" class="h-60px app-header-logo-default" />
                    </div>
                </div>
        @endif
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
                    @php $role = Auth::user()->role_id; @endphp
                    @foreach($app_moduls as $key => $modul)
                        @php
                            $firstMenu = DB::table('app_menus as t1')
                            ->select('t3.name')
                            ->join('app_sub_moduls as t2', 't1.app_sub_modul_id', 't2.id')
                            ->join('app_permissions as t3', 't1.id', 't3.type_id')
                            ->join('app_role_has_permissions as t4', 't3.id', 't4.permission_id')
                            ->where('t1.status', 't')
                            ->where('t1.app_modul_id', $modul->id)
                            ->where('t3.method', 'view')
                            ->where('t3.type', 'menu')
                            ->where('t4.role_id', $role)
                            ->orderBy('t2.order')
                            ->orderBy('t1.order')
                            ->first();
                        @endphp
                        @if($firstMenu)
                            @can($modul->target)
                                @php
                                    $route = str_replace('view ', '', str_replace('/', '.', $firstMenu->name)).'.index';
                                    $routeFirstMenu = Route::has($route) ? route($route) : '#';
                                @endphp
                                <a
                                    href="{{ $routeFirstMenu }}"
                                    class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2 {{ $modul->target == $selected_modul->target ? 'show here' : '' }}">
                                    <span class="menu-link">
                                        <span class="menu-title">{{ $modul->name }}</span>
                                        <span class="menu-arrow d-lg-none"></span>
                                    </span>
                                </a>
                            @endcan
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="app-navbar flex-shrink-0">
                <div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img src="{{ Auth::user()->photo ? asset('storage'.Auth::user()->photo) : asset('assets/media/blank-image.svg') }}" alt="user" />
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="{{ Auth::user()->photo ? asset('storage'.Auth::user()->photo) : asset('assets/media/blank-image.svg') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::user()->name }}
                                    </div>
                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="menu-item px-5">
                            @php
                                $currentRoute = Route::currentRouteName();
                            @endphp
                            <a href="#" class="menu-link px-5 btn-modal" data-bs-toggle="modal" data-url="{{ route('app.edit-profile', $currentRoute) }}">Edit Profile</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5 btn-modal" data-bs-toggle="modal" data-url="{{ route('app.edit-password', $currentRoute) }}">Ubah Password</a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-link px-5">Sign Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
                    <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_header_menu_toggle">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z" fill="currentColor" />
                                <path opacity="0.3" d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z" fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form/>
