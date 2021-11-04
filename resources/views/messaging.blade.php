@extends('layouts.master')

@section('title') Messaging @endsection

@section('content')
    <style>
        .no-transparent-avatar {
            background-color: #cadaef!important;
        }
        
        .chat-group-user-list-hide {
            display: none!important;
        }
        
        .user_item_hide {
            display: none!important;
        }
        
        .user_item_readonly_hide {
            display: none!important;
        }
        
        .user_item_hiddend_hide {
            display: none!important;
        }
        
        .chat_lists {
            max-height: 410px;
        }
        
        #chat_messaging_list {
            height: 504px;
            max-height: 504px;
        }
        
        .emojis {
            width: 490px;
            max-width: 100vw;
            padding: 4px!important;
            margin: 0px!important;
        }
        
        .emojis a {
            padding: 1px!important;
            line-height: 0px!important;
            margin: 0px!important;
        }
        
        .emojis img {
            width: 28px;
            height: 28px;
        }
        
        #chat_messaging_list img.emoji-img, 
        .chat-last-message img.emoji-img {
            width: 28px;
            height: 28px;
        }
        
        .chat-input {
            padding-right: 45px;
        }
        
        #chat_messaging_list .simplebar-content {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-height: 100%;
        }
        
        @media(max-width: 767.98px) {
            .main-content {
                max-height: 100vh;
                overflow: hidden;
            }

            .page-content {
                max-height: 100vh;
            }

            .container-fluid {
                display: flex;
                flex-direction: column;
                max-height: calc(100vh - 180px);
            }
            
            .footer {
                display: none;
            }
            
            .chat_lists {
                max-height: calc(100vh - 460px);
            }
            
            .chat-leftsidebar {
                max-height: 100%;
            }
            
            #chat_messaging_list {
                height: 100%;
                max-height: 100%;
            }
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center">
                <h4 class="mb-sm-0 font-size-18">Messaging</h4>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="d-md-flex">
            <div class="card-body bg-light chat-leftsidebar py-0">
                <div class="">
                    <div class="py-4 border-bottom">
                        <div class="media">
                            @if($photo)
                            <div class="align-self-center me-3">
                                <img src="{{ URL::asset('/avatars/crop-32/clinician/'.$photo) }}"
                                    class="rounded-circle avatar-xs" alt="">
                            </div>
                            @else
                            <div class="avatar-xs align-self-center me-3">
                                <span
                                    class="avatar-title rounded-circle bg-primary bg-soft text-primary">
                                    {{ Str::upper(Auth::user()->name[0]) }}
                                </span>
                            </div>
                            @endif
                            <div class="media-body">
                                <h5 class="font-size-15 mt-0 mb-1">{{ Auth::user()->name }}</h5>
                                <p class="text-muted mb-0">
                                    <i class="mdi mdi-circle text-success align-middle me-1"></i> Active
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="search-box chat-search-box py-4">
                        <div class="position-relative">
                            <input type="text" class="form-control" name="master_search" placeholder="Search..." value="">
                            <i class="bx bx-search-alt search-icon"></i>
                        </div>
                    </div>

                    <div class="chat-leftsidebar-nav">
                        <ul id="chat_tabs" class="nav nav-pills nav-justified">
                            <li class="nav-item">
                                <a href="#chat" data-bs-toggle="tab" aria-expanded="true" class="nav-link px-2 active">
                                    <i class="bx bx-chat font-size-20 d-sm-none"></i>
                                    <span class="d-none d-sm-block">Chat</span>
                                    <span class="badge rounded-pill bg-danger" style="display: none;">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#groups" data-bs-toggle="tab" aria-expanded="false" class="nav-link px-2">
                                    <i class="bx bx-group font-size-20 d-sm-none"></i>
                                    <span class="d-none d-sm-block">Groups</span>
                                    <span class="badge rounded-pill bg-danger" style="display: none;">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#clinicians" data-bs-toggle="tab" aria-expanded="false" class="nav-link px-2">
                                    <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                                    <span class="d-none d-sm-block">Clinicians</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content py-4">
                            <div class="tab-pane show active" id="chat">
                                <div>
                                    <h5 class="font-size-14 mb-3">Recent</h5>
                                    <ul class="list-unstyled chat-list chat-chat-list chat_lists" data-simplebar>
                                        {!! $chats !!}
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane" id="groups">
                                <h5 class="font-size-14 mb-3">Groups</h5>
                                <ul class="list-unstyled chat-list chat-group-list chat_lists" data-simplebar>
                                    {!! $groups !!}
                                </ul>
                            </div>
                            <div class="tab-pane" id="clinicians">
                                <h5 class="font-size-14 mb-3">Clinicians</h5>
                                <div class="chat_lists" data-simplebar>
                                    @foreach($clinicians as $group)
                                    <div class="{{ $loop->index > 0 ? 'mt-4' : '' }}">
                                        <div class="avatar-xs mb-3">
                                            <span class="avatar-title rounded-circle bg-primary bg-soft text-primary">
                                                {{ $group->char }}
                                            </span>
                                        </div>
                                        <ul class="list-unstyled chat-list chat-clinician-list">
                                            @foreach($group->clinicians as $row)
                                            <li>
                                                <a class="chat-clinician-item" href="#" data-id="{{ $row->id }}">
                                                    <h5 class="font-size-14 mb-0">{{ $row->name }}</h5>
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- <div class="w-100 user-chat"> -->
            <div class="user-chat" style="display: flex; flex-direction: column;">
                <!-- <button type="button" class="btn-close user-chat-close" aria-label="Close"></button> -->
                <!-- <div class="" style="flex-grow: 1; display: flex; flex-direction: column; max-height: 100vh;"> -->
                    <div class="p-4 border-bottom user-chat-header">
                        <div class="row">
                            <div id="chat_panel_name" class="col-md-4 col-sm-8 col-8">
                                <h5 class="font-size-15 mb-1" style="display: none;"></h5>
                                <p class="text-muted mb-0" style="display: none;">
                                    <i class="mdi mdi-circle text-light align-middle me-1 activity_indicator"></i>
                                    <span class="activity_indicator_text">Active now</span>
                                </p>
                            </div>
                            <div class="col-md-8 col-sm-4 col-4 user-chat-close-wrap">
                                <ul class="list-inline user-chat-nav text-end mb-0">
                                    <li id="chat_group_user_list" class="list-inline-item d-none d-sm-inline-block chat-group-user-list-hide">
                                        <div class="avatar-group float-start task-assigne" data-bs-toggle="modal" data-bs-target=".js-chat-participants-modal">
                                            <div class="user_list avatar-group float-start task-assigne"></div>
                                            <div class="avatar-group-item">
                                                <a href="javascript: void(0);" class="d-inline-block">
                                                    <div class="avatar-xs">
                                                        <span id="chat_group_participants_count" class="avatar-title rounded-circle bg-info text-white font-size-16">0</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <li id="chat_panel_control" class="list-inline-item me-md-0">
                                        <div class="dropdown">
                                            <button class="btn nav-btn waves-effect dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-cog"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target=".js-chat-add-new-group-modal">Add new group</a>
                                                <a class="dropdown-item" href="#add_participiant" data-bs-toggle="modal" data-bs-target=".js-chat-participants-modal">Add participant</a>
                                                <a class="dropdown-item" href="#clear_group">Clear conversation</a>
                                                <a class="dropdown-item" href="#delete_group">Delete</a>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-inline-item d-md-none">
                                        <button class="btn nav-btn waves-effect user-chat-close" type="button">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="messagesList" class="chat-conversation p-3" style="flex-grow: 1; overflow: hidden;">
                        <ul id="chat_messaging_list" class="chat-conversation-list list-unstyled mb-0" data-simplebar>
                            <li></li>
                        </ul>
                    </div>
                    <div id="chat_message_panel" class="p-3 chat-input-section" style="display: none;">
                        <form id="form_messaging" class="row" target="" method="post">
                            @csrf
                            <input type="hidden" name="group_id" value="">
                            <input type="hidden" name="to_id" value="">
                            <div class="col">
                                <div class="position-relative">
                                    <input name="message" type="text" class="form-control chat-input emoji-input" maxlength="1000"
                                           autocomplete="off" placeholder="Enter Message...">
                                    <div class="chat-input-links" id="tooltip-container">
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item dropup">
                                                <a href="#" title="Emoji" class="dropdown-toggle" 
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="mdi mdi-emoticon-happy-outline"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right emojis"></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-rounded chat-send w-md waves-effect waves-light">
                                    <span class="d-none d-sm-inline-block me-2">Send</span>
                                    <i class="mdi mdi-send"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                <!-- </div> -->
            </div>

        </div>
    </div>
   
    <!-- end row -->
    
