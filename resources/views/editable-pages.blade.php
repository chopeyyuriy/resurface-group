@extends('layouts.master')

@section('title') FAQ / Support @endsection

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
            <h4 class="mb-sm-0 font-size-18">FAQ / Support</h4>
            <a href="{{ route('faq.page_edit', '') }}" type="button" class="btn btn-primary btn-rounded waves-effect waves-light ms-sm-3">
                <i class="bx bx-plus-circle font-size-20 align-middle me-1"></i> Add New Page
            </a>
        </div>
    </div>
</div>

<div id="faq_pages_filter" class="row align-items-end">
    <div class="col-xxl-4 col-lg-3 col-md-5 col-12">
        <div class="mb-4">
            <label class="form-label">Search</label>
            <div class="input-icon-right-group">
                <input name="search_text" class="form-control" type="text" placeholder="By user title or author" value="">
                <button class="btn btn-clear">
                    <span class="bx bx-search-alt font-size-16 align-middle"></span>
                </button>
            </div>
        </div>
    </div>
    <div class="col-xxl-2 col-lg-3 col-md-3 col-sm-6">
        <div class="mb-4">
            <label class="form-label">Filter by Status</label>
            <select name="status" class="form-control select2">
                <option value="">All Statuses</option>
                @foreach(App\Models\FaqPage::STATUSES as $key => $val)
                <option value="{{ $key }}">{{ $val }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-3 col-md-4 col-sm-6">
        <div class="mb-4">
            <label class="form-label">Last Updated</label>
            <div class="input-daterange input-group" id="lastUpdatedFilter" data-date-format="mm/dd/yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#lastUpdatedFilter'>
                <input type="text" class="form-control" name="start" placeholder="Start Date" />
                <input type="text" class="form-control" name="end" placeholder="End Date" />
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="faqPagesTable" class="table align-middle table-nowrap table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="sorting sorting_asc">Title</th>
                                <th scope="col" class="sorting">Author</th>
                                <th scope="col" class="sorting">Last Updated</th>
                                <th scope="col" class="sorting">Status</th>
                                <th scope="col" class="td-fit">Action</th>
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
@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/moment/moment.min.js') }}"></script>

<!-- forms init -->
<script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#faq_pages_filter input[name="search_text"]').on('input', function () {
            faqPagesTable.draw();
        });
        
        $('#faq_pages_filter select[name="status"]').on('change', function () {
            faqPagesTable.draw();
        });
        
        $('#faq_pages_filter input[name="start"]').on('change', function () {
            faqPagesTable.draw();
        });
        
        $('#faq_pages_filter input[name="end"]').on('change', function () {
            faqPagesTable.draw();
        });
        
        faqPagesTable = $('#faqPagesTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            paging: true,
            ajax: {
                url: "{{ route('faq.list') }}",
                data: function (d) {
                    let search_text = $('#faq_pages_filter input[name="search_text"]').val();
                    if (search_text) d.search_text = search_text;
                    
                    let status = $('#faq_pages_filter select[name="status"]').val();
                    if (status) d.status = status;
                    
                    let start_date = $('#faq_pages_filter input[name="start"]').val();
                    let end_date = $('#faq_pages_filter input[name="end"]').val();
                    if (start_date && end_date) {
                        d.start_date = start_date;
                        d.end_date = end_date;
                    }
                }
            },
            sDom: "rtpr",
            columns: [
                { data: "title" },
                { data: "user" },
                { data: "updated_at" },
                { data: "status" },
                { data: "action", name: "action", orderable: false, searchable: false }
            ],
            searching: false,
            columnDefs: [
                { targets: 2, render: function (d) {
                        return moment.utc(d).local().format('MM/DD/YYYY HH:mm');
                    }
                },
                { orderable: false, targets: [4] }
            ],
            order: [[1, 'asc']],
            drawCallback: function () {
                $('#faqPagesTable ')
                
                $('#faqPagesTable a.faq-page-del').on('click', function () {
                    if (confirm('Are you sure?')) {
                        $.ajax({
                            url: '{{ route("faq.page_delete", "") }}/' + $(this).data('id'),
                            success: function (data) {
                                faqPagesTable.draw();
                            },
                        });
                    }
                    return false;
                });
            },
        });
    });
</script>
@endsection