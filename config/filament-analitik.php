<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tracking Enabled
    |--------------------------------------------------------------------------
    |
    | Whether the analytics tracking should be enabled.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Tracked Tables
    |--------------------------------------------------------------------------
    |
    | The table name used to store page views.
    |
    */
    'table_name' => 'filament_page_views',
    
    /*
    |--------------------------------------------------------------------------
    | Project ID
    |--------------------------------------------------------------------------
    |
    | A unique identifier for this project.
    |
    */
    'project_id' => env('FILAMENT_ANALITIK_PROJECT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Sidebar Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | Define the custom navigation label, group, and icon in the Filament sidebar.
    |
    | Group defaults to null (no group).
    |
    */
    'navigation' => [
        'label' => 'Analitik',
        'group' => null,
        'icon' => 'heroicon-o-chart-bar',
    ],
];
