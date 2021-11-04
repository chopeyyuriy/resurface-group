<?php

namespace App\Services;

use App\Models\User;
use App\Models\Clinician;
use Str;
use DB;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\MessagingSendRequest;
use App\Http\Requests\ChatAddGroupRequest;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\ChatGroupToUser;
use App\Models\ChatActivity;
use OneSignal;

use Log;

class MessagingService 
{
    const ADD_USER_TO_GROUP_MSG = 'Added a new chat participant';
    const DEL_USER_FROM_GROUP_MSG = 'Chat participant has been deleted';
    
    /**
     * Separates the list of clinicians by the first letter of the name.
     * 
     * @param type $clinicians
     * @return type
     */
    public function getClinicianListSplitByFirstChar()
    {
        $clinicians = User::where('id', '<>', Auth::user()->id)
                        ->whereStatus(1)
                        ->orderBy('name')
                        ->get();
        
        $result = [];
        $char = '';
        $group = false;
        foreach($clinicians as $row) {
            $name = $row->name;
            $name_0 = Str::upper($name[0]);
            
            if ($char == '' || $char != $name_0) {
                $char = $name_0;
                $group = (object)[
                    'char' => $char,
                    'clinicians' => [],
                ];
                $result[] = $group;
            }
            
            if ($group) {
                $group->clinicians[] = (object)[
                    'id' => $row->id,
                    'name' => $name,
                ];
            }
        }
        
        return $result;
    }
    
    /**
     * 
     * 
     * @return type
     */
    public function getClinicianSortList()
    {   
        $sql = "select u.id, u.name, c.photo, if(u.status = 1, 0, 1) hiddend
                  from users u left join clinicians c on u.userable_id = c.id and u.userable_type = :type
                order by u.name ";
        return DB::select($sql, ['type' => Clinician::class]);
    }
    
