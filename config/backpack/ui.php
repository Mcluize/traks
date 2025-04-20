<?php

return [

    // IMPORTANT NOTE: The configurations here get overridden by theme config files.
    //
    // Eg. If you're using theme-tabler and config/backpack/theme-tabler.php
    // has "breadcrumbs" set as false, then THAT value will be used instead
    // of the value in this file.

    /*
    |--------------------------------------------------------------------------
    | Theme (User Interface)
    |--------------------------------------------------------------------------
    */
    // Change the view namespace in order to load a different theme than the one Backpack provides.
    // You can create child themes yourself, by creating a view folder anywhere in your resources/views
    // and choosing that view_namespace instead of the default one. Backpack will load a file from there
    // if it exists, otherwise it will load it from the fallback namespace.

    'view_namespace' => 'backpack.theme-tabler::',
    'view_namespace_fallback' => 'backpack.theme-tabler::',

    /*
    |--------------------------------------------------------------------------
    | Look & feel customizations
    |--------------------------------------------------------------------------
    |
    | To make the UI feel yours.
    |
    | Note that values set here might be overridden by theme config files
    | (eg. config/backpack/theme-tabler.php) when that theme is in use.
    |
    */

    // Date & Datetime Format Syntax: https://carbon.nesbot.com/docs/#api-localization
    'default_date_format' => 'D MMM YYYY',
    'default_datetime_format' => 'D MMM YYYY, HH:mm',

    // Direction, according to language
    // (left-to-right vs right-to-left)
    'html_direction' => 'ltr',

    // ----
    // HEAD
    // ----

    // Project name - shown in the window title
    'project_name' => 'Tourist Tracking System',

    // Content of the HTML meta robots tag to prevent indexing and link following
    'meta_robots_content' => 'noindex, nofollow',

    // ------
    // HEADER
    // ------

    // When clicking on the admin panel's top-left logo/name,
    // where should the user be redirected?
    // The string below will be passed through the url() helper.
    // - default: '' (project root)
    // - alternative: 'admin' (the admin's dashboard)
    'home_link' => 'admin',

    // Menu logo. You can replace this with an <img> tag if you have a logo.
    'project_logo' => '<svg width="169" height="26" viewBox="0 0 169 26" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M20.5137 25.0176H11.3027V8.88086H0.351562V1.39258H31.4648V8.88086H20.5137V25.0176ZM34.5586 1.375H57.3223C62.1914 1.32227 66.3223 5.41797 66.252 10.3047C66.252 12.0801 65.7773 13.6973 64.8281 15.1387C63.8965 16.5625 62.6484 17.6523 61.1016 18.373L66.252 25H56.4434L51.9961 19.2344H43.7695V25H34.5586V1.375ZM55.002 8.2832H43.7695V12.3086H55.002C55.5645 12.3086 56.0391 12.1152 56.4258 11.7285C56.8301 11.3242 57.0234 10.8496 57.0234 10.3047C57.0234 9.17969 56.127 8.2832 55.002 8.2832ZM85.1309 8.23047L81.8789 15.209H88.3652L85.1309 8.23047ZM66.9727 25.0176L79.6465 1.39258H90.5977L103.271 25.0176H92.9004L91.0371 20.9746H79.207L77.3438 25.0176H66.9727ZM117.229 16.9141H113.203V24.9824H103.992V1.35742H113.203V9.42578H117.229L125.895 1.35742H137.988L125.736 13.1699L137.988 24.9824H125.895L117.229 16.9141ZM146.127 9.14453C146.127 10.1816 148.992 10.3223 152.701 10.498C159.293 10.832 168.557 11.2891 168.539 17.2129C168.539 23.9629 160.805 25.5625 152.508 25.5625C144.229 25.5449 137.355 24.543 136.477 17.2129H146.127C147.182 19.041 149.625 19.5156 152.508 19.5156C155.373 19.5156 158.889 19.041 158.889 17.2129C158.889 16.1758 156.023 16.0176 152.314 15.8418C145.723 15.5078 136.459 15.0508 136.477 9.14453C136.477 2.39453 144.211 0.777344 152.508 0.777344C160.787 0.830078 167.66 1.76172 168.539 9.14453H158.889C157.834 7.28125 155.391 6.8418 152.508 6.8418C149.643 6.8418 146.127 7.26367 146.127 9.14453Z" fill="#292D32"/>
</svg>
',

    // Show / hide breadcrumbs on admin panel pages.
    'breadcrumbs' => true,

    // ------
    // FOOTER
    // ------

    // Developer or company name. Shown in footer.
    'developer_name' => false,

    // Developer website. Link in footer. Type false if you want to hide it.
    'developer_link' => false,

    // Show powered by Laravel Backpack in the footer? true/false
    'show_powered_by' => false,

    // ---------
    // DASHBOARD
    // ---------

    // Show "Getting Started with Backpack" info block?
    'show_getting_started' => false,

    // -------------
    // GLOBAL STYLES
    // -------------

    // CSS files that are loaded in all pages, using Laravel's asset() helper
    'styles' => [
        'public/css/dashboard.css'
        // 'styles/example.css',
        // 'https://some-cdn.com/example.css',
    ],

    // CSS files that are loaded in all pages, using Laravel's mix() helper
    'mix_styles' => [ // file_path => manifest_directory_path
        // 'css/app.css' => '',
    ],

    // CSS files that are loaded in all pages, using Laravel's @vite() helper
    // Please note that support for Vite was added in Laravel 9.19. Earlier versions are not able to use this feature.
    'vite_styles' => [ // resource file_path
        // 'resources/css/app.css',
    ],

    // --------------
    // GLOBAL SCRIPTS
    // --------------

    // JS files that are loaded in all pages, using Laravel's asset() helper
    'scripts' => [
        // 'js/example.js',
        // 'https://unpkg.com/vue@2.4.4/dist/vue.min.js',
        // 'https://unpkg.com/react@16/umd/react.production.min.js',
        // 'https://unpkg.com/react-dom@16/umd/react-dom.production.min.js',
    ],

    // JS files that are loaded in all pages, using Laravel's mix() helper
    'mix_scripts' => [ // file_path => manifest_directory_path
        // 'js/app.js' => '',
    ],

    // JS files that are loaded in all pages, using Laravel's @vite() helper
    'vite_scripts' => [ // resource file_path
        // 'resources/js/app.js',
    ],

    'classes' => [
        /**
         * Use this as fallback config for themes to pass classes to the table displayed in List Operation
         * It defaults to: "table table-striped table-hover nowrap rounded card-table table-vcenter card-table shadow-xs border-xs".
         */
        'table' => null,

        /**
         * Use this as fallback config for themes to pass classes to the table wrapper component displayed in List Operation.
         */
        'tableWrapper' => null,
    ],

];

