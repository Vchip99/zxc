<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\ClientChatMessage;
use DB;

class ClientChatRoom extends Model
{
	protected $connection = 'mysql2';

    protected $fillable = ['room_type', 'user_ids', 'client_id'];
}
