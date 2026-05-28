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
    'table_name' => 'analitik',
    
    /*
    |--------------------------------------------------------------------------
    | Project ID
    |--------------------------------------------------------------------------
    |
    | A unique identifier for this project. Useful for multi-tenant, SaaS,
    | or centralized analytics setups where you collect analytics from 
    | multiple applications/websites into a single database.
    |
    | Default: null (no project ID recorded)
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
