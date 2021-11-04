<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\MessagingService;
use App\Models\ChatGroup;
use App\Http\Requests\MessagingSendRequest;
use App\Http\Requests\ChatAddGroupRequest;
use App\Models\ChatActivity;

class MessagingController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    private $_messagingService;
    
    public function __construct(MessagingService $messagingService) {
        //parent::__construct();
        
        $this->_messagingService = $messagingService;
    }
    
    /**
     * This route is for requesting a messaging page.
     * 
     * @return type
     */
    public function index()
    {
        ChatActivity::registerCurrentUser();
        
        $photo = '';
        $clinicians = $this->_messagingService->getClinicianListSplitByFirstChar();
        $cliniciansAll = $this->_messagingService->getClinicianSortList();
        
        return view('messaging', [
            'photo' => $photo,
            'chats' => $this->chatList()->render(),
            'groups' => $this->groupList()->render(),
            'clinicians' => $clinicians,
            'cliniciansAll' => $cliniciansAll,
            'addUserToGroupMsg' => MessagingService::ADD_USER_TO_GROUP_MSG,
            'delUserFromGroupMsg' => MessagingService::DEL_USER_FROM_GROUP_MSG,
        ]);
    }
    
    /**
     * This route is for requesting a chat list.
     * 
     * @return type
     */
    public function chatList()
    {
        return view('messaging-chats', [
            'chats' => $this->_messagingService->getChatList(),
        ]);
    }
    
    /**
     * This route is for requesting a group list.
     * 
     * @return type
     */
    public function groupList()
    {
        return view('messaging-groups', [
            'groups' => ChatGroup::getSortListWithAuth(),
        ]);
    }
    
    /**
     * This route is for requesting a messaging data
     * 
     * @param int $lastId
     * @return json
     */
    public function data(int $lastId, int $groupId = null)
    {
        return response()->json((object)[
            'data' => $this->_messagingService->loadLastData($lastId, $groupId),
        ]);
    }
    
    /**
     * This route to send a message in a group.
     * 
     * @param MessagingSendRequest $request
     * @return type
     */
    public function send(MessagingSendRequest $request)
    {
        $group_id = $this->_messagingService->sendMessage($request);
        return response()->json((object)[
            'group_id' => $group_id,
        ]);
    }
    
    /**
     * Checks for new messages and returns the count.
     * 
     * @return type
     */
    public function checkNewMessages()
    {
        return response()->json((object)[
            'activity' => $this->_messagingService->checkActivity(),
            'data' => $this->_messagingService->checkNewMessages(),
            'view' => $this->_messagingService->checkMessagesView(),
        ]);
    }
    
    /**
     * Set as seen message.
     * 
     * @param int $groupId
     * @param int $id
     */
    public function setAsSeenMessage(int $groupId, int $id)
    {
        $this->_messagingService->setAsSeenMessage($groupId, $id);
        return 'OK';
    }
    
    /**
     * This route to add a group.
     * 
     * @param ChatAddGroupRequest $request
     * @return type
     */
    public function addGroup(ChatAddGroupRequest $request)
    {
        $group = $this->_messagingService->addGroupFromRequest($request);
        return response()->json([
            'result' => 'success',
            'data' => $group->id,
        ]);
    }
    
    /**
     * This route to remove the group.
     * 
     * @param int $id
     * @return string
     */
    public function delGroup(int $id)
    {
        $this->_messagingService->delGroup($id);
        return 'OK';
    }
    
    /**
     * This route to remove all messages from the group.
     * 
     * @param int $id
     * @return string
     */
    public function clearGroup(int $id)
    {
        $this->_messagingService->clearGroup($id);
        return 'OK';        
    }
    
    /**
     * This route to add a user to a group.
     * 
     * @param int $groupId
     * @param int $id
     * @return string
     */
    public function addUserToGroup(int $groupId, int $id)
    {
        $this->_messagingService->addUserToGroup($groupId, $id);
        return 'OK';
    }
    
    /**
     * This route to delete a user from a group
     * 
     * @param int $groupId
     * @param int $id
     * @return string
     */
    public function delUserFromGroup(int $groupId, int $id)
    {
        $this->_messagingService->delUserFromGroup($groupId, $id);
        return 'OK';
    }
    
    /**
     * This route to search for groups by text messages.
     * 
     * @param Request $request
     * @return type
     */
    public function search(Request $request)
    {
        return response()->json((object)[
            'text' => $request->text,
            'data' => $this->_messagingService->searchInChats($request),
        ]);
    }
}
