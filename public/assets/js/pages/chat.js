$(document).ready(function () {
    chatReloadMessagesPanel('', '', -1);
    
    chatLoadMessages();
    
    $('#chat_tabs').on('show.bs.tab', function (e) {
        let item = false;
        switch ($(e.target).attr('href')) {
            case '#chat':
                chatMessagesPanelType = 'chat';
                item = $('.chat-chat-list .chat-list-item.active a');
                if (item.length) {
                    item.trigger('click');
                }
                break;
            case '#groups':
                chatMessagesPanelType = 'group';
                item = $('.chat-group-list li.active a.chat-group-item');
                if (item.length) {
                    item.trigger('click');
                }
                break;
            case '#clinicians':
                chatMessagesPanelType = 'clinicians';
                item = $('#clinicians .active a.chat-clinician-item');
                if (item.length) {
                    item.trigger('click');
                }
                break;
        }
        
        if (!item || item.length == 0) {
            chatReloadMessagesPanel('', '', -1);
        }
    });
    
    $('#form_messaging input[name="message"]').on('input', function () {
        if ($(this).val()) {
            $('#form_messaging button[type="submit"]').removeClass('disabled');
        } else {
            $('#form_messaging button[type="submit"]').addClass('disabled');
        }
    }).trigger('input');
    
    $('#form_messaging').submit(function (event) {
        event.preventDefault();
        
        if ($('#form_messaging button[type="submit"]').hasClass('disabled')) {
            return ;
        }
        
        let formData = new FormData($(this)[0]);
        
        $('input[name="message"]', this).val('').trigger('input');
        
        $.ajax({
            method: 'post',
            url: '/messaging/send',
            contentType: false,
            dataType: "json",
            processData: false,
            data: formData,
            success: function (data) {
                if (data.group_id == -1) {
                    chatReloadChatsAndGroupLists();
                    alert('This group is closed');
                } else
                if (chatMessagesCurrentGroupId != data.group_id) {
                    chatMessagesCurrentGroupId = data.group_id;
                    chatReloadChatsAndGroupLists();
                }
            },
            error: function (err) {
                console.log(err);
            },
        });
    });
    
    chatChatEvents();
    chatGroupEvents();
    chatClinicianEvents();
    
    setInterval(function () {
        chatRecalculateChatListTime();
    }, chatMessagesEnv.recalTimeInterval);
    
    /* -------------- */
    closeMobileChat();    
    /* -------------- */
    
    $('input[name="master_search"]').on('input', function () {
        clearTimeout(chatSearchTimeout);
        
        let text = $(this).val();
        if (text) {
            chatSearchTimeout = setTimeout(function () {
                $.ajax({
                    url: '/messaging/search',
                    data: {
                        text: text,
                    },
                    success: function (data) {
                        if (data.text != text) return ;

                        chatGlobalSearchResult = data.data;
                        chatChatRecalcItems();
                        chatGroupRecalcItems();
                    },
                    error: function (err) {
                        console.log(err);
                    },
                });
            }, 250);
        } else {
            chatGlobalSearchResult = false;   
            chatChatRecalcItems();
            chatGroupRecalcItems();
        }
    }).trigger('input');
    
    $(window).on('UPDATED_CHAT_ACTIVITIES', function () {
        chatUpdateActivities();
        chatUpdateMessagesView();
    });
});

var chatSearchTimeout = false;

