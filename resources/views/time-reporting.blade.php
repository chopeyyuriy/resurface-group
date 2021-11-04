@extends('layouts.master')

@section('title') Time Reporting @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center">
            <h4 class="mb-sm-0 font-size-18">Time Reporting</h4>
            <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light ms-sm-3" data-bs-toggle="modal" data-bs-target=".js-new-time-entry-modal">
                <i class="bx bx-time-five font-size-20 align-middle me-1"></i>New time entry
            </button>
        </div>
    </div>
</div>

<div class="row align-items-end time-reporting-filter">
    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6">
        <div class="mb-4">
            <label class="form-label">Date Range</label>
            <div class="input-daterange input-group" id="dateRangePicker" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#dateRangePicker'>
                <input type="text" class="form-control" name="start" placeholder="Start Date" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
                <input type="text" class="form-control" name="end" placeholder="End Date" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6  col-md-8 col-sm-6">
        <div class="mb-4">
            <label class="form-label">Clinician</label>
            <select class="form-control select2 select2_search" name="clinician" data-placeholder="Search and select user" multiple>
                @foreach(App\Models\Clinician::getSortListWithAuth() as $row)
                <option value="{{ $row->id }}">{{ $row->getNameAttribute() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="w-100 d-xl-none"></div>
    <div class="col-xl-2 col-lg-4 col-sm-4">
        <div class="mb-4">
            <label class="form-label">Project / Client</label>
            <select class="form-control select2" name="client">
                <option value="0">All</option>
                @foreach(App\Models\Client::getSortList() as $row)
                <option value="{{ $row->id }}">{{ $row->getNameAttribute() }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-4">
        <div class="mb-4">
            <label class="form-label">Activity Type</label>
            <select class="form-control select2" name="activity_type">
                <option value="0">All</option>
                @foreach(\App\Models\TimeEntry::ACTIVITY_TYPES as $key => $val)
                <option value="{{ $key }}">{{ $val }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-sm-4">
        <div class="mb-4">
            <button type="button" id="generateTimeReportBtn" class="btn btn-primary waves-effect waves-light col-sm-12">Generate Report</button>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Buttons -->
                <!-- <div id="timeReportTableEditButtons" class="text-end">
                    <button type="button" class="btn btn-sm btn-secondary waves-effect waves-light mt-2 mt-md-0"><i class="bx bx-pencil font-size-14 align-middle me-1"></i>Edit Selected</button>
                    <button type="button" class="btn btn-sm btn-secondary waves-effect waves-light ms-2 mt-2 mt-md-0"><i class="bx bx-trash font-size-14 align-middle me-1"></i><span class="align-middle">Delete Selected</span></button>
                </div> -->
                <!-- end: Buttons -->
                <!-- Table -->
                <div class="table-responsive">
                    <table id="timeReportTable" class="table align-middle table-nowrap table-check nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="align-middle">Date</th>
                                <th class="align-middle">Clinician</th>
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
                                <td colspan="4" class="border-0 text-end"><strong>Total</strong></td>
                                <td class="border-0"><h4 class="m-0"></h4></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- end: Table -->
            </div>
        </div>
    </div>
</div>

@include('popups.time_entry_edit')

@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>

    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>
    <!-- <script src="{{ URL::asset('/assets/js/pages/form-time-entry.js') }}"></script> -->
    
    <script>
        var timeReportTable;
        
        $(document).ready(function() {
            $('#generateTimeReportBtn').on('click', function () {
                timeReportTable.draw();
            });
            
            timeReportTable = $('#timeReportTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                paging: false,
                ajax: {
                    url: "{{ route('time_reporting.data') }}",
                    data: function (d) {                        
                        d.dateStart = $('.time-reporting-filter #dateRangePicker input[name="start"]').val();
                        d.dateEnd = $('.time-reporting-filter #dateRangePicker input[name="end"]').val();
                        d.clinician = $('.time-reporting-filter select[name="clinician"]').val();
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
                    { data: "clinician" },
                    { data: "client" },
                    { data: "activity_type" },
                    { data: "time" },
                    { data: "action", name: "action", orderable: false, searchable: false }
                ],
                searching: false,
                columnDefs: [
                    { orderable: false, targets: [5] }
                ],
                order: [[1, 'asc']],
                footerCallback: function (row, data, start, end, display) {
                    let api = this.api();
                    total = 0;
                    for (let i = 0; i < data.length; i++) {
                        total += data[i].spent;
                    }
                    totalText = Math.trunc(total / 60) + ':' + total % 60;
                    $(api.column(4).footer()).html('<h4 class="m-0">' + totalText + '</h4>');
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
