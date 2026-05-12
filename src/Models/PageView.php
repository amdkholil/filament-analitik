<?php

namespace Kholil\FilamentAnalitik\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function getTable()
    {
        return config('filament-analitik.table_name', 'filament_page_views');
    }
}