    /**
     * Requesting messages in groups by last id.
     * 
     * @param int $lastId
     * @return type
     */
    public function loadLastData(int $lastId, int $groupId = null)
    {
        $user_id = Auth::user()->id;
        
        if ($groupId > 0) { // Used to bootstrap messages into a group.
            $sql = "select a.* from (
                        select m.id, m.user_id, m.chat_group_id group_id, m.message, m.created_at, u.name user_name, 
                               if(u.id = $user_id, 1, 0) is_my
                          from chat_messages m, chat_group_to_user gu, users u
                         where m.chat_group_id = gu.chat_group_id
                           and gu.user_id = $user_id
                           and gu.chat_group_id = $groupId
                           and m.user_id = u.id
                        order by m.created_at desc
                        limit 100
                    ) a
                    order by a.created_at asc";
        } else
        if ($lastId <= 0) { // Used to bootstrap one message to each group.
            $sql = "select m.id, m.user_id, m.chat_group_id group_id, m.message, m.created_at, u.name user_name, 
                           if(u.id = $user_id, 1, 0) is_my
                      from chat_messages m, chat_group_to_user gu, users u
                     where m.chat_group_id = gu.chat_group_id
                       and gu.user_id = $user_id
                       and m.user_id = u.id
                       and m.id = (select max(m2.id)
                                     from chat_messages m2
                                    where m2.chat_group_id = gu.chat_group_id)
                    order by m.created_at ";
        } else
        if ($lastId > 0) { // Used to download only new messages for all groups.
            $sql = "select m.id, m.user_id, m.chat_group_id group_id, m.message, m.created_at, u.name user_name, 
                           if(u.id = $user_id, 1, 0) is_my
                      from chat_messages m, chat_group_to_user gu, users u
                     where m.chat_group_id = gu.chat_group_id
                       and gu.user_id = $user_id
                       and m.user_id = u.id
                       and m.id > $lastId
                    order by m.created_at ";
        }
        return DB::select($sql);
    }
    
    /**
     * Returns information on new messages.
     * 
     * @return type
     */
    public function checkNewMessages()
    {
        $user_id = Auth::user()->id;
        $sql = "select gu.chat_group_id group_id,
                       (select count(*)
                          from chat_messages m
                         where m.chat_group_id = gu.chat_group_id
                           and m.user_id <> $user_id
                           and m.id > ifnull(gu.last_view_message_id, 0)) cou
                  from chat_group_to_user gu
                 where gu.user_id = $user_id";
        return DB::select($sql);
    }
    
    /**
     * Returns information on user views of all groups that I belong to.
     * 
     * @return type
     */
    public function checkMessagesView()
    {
        $user_id = Auth::user()->id;
        $sql = "select gu.chat_group_id group_id, gu.user_id, gu.last_view_message_id last_id
                  from chat_group_to_user gu, chat_group_to_user gu2
                 where gu.chat_group_id = gu2.chat_group_id
                   and gu2.user_id = $user_id";
        return DB::select($sql);
    }
    
    /**
     * Returns information on activity time.
     * 
     * @return type
     */
    public function checkActivity() 
    {
        $sql = "select a.user_id, a.reload_groups, a.updated_at
                  from chat_activities a";
        $res = DB::select($sql);
        
        $user_id = Auth::user()->id;
        
        // Updating activity time
        $sql = "update chat_activities a
                   set a.updated_at = utc_timestamp(), 
                       a.reload_groups = null 
                 where a.user_id = $user_id";
        DB::update($sql);
        // ---------------------
        return $res;
    }
    
    /**
     * Requesting chats list data.
     * 
     * @return type
     */
    public function getChatList()
    {
        $user_id = Auth::user()->id;
        $sql = "select u.id, u.name, c.photo, gu.id group_id
                  from users u 
                       left join clinicians c on u.userable_id = c.id
                       inner join chat_groups gu on gu.user_to_id = u.id
                 where gu.name is null 
                   and gu.user_from_id = $user_id
                union
                select u.id, u.name, c.photo, gu.id group_id
                  from users u 
                       left join clinicians c on u.userable_id = c.id
                       inner join chat_groups gu on gu.user_from_id = u.id
                 where gu.name is null 
                   and gu.user_to_id = $user_id";
            
        return DB::select($sql);
    }
    
    /**
     * Requesting groups list data.
     * 
     * @return type
     */
    public function getGroupList()
    {
        $user_id = Auth::user()->id;
        $res = DB::select("select g.id, g.name
                             from chat_groups g, chat_group_to_user gu
                            where not g.name is null
                              and gu.chat_group_id = g.id
                              and gu.user_id = $user_id
                           order by g.name");
        return $res;
    }
    
    /**
     * 
     * @param MessagingSendRequest $request
     * @return type
     */
    public function sendMessage(MessagingSendRequest $request)
    {
        // Find exists group
        $group = ChatGroup::find($request->group_id);
        if (!$group) { // If group not exists 
            if ($request->to_id) { // It is starting chat
                $group = ChatGroup::createChat($request->to_id);
            } else {
                //
            }
        }
        
        if ($group) {
            if (Auth::user()->hasRole('admin')) {
                ChatGroupToUser::appendUserToGroup(Auth::user()->id, $group->id);
            }
            
            $item = ChatMessage::create([
                'user_id' => Auth::user()->id,
                'chat_group_id' => $group->id,
                'message' => $request->message,
            ]);
            
            $this->setAsSeenMessage($group->id, $item->id);
            
            $this->sendPushNotificationsAtId($group->id, $request->message);
            
            return $group->id;
        }
        
        return -1;
    }
    
    public function sendPushNotificationsAtId($groupId, $messageText)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $subtitle = $user->name;
        $url = route('messaging');
        
        $sql = "select u.onesignal
                  from chat_group_to_user gu, users u
                 where gu.user_id = u.id
                   and gu.user_id <> $user_id
                   and gu.chat_group_id = $groupId";
        
        $ids = [];
        foreach(DB::select($sql) as $row) {
            if ($row->onesignal) {
                $ids[] = $row->onesignal;
            }
        }
        
        if (count($ids)) {
            try {
                $parameters = [
                    'contents' => [
                        'en' => $messageText,
                    ],
                    'include_player_ids' => $ids,
                    'url' => $url,
                    'subtitle' => [
                        'en' => $subtitle,
                    ],
                ];
                OneSignal::sendNotificationCustom($parameters);
            } catch (\Exception $ex) {

            }
        }
    }
    
    /**
     * 
     * @param int $groupId
     * @param int $id
     */
    public function setAsSeenMessage(int $groupId, int $id)
    {
        $item = ChatGroupToUser::whereChatGroupId($groupId)
            ->whereUserId(Auth::user()->id)
            ->first();
        
        if ($item && $id > $item->last_view_message_id) {
            $item->last_view_message_id = $id;
            $item->save();
        }
    }
    
    /**
     * 
     * @param ChatAddGroupRequest $request
     */
    public function addGroupFromRequest(ChatAddGroupRequest $request)
    {
        return ChatGroup::createGroup($request->name);
    }
    
    /**
     * 
     * @param int $groupId
     */
    public function delGroup(int $groupId)
    {
        $group = ChatGroup::find($groupId);
        
        if ($group && ($group->user_from_id == Auth::user()->id || Auth::user()->hasRole('admin'))) {
            DB::transaction(function () use ($group) {
                $sql = "update chat_activities a
                           set reload_groups = 1 
                         where exists(select * 
                                        from chat_group_to_user gu
                                       where gu.chat_group_id = $group->id
                                         and gu.user_id = a.user_id)";
                DB::update($sql);
                ChatMessage::whereChatGroupId($group->id)->delete();
                ChatGroupToUser::whereChatGroupId($group->id)->delete();
                $group->delete();
            });
        }
    }
    
    /**
     * 
     * @param int $groupId
     */
    public function clearGroup(int $groupId)
    {
        $group = ChatGroup::find($groupId);
        
        if ($group && ($group->user_id == Auth::user()->id || Auth::user()->hasRole('admin'))) {
            ChatMessage::whereChatGroupId($group->id)->delete();
        }
    }
    
    /**
     * 
     * @param int $groupId
     * @param int $id
     */
    public function addUserToGroup(int $groupId, int $id)
    {
        $chat = ChatGroupToUser::appendUserToGroup($id, $groupId);
        
        $chat->last_view_message_id = ChatMessage::max('id') ?? 0;
        $chat->save();
        
        $item = ChatMessage::create([
            'user_id' => $id,
            'chat_group_id' => $groupId,
            'message' => self::ADD_USER_TO_GROUP_MSG,
        ]);
    }
    
    /**
     * 
     * @param int $groupId
     * @param int $id
     */
    public function delUserFromGroup(int $groupId, int $id)
    {
        $group = ChatGroup::find($groupId);
        if (!$group) abort(404);
        
        if ($group->user_from_id == Auth::user()->id || Auth::user()->hasRole('admin')) {
            $link = ChatGroupToUser::whereChatGroupId($groupId)->whereUserId($id)->first();
            if ($link) {
                $link->delete();
            }
            
            $item = ChatMessage::create([
                'user_id' => $id,
                'chat_group_id' => $groupId,
                'message' => self::DEL_USER_FROM_GROUP_MSG,
            ]);
            
            $act = ChatActivity::whereUserId($id)->first();
            if ($act) {
                $act->reload_groups = 1;
                $act->save();
            }
        }
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function searchInChats(Request $request)
    {
        $user_id = Auth::user()->id;
        $sql = "select m.chat_group_id group_id
                  from chat_messages m, chat_group_to_user gu
                 where m.chat_group_id = gu.chat_group_id
                   and gu.user_id = $user_id
                   and m.message like :text
                group by m.chat_group_id";
        
        $res = [];
        foreach (DB::select($sql, ['text' => '%'.$request->text.'%']) as $row) {
            $res[] = $row->group_id;
        }
        
        return $res;
    }
}
