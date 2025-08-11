<?php

namespace Feedback\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancelationVariant extends Model
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $table = 'feedback_order_cancelation_variants';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
    ];
}