function chatChatRecalcItems() {
    if (!chatGlobalSearchResult) {
        $('a[href="#chat"] span.bg-danger').hide();
        $('.chat-chat-list .chat-chat-item').show();
        return ;
    }
    
    let chats = 0;
    $('.chat-chat-list .chat-chat-item').each(function () {
        let id = $(this).data('group-id');
        if (chatGlobalSearchResult.indexOf(id) > -1) {
            chats++;
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    if (chats) {
        $('a[href="#chat"] span.bg-danger').text(chats);
        $('a[href="#chat"] span.bg-danger').show();
    } else {
        $('a[href="#chat"] span.bg-danger').hide();
    }
}

function chatGroupRecalcItems() {
    if (!chatGlobalSearchResult) {
        $('a[href="#groups"] span.bg-danger').hide();
        $('.chat-group-list .chat-group-item').show();
        return ;
    }
    
    let groups = 0;
    $('.chat-group-list .chat-group-item').each(function () {
        let id = $(this).data('id');
        if (chatGlobalSearchResult.indexOf(id) > -1) {
            groups++;
            $(this).show();
        } else {
            $(this).hide();
        }
    });

    if (groups) {
        $('a[href="#groups"] span.bg-danger').text(groups);
        $('a[href="#groups"] span.bg-danger').show();
    } else {
        $('a[href="#groups"] span.bg-danger').hide();
    }
}

function chatChatEvents() {
    $('.chat-chat-item').on('click', function () {
        $('.chat-chat-list li.active').removeClass('active');
        $(this).parent().addClass('active');
        
        if (chatMessagesPanelType != 'chat') return false;
        
        let group_id = $(this).data('group-id');
        $('#form_messaging input[name="group_id"]').val(group_id);
        $('#form_messaging input[name="to_id"]').val('');
        chatMessagesCurrentGroupId = group_id;
        chatReloadMessagesPanel('chat', $('.span_name', this).text(), group_id);
        
        return false;
    }).on('mouseup', function () {
        $('.user-chat').addClass('opened');
    });
}

function chatGroupEvents() {
    $('.chat-group-item').on('click', function () {
        $('.chat-group-list li.active').removeClass('active');
        $(this).parent().addClass('active');
        
        if (chatMessagesPanelType != 'group') return false;
        
        let group_id = $(this).data('id');
        $('#form_messaging input[name="group_id"]').val(group_id);
        $('#form_messaging input[name="to_id"]').val('');
        chatMessagesCurrentGroupId = group_id;
        chatReloadMessagesPanel('group', $('.span_name', this).text(), group_id);

        return false;
    }).on('mouseup', function () {
        $('.user-chat').addClass('opened');
    });
}

function chatClinicianEvents() {
    $('.chat-clinician-item').on('click', function () {
        $('.chat-clinician-list li.active').removeClass('active');
        $(this).parent().addClass('active');
        
        if (chatMessagesPanelType != 'clinicians') return false;
        
        let to_id = $(this).data('id');
        
        /* find an existing group (chat) with this user */
        let group = $('.chat-chat-item[data-id="' + to_id + '"]');
        if (group.length) {
            chatMessagesCurrentGroupId = parseInt(group.data('group-id'));
        } else {
            chatMessagesCurrentGroupId = -1;
        }
        /* ------------------------------------- */
        
        $('#form_messaging input[name="group_id"]').val(chatMessagesCurrentGroupId > 0 ? chatMessagesCurrentGroupId : '');
        $('#form_messaging input[name="to_id"]').val(to_id);
        
        chatReloadMessagesPanel('clinicians', $('h5', this).text(), chatMessagesCurrentGroupId);

        return false;
    }).on('mouseup', function () {
        $('.user-chat').addClass('opened');
    });
}

var chatMessagesLastId = -1;
var chatMessagesData = new Array();
var chatChatsData = new Array();
var chatMessagesPanelType = 'chat';
var chatMessagesCurrentGroupId = -1;
var chatMessagesCurrentDay = false;
var chatMessagesTimeout = false;
var chatGlobalStarting = false;
var chatGlobalSearchResult = false;

function chatLoadMessages(groupId = 0, errorHandler = null) {
    clearTimeout(chatMessagesTimeout);
    
    $.ajax({
        url: '/messaging/data/' + chatMessagesLastId + '/' + groupId,
        data: {},
        success: function (data) {
            let msgGroups = new Array();
            let newInCurrentGroup = false;
            let needReloadGroups = false;
            data.data.forEach(function (item) {                
                chatMessagesData.push(item);
                if (chatMessagesLastId < item.id) {
                    chatMessagesLastId = item.id;
                }
                
                if (item.group_id == chatMessagesCurrentGroupId) {
                    chatAppendMessageToList(item);
                    newInCurrentGroup = true;
                } else {
                    if (msgGroups.indexOf(item.group_id) == -1) {
                        msgGroups.push(item.group_id);
                    }
                }
                
                if (item.message == chatMessagesEnv.addUserToGroupMsg || item.message == chatMessagesEnv.delUserFromGroupMsg) {
                    needReloadGroups = true;
                }
                
                /* Sync chats  -------------- */
                let chatFind = false;
                for (let i = 0; i < chatChatsData.length; i++) {
                    if (chatChatsData[i].id == item.group_id) {
                        let d = moment.utc(item.created_at).local();
                        if (chatChatsData[i].lastTime < d) {
                            chatChatsData[i].lastTime = d;
                            chatChatsData[i].lastMessage = item.message;
                        }
                        chatFind = true;
                        break;
                    }
                }
                if (!chatFind) {
                    chatChatsData.push({
                        id: item.group_id,
                        lastTime: moment.utc(item.created_at).local(),
                        lastMessage: item.message,
                        init: false,
                    });
                }
                /* --------------------------- */
            });
            
            if (newInCurrentGroup) {
                chatMessagesScrollDown();
                chatUpdateMessagesView();
            }
            
            if (needReloadGroups == false) {
                msgGroups.forEach(function (item) {
                    if (($('.chat-chat-list[data-group-id="' + item.group_id + '"]').length == 0) && 
                        ($('.chat-group-list[data-id="' + item.group_id + '"]').length == 0)) {
                        needReloadGroups = true;
                    }
                });
            }
            
            if (needReloadGroups) { // Need to reload chats and groups lists.
                chatReloadChatsAndGroupLists();
            }
            
            if (data.data.length) {
                chatRecalculateChatList();
            }
            
            chatMessagesTimeout = setTimeout(chatLoadMessages, chatMessagesEnv.loadInterval);
        },
        error: function (err) {
            chatMessagesTimeout = setTimeout(chatLoadMessages, chatMessagesEnv.loadErrorInterval);
            console.log(err);
            
            if (errorHandler) {
                errorHandler();
            }
        }
    });
}

function chatReloadChatsAndGroupLists(afterChatHandler, afterGroupHandler) {
    $.ajax({
        url: '/messaging/load/chats',
        success: function (data) {
            let id = $('.chat-chat-list .chat-list-item.active .chat-chat-item').data('id');
            $('.chat-chat-list .simplebar-content').html(data);
            chatChatEvents();
            chatRecalculateChatList();
            if (id) {
                $('.chat-chat-list .simplebar-content .chat-chat-item[data-id="' + id + '"]').trigger('click');
            }
            chatChatRecalcItems();
            
            if (afterChatHandler) {
                afterChatHandler();
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
    
    $.ajax({
        url: '/messaging/load/groups',
        success: function (data) {
            let id = $('.chat-group-list li.active .chat-group-item').data('id');
            $('.chat-group-list .simplebar-content').html(data);
            
            // check in groups  
            $('.chat-group-list li .chat-group-item').each(function () {
                if ($(this).data('in-group') == 0) {
                    let id = parseInt($(this).data('id'));
                    for (let k = 0; k < chatMessagesData.length; k++) {
                        if (chatMessagesData[k].group_id == id) {
                            chatMessagesData[k].group_id = -100;
                        }
                    }
                    
                    for (let k = 0; k < chatChatsData.length; k++) {
                        if (chatChatsData[k].id == id) {
                            chatChatsData.splice(k, 1);
                            break;
                        }
                    }
                }                
            });
            // ---------------
            
            chatGroupEvents();
            if (id) {
                let item = $('.chat-group-list .simplebar-content .chat-group-item[data-id="' + id + '"]');
                if (item.length) {
                    item.trigger('click');
                } else {
                    if (chatMessagesPanelType == 'group') {
                        chatReloadMessagesPanel('', '', -1);
                    }
                }
                chatGroupParticipiantsListRecalculate();
            }
            chatGroupRecalcItems();
            
            if (afterGroupHandler) {
                afterGroupHandler();
            }
        },
        error: function (err) {
            console.log(err);
        },
    })
}

function chatReloadMessagesPanel(panelType, panelName, panelGroupId) {
    let messagingList = $('#chat_messaging_list .simplebar-content');
    messagingList.html('');
    chatMessagesCurrentDay = false;
    
    /* Check init group */
    if (panelGroupId > 0) {
        for (let i = 0; i < chatChatsData.length; i++) {
            if (chatChatsData[i].id == panelGroupId) {
                if (chatChatsData[i].init == false) {
                    // Clear prev data
                    for (let k = chatMessagesData.length - 1; k >= 0; k--) {
                        if (chatMessagesData[k].group_id == panelGroupId) {
                            chatMessagesData.splice(k, 1);
                        }
                    }
                    
                    chatChatsData[i].init = true;
                    chatLoadMessages(panelGroupId, function () {
                        chatChatsData[i].init = false;
                    });
                }
                break;
            }
        }
    } else {
        chatMessagesCurrentGroupId = -1;
    }
    /* ---------------- */
    
    if (panelGroupId > 0) {
        let lastId = 0;
        chatMessagesData.forEach(function (item) {
            if (item.group_id == panelGroupId) {
                chatAppendMessageToList(item);
                lastId = item.id;
            }
        });
        chatMessagesScrollDown();
        chatSetAsSeenMessage(panelGroupId, lastId);
        chatUpdateMessagesView();
    }
    
    $('#chat_panel_name h5').text(panelName).show();
    switch (panelType) {
        case 'chat':
        case 'clinicians':
            chatUpdateActivities();
            
            $('#chat_message_panel').show();
            $('#chat_panel_name p').show();
            $('#chat_group_user_list').addClass('chat-group-user-list-hide');
            
            $('a[href="#add_participiant"]').hide();
            $('a[href="#clear_group"]').hide();
            $('a[href="#delete_group"]').hide();
            break;
        case 'group':
            $('#chat_panel_name p').hide();
            $('#chat_group_user_list').removeClass('chat-group-user-list-hide');
            
            let group = $('.chat-group-item[data-id="' + panelGroupId + '"]');
            
            let p = group.data('participants') + '';
            let a = p ? p.split(',') : new Array();
            
            $('#chat_group_user_list .user_list').html('');
            $('#chat_group_participants_count').text(a.length + '+');
            a.forEach(function (item) {
                let img = $('.js-chat-participants-modal .user_item[data-id="' + item + '"] .user_avatar').html();
                let avatar = '<div class="avatar-group-item"><a href="javascript: void(0);" class="d-inline-block" value="member-6">' + img + '</a></div>';
                $('#chat_group_user_list .user_list').append(avatar);
            });
            
            if (group.data('readonly') == '0') {
                $('a[href="#add_participiant"]').show();
                $('a[href="#clear_group"]').show();
                $('a[href="#delete_group"]').show();
            } else {
                $('a[href="#add_participiant"]').hide();
                $('a[href="#clear_group"]').hide();
                $('a[href="#delete_group"]').hide();
            }
            
            if (panelGroupId > 0 && group.data('in-group')) {
                $('#chat_message_panel').show();
            } else {
                $('#chat_message_panel').hide();
            }
            break;
        default:
            $('#chat_message_panel').hide();
            $('#chat_panel_name p').hide();
            $('#chat_group_user_list').addClass('chat-group-user-list-hide');
            
            $('a[href="#add_participiant"]').hide();
            $('a[href="#clear_group"]').hide();
            $('a[href="#delete_group"]').hide();
    }
}

function chatMessagesScrollDown() {
    $('#chat_messaging_list .simplebar-content-wrapper').scrollTop($('#chat_messaging_list .simplebar-content').height());
}

function chatAppendMessageToList(item) {
    let messagingList = $('#chat_messaging_list .simplebar-content');
    
    let localDateTime = moment.utc(item.created_at).local();
    let date = localDateTime.format('MM/DD/YYYY')
    if (!chatMessagesCurrentDay || chatMessagesCurrentDay != date) {
        chatMessagesCurrentDay = date;
        if (date == moment().local().format('MM/DD/YYYY')) {
            date = 'Today';
        }
        let day = 
`<li>
    <div class="chat-day-title">
        <span class="title">` + date + `</span>
    </div>
</li>`;
        messagingList.append(day);
    }
    
    let time = localDateTime.format('HH:mm');
    let message = emojiConvert(item.message);
    let msg = 
`<li class="` + (item.is_my ? 'right' : '') + ` chat_message_item" data-id="` + item.id + `">
    <div class="conversation-list">
        <div class="ctext-wrap">
            <div class="conversation-name">` + item.user_name + `</div>
            <p>` + message + `</p>
            <p class="chat-time mb-0"><i class="bx bx-time-five align-middle me-1"></i> ` + time + `</p>
        </div>
    </div>
</li>`;
    messagingList.append(msg);
}

function chatRecalculateChatList() {   
    // Sort chats by last time
    chatChatsData.sort(function (a, b) {
        if (a.lastTime == b.lastTime) return 0;
        if (a.lastTime > b.lastTime) return -1;
        if (a.lastTime < b.lastTime) return 1;
    });
    
    let ls = $('.chat-chat-list .chat-chat-item');
    
    let prevChat = false;
    for (let i = 0; i < chatChatsData.length; i++) {
        let id = chatChatsData[i].id;
        let chat = $('.chat-chat-list .chat-chat-item[data-group-id="' + id + '"]');
        if (chat.length) {
            chat = chat.parent();
            if (prevChat) {
                chat.insertAfter(prevChat);
            } else {
                $('.chat-chat-list .simplebar-content').prepend(chat);
            }
            $('.chat-last-message', chat).html(emojiConvert(chatChatsData[i].lastMessage));
            $('.chat-last-time', chat).data('last-time', chatChatsData[i].lastTime);
            prevChat = chat;
        }
    }
    
    chatRecalculateChatListTime();
    
    if (!chatGlobalStarting) {
        let ls = $('.chat-chat-list .chat-chat-item');
        if (ls.length) {
            $(ls[0]).trigger('click');
        }
        chatGlobalStarting = true;
    }
}

function chatRecalculateChatListTime() {
    $('.chat-chat-list .chat-last-time').each(function () {
        let diff = moment().diff($(this).data('last-time')) / 60000; // to min
        let time = '';
        if (diff < 60) { // 60 min
            time = Math.trunc(diff) + ' min';
        } else 
        if (diff < 1440) { // 24 * 60 
            let hr = Math.trunc(diff / 60);
            if (hr > 1) {
                time = hr + ' hrs';
            } else {
                time = hr + ' hr';
            }
        } else {
            let day = Math.trunc(diff / 1440);
            if (day > 1) {
                time = day + ' days';
            } else {
                time = day + ' day';
            }
        }
        
        $(this).text(time);
    });
}

function chatSetAsSeenMessage(groupId, id) {
    $.ajax({
        url: '/messaging/set-as-seen-message/' + groupId + '/' + id,
        data: {},
        success: function (data) {
            
        },
        error: function (data) {
            
        },
    });
}

$(document).ready(function () {
    $("#form_chat_add_new_group").submit(function (event) {
        var formData = new FormData($(this)[0]);

        $.ajax({
            method: 'post',
            url: $(this).attr('action'),
            contentType: false,
            dataType: "json",
            processData: false,
            data: formData,
            success: function (data) {
                $('.js-chat-add-new-group-modal').modal('hide');
                $('.js-chat-add-new-group-modal input[name="name"]').val('');
                chatReloadChatsAndGroupLists(null, function () {
                    let g = $('.chat-group-list .chat-group-item[data-id="' + data.data + '"]');
                    g.trigger('click');
                    $('#chat_tabs a[href="#groups"]').tab('show');
                });
            },
            error: function (err) {
                console.log(err);
            }
        });

        event.preventDefault();
    });
    
    $('a[href="#delete_group"]').on('click', function () {      
        $('#chat_panel_control .dropdown-toggle').dropdown('hide');
        
        if (confirm('Are you sure?')) {
            $.ajax({
                url: '/messaging/group/del/' + chatMessagesCurrentGroupId,
                data: {},
                success: function (data) {
                    chatReloadChatsAndGroupLists();
                },
                error: function (err) {
                    console.log(err);
                }
            })
        }
        return false;
    });
    
    $('a[href="#clear_group"]').on('click', function () {
        $('#chat_panel_control .dropdown-toggle').dropdown('hide');
        
        if (confirm('Are you sure?')) {
            $.ajax({
                url: '/messaging/group/clear/' + chatMessagesCurrentGroupId,
                data: {},
                success: function (data) {
                    chatReloadChatsAndGroupLists();
                },
                error: function (err) {
                    console.log(err);
                }
            })
        }
        return false;
    });
    
    $('.js-chat-participants-modal a[href="#add"]').on('click', function () {
        $.ajax({
            url: '/messaging/group/add-user/' + chatMessagesCurrentGroupId + '/' + $(this).parent().parent().data('id'),
            success: function (data) {
                chatReloadChatsAndGroupLists();
            },
            error: function (err) {
                console.log(err);
            }
        });
        return false;
    });
    
    $('.js-chat-participants-modal a[href="#delete"]').on('click', function () {
        $.ajax({
            url: '/messaging/group/del-user/' + chatMessagesCurrentGroupId + '/' + $(this).parent().parent().data('id'),
            success: function (data) {
                chatReloadChatsAndGroupLists();
            },
            error: function (err) {
                console.log(err);
            }
        });
        return false;
    });
    
    $('.js-chat-participants-modal input[name="search"]').on('input', function () {
        let s = $(this).val();

        if (s) {
            s = s.toUpperCase();
            $('.js-chat-participants-modal .user_item').each(function () {
                let name = $(this).data('name').toUpperCase();
                if (name.indexOf(s) > -1) {
                    $(this).removeClass('user_item_hide');
                } else {
                    $(this).addClass('user_item_hide');
                }
            });
        } else {
            $('.js-chat-participants-modal .user_item').removeClass('user_item_hide');
        }
    });
    
    $('.js-chat-participants-modal').on('show.bs.modal', function () {
        $('.js-chat-participants-modal input[name="search"]').val('').trigger('input');
        chatGroupParticipiantsListRecalculate();
    });    
});

function chatGroupParticipiantsListRecalculate() {
    let group = $('.chat-group-item[data-id="' + chatMessagesCurrentGroupId + '"]');
    let readonly = group.data('readonly');
    let p = group.data('participants') + '';
    let a = p.split(',');

    let title = '';
    if (a.length > 1) {
        title = a.length + ' participants in the chat';
    } else {
        title = a.length + ' participant in the chat';
    }

    $('.js-chat-participants-modal .modal-title').text(title);

    $('.js-chat-participants-modal .modal-body .user_item').each(function () {
        let id = $(this).data('id') + '';
        let add = $('.user_add_to_group', this);
        let del = $('.user_del_from_group', this);

        if (readonly == '0') {
            if (a.indexOf(id) > -1) {
                add.hide();
                del.show();
            } else {
                add.show();
                del.hide();
            }
            
            $(this).removeClass('user_item_readonly_hide');
        } else {
            add.hide();
            del.hide();
            
            if (a.indexOf(id) > -1) {
                $(this).removeClass('user_item_readonly_hide');
            } else {
                $(this).addClass('user_item_readonly_hide');
            }
        }
    });
}

function chatUpdateActivitiesOneUser(userId) {
    if (!chatActivitiesData) return {status: 0, diff: 0};

    for (let i = 0; i < chatActivitiesData.length; i++) {
        if (chatActivitiesData[i].user_id == userId) {
            let d = moment().diff(moment.utc(chatActivitiesData[i].updated_at).local());
            
            if (d < 60000) { // 1 min
                return {status: 1, diff: d};
            } else
            if (d < 86400000) { // 1 day
                return {status: 2, diff: d};
            }
            return {status: 0, diff: d};
        }
    }
    
    return {status: 0, diff: 0};
}

function chatUpdateActivities() {    
    $('.chat-chat-list .chat-chat-item').each(function () {
        $('.activity_indicator', this)
            .removeClass('text-success')
            .removeClass('text-warning')
            .removeClass('text-light');
        switch (chatUpdateActivitiesOneUser($(this).data('id')).status) {
            case 1:
                $('.activity_indicator', this).addClass('text-success');
                break;
            case 2:
                $('.activity_indicator', this).addClass('text-light');
                break;
            default:
                $('.activity_indicator', this).addClass('text-light');
                break;
        }
    });
    
    $('.js-chat-participants-modal .user_item').each(function () {
        $('.activity_indicator', this)
            .removeClass('text-success')
            .removeClass('text-warning')
            .removeClass('text-light');
        switch (chatUpdateActivitiesOneUser($(this).data('id')).status) {
            case 1:
                $('.activity_indicator', this).addClass('text-success');
                break;
            case 2:
                $('.activity_indicator', this).addClass('text-light');
                break;
            default:
                $('.activity_indicator', this).addClass('text-light');
                break;
        }
    });
    
    let userId = false;
    
    switch (chatMessagesPanelType) {
        case 'chat':
            let chat = $('.chat-chat-list li.active .chat-chat-item');
            if (chat.length) {
                userId = chat.data('id');
            }
            break;
        case 'clinicians':
            let item = $('#clinicians li.active .chat-clinician-item');
            if (item.length) {
                userId = item.data('id');
            }
            break;
    }
    
    if (userId) {
        let ind = $('#chat_panel_name .activity_indicator');
        let ind_text = $('#chat_panel_name .activity_indicator_text');
        ind.removeClass('text-success')
            .removeClass('text-warning')
            .removeClass('text-light');
        let activity = chatUpdateActivitiesOneUser(userId);
        
        switch (activity.status) {
            case 1:
                ind.addClass('text-success');
                ind_text.text('Active now');
                break;
            case 2:
                ind.addClass('text-light');
                let m = Math.trunc(activity.diff / 60000);
                if (m < 60) {
                    ind_text.text('Was active ' + m + ' min ago');
                } else {
                    let h = Math.trunc(m / 60);
                    ind_text.text('Was active ' + h + ' ' + (h < 2 ? 'hour' : 'hours') + ' ago');
                }
                break;
            default:
                ind.addClass('text-light');
                if (activity.diff > 0) {
                    let d = Math.trunc(activity.diff / 3600000 / 24);
                    ind_text.text('Was active ' + d + ' ' + (d < 2 ? 'day' : 'days') + ' ago');
                } else {
                    ind_text.text('Not active');
                }
                break;
        }
    }
    
    
    // Check reload group
    if (chatActivitiesData) {
        for (let i = 0; i < chatActivitiesData.length; i++) {
            if (chatActivitiesData[i].user_id == chatMessagesEnv.id) {
                if (chatActivitiesData[i].reload_groups > 0) {
                    chatReloadChatsAndGroupLists();
                    chatActivitiesData[i].reload_groups = null;
                }
                break;
            }
        }
    }
}

function chatUpdateMessagesView() {
    if (!chatMessagesView) return ;
    
    let maxMy = 0;
    let maxOther = 0;
    
    let v = new Array();
    chatMessagesView.forEach(function (item) {
        if (item.group_id == chatMessagesCurrentGroupId) {
            if (item.user_id == chatMessagesEnv.id) {
                if (item.last_id > maxMy) {
                    maxMy = item.last_id;
                }
            } else {
                if (item.last_id > maxOther) {
                    maxOther = item.last_id;
                }                
            }
        }
    });
    
    let messageMy = false;
    let messageOther = false;
    
    $('#chat_messaging_list li.chat_message_item.last-chat').removeClass('last-chat');
    
    $('#chat_messaging_list li.chat_message_item').each(function () {
        let id = $(this).data('id');
        
        if ($(this).hasClass('right')) {
            if (id <= maxOther) {
                messageOther = $(this);
            }
        } else {
            if (id <= maxMy) {
                messageMy = $(this);
            }
        }
    });
    
    if (messageMy) {
        messageMy.addClass('last-chat');
    }
    
    if (messageOther) {
        messageOther.addClass('last-chat');
    }
    
    /* Update bages -------------- */
    
    let checkBage = function (id) {
        if (!chatMessagesNoViewDetail || chatMessagesNoViewDetail.length == 0) return 0;
        
        for (let i = 0; i < chatMessagesNoViewDetail.length; i++) {
            if (chatMessagesNoViewDetail[i].group_id == id) {
                return chatMessagesNoViewDetail[i].cou;
            }
        }
        return 0;
    }
    
    $('.chat-chat-list .chat-chat-item').each(function () {
        let n = checkBage($(this).data('group-id'));
        let bage = $('.badge', $(this));
        if (n > 0) {
            bage.text(n).show();
        } else {
            bage.hide();
        }
    });
    
    $('.chat-group-list .chat-group-item').each(function () {
        let n = checkBage($(this).data('id'));
        let bage = $('.badge', $(this));
        if (n > 0) {
            bage.text(n).show();
        } else {
            bage.hide();
        }
    });
    
    /* --------------------------- */
    
}

function closeMobileChat() {
    $('.user-chat-close').click(function () {
        $('.user-chat').removeClass('opened');
    });
}