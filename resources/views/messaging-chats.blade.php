@forelse($chats as $chat)
<li class="chat-list-item">
    <a href="#" class="chat-chat-item" data-id="{{ $chat->id }}" data-group-id="{{ $chat->group_id }}">
        <div class="media">
            <div class="align-self-center me-3">
                <i class="mdi mdi-circle font-size-10 text-light activity_indicator"></i>
            </div>

            @if($chat->photo)
            <div class="align-self-center me-3">
                <img src="{{ URL::asset('/avatars/crop-32/clinician/'.$chat->photo) }}"
                    class="rounded-circle avatar-xs" alt="">
            </div>
            @else
            <div class="avatar-xs align-self-center me-3">
                <span
                    class="avatar-title rounded-circle bg-primary bg-soft text-primary">
                    {{ Str::upper($chat->name[0]) }}
                </span>
            </div>
            @endif
            <div class="media-body overflow-hidden">
                <h5 class="text-truncate font-size-14 mb-1">
                    <span class="badge rounded-pill bg-danger" style="display: none;">0</span>
                    <span class="span_name">{{ $chat->name }}</span>
                </h5>
                <p class="text-truncate mb-0 chat-last-message"></p>
            </div>
            <div class="font-size-11 chat-last-time"></div>
        </div>
    </a>
</li>
@empty
<li></li>
@endforelse