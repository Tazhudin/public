<?php

namespace Feedback\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderEvaluation extends Model
{
    use HasFactory;

    public $table = 'feedback_order_evaluations';

    protected $fillable = [
        'order_id',
        'user_id',
        'evaluation',
        'comment',
        'comments',
        'images',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'comments' => 'array',
        'images' => 'array',
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
