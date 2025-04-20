{{-- Custom sidebar layout with background, border, and fixed position --}}
<div class="main-sidebar d-flex flex-column justify-content-between" style="background-color: #FFFFFF; height: 100vh; width: 260px; position: fixed; top: 0; left: 0; z-index: 1030; border-right: 1px solid #C5C5C5; overflow-x: hidden;">
    {{-- Top Section: Project Logo from config --}}
    <div class="sidebar-header p-3 text-center" style="border-bottom: 1px solid #C5C5C5; padding-bottom: 13px !important;">
        {!! config('backpack.ui.project_logo') !!}
    </div>
    
    {{-- Middle Section: Menu Items without bullets --}}
    <div class="sidebar-menu flex-grow-1 px-2" style="overflow-y: auto; height: calc(100vh - 76px);">
        <style>
            .sidebar-menu ul, .sidebar-menu .nav, .sidebar-menu .nav-item {
                list-style: none !important;
                margin: 0;
                padding: 0;
            }
            .sidebar-menu .nav-item::marker, .sidebar-menu .nav-item > li::before {
                content: none !important;
            }
            .sidebar-menu .nav-link {
                padding-left: 0.75rem;
            }
           
            /* Media query for mobile responsiveness */
            @media (max-width: 768px) {
                body {
                    padding-left: 0;
                }
                .main-sidebar {
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                }
                .main-sidebar.show {
                    transform: translateX(0);
                }
            }
        </style>
        @include(backpack_view('inc.menu_items'))
    </div>
</div>