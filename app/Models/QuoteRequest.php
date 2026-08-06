<?php

namespace App\Models;

use App\Enums\QuoteRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'company', 'email', 'phone', 'country', 'city', 'product_interest',
        'message', 'uploaded_file', 'status', 'admin_note', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuoteRequestStatus::class,
        ];
    }
}
