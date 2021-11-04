<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('root') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset ('/assets/images/logo.svg') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset ('/assets/images/logo-dark.png') }}" alt="" height="17">
                    </span>
                </a>

                <a href="{{ route('root') }}" class="logo logo-light">
                    <span class="logo-sm" style="margin-left: -11px;">
                        <img src="{{ URL::asset ('/assets/images/logo-mini3.png') }}" alt="" height="44">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset ('/assets/images/logo-light-new2.png') }}" alt="" height="30">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control basicAutoComplete"
                           placeholder="@lang('translation.Search')">
                    <span class="bx bx-search-alt"></span>
                </div>
            </form>

        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                     aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input class="form-control" type="text" autocomplete="off"
                                       placeholder="@lang('translation.Search')" aria-label="Search input">
                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="btn header-item waves-effect d-flex align-items-center">
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                        data-bs-target=".js-new-time-entry-modal">
                    <i class="bx bx-time-five font-size-16 align-middle me-sm-2 d-none d-sm-inline-block"></i><i
                            class="bx bx-time-five font-size-22 align-middle d-sm-none"></i> <span
                            class="d-none d-sm-inline-block">New time entry</span>
                </button>
            </div>
            <div class="dropdown d-inline-block">
                <a href="{{ route('messaging') }}" class="btn header-item noti-icon waves-effect">
                    <i class="bx bx-message" style="margin-top: 14px;"></i>
                    <span class="badge bg-danger rounded-pill" id="chat_new_messages_count_lg"
                          style="display: none;"></span>
                </a>
            </div>
            @inject('notification','App\Services\EventsNotifications')
            <!-- <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    <i class="bx bx-bell bx-tada"></i>
                    @if($notification->EventsNotifications()->count_notifications)
                        <span class="badge bg-danger rounded-pill">{{ $notification->EventsNotifications()->count_notifications }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                     aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications"> @lang('translation.Notifications') </h6>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        @foreach($notification->EventsNotifications()->events_notifications as $item)
                            <a href="{{ url('/calendar?eid=' . $item->event_id) }}"
                               class="text-reset notification-item">
                                <div class="media">
                                    <div class="media-body">
                                        <h6 class="mt-0 mb-1" key="t-your-order">{{ $item->title }}</h6>
                                        <div class="font-size-12 text-muted">
                                            <p class="mb-1" key="t-grammer">{{ $item->message }}</p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span
                                                        key="t-min-ago">{{ $item->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div> -->

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display: flex; align-items: center;">
                    @if(data_get(Auth::user(), 'userable.photo'))
                    <img class="rounded-circle header-profile-user"
                         src="{{ data_get(Auth::user(), 'userable.photo') ? '/avatars/crop-32/clinician/' . data_get(Auth::user(), 'userable.photo') : asset('/assets/images/default-user.jpg') }}"
                         alt="Header Avatar">
                    @else
                    <span class="avatar-md avatar-title header-profile-user rounded-circle" style="width: 30px;height: 30px;margin-right: 4px;">
                        {{ Str::upper(Auth::user()->name[0]) }}
                    </span>
                    @endif
                    <span class="d-none d-xl-inline-block ms-1"
                          key="t-henry">{{ data_get(Auth::user(), 'name') }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu user-dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('clinician.form', Auth::user()->userable_id) }}"><i
                                class="bx bx-user font-size-16 align-middle me-1"></i> <span
                                key="t-profile">My Profile</span></a>
                    <a class="dropdown-item" href="{{ route('messaging') }}">
                        <span id="chat_new_messages_count" class="badge bg-danger rounded-pill float-end"
                              style="display: none;">0</span>
                        <i class="bx bx-message-dots font-size-16 align-middle me-1"></i>
                        <span key="t-messaging" class="pe-2">Messaging</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void();"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                                key="t-logout">Sign out</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!--  Change-Password example -->
<div class="modal fade change-password" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="change-password">
                    @csrf
                    <input type="hidden" value="{{ Auth::user()->id }}" id="data_id">
                    <div class="mb-3">
                        <label for="current_password">Current Password</label>
                        <input id="current-password" type="password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               name="current_password" autocomplete="current_password"
                               placeholder="Enter Current Password" value="{{ old('current_password') }}">
                        <div class="text-danger" id="current_passwordError" data-ajax-feedback="current_password"></div>
                    </div>

                    <div class="mb-3">
                        <label for="newpassword">New Password</label>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror" name="password"
                               autocomplete="new_password" placeholder="Enter New Password">
                        <div class="text-danger" id="passwordError" data-ajax-feedback="password"></div>
                    </div>

                    <div class="mb-3">
                        <label for="userpassword">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                               autocomplete="new_password" placeholder="Enter New Confirm password">
                        <div class="text-danger" id="password_confirmError" data-ajax-feedback="password-confirm"></div>
                    </div>

                    <div class="mt-3 d-grid">
                        <button class="btn btn-primary waves-effect waves-light UpdatePassword"
                                data-id="{{ Auth::user()->id }}"
                                type="submit">Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

