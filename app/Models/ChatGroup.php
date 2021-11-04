<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use App\Models\ChatGroupToUser;

class ChatGroup extends Model
{
    protected $fillable = [];
    
    public static function getSortListWithAuth()
    {
        $user_id = Auth::user()->id;
        
        if (Auth::user()->hasRole('admin')) {
            return DB::select("select g.*, 0 readonly, 
                                      (select GROUP_CONCAT(gu.user_id SEPARATOR ',')
                                         from chat_group_to_user gu 
                                        where gu.chat_group_id = g.id) participants,
                                      if(exists (select * 
                                                   from chat_group_to_user gu3 
                                                  where gu3.chat_group_id = g.id
                                                    and gu3.user_id = $user_id), 1, 0) in_group
                                 from chat_groups g
                                where not g.name is null 
                               order by g.name");
        } else {
            return DB::select("select g.*, if(g.user_from_id = $user_id, 0, 1) readonly,
                                      (select GROUP_CONCAT(gu1.user_id SEPARATOR ',')
                                         from chat_group_to_user gu1
                                        where gu1.chat_group_id = g.id) participants,
                                      if(exists (select * 
                                                   from chat_group_to_user gu3 
                                                  where gu3.chat_group_id = g.id
                                                    and gu3.user_id = $user_id), 1, 0) in_group
                                 from chat_groups g
                                where not g.name is null 
                                  and exists (select gu.*
                                                from chat_group_to_user gu
                                               where gu.chat_group_id = g.id
                                                 and gu.user_id = $user_id)
                               order by g.name");
        }
    }
    
    public static function createChat($user_to_id)
    {
        try {
            $item = new ChatGroup();
            $item->user_from_id = Auth::user()->id;
            $item->user_to_id = $user_to_id;
            $item->save();
        } catch (\Exception $e) {
            $item = ChatGroup::whereUserFromId($user_to_id)->orWhere('user_to_id', '=', $user_to_id)->first();
        }
        
        if ($item) { // Register link to group
            ChatGroupToUser::appendUserToGroup(Auth::user()->id, $item->id);
            ChatGroupToUser::appendUserToGroup($user_to_id, $item->id);
        }
        return $item;
    }
    
    public static function createGroup($name)
    {
        $item = new ChatGroup();
        $item->name = $name;
        $item->user_from_id = Auth::user()->id;
        $item->save();
        
        ChatGroupToUser::appendUserToGroup(Auth::user()->id, $item->id);
        
        return $item;
    }
    
    public static function clearAllForUser(int $id)
    {
        $sql = "delete
                  from chat_messages
                 where user_id = $id";
        DB::delete($sql);
        
        $sql = "delete 
                  from chat_group_to_user
                 where user_id = $id";
        DB::delete($sql);
        
        $sql = "delete
                  from chat_group_to_user
                 where id in (select id
                                from chat_groups
                               where (user_from_id = 56 or user_to_id = 56))";
        DB::delete($sql);
        
        $sql = "delete
                  from chat_groups
                 where (user_from_id = $id or user_to_id = $id)";
        DB::delete($sql);
    }
}
