<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_project_id',
        'api_route_id',
        'method',
        'url',
        'request_headers',
        'query_params',
        'request_body',
        'status_code',
        'duration_ms',
        'response_headers',
        'response_body',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'request_headers' => 'array',
            'query_params' => 'array',
            'response_headers' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ApiProject::class, 'api_project_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(ApiRoute::class, 'api_route_id');
    }
}
