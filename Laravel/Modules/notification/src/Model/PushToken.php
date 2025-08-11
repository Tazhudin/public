<?php

namespace Notification\Model;

use Illuminate\Database\Eloquent\Model;

class PushToken extends Model
{
    //phpcs:disable
    protected $primaryKey = 'token';
    protected $keyType = 'string';
    protected $table = 'notification__push_token';
    public $timestamps = false;
}
