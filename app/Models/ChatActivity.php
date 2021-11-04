<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class ChatActivity extends Model
{
    protected $fillable = [
        'user_id',
    ];
    
    public static function registerCurrentUser()
    {
        $user_id = Auth::user()->id;
        try {
            if (!ChatActivity::whereUserId($user_id)->first()) {
                ChatActivity::create([
                    'user_id' => $user_id,
                ]);
            }            
        } catch (\Exception $ex) {

        }
    }
}
