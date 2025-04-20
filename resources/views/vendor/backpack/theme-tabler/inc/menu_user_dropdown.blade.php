<div class="nav-item dropdown" style="margin-right: 23px;">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0 position-relative" data-bs-toggle="dropdown" aria-label="Open notification menu">
        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" style=" border-radius: 5px;">
            <path d="M21.4884 15.9984C21.4073 15.9047 21.3277 15.8109 21.2496 15.7204C20.1754 14.4731 19.5255 13.7203 19.5255 10.1892C19.5255 8.36107 19.0699 6.86107 18.172 5.73607C17.5099 4.90498 16.6148 4.27451 15.4351 3.80857C15.42 3.80047 15.4064 3.78983 15.3951 3.77717C14.9708 2.4131 13.8097 1.49951 12.5001 1.49951C11.1905 1.49951 10.0299 2.41311 9.60556 3.77576C9.59424 3.78796 9.58087 3.79826 9.56601 3.80623C6.81308 4.8942 5.47519 6.98154 5.47519 10.1878C5.47519 13.7203 4.82627 14.4731 3.75107 15.719C3.67295 15.8095 3.59336 15.9014 3.5123 15.997C3.30293 16.2394 3.17027 16.5343 3.13003 16.8468C3.08979 17.1594 3.14366 17.4764 3.28525 17.7604C3.58652 18.3698 4.22861 18.7481 4.96152 18.7481H20.044C20.7735 18.7481 21.4112 18.3703 21.7135 17.7637C21.8557 17.4796 21.91 17.1623 21.8701 16.8494C21.8303 16.5366 21.6978 16.2412 21.4884 15.9984V15.9984Z" fill="#AAAAAA"/>
            <path d="M12.5 22.5C13.2055 22.4995 13.8978 22.3156 14.5034 21.9679C15.1089 21.6202 15.6051 21.1217 15.9394 20.5252C15.9552 20.4966 15.9629 20.4646 15.962 20.4322C15.961 20.3999 15.9514 20.3684 15.934 20.3407C15.9166 20.313 15.892 20.2901 15.8627 20.2742C15.8334 20.2583 15.8002 20.25 15.7666 20.25H9.23433C9.2006 20.2499 9.16742 20.2582 9.13801 20.274C9.1086 20.2899 9.08397 20.3128 9.06652 20.3405C9.04906 20.3682 9.03938 20.3997 9.03841 20.4321C9.03744 20.4645 9.04522 20.4965 9.06099 20.5252C9.39523 21.1216 9.89138 21.6201 10.4968 21.9678C11.1023 22.3155 11.7945 22.4994 12.5 22.5Z" fill="#AAAAAA"/>
        </svg>
        <span class="notification-badge" style="position: absolute; top: 2px; right: 3px; width: 8px; height: 8px; background-color: #FF0000; border-radius: 50%; border: 1px solid #FFFFFF; color: #292D32; font-size: 6px; display: flex; justify-content: center; align-items: center;">3</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <a href="{{ route('notifications.index') }}" class="dropdown-item">Notification 1</a>
        <a href="{{ route('notifications.index') }}" class="dropdown-item">Notification 2</a>
        <a href="{{ route('notifications.index') }}" class="dropdown-item">Notification 3</a>
    </div>
</div>



<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
        <span class="avatar avatar-sm rounded-circle">
            <!-- Updated Avatar to use the uploaded profile image -->
            @if(backpack_user()->profile_image)
                <img class="avatar avatar-sm rounded-circle bg-transparent" src="{{ asset('storage/' . backpack_user()->profile_image) }}"
                    alt="{{ backpack_user()->name }}" onerror="this.style.display='none'"
                    style="margin: 0;position: absolute;left: 0;z-index: 1;">
            @else
                <!-- Default Avatar if no profile image is set -->
                <span class="avatar avatar-sm rounded-circle backpack-avatar-menu-container text-center">
                    {{ backpack_user()->getAttribute('name') ? mb_substr(backpack_user()->name, 0, 1, 'UTF-8') : 'A' }}
                </span>
            @endif
        </span>
        <div class="d-none d-xl-block ps-2">
            <div class="mt-1 medium text-muted">{{ trans('backpack::crud.admin') }}</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        @if(config('backpack.base.setup_my_account_routes'))
            <a href="{{ route('backpack.account.info') }}" class="dropdown-item"><i class="la la-user me-2"></i>{{ trans('backpack::base.my_account') }}</a>
            <div class="dropdown-divider"></div>
        @endif
        <a href="{{ backpack_url('logout') }}" class="dropdown-item"><i class="la la-lock me-2"></i>{{ trans('backpack::base.logout') }}</a>
    </div>
</div>
