<?php

namespace Feedback\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class WishesProducts extends Model
{
    protected $table = 'feedback_wishes_products';

    protected $fillable = [
        'source',
        'comment',
        'phone_number',
        'store'
    ];
}