<!-- Add new group -->
<div class="modal fade js-chat-add-new-group-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add new group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_chat_add_new_group" action="{{ route('messaging.group.add') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" required="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary">Add group</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end. Add new group -->

<!-- Chat Participants Modal -->
<div class="modal fade js-chat-participants-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">6 participants in the chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="search-box chat-search-box">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search..." name="search" autocomplete="off">
                        <i class="bx bx-search-alt search-icon"></i>
                    </div>
                </div>
            </div>
            <div class="modal-body" data-simplebar style="max-height: 400px; min-height: 400px;">
                @forelse($cliniciansAll as $row)
                <div class="d-flex align-items-center p-1 rounded mb-2 user_item {{ $row->hiddend ? 'user_item_hiddend_hide' : '' }}" 
                    role="button" data-id="{{ $row->id }}" data-name="{{ $row->name }}">
                    <div class="flex-shrink-0 user_avatar">
                        @if($row->photo)
                        <img src="{{ URL::asset('/avatars/crop-32/clinician/'.$row->photo) }}" class="avatar-xs rounded-circle">
                        @else
                        <div class="avatar-xs align-self-center">
                            <span class="avatar-title rounded-circle text-primary no-transparent-avatar">
                                {{ Str::upper($row->name[0]) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="font-size-14 mb-0"><span>{{ $row->name }}</span>
                            <i class="mdi mdi-circle font-size-10 ms-1 activity_indicator"></i>
                        </h5>
                    </div>
                    <div class="contact-links flex-grow-0 flex-shrink-0 ms-3 user_add_to_group">
                        <a href="#add" title="Add">Add</a>
                    </div>
                    <div class="contact-links flex-grow-0 flex-shrink-0 ms-3 user_del_from_group">
                        <a href="#delete" title="Delete"><i class="mdi mdi-close-circle-outline font-size-20"></i></a>
                    </div>
                </div>
                @endforeach
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- end: Chat Participants Modal -->

@endsection

@section('script')
    <script>
        var chatMessagesEnv = {
            id: {{ Auth::user()->id }},
            loadInterval: 2000,
            loadErrorInterval: 5000,
            recalTimeInterval: 1000,
            addUserToGroupMsg: '{{ $addUserToGroupMsg }}',
        };
    </script>
    <script src="{{ URL::asset('/assets/js/pages/emojis.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/chat.js') }}"></script>
@endsection