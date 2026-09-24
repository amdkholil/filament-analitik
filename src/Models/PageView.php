<?php

namespace Kholil\FilamentAnalitik\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'url',
        'path',
        'method',
        'ip',
        'user_agent',
        'city',
        'state',
        'country',
        'project_id',
        'created_at',
    ];

    public function getTable(): string
    {
        return config('filament-analitik.table_name', 'analitik');
    }
}
