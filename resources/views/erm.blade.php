@extends('layouts.master')

@section('title') EMR @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .avatar-title-notransparent {
            width: 2rem!important;
            height: 2rem!important;
            background-color: #2a6dc0!important;
            font-weight: 500;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center">
                <h4 class="mb-sm-0 font-size-18">EMR</h4>
                @hasrole('admin')
                <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light ms-sm-3" data-bs-toggle="modal" data-bs-target=".js-add-client-modal">
                    <i class="bx bx-user-plus font-size-20 align-middle me-sm-1"></i><span class="d-none d-sm-inline">Add New Client</span>
                </button>
                <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light ms-2 ms-sm-3" data-bs-toggle="modal" data-bs-target=".js-add-family-modal">
                    <i class="bx bx-group font-size-20 align-middle me-sm-1"></i><span class="d-none d-sm-inline">Add New Family</span>
                </button>
                @endhasrole
            </div>
        </div>
    </div>
    <form method="post" action="{{ route('erm') }}">
        @csrf
        <div class="row align-items-end">
            <div class="col-xxl-4 col-lg-3 col-sm-8">
                <div class="mb-4">
                    <label class="form-label">Search</label>
                    <div class="input-icon-right-group">
                        <input name="title" class="form-control" type="text" value="{{ request('title') }}" placeholder="By Family Name or Client Name">
                        <button class="btn btn-clear">
                            <span class="bx bx-search-alt font-size-16 align-middle"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-100 d-lg-none"></div>
            <div class="col-xxl-2 col-lg-3 col-4">
                <div class="mb-4">
                    <label class="form-label">Filter by Location</label>
                    <select name="location" class="form-control select2">
                        <option value="0">All Locations</option>
                        @foreach(App\Models\Directories::rootList() as $row)
                        <option value="{{ $row->id }}" {{ request('location') == $row->id ? 'selected' : '' }}>{{ $row->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xxl-2 col-lg-3 col-4">
                <div class="mb-4">
                    <label class="form-label">Filter by Status</label>
                    <select name="status" class="form-control select2">
                        <option value="0" @if(request('status') == 0) checked @endif>All Statuses</option>
                        @foreach(config('client.status') as $key => $val)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xxl-2 col-lg-3 col-4">
                <div class="mb-4">
                    <label class="form-label">Admission Date</label>
                    <div class="input-group input-group-icon-end" id="admissionDateFilter">
                        <input name="admission" type="text" class="form-control" placeholder="mm/dd/yyyy"
                            data-date-format="mm/dd/yyyy" data-date-container='#admissionDateFilter'
                            data-provide="datepicker" data-date-autoclose="true"
                            value="{{ request('admission') }}">
                        <span class="input-group-text input-group-icon bg-transparent border-0"><i class="bx bx-calendar-alt font-size-16"></i></span>
                    </div>
                </div>
            </div>

            <div class="col-xxl-2 col-lg-3 col-4">
                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">Show</button>
                    <a href="{{ route('erm') }}" class="btn btn-warning">Reset</a>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        @foreach($family as $v_family)
            <!-- Patient card -->
            <div class="col-sm-6 col-md-4 col-ld-3 col-xxl-2">
                <div class="card overflow-hidden"> <!-- card-disabled -->
                    @php
                        $clients = $v_family->clients()->orderBy('relationship_status')->get();
                        $firstClientId = (count($clients) ? $clients[0]->id : false);
                    @endphp
                    
                    <div class="card-body card-bg-img card-bg-img-primary @if($v_family->status === 3) card-bg-img-disabled @else card-bg-img-primary @endif p-3"
                         data-id="{{ $firstClientId }}">
                        @hasrole('admin')
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-settings-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-cog font-size-22 m-0"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item editfamily-details" href="#" data-bs-toggle="modal" data-bs-target=".js-edit-family-modal" data-id="{{ data_get($v_family, 'id') }}">Edit</a>
                                <a class="dropdown-item delete-alert" href="{{ route('erm.delete.family', ['id' => data_get($v_family, 'id')]) }}" onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                        @endhasrole
                        <div>
                            <h4 class="card-title font-size-22 text-white mb-3"><div class="text-white">{{ data_get($v_family, 'title') }}</div></h4>
                            <div class="text-white fw-bold"><i class="bx bx-map font-size-16 align-middle me-1"></i> {{ data_get($v_family, 'locationData.title') }}</div>
                            <div class="text-white fw-bold"><i class="bx bx-group font-size-16 align-middle me-1"></i> {{ data_get($v_family->mainPatient(), 'name') }} (+{{ $v_family->numFamilyMembers() }})</div>
                            <div class="text-white fw-bold"><i class="bx bx-calendar-alt font-size-16 align-middle me-1"></i> {{ data_get($v_family, 'admissionFormat') }}</div>
                        </div>
                    </div>

                    @if(!empty($clients) && count($clients) > 0)
                        <div class="card-body p-3">
                            <div class="avatar-group float-start task-assigne">
                                @foreach($clients as $client)
                                    <div class="avatar-group-item" style="font-size: 0px;">
                                        <a href="{{ route('client.view', ['id' => $client->id]) }}" class="d-inline-block" value="member-4">
                                            @if($client->photo)
                                            <img src="{{ data_get($client, 'photo') ? '/avatars/crop-32/client/' . data_get($client, 'photo') : asset('/assets/images/default-user.jpg') }}" alt="" class="rounded-circle avatar-xs">
                                            @else                                            
                                            <span class="avatar-xs avatar-title rounded-circle text-white avatar-title-notransparent">
                                                {{ Str::upper($client->name[0]) }}
                                            </span>
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @php
                            unset($clients);
                        @endphp
                    @endif
                </div>
            </div>
            <!-- end: Patient card -->
        @endforeach
    </div>

    @include('popups.client_add')
    @include('popups.family_add')
    @include('popups.family_edit')
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>
    
    <script>
        $(document).ready(function () {
            $('.card-bg-img-primary').on('click', function (e) {
                if ($(e.target).hasClass('bx bx-cog font-size-22 m-0')) return ;
                if ($(this).data('id')) {
                    window.location.href = "{{ route('client.view', '') }}/" + $(this).data('id');
                }
            });
        });
    </script>
@endsection
