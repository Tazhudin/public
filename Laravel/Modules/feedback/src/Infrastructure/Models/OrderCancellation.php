<?php

namespace Feedback\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $table = 'feedback_order_cancelations';

    protected $fillable = [
        'customer_id',
        'order_id',
        'order_status',
        'reason',
        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'reason' => 'array',
    ];

    protected function asJson(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
