<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'version',
        'author',
        'description',
        'icon',
        'is_enabled',
        'config',
        'manifest',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'array',
        'manifest' => 'array',
    ];
}
