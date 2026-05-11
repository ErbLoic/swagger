<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_project_id',
        'method',
        'uri',
        'name',
        'action',
        'middleware',
        'parameters',
        'headers',
        'body_schema',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'middleware' => 'array',
            'parameters' => 'array',
            'headers' => 'array',
            'body_schema' => 'array',
            'metadata' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ApiProject::class, 'api_project_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ApiRequestHistory::class);
    }
}
