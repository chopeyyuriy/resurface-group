@extends('layouts.master')

@section('title') Documents @endsection

@section('content')
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Documents</h4>
            </div>
        </div>
    </div>
    <!-- end: Page title -->

    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="card filemanager-sidebar me-md-2">
                    <div class="card-body">

                        <div class="d-flex flex-column h-100">
                            <div class="mb-4">
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <div class="dropdown">
                                            <button class="btn btn-light w-100" type="button" data-bs-toggle="modal"
                                                    data-bs-target=".modalNewFile" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="mdi mdi-plus me-1"></i> Create New File
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list-unstyled categories-list">
                                    @foreach($directories as $directory)
                                        <li>
                                            <div class="custom-accordion">
                                                <a class="text-body fw-medium py-1 d-flex align-items-center collapsed parent-client-document"
                                                   data-bs-toggle="collapse"
                                                   href="#categories-collapse-{{$directory->id}}" role="button"
                                                   aria-expanded="false"
                                                   aria-controls="categories-collapse-{{$directory->id}}">
                                                    <i class="mdi mdi-folder font-size-16 text-warning me-2"></i> {{ $directory->title }}
                                                    <i class="mdi mdi-chevron-up accor-down-icon ms-auto"></i>
                                                </a>
                                                @foreach($directory->childrens as $directoryLevel2)
                                                    <div class="collapse @if(request()->location == $directory->id) show @endif"
                                                         id="categories-collapse-{{$directory->id}}">
                                                        <div class="card border-0 shadow-none ps-2 mb-0">
                                                            <a class="text-body fw-medium py-1 d-flex align-items-center collapsed"
                                                               data-bs-toggle="collapse"
                                                               href="#categories-collapse-{{$directory->id}}{{$directoryLevel2->id}}"
                                                               role="button" aria-expanded="false"
                                                               aria-controls="categories-collapse-{{$directory->id}}{{$directoryLevel2->id}}">
                                                                <i class="mdi mdi-folder font-size-16 text-warning me-2"></i>
                                                                {{ $directoryLevel2->title }} <i
                                                                        class="mdi mdi-chevron-up accor-down-icon ms-auto"></i>
                                                            </a>
                                                            @foreach($directoryLevel2->childrens as $directoryLevel3)
                                                                <div class="collapse @if(request()->children == $directoryLevel2->id) show @endif"
                                                                     id="categories-collapse-{{$directory->id}}{{$directoryLevel2->id}}">
                                                                    <div class="card border-0 shadow-none ps-2 mb-0">
                                                                        <a class="text-body fw-medium py-1 d-flex align-items-center collapsed @if($directoryLevel3->type == 'clinicians') clinicians-document-new @endif"
                                                                           data-id="{{$directoryLevel3->id}}"
                                                                           data-bs-toggle="collapse"
                                                                           href="#categories-collapse-{{$directory->id}}{{$directoryLevel3->id}}"
                                                                           role="button"
                                                                           aria-expanded="false"
                                                                           aria-controls="categories-collapse-{{$directory->id}}{{$directoryLevel3->id}}">
                                                                            <i class="mdi mdi-folder font-size-16 text-warning me-2"></i>
                                                                            {{ $directoryLevel3->title }}
                                                                            @if($directoryLevel3->type !== 'clinicians')
                                                                                <i class="mdi mdi-chevron-up accor-down-icon ms-auto"></i>
                                                                            @endif
                                                                        </a>
                                                                        @foreach($directoryLevel3->childrens as $directoryLevel4)
                                                                            <div class="collapse @if(request()->user == $directoryLevel3->id) show @endif"
                                                                                 id="categories-collapse-{{$directory->id}}{{$directoryLevel3->id}}">
                                                                                <div class="card border-0 shadow-none ps-2 mb-0">
                                                                                    <ul class="list-unstyled mb-0">
                                                                                        <li><a href="#"
                                                                                               data-id="{{$directoryLevel4->client_id}}"
                                                                                               class="d-flex align-items-center client-document"><span
                                                                                                        class="me-auto">{{ $directoryLevel4->title }}</span></a>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                        </div>

                    </div>
                </div>
                <!-- filemanager-leftsidebar -->

                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="row mb-3">
                                    <div class="col-xl-6 col-sm-3">
                                        <div class="mt-2">
                                            <h5>Files</h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-sm-6">
                                        <form class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                            <div class="search-box mb-2 me-2">
                                                <div class="position-relative">
                                                    <input type="text"
                                                           class="form-control bg-light border-light rounded"
                                                           placeholder="By file name" id="search_document_name">
                                                    <i class="bx bx-search-alt search-icon"></i>
                                                </div>
                                            </div>
                                        </form>

                                        <dic class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                            <div class="search-box mb-2 me-2">
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target=".modalNewFile">
                                                    Upload
                                                </button>
                                            </div>
                                        </dic>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <hr class="mt-2">
                                <div class="table-responsive">
                                    <input id="input_client_id" type="hidden" name="client_id">
                                    <input id="input_clinician_id" type="hidden" name="clinician_id">
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
                    </div>
                    <!-- end card -->
                </div>
                <!-- end w-100 -->
            </div>
        </div>

    </div>
    <!-- end row -->

    <!-- New Folder -->
    <div class="modal fade modalNewFolder" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('document.new.folder') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <select id="folder_id" class="form-control">
                                        <option selected disabled>Location</option>
                                        @foreach($directories as $directory)
                                            <option value="{{ $directory->id }}">{{ $directory->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Clients/Clinicians</label>
                                    <select id="clientAndClinician" name="client_folder_id" class="form-control">
                                        <option>None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="blocFamilyAndDoctor col-12">
                                <div class="mb-3">
                                    <label class="form-label">Family/Clinician</label>
                                    <select id="familyAndDoctor" name="folder_id" class="form-control">
                                        <option>None</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Folder Title</label>
                                    <input class="form-control" type="text" name="folder_name" placeholder="Client"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-start">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!-- New File -->
    <div class="modal fade modalNewFile" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="{{ route('document.client.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Location</label>
                                    <select id="file_location_id" class="form-control" name="location_id">
                                        <option selected disabled>Location</option>
                                        @foreach($directories as $directory)
                                            <option value="{{ $directory->id }}">{{ $directory->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Clients/Clinicians <img class="clients_loading" src="{{ URL::asset ('/assets/images/loading.gif') }}" height="20" width="30" style="display: none"></label>
                                    <select id="fileClientAndClinician" class="form-control" name="children_id"></select>
                                </div>
                            </div>
                            <div class="blocFamilyAndDoctor col-12">
                                <div class="mb-3">
                                    <label class="form-label">Family/Clinician <img class="family_loading" src="{{ URL::asset ('/assets/images/loading.gif') }}" height="19" width="30" style="display: none"></label>
                                    <select id="fileFamilyAndDoctor" name="family_clinician"
                                            class="form-control @error('family_clinician') is-invalid @enderror">
                                    </select>
                                </div>
                            </div>
                            <div class="blocClients col-12" style="display: none">
                                <div class="mb-3">
                                    <label class="form-label">Clients <img class="Client_loading" src="{{ URL::asset ('/assets/images/loading.gif') }}" height="19" width="30" style="display: none"></label>
                                    <select id="fileClients" name="user_id"
                                            class="form-control @error('user_id') is-invalid @enderror">
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Attach Document</label>
                                <input type="file" id="newAvatar" name="document" required=""
                                       accept=".txt, .doc, .pdf, .xls, .xlsx, .docx, .jpg, .gif, .png, .ppt, .pptx, .rtf"
                                       style="display: none;">
                                <div class="input-group" style="max-width: 500px;margin-right: 5px;">
                                    <label for="newAvatar" class="form-control"
                                           style="margin:0px;cursor: pointer;padding-right: 26px;">
                                        <span id="documentFileName"></span>
                                        <a id="documentFileNameClear" href="#" style="display:none;"><i
                                                    class="mdi mdi-close-circle-outline"></i></a>
                                    </label>
                                    <label class="btn btn-primary waves-effect waves-light mt-2" for="newAvatar"
                                           style="margin:0px!important;">
                                        <i class="bx bx-upload font-size-16 align-middle"></i> Attach Document
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-start">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    @include('popups.file_edit')
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/moment/moment.min.js') }}"></script>

    <!-- form repeater js -->
    <script src="{{ URL::asset('/assets/libs/jquery-repeater/jquery-repeater.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-repeater.int.js') }}"></script>

    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

    <script>
        $(function () {
            "use strict";

            // File input
            $('[name="document"]').on('change', function () {
                $('#documentFileName').text(this.files[0].name);
                $('#documentFileNameClear').show();
            });

            $('#documentFileNameClear').on('click', function () {
                $('[name="document"]').val('');
                $('#documentFileName').text('');
                $('#documentFileNameClear').hide();
                return false;
            });


            var table = $('#document-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('document.clients') }}",
                    data: function (d) {
                        d.id = $('#input_client_id').val();
                        d.name = $('#search_document_name').val();
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


            $(document).on('click', '.client-document', function (e) {
                e.preventDefault();

                var client_id = $(this).data('id');
                $('#input_client_id').val(client_id);

                table.draw();
            });

            $(document).on('click', '.clinicians-document-new', function (e) {
                e.preventDefault();

                var clinician_id = $(this).data('id');
                $('#input_client_id').val(clinician_id);


                table.draw();
            });

            $('.parent-client-document').click(function () {

                $('#input_client_id').val(0);

                table.draw();
            });

            $('#search_document_name').keyup(function () {
                table.draw();
            });

            var getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = window.location.search.substring(1),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;

                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');

                    if (sParameterName[0] === sParam) {
                        return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                    }
                }
                return false;
            };

            var client = getUrlParameter('client');
            if (client) {

                $('#input_client_id').val(client);
                table.draw();
            }

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


            /* New Folder*/

            $(document).on('click', '#folder_id', function () {

                var folder_id = $(this).val();

                $.ajax({
                    method: 'get',
                    url: '/documents/location-folder/' + folder_id,
                    contentType: false,
                    dataType: "json",
                    success: function (responce) {
                        var folders = responce.folders;

                        var options = '';
                        $.each(folders, function (idx, item) {
                            var html = '<option value="' + item.id + '">' + item.title + '</option>';
                            options += html;
                        });

                        $('#clientAndClinician').html(options);
                    },

                });
            });

            $(document).on('click', '#clientAndClinician', function () {

                var folder_id = $(this).val();

                if (folder_id.length > 0) {

                    $.ajax({
                        method: 'get',
                        url: '/documents/location-folder/' + folder_id,
                        contentType: false,
                        dataType: "json",
                        success: function (responce) {
                            var folders = responce.folders;
                            var type = responce.type;

                            if (type === 'clinicians') {
                                $('.blocFamilyAndDoctor').css('display', 'none');
                            } else {
                                $('.blocFamilyAndDoctor').css('display', 'block');

                                var options = '<option value="0">New Family</option>';
                                $.each(folders, function (idx, item) {
                                    var html = '<option value="' + item.id + '">' + item.title + '</option>';
                                    options += html;
                                });

                                $('#familyAndDoctor').html(options);
                            }


                        }

                    });
                }
            });

            /* New File */

            $(document).on('change', '#file_location_id', function () {

                var folder_id = $(this).val();

                $('.clients_loading').css('display', 'inline');

                $.ajax({
                    method: 'get',
                    url: '/documents/location-folder/' + folder_id,
                    dataType: "json",
                    success: function (responce) {
                        var folders = responce.folders;

                        if (folders.length > 0) {
                            var options = '<option selected disabled>Clients/Clinicians</option>';
                            $.each(folders, function (idx, item) {
                                var html = '<option value="' + item.id + '">' + item.title + '</option>';
                                options += html;
                            });

                            $('#fileClientAndClinician').html(options);
                            $('.clients_loading').css('display', 'none');
                        }
                    },

                });
            });

            $(document).on('change', '#fileClientAndClinician', function () {

                var folder_id = $(this).val();
                $('.family_loading').css('display', 'inline');

                $.ajax({
                    method: 'get',
                    url: '/documents/location-folder/' + folder_id,
                    dataType: "json",
                    success: function (responce) {
                        var folders = responce.folders;
                        var type = responce.type;

                        if (type === 'clinicians') {
                            $('.blocClients').css('display', 'none');
                        }

                        if (folders.length > 0) {

                            var options = '<option selected disabled>Family/Clinician</option>';
                            $.each(folders, function (idx, item) {
                                var html = '<option value="' + item.id + '">' + item.title + '</option>';
                                options += html;
                            });

                            $('#fileFamilyAndDoctor').html(options);
                            $('.family_loading').css('display', 'none');
                        }


                    }

                });
            });

            $(document).on('change', '#fileFamilyAndDoctor', function () {

                var folder_id = $(this).val();
                $('.client_loading').css('display', 'inline');

                $.ajax({
                    method: 'get',
                    url: '/documents/location-folder/' + folder_id,
                    dataType: "json",
                    success: function (responce) {
                        var folders = responce.folders;
                        var type = responce.type;

                        if (type === 'families' && folders.length > 0) {

                            $('.blocClients').css('display', 'block');

                            var options = '';
                            $.each(folders, function (idx, item) {
                                var html = '<option value="' + item.id + '">' + item.title + '</option>';
                                options += html;
                            });

                            $('#fileClients').html(options);
                            $('.client_loading').css('display', 'none');

                        } else {
                            $('.blocClients').css('display', 'none');
                        }
                    }

                });
            });

        });
    </script>

@endsection


