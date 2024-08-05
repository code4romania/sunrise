<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state',
        'age',
        //'birthdate',
        'birth_date',
        'aggressor_relationship',
        'maintenance_sources',
        'location',
        'observations',
    ];
}
