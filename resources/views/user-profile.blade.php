@extends('layouts.master')

@section('title') User Details @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/iEdit/iEdit.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Calendar -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/tui-time-picker/tui-time-picker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/tui-date-picker/tui-date-picker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/tui-calendar/tui-calendar.min.css') }}"/>

    <!-- FullCalendar -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.bundle.css') }}"/>
    <style>
        .profile-avatar-edit__btn {
            border: 2px solid #ffffff;
        }
        
        .profile-avatar-delete__btn {
            cursor: pointer;
            width: 32px;
            height: 32px;
            background: #2a6dc0;
            color: #fff;
            border-radius: 100%;
            border: 2px solid #ffffff;
            position: absolute;
            left: 0px;
            bottom: 0;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .avatar-title-notransparent {
            width: 2rem!important;
            height: 2rem!important;
            background-color: #2a6dc0!important;
            font-weight: 500;
            font-size: 14px;
        }
        
        .avatar-photo-notransparent {
            position: absolute;
            left: 0px;
            top: 0px;
            background-color: #2a6dc0!important;
            font-weight: 500;
            font-size: 65px;
        }
    </style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Clinician Directory @endslot
        @slot('title') User Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body pb-0">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom border-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#demographics" role="tab">
                                <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                                <span class="d-none d-sm-block">Demographics</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#eventCalendar" role="tab">
                                <span class="d-block d-sm-none"><i class="bx bx-calendar"></i></span>
                                <span class="d-none d-sm-block">Event Calendar</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#timeReports" role="tab">
                                <span class="d-block d-sm-none"><i class="bx bx-time-five"></i></span>
                                <span class="d-none d-sm-block">Time Reports</span>
                            </a>
                        </li>
                    </ul>
                    <!-- end: Tabs -->
                </div>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Demographics Tab -->
                    <div class="tab-pane active" id="demographics" role="tabpanel">
                        <form class="outer-repeater" method="post" action="{{ route('clinician.save', ['id' => data_get($clinician, 'id', 0)]) }}" enctype="multipart/form-data">
                            @csrf
                            @hasrole('admin')
                            <div class="card-body bg-light">
                                <h4 class="card-title mb-3 pb-2 border-bottom">Administrative controls</h4>
                                <!--  -->
                                <div class="row align-items-end">
                                    <div class="col-xxl-3 col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label">User Status</label>
                                            <select name="status" class="form-control select2" @hasrole('admin') @else disabled @endhasrole>
                                                @foreach(config('client.status') as $idStatus => $valStatus)
                                                <option value="{{ $idStatus }}"
                                                            @if($idStatus == data_get($clinician, 'user.status', 0)) selected @endif>{{ $valStatus }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label">User Type</label>
                                            <select name="type" class="form-control select2" @hasrole('admin') @else disabled @endhasrole>
                                                @foreach(config('clinician.types') as $key => $val)
                                                <option value="{{ $key }}" {{ (old('type', data_get($clinician, 'type')) == $key) ? 'selected' : '' }}>{{ $val }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-sm-4">
                                        <div class="mb-3" style="position: relative;">
                                            <label class="form-label">User Location</label>
                                            <select name="location[]" class="form-control select2 select2_search" multiple="">
                                                @foreach(App\Models\Directories::rootList() as $row)
                                                <option value="{{ $row->id }}" {{ in_array($row->id, old('location', $locations)) ? 'selected' : '' }}>{{ $row->title }}</option>
                                                @endforeach
                                            </select>
                                            @error('location')
                                                <span class="text-danger" style="position: absolute;left: 0px;top: 65px;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                            </div>
                            @endhasrole

                            <div class="card-body">
                                <h4 class="card-title mb-3 pb-2 border-bottom">User information</h4>
                                <!--  -->
                                <div class="row">
                                    <div class="col-xl-3 order-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label">Profile Picture</label>
                                            <div class="profile-avatar-edit">
                                                <img src="{{ data_get($clinician, 'photo') ? '/avatars/crop-600/clinician/' . data_get($clinician, 'photo') : asset('/assets/images/default-user.jpg') }}" alt="Photo" class="profile-avatar-edit__img" id="profile-avatar">
                                                <span class="avatar-xs avatar-title rounded-circle text-white avatar-photo-notransparent" style="{{ data_get($clinician, 'photo') ? 'display:none;' : '' }}">
                                                    {{ Str::upper(data_get($clinician, 'name') ? data_get($clinician, 'name')[0] : '') }}
                                                </span> 
                                                @if(Auth::user()->hasRole('admin') || $clinician->user->id == Auth::user()->id)
                                                <label class="profile-avatar-delete__btn" style="display: none;">
                                                    <i class="mdi mdi-close-circle-outline"></i>
                                                </label>
                                                <input type="hidden" name="avatar_delete" value="{{ old('avatar_delete') }}">
                                                <label for="edit-profile-avatar" class="profile-avatar-edit__btn">
                                                    <input name="photo_load" type="file" id="edit-profile-avatar" value="{{ old('photo', data_get($clinician, 'photo')) }}">
                                                    <input name="photo_name" type="hidden" id="edit-profile-avatar-name" value="{{ old('photo_name') }}">
                                                    <input name="photo" type="hidden" id="edit-profile-avatar-data" value="{{ old('photo') }}">
                                                    <span class="bx bx-edit font-size-16 align-middle"></span>
                                                </label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xl-3">
                                        <div class="mb-3">
                                            <label class="form-label">First Name</label>
                                            <input name="first_name" class="form-control" type="text" value="{{ old('first_name', data_get($clinician, 'first_name')) }}">
                                            @error('first_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xl-3">
                                        <div class="mb-3">
                                            <label class="form-label">Middle Name</label>
                                            <input name="middle_name" class="form-control" type="text" value="{{ old('middle_name', data_get($clinician, 'middle_name')) }}">
                                            @error('middle_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xl-3">
                                        <div class="mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input name="last_name" class="form-control" type="text" value="{{ old('last_name', data_get($clinician, 'last_name')) }}">
                                            @error('last_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- Contact information -->
                                <h4 class="card-title mb-3 pb-2 border-bottom">Contact information</h4>
                                <div class="row">
                                    <div class="col-md-8 col-xl-9 col-xxl-6">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Email Address</label>
                                                    <input name="email" class="form-control" type="text" value="{{ old('email', data_get($clinician, 'user.email')) }}">
                                                    @error('email')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="outer" data-repeater-list="outer-group">
                                            @forelse($phones as $phone)
                                            <!-- phone number -->
                                            <div class="row inner" data-repeater-item>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Phone Number</label>
                                                        <input name="phone" class="form-control" type="tel" value="{{ data_get($phone, 'phone') }}">
                                                    </div>
                                                </div>
                                                <div class="col-auto flex-grow-1 col-sm-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Type</label>
                                                        <select name="type" class="form-control select2">
                                                            @foreach(config('client.phone') as $key => $val)
                                                            <option value="{{ $key }}" {{ data_get($phone, 'type') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-auto col-sm-3 d-flex">
                                                    <input data-repeater-delete type="button" class="btn btn-light waves-effect w-100 mb-3 align-self-end inner delete-phone" data-id="{{ data_get($phone, 'id') }}" value="Delete" />
                                                </div>
                                            </div>
                                            <!-- end: phone number -->
                                            @empty
                                            <!-- phone number -->
                                            <div class="row inner" data-repeater-item>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Phone Number</label>
                                                        <input name="phone" class="form-control" type="tel" value="">
                                                    </div>
                                                </div>
                                                <div class="col-auto flex-grow-1 col-sm-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Type</label>
                                                        <select name="type" class="form-control select2">
                                                            @foreach(config('client.phone') as $key => $val)
                                                            <option value="{{ $key }}">{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-auto col-sm-3 d-flex">
                                                    <input data-repeater-delete type="button" class="btn btn-light waves-effect w-100 mb-3 align-self-end inner" value="Delete" />
                                                </div>
                                            </div>
                                            <!-- end: phone number -->
                                            @endforelse
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <input data-repeater-create type="button" class="btn btn-secondary waves-effect waves-light inner" value="Add new phone number" />
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- end: Contact information -->

                                <br />

                                <!-- Password -->
                                @if(Auth::user()->hasRole('admin') || $clinician->user->id == Auth::user()->id)
                                <h4 class="card-title mb-3 pb-2 border-bottom">Password</h4>
                                <div class="row">
                                    <div class="col-md-8 col-xl-9 col-xxl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input name="password" class="form-control" type="password" autocomplete="off">
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-xl-9 col-xxl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Re-type password</label>
                                            <input name="password_confirmation" class="form-control" type="password" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <!-- end: Password -->

                                <!-- Admin Info Footer -->
                                <div id="admin_info_buttons" class="mt-3 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light me-2">Save Changes</button>
                                    <a href="{{ route('clinician_directory') }}" class="btn btn-secondary waves-effect waves-light">Cancel</a>
                                </div>
                                <!-- end: Admin Info Footer -->
                                @endif
                            </div>
                        </form>

                    </div>
                    <!-- end: Demographics Tab -->

                    <!-- Event Calendar Tab -->
                    <div class="tab-pane" id="eventCalendar" role="tabpanel">
                        <div class="card card-custom">
                            <div class="card-body">
                                <div class="card-header">
                                    <div id="schedule-calendar"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade js-event-modal" role="dialog" aria-hidden="true" id="schedule-add">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div id="modal-html"></div>
                                    <div class="modal-footer justify-content-start">
                                        <input id="user_id" type="hidden" name="user_id" value="{{ $clinician ? $clinician->id : 0 }}">
                                        @if($user_id == Auth::id())
                                            <button type="submit" class="btn btn-primary" form="form-new-event">Submit</button>
                                            <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Close</button>
                                            <form id="remove_event" action="" data-url="{{ url('/calendar/remove/%id%') }}" method="post">
                                                @csrf
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete Event?')" title="Delete">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end: Event Calendar Tab -->

                    <!-- Time Reports Tab -->
                    <div class="tab-pane" id="timeReports" role="tabpanel">
                        <div class="card-body bg-light">
                            <!--  -->
                            <div class="row align-items-end time-reporting-filter">
                                <div class="col-xxl-4 col-lg-3 col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label">Date Range</label>
                                        <div class="input-daterange input-group" id="dateRangePicker" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#dateRangePicker'>
                                            <input type="text" class="form-control" name="start" placeholder="Start Date" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
                                            <input type="text" class="form-control" name="end" placeholder="End Date" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-3 col-sm-4">
                                    <div class="mb-3">
                                        <label class="form-label">Project / Client</label>
                                        <select class="form-control select2" name="client">
                                            <option value="0">All</option>
                                            @foreach(App\Models\Client::getSortList() as $row)
                                            <option value="{{ $row->id }}">{{ $row->getNameAttribute() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-3 col-sm-4">
                                    <div class="mb-3">
                                        <label class="form-label">Activity Type</label>
                                        <select class="form-control select2" name="activity_type">
                                            <option value="0">All</option>
                                            @foreach(\App\Models\TimeEntry::ACTIVITY_TYPES as $key => $val)
                                            <option value="{{ $key }}">{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-2 col-lg-3 col-sm-4">
                                    <div class="mb-3">
                                        <button type="button" id="generateTimeReportBtn" class="btn btn-primary waves-effect waves-light col-sm-12">Generate Report</button>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                        </div>
                        <div class="card-body">
                            <!-- Table -->
                            <div class="table-responsive">
                                <table id="timeReportTable" class="table align-middle table-nowrap table-check nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="align-middle">Date</th>
                                            <th class="align-middle">Project / Client</th>
                                            <th class="align-middle">Activity Type</th>
                                            <th class="align-middle">Time Logged</th>
                                            <th class="td-fit"></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="border-0 text-end"><strong>Total</strong></td>
                                            <td class="border-0"><h4 class="m-0"></h4></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- end: Table -->
                        </div>
                    </div>
                    <!-- end: Time Reports Tab -->
                </div>
                <!-- end: Tab panes -->

            </div>
        </div>
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h4 class="card-title mb-2 pb-2 border-bottom">Associated clients</h4>
                    @foreach($clients as $client)
                        <div class="d-flex align-items-center mt-3">
                            <div class="flex-shrink-0">
                                @if($client->photo)
                                <img src="{{ data_get($client, 'photo') ? '/avatars/crop-32/client/' . data_get($client, 'photo') : asset('/assets/images/default-user.jpg') }}" class="avatar-xs rounded-circle">
                                @else
                                <span class="avatar-xs avatar-title rounded-circle text-white avatar-title-notransparent">
                                    {{ Str::upper($client->name[0]) }}
                                </span> 
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="card-title mb-0"><a href="{{ route('client.view', ['id' => data_get($client, 'id')]) }}" class="text-dark">{{ data_get($client, 'name') }}</a></h4>
                                <div class="text-muted font-size-11">{{ data_get($client, 'family.title') }}&nbsp;
                                    / {{ data_get($client, 'family.locationData.state_id') }} {{ data_get($client, 'family.locationData.city') }}&nbsp;
                                    / {{ $client->family->mainPatient('name') }}</div>
                            </div>
                            @hasrole('admin')
                            <div class="contact-links flex-grow-0 flex-shrink-0 ms-3">
                                <a href="{{ route('clinician.detach.client', ['client_id' => data_get($client, 'id'), 'clinician_id' => data_get($clinician, 'id')]) }}" title="Detach" onclick="return confirm('Are you sure ?')">
                                    <i class="mdi mdi-close-circle-outline font-size-20"></i></a>
                            </div>
                            @endhasrole
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @include('popups.time_entry_edit')
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/iEdit/iEdit.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <!-- form repeater js -->
    <script src="{{ URL::asset('/assets/libs/jquery-repeater/jquery-repeater.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-repeater.int.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>

    <!-- Calendar -->
    <script src="https://uicdn.toast.com/tui.code-snippet/latest/tui-code-snippet.min.js"></script>
    <script src="{{ URL::asset('/assets/libs/tui-dom/tui-dom.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/libs/tui-time-picker/tui-time-picker.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/tui-date-picker/tui-date-picker.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/chance/chance.min.js') }}"></script>

    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

    <!-- fullCalendar -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src='{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.bundle.js') }}'></script>
    <script src='{{ URL::asset('/assets/libs/fullcalendar/script.js') }}'></script>

    <script>
        var timeReportTable;

        $(document).ready(function () {
            "use strict";

            @if(!Auth::user()->hasRole('admin') && $clinician->user->id != Auth::user()->id)
                $('#demographics input').attr('disabled', '');
                $('#demographics select').attr('disabled', '');
            @else

            // profile avatar --------------

            $("#edit-profile-avatar").change(function(e){
                var avatar = e.target.files[0];

                if(!iEdit.open(avatar, true, function(res) {
                    $('.avatar-photo-notransparent').hide();
                    $("#edit-profile-avatar").val('');
                    $("#profile-avatar").attr("src", res);
                    $('#edit-profile-avatar-data').val(res);
                    $('#edit-profile-avatar-name').val(avatar.name);
                    $('input[name="avatar_delete"]').val('');
                    $('.profile-avatar-delete__btn').show();
                })){
                    alert("Whoops! That is not an image!");
                }
            });

            $('.profile-avatar-delete__btn').on('click', function () {
                $('.avatar-photo-notransparent').show();
                $("#profile-avatar").attr("src", "{{ asset('/assets/images/default-user.jpg') }}");
                $('input[name="avatar_delete"]').val('delete');
                $("#edit-profile-avatar").val('');
                $('#edit-profile-avatar-name').val('');
                $('#edit-profile-avatar-data').val('');

                $(this).hide();
            });

            @if(data_get($clinician, 'photo'))
            $('.profile-avatar-delete__btn').show();
            @else
            $('.profile-avatar-delete__btn').hide();
            @endif

            if ($('input[name="avatar_delete"]').val()) {
                $("#profile-avatar").attr("src", "{{ asset('/assets/images/default-user.jpg') }}");
                $('input[name="photo"]').val('');
                $('.profile-avatar-delete__btn').hide();
                $('.avatar-photo-notransparent').show();
            } else {
                let p = $('input[name="photo"]').val();
                if (p) {
                    $("#profile-avatar").attr("src", p);
                    $('.profile-avatar-delete__btn').show();
                    $('.avatar-photo-notransparent').hide();
                }
            }

            // end. profile avatar -----------

            @endif

            $('#generateTimeReportBtn').on('click', function () {
                timeReportTable.draw();
            });

            // Data Table
            timeReportTable = $('#timeReportTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                paging: false,
                ajax: {
                    url: "{{ route('time_reporting.data') }}",
                    data: function (d) {
                        let a = new Array();
                        a.push({{ data_get($clinician, 'id', -1) }});

                        d.dateStart = $('.time-reporting-filter #dateRangePicker input[name="start"]').val();
                        d.dateEnd = $('.time-reporting-filter #dateRangePicker input[name="end"]').val();
                        d.clinician = a;
                        d.client = $('.time-reporting-filter select[name="client"]').val();
                        d.activityType = $('.time-reporting-filter select[name="activity_type"]').val();
                    }
                },
                buttons: [
                    {extend: 'excel', className: 'btn-sm'},
                    {extend: 'pdf', className: 'btn-sm'},
                    {extend: 'print', className: 'btn-sm'}
                ],
                sDom: "Brtpr",
                columns: [
                    { data: "date" },
                    { data: "client" },
                    { data: "activity_type" },
                    { data: "time" },
                    { data: "action", name: "action", orderable: false, searchable: false }
                ],
                searching: false,
                columnDefs: [
                    { orderable: false, targets: [4] }
                ],
                order: [[1, 'asc']],
                footerCallback: function (row, data, start, end, display) {
                    let api = this.api();
                    let total = 0;
                    for (let i = 0; i < data.length; i++) {
                        total += data[i].spent;
                    }
                    let totalText = Math.trunc(total / 60) + ':' + total % 60;
                    $(api.column(3).footer()).html('<h4 class="m-0">' + totalText + '</h4>');
                },
                drawCallback: function () {
                    $('#timeReportTable a.time-entry-edit').on('click', function () {
                        timeReportEditJsonQuery($(this).data('id'));
                        return false;
                    });

                    $('#timeReportTable a.time-entry-del').on('click', function () {
                        if (confirm('Are you sure?')) {
                            $.ajax({
                                url: '{{ route("del_time_entry", "") }}/' + $(this).data('id'),
                                success: function (data) {
                                    timeReportTable.draw();
                                },
                            });
                        }
                        return false;
                    });
                },
            });
        });

        function timeReportEditJsonQuery(id) {
            $.ajax({
                method: 'post',
                url: '{{ route("edit_time_entry.json", "") }}/' + id,
                success: function (data) {
                    $('#form-edit-time-entry input[name="id"]').val(data.data.id);
                    $('#form-edit-time-entry input[name="date"]').val(data.data.date);
                    $('#form-edit-time-entry input[name="time"]').val(data.data.time);
                    $('#form-edit-time-entry select[name="clinicians[]"]').val(data.data.clinicianIds).trigger('change');
                    $('#form-edit-time-entry select[name="client"]').val(data.data.client_id).trigger('change');
                    $('#form-edit-time-entry select[name="activity_type"]').val(data.data.activity_type).trigger('change');
                    $('#form-edit-time-entry textarea[name="notes"]').val(data.data.notes);

                    $('.js-edit-time-entry-modal').modal('show');
                },
                failure: function (errMsg) {
                    console.log(errMsg);
                }
            });
        }
    </script>
@endsection
