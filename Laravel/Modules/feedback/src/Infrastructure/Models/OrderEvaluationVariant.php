<?php

namespace Feedback\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderEvaluationVariant extends Model
{
    use HasFactory;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    public $table = 'feedback_order_evaluation_variants';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'comments' => 'array',
        'evaluations' => 'array',
    ];

    protected function asJson(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
