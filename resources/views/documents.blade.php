@extends('layouts.master')

@section('title') Client details @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/iEdit/iEdit.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Skote @endslot
        @slot('title') Documents @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="card-body">
                        <!--  -->
                        <div class="pb-3 border-bottom mb-2">
                            <div class="row align-items-end pb-1">
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-sm-0">
                                        <label class="form-label">Search document</label>
                                        <div class="input-icon-right-group">
                                            <input class="form-control" type="text" placeholder="By file name" id="search_document_name">
                                            <button class="btn btn-clear">
                                                <span class="bx bx-search-alt font-size-16 align-middle"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div>
                                        <label class="form-label">Filter by Type</label>
                                        <select id="search_document_type" class="form-control select2">
                                            <option value="application" selected>All file types</option>
                                            <option value="application/x-zip-compressed">zip</option>
                                            <option value="application/msword">doc</option>
                                            <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                                docx
                                            </option>
                                            <option value="text/plain">txt</option>
                                            <option value="application/pdf">pdf</option>
                                            <option value="application/vnd.ms-excel">xls</option>
                                            <option value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                                xlsx
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-end pb-1 mt-2">
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-sm-0">
                                        <label class="form-label">Search Clinicians</label>
                                        <div class="input-icon-right-group">
                                            <select name="clinician_id" id="search_document_clinician" class="form-control select2" data-placeholder="Search and select user">
                                                <option>All files</option>
                                                @foreach($clinicians as $clinician)
                                                    <option value="{{ data_get($clinician, 'id', 0) }}">{{ data_get($clinician, 'name') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3 mb-sm-0">
                                        <label class="form-label">Search Clients</label>
                                        <div class="input-icon-right-group">
                                            <select name="id" id="search_document_client" class="form-control select2" data-placeholder="Search and select user">
                                                <option>All files</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ data_get($client, 'id', 0) }}">{{ data_get($client, 'name') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-hover mb-0" id="document-table"
                                   style="width: 100%">
                                <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Date modified</th>
                                    <th scope="col">Size</th>
                                    <th class="td-fit"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end: Tab panes -->
            </div>
        </div>
    </div>

    @include('popups.file_edit')
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/iEdit/iEdit.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-editable/bootstrap-editable.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/moment/moment.min.js') }}"></script>

    <!-- form repeater js -->
    <script src="{{ URL::asset('/assets/libs/jquery-repeater/jquery-repeater.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-repeater.int.js') }}"></script>

    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

    <script>
        $(function () {
            "use strict";
            $("#edit-profile-avatar").change(function (e) {
                var avatar = e.target.files[0];

                if (!iEdit.open(avatar, true, function (res) {
                    $("#profile-avatar").attr("src", res);
                })) {
                    alert("Whoops! That is not an image!");
                }

            });

            //modify buttons style
            $.fn.editableform.buttons =
                '<button id="editableformsave" class="btn btn-success editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button>' +
                '<button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect waves-light"><i class="mdi mdi-close"></i></button>';


            var table = $('#document-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('document.clients') }}",
                    data: function (d) {
                        d.id = $('#search_document_client').val();
                        d.clinician_id = $('#search_document_clinician').val();
                        d.name = $('#search_document_name').val();
                        d.type = $('#search_document_type').val();
                    }
                },
                sDom: "rtipr", //lfrti
                pageLength: "10",
                columns: [
                    {"data": "name", "name": "name"},
                    {"data": "updated_at", "name": "updated_at"},
                    {"data": "size", "name": "size", "type": "num"},
                    {"data": "action", "name": "", "orderable": false, "searchable": false}
                ]
            });

            $('#search_document_name').keyup(function () {
                table.draw();
            });

            $('#search_document_type').change(function () {
                table.draw();
            });

            $('#search_document_client').change(function () {
                table.draw();
            });

            $('#search_document_clinician').change(function () {
                table.draw();
            });

        });
    </script>

    <script>
        $(function () {
            "use strict";
            $(document).on("click", "#editableformsave", function (e) {
                var id = $("#editableformsave").attr("noteid");
                var comment = $.trim($(".note-id-" + id).val());
                $.ajax({
                    url: '/note/update/' + id,
                    method: 'post',
                    dataType: 'json',
                    data: {text: comment},
                    success: function (data) {
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function () {

            $(document).on('click', '.rename-file', function () {

                var document_id = $(this).data('document_id');
                var name = $(this).data('name');

                console.log('name', name);

                $('[name="document_id"]').val(document_id);
                $('#rename_file_name').val(name);

            });


            $("#rename_file_form").submit(function (event) {
                var formData = new FormData($(this)[0]);

                $.ajax({
                    method: 'post',
                    url: '/document/rename/client/' + $('[name="document_id"]').val(),
                    contentType: false,
                    dataType: "json",
                    processData: false,
                    data: formData,
                    success: function (data) {
                        location.reload();
                    },
                    failure: function (errMsg) {
                    }
                });

                event.preventDefault();
            });

        });
    </script>

@endsection
