@extends('layouts.master')

@section('title') Events calendar @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <link href="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <link rel="stylesheet" type="text/css"
          href="{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.bundle.css') }}"/>
@endsection

@section('content')

    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center">
                <h4 class="mb-sm-0 font-size-18">Events Calendar</h4>
                @if(Auth::user()->hasRole('admin'))
                    <a href="#" class="btn btn-primary btn-rounded waves-effect waves-light ms-sm-3 create-event">
                        <i class="bx bx-user-plus font-size-20 align-middle me-1"></i>New Event
                    </a>
                @else
                    @if(isset($user_id))
                        @if($user_id == Auth::user()->userable_id)
                            <a href="#"
                               class="btn btn-primary btn-rounded waves-effect waves-light ms-sm-3 create-event">
                                <i class="bx bx-user-plus font-size-20 align-middle me-1"></i>New Event
                            </a>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
    <!-- end: Page title -->

    <div id="clinicians_search" class="row align-items-end">
        <div class="col-xxl-4 col-lg-3 col-sm-8">
            <div class="mb-4">
                <label class="form-label">Users</label>
                <div class="input-icon-right-group">
                    <select name="user-type-filter" class="form-control select2" id="usersTypeFilter">
                        @foreach($clinicians as $clinician)
                            <option value="{{ route('calendar', $clinician->id) }}"
                                    @if($user_id == $clinician->id) selected @endif>{{ $clinician->getNameAttribute() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-body">
            <div class="card-header">
                <div id="schedule-calendar"></div>
            </div>
        </div>
    </div>
    <!-- Add Modal -->

    <!-- Calendar Event Create and Edit Modal -->
    <div class="modal fade js-event-modal" role="dialog" aria-hidden="true" id="schedule-add">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div id="modal-html">

                </div>
                <div class="modal-footer justify-content-start">
                    <input id="user_id" type="hidden" name="user_id" value="{{ $user_id }}">
                    <input id="auth_id" type="hidden" name="auth_id" value="{{ Auth::id() }}">
                    @if(Auth::user()->hasRole('admin'))
                        <button type="submit" class="btn btn-primary" form="form-new-event">Submit</button>
                        <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Close</button>
                        <form id="remove_event" action="" data-url="{{ url('/calendar/remove/%id%') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete Event?')"
                                    title="Delete">Delete
                            </button>
                        </form>
                    @else
                        @if(isset($user_id))
                            @if($user_id == Auth::user()->userable_id)
                                <button type="submit" class="btn btn-primary" form="form-new-event">Submit</button>
                                <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Close
                                </button>
                                <form id="remove_event" action="" data-url="{{ url('/calendar/remove/%id%') }}"
                                      method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Delete Event?')"
                                            title="Delete">Delete
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endif
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- end: Calendar Event Create and Edit Modal -->

@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/moment/moment.min.js') }}"></script>

    {{--Modal--}}
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

    {{--Autocomplete--}}
    <script src="{{ URL::asset('/assets/libs/bootstrap-autocomplete/bootstrap-autocomplete.min2.js') }}"></script>

    {{--FullCalendar--}}
    <script src='{{ URL::asset('/assets/libs/fullcalendar/fullcalendar.bundle.js') }}'></script>
    <script src='{{ URL::asset('/assets/libs/fullcalendar/script.js') }}'></script>
    
    <script>
        var calendarMainPageView = true;
    </script>

    @if(Session::has('event_id'))
        <script>
            eventModalById({{Session::get('event_id')}});
        </script>
    @endif


@endsection
