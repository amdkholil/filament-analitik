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
];
