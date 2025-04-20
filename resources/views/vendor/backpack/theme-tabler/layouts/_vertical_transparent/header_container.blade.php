<header class="d-print-none {{ backpack_theme_config('classes.topHeader') ?? 'd-none d-lg-block px-3 navbar navbar-expand-md topbar-transparent' }}" 
    style=" border-bottom: 1px solid #C5C5C5; padding-bottom: 6px !important; background-color: #FFFFFF !important; ">
    <div class="{{ backpack_theme_config('options.useFluidContainers') ? 'container-fluid' : 'container-xl' }} d-flex justify-content-end">
        <div class="navbar-nav">
            @include(backpack_view('inc.menu'))
        </div>
    </div>
</header>
