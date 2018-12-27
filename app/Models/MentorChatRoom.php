<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorChatRoom extends Model
{
    protected $fillable = ['room_type', 'user_ids'];
}
