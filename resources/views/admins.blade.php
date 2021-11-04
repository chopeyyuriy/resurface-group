@extends('layouts.master')

@section('title') Admins @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        #users-table_filter {
            text-align: left;
        }
        
        #users-table_filter > label {
            display: flex;
            flex-direction: row;
            max-width: 300px;
            align-items: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center">
                <h4 class="mb-sm-0 font-size-18">Admins</h4>
                <a href="{{ route('users.form') }}" class="btn btn-primary btn-rounded waves-effect waves-light ms-sm-3">
                    <i class="bx bx-user-plus font-size-20 align-middle me-1"></i>Add New
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-hover" id="users-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle sorting sorting_asc">Name</th>
                                    <th class="align-middle sorting">Email</th>
                                    <th class="align-middle sorting">Status</th>
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
    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                processing: false,
                serverSide: true,
                searching: true,
                ajax: "{{ route('users.table') }}",
                sDom: "rftpr",
                columns: [
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "status" },
                    { "data": "action", "name": "action", "orderable": false, "searchable": false }
                ],
                initComplete: function () {
                    $('#users-table_filter .form-control-sm').removeClass('form-control-sm');
                },
            });
        });
    </script>
@endsection
