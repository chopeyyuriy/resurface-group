@extends('layouts.master')

@section('title') Dashboard @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .margin_2 {
            margin-right: 2px;
            margin-bottom: 2px;
        }
        
        .avatar-title-notransparent {
            width: 72px!important;
            height: 72px!important;
            background-color: #2a6dc0!important;
            font-weight: 500;
            font-size: 30px;
        }
    </style>
@endsection

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            </div>
        </div>
    </div>
    <!-- end: Page title -->

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 order-lg-1 col-xl-3">
                            <div class="media">
                                <div class="me-3">
                                    @if($clinician->photo)
                                    <img class="avatar-md rounded-circle img-thumbnail" 
                                         src="{{ data_get($clinician, 'photo') ? URL::asset('/avatars/crop-32/clinician/'.data_get($clinician, 'photo')) : asset('/assets/images/default-user.jpg') }}">
                                    @else
                                    <span class="avatar-md avatar-title rounded-circle text-white avatar-title-notransparent img-thumbnail">
                                        {{ Str::upper($clinician->name[0]) }}
                                    </span> 
                                    @endif
                                </div>
                                <div class="media-body align-self-center">
                                    <div class="text-muted">
                                        <p class="mb-2">Welcome</p>
                                        <h5 class="mb-1">{{ data_get($clinician, 'first_name') }}, {{ data_get($clinician, 'last_name') }}</h5>
                                        <p class="mb-0">{{ data_get($clinician, 'locationData.title') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 pt-lg-2 pt-xl-0 order-lg-3 order-xl-2 col-xl-7 align-self-center">
                            <div class="text-xl-center mt-4 mt-lg-0">
                                <div class="row">
                                    <div class="col-4 col-sm-3 col-xxl-2">
                                        <div>
                                            <p class="text-muted mb-2">Clients</p>
                                            <h5 class="mb-0"><a href="erm" class="text-body">{{ $clinician->clients->count() ?? 0 }}</a></h5>
                                        </div>
                                    </div>
                                    <div class="col-8 col-sm-4 col-xxl-5">
                                        <div>
                                            <p class="text-muted mb-2">Upcoming Appointments</p>
                                            <h5 class="mb-0"><a id="events_count" href="calendar" class="text-body"></a></h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-5 col-xxl-5 pt-3 pt-sm-0">
                                        <div>
                                            <p class="text-muted mb-2">Your Next Appointment</p>
                                            <h6 class="mb-0">
                                                <a id="next_event" href="#" class="text-body show_event"></a>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4  order-lg-2 order-xl-3 col-xl-2 d-none d-lg-block">
                            <div class="clearfix mt-4 mt-lg-0 text-end">
                                <a href="{{ route('clinician.form', $clinician->id) }}" class="btn btn-primary" type="button">
                                    <i class="bx bx-user align-middle me-1"></i>
                                    My Profile
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
            </div>
        </div>
    </div>
    <!-- end: Welcome Card -->

    <div class="row">
        <!-- My Appointments -->
        <div class="col-lg-12 col-xxl-6 d-xxl-flex flex-xxl-column">
            <div class="card flex-xxl-grow-1">
                <div class="card-body">
                    <h4 class="card-title mb-4">My Appointments</h4>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="td-fit">#</th>
                                    <th class="align-middle">Date & Time</th>
                                    <th class="align-middle">Event name</th>
                                    <th class="align-middle td-fit">Action</th>
                                </tr>
                            </thead>
                            <tbody id="events_table_body">
                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
            </div>
        </div>
        <!-- end: My Appointments -->

        <!-- Time Entries -->
        <div class="col-lg-12 col-xxl-6 d-xxl-flex flex-xxl-column">
            <div class="card flex-xxl-grow-1">
                <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">Time Entries This Week</h4>
                        <div class="ms-auto">
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target=".js-new-time-entry-modal">
                            <i class="bx bx-plus-circle align-middle me-1"></i>
                            Add New
                        </button>
                        <a href="time-reporting" class="btn btn-primary ms-2" type="button">View All</a>
                        </div>
                    </div>

                    <div id="stacked-column-chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <!-- end: Time Entries -->
    </div>


    <!-- Transaction Modal -->
    <div class="modal fade event-details-modal" role="dialog"
        aria-labelledby="event-details-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="event-details-modalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-muted fw-bolder mb-1">Date</div>
                            <div id="event_date"></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted fw-bolder mb-1">Event Type</div>
                            <div id="event_type" class="px-2 py-1 rounded d-inline-block text-white" style="background-color: #BF3D97;"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-muted fw-bolder mb-1">Start</div>
                            <div id="event_start"></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted fw-bolder mb-1">End</div>
                            <div id="event_end"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <div class="text-muted fw-bolder mb-1">Location</div>
                            <div id="event_location"></div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="text-muted fw-bolder mb-1">Host</div>
                            <div id="event_host"></div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="text-muted fw-bolder mb-1">Participants</div>
                            <div id="event_participants">
                                <span class="px-2 py-1 rounded d-inline-block bg-light">John Smith</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted fw-bolder mb-1">Notes</div>
                            <div id="event_notes"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> -->
            </div>
        </div>
    </div>
    <!-- end modal -->

    <!-- subscribeModal -->
    <!-- <div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-light rounded-circle text-primary h1">
                                <i class="mdi mdi-email-open"></i>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <h4 class="text-primary">Subscribe !</h4>
                                <p class="text-muted font-size-14 mb-4">Subscribe our newletter and get notification to stay
                                    update.</p>

                                <div class="input-group bg-light rounded">
                                    <input type="email" class="form-control bg-transparent border-0"
                                        placeholder="Enter Email address" aria-label="Recipient's username"
                                        aria-describedby="button-addon2">

                                    <button class="btn btn-primary" type="button" id="button-addon2">
                                        <i class="bx bxs-paper-plane"></i>
                                    </button>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- end modal -->

@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- dashboard init -->
    <!-- <script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script> -->
    <!-- <script src="{{ URL::asset('/assets/js/pages/form-time-entry.js') }}"></script> -->
    <script src="{{ URL::asset('/assets/libs/moment/moment.min.js') }}"></script>
    
    <script>
        $(function () {
            "use strict";
            
            $.ajax({
                url: '{{ route("root.dash_json", "") }}/' + moment().format('YYYY-MM-DD H:m:s'),
                success: function (data) {
                    $('#events_table_body').html(data.table);
                    $('#events_count').text(data.data.count);
                    if (data.data.next) {
                        $('#next_event').html(data.data.next.text).data('id', data.data.next.id);
                    } else {
                        $('#next_event').html('not found').data('id', '');
                    }
                    eventsViewSetClick();
                },
                error: function (err) {
                    console.log(err);
                }
            });
            
        // Chart
        var options = {
            chart: {
                height: 360,
                type: 'bar',
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '25%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Hours',
                data: [
                @for($i = 1; $i <= 7; $i++)
                {{ isset($time_report[$i]) ? $time_report[$i]->spent : 0 }},
                @endfor
                ]
            }],
            xaxis: {
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            },
            yaxis: {
                tickAmount: 1,
                labels: {
                    formatter: function (value) {
                        value = Math.round(value);
                        let h = Math.floor(value / 60);
                        let m = value - h * 60;
                        if (m < 10) {
                            m = '0' + m;
                        }
                        return h + ':' + m;
                    }
                },
            },
            colors: ['#556ee6', '#f1b44c', '#34c38f'],
            legend: {
                position: 'bottom',
            },
            fill: {
                opacity: 1
            },
        }
        
        let sumValue = 0;
        options.series[0].data.forEach(function (item) {
            sumValue += item;
        });
        
        if (sumValue == 0) {
            options.yaxis.show = false;
        }

        var chart = new ApexCharts(
            document.querySelector("#stacked-column-chart"),
            options
        );

        chart.render();

        });
        
        function eventsViewSetClick() {
            $('.show_event').on('click', function () {
                if (!$(this).data('id')) return ;
                
                $.ajax({
                    url: "{{ route('root.event_json', '') }}/" + $(this).data('id'),
                    success: function (data) {
                        $('#event_date').text(moment(data.event.date).format('MM/DD/YYYY'));
                        $('#event_type').text(data.event.type_name);
                        $('#event_start').text(data.event.from_text);
                        $('#event_end').text(data.event.to_text);
                        $('#event_location').text(data.event.location);
                        $('#event_host').text(data.event.host_name);
                        $('#event_participants').html('');
                        for (let i = 0; i < data.participants.length; i++) {
                            let html = '<span class="px-2 py-1 rounded d-inline-block bg-light margin_2">' + data.participants[i].name + '</span>';
                            $('#event_participants').append(html);
                        }
                        $('#event_notes').text(data.event.notes);
                        
                        $('.event-details-modal').modal('show');
                    },
                    error: function (err) {
                        console.log(err);
                    }
                })
            });
        }
    </script>
@endsection
