<?php

namespace Feedback\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;

    public $table = 'feedback_faqs';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
