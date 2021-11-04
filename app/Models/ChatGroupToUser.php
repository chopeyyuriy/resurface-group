<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatGroupToUser extends Model
{
    protected $table = 'chat_group_to_user';
    
    public static function appendUserToGroup(int $id, int $group_id)
    {
        try {
            $item = new ChatGroupToUser();
            $item->chat_group_id = $group_id;
            $item->user_id = $id;
            $item->save();
            
            return $item;
        } catch (\Exception $ex) {
            return ChatGroupToUser::whereChatGroupId($group_id)
                        ->whereUserId($id)
                        ->first();
        }
    }
}
