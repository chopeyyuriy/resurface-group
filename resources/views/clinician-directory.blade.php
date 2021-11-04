@extends('layouts.master')

@section('title') Clinician Directory @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center">
                <h4 class="mb-sm-0 font-size-18">Clinician Directory</h4>
                @hasrole('admin')
                <a href="{{ route('clinician.form', ['id' => 0]) }}" class="btn btn-primary btn-rounded waves-effect waves-light ms-sm-3">
                    <i class="bx bx-user-plus font-size-20 align-middle me-1"></i>Add New User
                </a>
                @endhasrole
            </div>
        </div>
    </div>

    <div id="clinicians_search" class="row align-items-end">
        <div class="col-xxl-4 col-lg-3 col-sm-8">
            <div class="mb-4">
                <label class="form-label">Search</label>
                <div class="input-icon-right-group">
                    <input name="search_name_email" class="form-control" type="text" placeholder="By user name or email address" id="search_name_email">
                    <button class="btn btn-clear">
                        <span class="bx bx-search-alt font-size-16 align-middle"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="w-100 d-lg-none"></div>
        <div class="col-xxl-2 col-lg-3 col-4">
            <div class="mb-4">
                <label class="form-label">Filter by User Type</label>
                <select name="user-type-filter" class="form-control select2" id="clinicianTypeFilter">
                    <option value="0">All Types</option>
                    @foreach(config('clinician.types') as $key => $val)
                    <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-3 col-4">
            <div class="mb-4">
                <label class="form-label">Filter by Location</label>
                <select name="location-filter" class="form-control select2" id="clinicianLocationFilter">
                    <option value="0">All Locations</option>
                    @foreach(App\Models\Directories::rootList() as $row)
                    <option value="{{ $row->id }}">{{ $row->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xxl-2 col-lg-3 col-4">
            <div class="mb-4">
                <label class="form-label">Filter by Status</label>
                <select name="status" class="form-control select2" id="clinicianStatusFilter">
                    @foreach(config('client.status') as $idStatus => $valStatus)
                        <option value="{{ $idStatus }}">{{ $valStatus }}</option>
                    @endforeach
                </select>

            </div>
        </div>
        
        <div class="col-xxl-2 col-lg-3 col-4">
            <div class="mb-4">
                <button id="btn_reset" class="btn btn-warning">Reset</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-hover w-100" id="clinician-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 65px;">#</th>
                                        <th scope="col" class="sorting sorting_asc js-table-search">Name / Location</th>
                                        <th scope="col" class="sorting js-table-search">Email</th>
                                        <th scope="col" class="sorting" id="clinicianType">Type</th>
                                        <th scope="col" class="sorting">Status</th>
                                        @hasrole('admin')
                                        <th scope="col" class="td-fit">Action</th>
                                        @endhasrole
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>

<!-- forms init -->
<script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

<script>
    $(document).ready(function() {
        var table = $('#clinician-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('clinician.table') }}",
                data: function (d) {
                    d.name = $('#search_name_email').val();
                    d.email = $('#search_name_email').val();
                    d.type = $('#clinicianTypeFilter').val();
                    d.status = $('#clinicianStatusFilter').val();
                    d.name_location = $('#clinicianLocationFilter').val();
                }
            },
            sDom: 'rtipr', //lfrti
            pageLength: "25",
            columns: [
                {data: "avatar", orderable: false, searchable: false},
                {data: "name" },
                {data: "email" },
                {data: "type" },
                {data: "status" },
                @hasrole('admin')
                {data: "action", name: "action", orderable: false, searchable: false},
                @endhasrole
            ],
            order: [[1, 'asc']],
        });

        $('#search_name_email').keyup(function() {
            table.draw();
        });

        $('#clinicianStatusFilter').change(function(){
            table.draw();
        });

        $('#clinicianLocationFilter').change(function(){
            table.draw();
        });

        $('#clinicianTypeFilter').change(function(){
            // let colIndex = $('#clinicianType').index();
            // table.column(colIndex)
            // .search($(this).val(), false, false, true)
            // .draw();
            table.draw();
        });
        
        $('#btn_reset').on('click', function () {
            $('#clinicians_search input[name="search_name_email"]').val('');
            $('#clinicians_search select[name="user-type-filter"]').val(0).trigger('change');
            $('#clinicians_search select[name="location-filter"]').val(0).trigger('change');
            $('#clinicians_search select[name="status"]').val(1).trigger('change');
        });
    });
</script>
@endsection
