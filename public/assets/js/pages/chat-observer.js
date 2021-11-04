$(document).ready(function () {
    chatObserver();
});

var chatObserverTimeout = false;
var chatActivitiesData = false;
var chatMessagesView = false;
var chatMessagesNoViewDetail = false;

function chatObserver() {
    $.ajax({
        url: '/messaging/check-new-messages',
        data: {},
        success: function (data) {
            chatActivitiesData = data.activity;
            chatMessagesView = data.view;
            chatMessagesNoViewDetail = data.data;
            
            let n = 0;
            if (chatMessagesNoViewDetail && chatMessagesNoViewDetail.length > 0) {
                chatMessagesNoViewDetail.forEach(function (item) {
                    n += item.cou;
                });               
            }
            
            if (n > 0) {
                $('#chat_new_messages_count').text(n).show();
                $('#chat_new_messages_count_lg').text(n).show();
            } else {
                $('#chat_new_messages_count').hide();
                $('#chat_new_messages_count_lg').hide();
            }
            
            window.dispatchEvent(new Event('UPDATED_CHAT_ACTIVITIES'));
            
            clearTimeout(chatObserverTimeout);
            chatObserverTimeout = setTimeout(chatObserver, 30000);
        },
        error: function (err) {
            console.log(err);
            clearTimeout(chatObserverTimeout);
            chatObserverTimeout = setTimeout(chatObserver, 5000);
        }
    });
}