<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotPrompt extends Model
{
    protected $fillable = [
        'title',
        'prompt_text',
        'is_active',
    ];
}
