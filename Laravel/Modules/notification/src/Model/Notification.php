<?php

namespace Notification\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $table = 'notification__notification';

    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'type',
        'hash',
        'message',
        'status',
        'provider_response',
        'payload',
    ];

    protected $casts = [
        'created_at' => 'immutable_datetime',
        'provider_response' => 'json',
        'payload' => 'json',
        'type' => NotificationType::class,
        'status' => NotificationStatus::class,
    ];
}
