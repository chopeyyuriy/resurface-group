@extends('layouts.master')

@section('title') Client details @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/iEdit/iEdit.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
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

        .attach_form {
            display: flex;
            flex-direction: row;
            width: 100%;
            justify-content: flex-end;
        }

        #documentFileName {
            text-align: left;
            display: inline-block;
            width: 100%;
        }

        #documentFileNameClear {
            position: relative;
            padding: 0px;
            width: 10px;
        }

        #documentFileNameClear > i {
            position: absolute;
            top: -3px;
            left: 5px;
            font-size: 16px;
        }

        li.select2-results__option[aria-disabled="true"] {
            display: none;
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
        
        .note_find_text {
            background-color: #ffff00;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Client details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('erm') }}">EMR</a></li>
                        <li class="breadcrumb-item">Families</li>
                        <li class="breadcrumb-item">{{ data_get($client, 'family.title') }}</li>
                        <li class="breadcrumb-item active">{{ data_get($client, 'name') }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3">
            <div class="card overflow-hidden">
                <div class="card-body card-bg-img card-bg-img-primary p-3" style="cursor: default;">
                    @hasrole('admin')
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-settings-icon" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <i class="bx bx-cog font-size-22 m-0"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item editfamily-details" href="#" data-bs-toggle="modal"
                               data-bs-target=".js-edit-family-modal" data-id="{{ data_get($client, 'family.id') }}">Edit</a>
                            <a class="dropdown-item deletetask"
                               href="{{ route('erm.delete.family', ['id' => data_get($client, 'family.id')]) }}"
                               onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                    @endhasrole
                    <div>
                        <h4 class="card-title font-size-22 text-white mb-2">{{ data_get($client, 'family.title') }}</h4>
                        <div class="text-white fw-bold"><i
                                    class="bx bx-map font-size-16 align-middle me-1"></i> {{ data_get($client, 'family.locationData.title') }}
                        </div>
                        <div class="text-white fw-bold"><i
                                    class="bx bx-group font-size-16 align-middle me-1"></i> {{ data_get($mainPatient, 'name') }}
                            (+{{ $numFamilyMembers }})
                        </div>
                        <div class="text-white fw-bold"><i
                                    class="bx bx-calendar-alt font-size-16 align-middle me-1"></i> {{ data_get($client, 'family.admission_format') }}
                        </div>
                    </div>
                </div>
                <div class="card-body p-2">
                @foreach($familyMembers as $idFMembers => $valFMembers)
                    <!-- patient -->
                        <div class="d-flex align-items-center p-1 rounded mb-2 @if(data_get($valFMembers, 'id') == data_get($client, 'id')) bg-light @endif"
                             role="button">
                            <div class="flex-shrink-0">
                                @if($valFMembers->photo)
                                <img src="{{ $valFMembers->photo ? '/avatars/crop-32/client/' . data_get($valFMembers, 'photo') : asset('/assets/images/default-user.jpg') }}"
                                     class="avatar-xs rounded-circle">
                                @else
                                <span class="avatar-xs avatar-title rounded-circle text-white avatar-title-notransparent">
                                    {{ Str::upper($valFMembers->name[0]) }}
                                </span>                                
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <a href="{{ route('client.view', ['id' => data_get($valFMembers, 'id')]) }}">
                                    <h4 class="card-title mb-0">{{ data_get($valFMembers, 'name') }}</h4>
                                    <div class="text-muted font-size-11">{{ config('client.relationship.' . data_get($valFMembers, 'relationship_status')) }}&nbsp;</div>
                                </a>
                            </div>
                            {{-- @hasrole('admin')
                            <div class="contact-links flex-grow-0 flex-shrink-0 ms-3">
                                <a href="#" class="client_delete_href" data-id="{{ data_get($valFMembers, 'id', 0) }}"
                                   title="Delete"><i class="mdi mdi-close-circle-outline font-size-20"></i></a>
                            </div>
                            @endhasrole --}}
                        </div>
                        <!-- end: patient -->
                    @endforeach
                    <form method="post" action="{{ route('client.add') }}">
                        @csrf
                        <input type="hidden" name="family_id" value="{{ data_get($client, 'family.id') }}">
                        <input type="hidden" name="relationship_status" value="3">
                        <button type="submit" class="btn btn-secondary waves-effect waves-light col-xl-12"
                                data-bs-toggle="modal" data-bs-target=".js-add-client-modal">Add new family member
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body pb-0">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#demographics" role="tab">
                                <span class="d-block d-sm-none"><i class="bx bx-user"></i></span>
                                <span class="d-none d-sm-block">Demographics</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#clinicalNotes" role="tab">
                                <span class="d-block d-sm-none"><i class="bx bx-edit-alt"></i></span>
                                <span class="d-none d-sm-block">Clinical Notes</span>
                            </a>
                        </li>
                        @if($client->first_name)
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#linkedDocuments" role="tab">
                                    <span class="d-block d-sm-none"><i class="bx bx-folder "></i></span>
                                    <span class="d-none d-sm-block">Forms</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                    <!--  -->

                </div>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="demographics" role="tabpanel">
                        <form class="outer-repeater" method="post"
                              action="{{ route('client.update', ['id' => $client->id]) }}"
                              enctype="multipart/form-data">
                            @csrf
                            @hasrole('admin')
                            <div class="card-body bg-light">
                                <h4 class="card-title mb-3 pb-2 border-bottom">Administrative controls</h4>
                                <!--  -->
                                <div class="row align-items-end">
                                    <div class="col-xxl-3 col-sm-4">
                                        <div class="mb-3 mb-sm-0">
                                            <label class="form-label">Client Status</label>
                                            <select name="status" class="form-control select2">
                                                @foreach(config('client.status') as $idStatus => $valStatus)
                                                    <option value="{{ $idStatus }}"
                                                            @if($idStatus == data_get($client, 'status', 0)) selected @endif>{{ $valStatus }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-lg-4 col-md-6 col-sm-8">
                                        <div>
                                            <label class="form-label">Add new clinician on this case</label>
                                            <select name="clinician[]" class="form-control select2 select2_search"
                                                    data-placeholder="Search and select user" multiple>
                                                <option></option>
                                                @foreach($clinicians as $clinician)
                                                    <option value="{{ data_get($clinician, 'id', 0) }}"
                                                            @if($client->checkClinician(data_get($clinician, 'id', 0))) selected @endif>{{ data_get($clinician, 'name') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                            </div>
                            @endhasrole
                            <div class="card-body">
                                <h4 class="card-title mb-3 pb-2 border-bottom">Clinicians on cases</h4>
                                <div class="row">
                                    @foreach(data_get($client, 'clinician', []) as $clinician)
                                        <div class="col-sm-6 col-md-4 col-xxl-auto mb-3">
                                            <!-- clinician -->
                                            <div class="d-flex align-items-center p-2 rounded shadow-sm h-100">
                                                <div class="flex-shrink-0">
                                                    @if($clinician->photo)
                                                    <img src="{{ data_get($clinician, 'photo') ? '/avatars/crop-32/clinician/' . data_get($clinician, 'photo') : asset('/assets/images/default-user.jpg') }}"
                                                         class="avatar-xs rounded-circle">
                                                    @else
                                                    <span class="avatar-xs avatar-title rounded-circle text-white avatar-title-notransparent">
                                                        {{ Str::upper($clinician->name[0]) }}
                                                    </span> 
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h4 class="card-title mb-0">
                                                        <a href="{{ route('clinician.form', ['id' => data_get($clinician, 'id')]) }}"
                                                           class="text-dark"
                                                           target="_blank">{{ data_get($clinician, 'name') }}</a>
                                                    </h4>
                                                    <div class="text-muted font-size-11">{{ data_get($clinician, 'locationData.full_name') }}</div>
                                                </div>
                                                @hasrole('admin')
                                                <div class="contact-links flex-grow-0 flex-shrink-0 ms-3">
                                                    <a href="{{ route('client.detach.clinician', ['client_id' => $client->id, 'clinician_id' => $clinician->id]) }}"
                                                       title="Delete"><i
                                                                class="mdi mdi-close-circle-outline font-size-20"></i></a>
                                                </div>
                                                @endhasrole
                                            </div>
                                            <!-- end: clinician -->
                                        </div>
                                    @endforeach
                                </div>

                                <h4 class="card-title mb-3 pb-2 border-bottom mt-3">Client information</h4>
                                <div class="row">
                                    <div class="col-md-2 col-xl-2 order-md-2">
                                        <div class="mb-3">
                                            <label class="form-label">Profile Picture</label>
                                            <div class="profile-avatar-edit">
                                                <img src="{{ $client->photo ? '/avatars/crop-600/client/'.data_get($client, 'photo') : asset('/assets/images/default-user.jpg') }}"
                                                     alt="Photo" class="profile-avatar-edit__img" id="profile-avatar">
                                                <span class="avatar-xs avatar-title rounded-circle text-white avatar-photo-notransparent" style="{{ $client->photo ? 'display:none;' : '' }}">
                                                    {{ Str::upper($client->name[0]) }}
                                                </span> 
                                                <label class="profile-avatar-delete__btn" style="display: none;">
                                                    <i class="mdi mdi-close-circle-outline"></i>
                                                </label>
                                                <input type="hidden" name="avatar_delete" value="{{ old('avatar_delete') }}">
                                                <label for="edit-profile-avatar" class="profile-avatar-edit__btn">
                                                    <input name="photo_load" type="file" id="edit-profile-avatar" value="">
                                                    <input name="photo_name" type="hidden" id="edit-profile-avatar-name" value="{{ old('photo_name') }}">
                                                    <input name="photo" type="hidden" id="edit-profile-avatar-data" value="{{ old('photo') }}">
                                                    <span class="bx bx-edit font-size-16 align-middle"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10 col-xl-10 order-md-1">
                                        <div class="row align-items-end">
                                            <div class="col-sm-6 col-xl-7 col-xxl-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Associated Family</label>
                                                    <select name="family_id" class="form-control select2">
                                                        @foreach($families as $family)
                                                            <option value="{{ data_get($family, 'id') }}"
                                                                    data-main-patient="{{ $family->mainPatient('id') }}"
                                                                    @if(data_get($family, 'id') == data_get($client, 'family_id')) selected @endif>{{ data_get($family, 'title') }}
                                                                - {{ data_get($family, 'locationData.title') }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-3 col-xl-5 col-xxl-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Relationship to Identified Patient</label>
                                                    <select name="relationship_status" class="form-control select2"
                                                            data-placeholder="Please Select">
                                                        <option></option>
                                                        @foreach(config('client.relationship') as $idRelationship => $valRelationship)
                                                            <option value="{{ $idRelationship }}"
                                                                    @if(data_get($client, 'relationship_status') == $idRelationship) selected @endif>{{ $valRelationship }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('relationship_status')
                                                    <span class="text-danger">This field is required.</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row align-items-end">
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">First Name</label>
                                                    <input name="first_name" class="form-control" type="text"
                                                           value="{{ old('first_name', data_get($client, 'first_name')) }}">
                                                    @error('first_name')
                                                    <span class="text-danger">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Middle Name</label>
                                                    <input name="middle_name" class="form-control" type="text"
                                                           value="{{ old('middle_name', data_get($client, 'middle_name')) }}">
                                                    @error('middle_name')
                                                    <span class="text-danger">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Last Name</label>
                                                    <input name="last_name" class="form-control" type="text"
                                                           value="{{ old('last_name', data_get($client, 'last_name')) }}">
                                                    @error('last_name')
                                                    <span class="text-danger">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Relationship Status</label>
                                                    <select name="marital_status" class="form-control select2"
                                                            data-placeholder="Please Select">
                                                        <option></option>
                                                        @foreach(config('client.marital_status') as $idMStatus => $valMStatus)
                                                            <option value="{{ $idMStatus }}"
                                                                    @if(data_get($client, 'marital_status') == $idMStatus) selected @endif>{{ $valMStatus }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('marital_status')
                                                    <span class="text-danger">This field is required.</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row align-items-end">
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Date of Birth</label>
                                                    <div class="input-group input-group-icon-end" id="birthDate">
                                                        <input name="date_birth" type="text" class="form-control"
                                                               placeholder="mm/dd/yyyy"
                                                               data-date-format="mm/dd/yyyy"
                                                               data-date-container='#birthDate'
                                                               data-provide="datepicker" data-date-autoclose="true"
                                                               value="{{ old('date_birth', data_get($client, 'date_birth')) }}">
                                                        <span class="input-group-text input-group-icon bg-transparent border-0"><i
                                                                    class="bx bx-calendar-alt font-size-16"></i></span>
                                                    </div>
                                                    @error('date_birth')
                                                    <span class="text-danger">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Admission Date</label>
                                                    <div class="input-group input-group-icon-end" id="admissionDate">
                                                        <input name="admission_date" type="text" class="form-control"
                                                               placeholder="mm/dd/yyyy"
                                                               data-date-format="mm/dd/yyyy"
                                                               data-date-container='#admissionDate'
                                                               data-provide="datepicker" data-date-autoclose="true"
                                                               value="{{ old('admission_date', data_get($client, 'admission_date')) }}">
                                                        <span class="input-group-text input-group-icon bg-transparent border-0"><i
                                                                    class="bx bx-calendar-alt font-size-16"></i></span>
                                                    </div>
                                                    @error('admission_date')
                                                    <span class="text-danger">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Gender</label>
                                                    <select name="gender" class="form-control select2" data-placeholder="Please Select">
                                                        <option></option>
                                                        @foreach(config('client.gender') as $idGender => $valGender)
                                                            <option value="{{ $idGender }}"
                                                                    @if(data_get($client, 'gender') == $idGender) selected @endif>{{ $valGender }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('gender')
                                                    <span class="text-danger">This field is required.</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Race</label>
                                                    <select name="race" class="form-control select2"
                                                            data-placeholder="Please Select">
                                                        <option></option>
                                                        @foreach(config('client.race') as $idRace => $valRace)
                                                            <option value="{{ $idRace }}"
                                                                    @if(data_get($client, 'race') == $idRace) selected @endif>{{ $valRace }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('race')
                                                    <span class="text-danger">This field is required.</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="card-title mb-3 pb-2 border-bottom mt-3">Referred By</h4>
                                <div class="row align-items-end">
                                    <div class="col-sm-6 col-md-4 col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input name="referred_name" class="form-control" type="text"
                                                   value="{{ old('referred_name', data_get($client, 'referred_name')) }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Company</label>
                                            <input name="referred_company" class="form-control" type="text"
                                                   value="{{ old('referred_company', data_get($client, 'referred_company')) }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input name="referred_email" class="form-control" type="email"
                                                   value="{{ old('referred_email', data_get($client, 'referred_email')) }}">
                                            @error('referred_email')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Phone number</label>
                                            <input name="referred_phone" class="form-control" type="text"
                                                   value="{{ old('referred_phone', data_get($client, 'referred_phone')) }}">
                                            @error('referred_phone')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <h4 class="card-title mb-3 pb-2 border-bottom mt-3">Contact Information</h4>
                                <div class="row align-items-end">
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <input name="address" class="form-control" type="text"
                                                   value="{{ old('address', data_get($client, 'address')) }}">
                                            @error('address')
                                            <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="row align-items-end">
                                            <div class="col-sm-4">
                                                <div class="mb-3">
                                                    <label class="form-label">City</label>
                                                    <input name="city" class="form-control" type="text"
                                                           value="{{ old('city', data_get($client, 'city')) }}">
                                                    @error('city')
                                                    <span class="text-danger">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="mb-3">
                                                    <label class="form-label">State</label>
                                                    <select name="state" class="form-control select2"
                                                            data-placeholder="Please Select">
                                                        @foreach($stateForSelect as $idState => $valState)
                                                            <option value="{{ $idState }}"
                                                                    @if(data_get($client, 'state') == $idState) selected @endif >{{ $valState }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Zipcode</label>
                                                    <input name="zipcode" class="form-control" type="text"
                                                           value="{{ old('zipcode', data_get($client, 'zipcode')) }}">
                                                    @error('zipcode')
                                                    <span class="text-danger">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input name="email" class="form-control" type="text"
                                                   value="{{ old('email', data_get($client, 'email')) }}">
                                            @error('email')
                                            <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="w-100"></div>

                                    <div class="col-md-10 col-xxl-5 outer" data-repeater-list="outer-group">
                                    @forelse($phones as $phone)
                                        <!-- phone number -->
                                            <div class="row inner" data-repeater-item>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Phone Number</label>
                                                        <input name="phone" class="form-control" type="tel"
                                                               value="{{ data_get($phone, 'phone') }}">
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
                                                    <input data-repeater-delete type="button"
                                                           class="btn btn-light waves-effect w-100 mb-3 align-self-end inner delete-phone"
                                                           data-id="{{ data_get($phone, 'id') }}" value="Delete"/>
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
                                                    <input data-repeater-delete type="button"
                                                           class="btn btn-light waves-effect w-100 mb-3 align-self-end inner"
                                                           value="Delete"/>
                                                </div>
                                            </div>
                                            <!-- end: phone number -->
                                        @endforelse
                                    </div>

                                    <div class="col-12">
                                        <input data-repeater-create type="button"
                                               class="btn btn-secondary waves-effect waves-light inner"
                                               value="Add new phone number"/>
                                    </div>

                                </div>

                                <!-- Admin Info Footer -->
                                @hasrole('admin')
                                <div class="mt-3 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light me-2">Save
                                        Changes
                                    </button>
                                    <button type="button" class="btn btn-secondary waves-effect waves-light">Cancel
                                    </button>
                                </div>
                                @endhasrole
                                <!-- end: Admin Info Footer -->

                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="clinicalNotes" role="tabpanel">
                        <div class="card-body">
                            <!-- Text area -->
                            <form id="note_add_form" method="post">
                                <div class="mb-3">
                                    <label class="form-label">Add a new clinical note</label>
                                    <textarea name="text" class="form-control" rows="6"></textarea>
                                </div>
                                <div class="row mb-4 pb-1 ">
                                    <div class="col-sm-4">
                                        <div class="input-icon-right-group">
                                            <input class="form-control" id="note_find" value="">
                                            <button type="button" class="btn btn-clear" id="note_find_btn">
                                                <span class="bx bx-search-alt font-size-16 align-middle"></span>
                                            </button>
                                        </div>
                                        <span id="note_find_empty" style="display:none;">No matches found</span>
                                    </div>
                                    <div class="col-sm-8 text-end">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light me-2">Submit</button>
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light">Cancel</button>
                                    </div>
                                </div>
                            </form>
                            <!-- end: Text area -->

                            <!-- Notes -->
                            <ul id="clientNotesList" class="verti-timeline list-unstyled mb-3">
                                @include('client-notes')
                            </ul>
                            <!-- end: Notes -->
                        </div>

                    </div>
                    <div class="tab-pane" id="linkedDocuments" role="tabpanel">
                        <div class="card-body">
                            <!--  -->
                            <div class="pb-3 border-bottom mb-2">
                                <div class="row align-items-end pb-1">
                                    <div class="col-sm-8 col-md-5 col-xl-5 col-xxl-6">
                                        <div class="mb-3 mb-sm-0">
                                            <label class="form-label">Search document</label>
                                            <div class="input-icon-right-group">
                                                <input class="form-control" type="text" placeholder="By file name"
                                                       id="search_document_name">
                                                <button class="btn btn-clear">
                                                    <span class="bx bx-search-alt font-size-16 align-middle"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-3 col-xl-3 col-xxl-2">
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
                            </div>
                            <!--  -->
                            <div class="table-responsive" style="min-height: 300px;">
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

                            <div class="text-sm-end mt-3">
                                <form class="attach_form" method="post" action="{{ route('client.upload.file') }}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ data_get($client, 'id') }}">
                                    <input type="file" id="newAvatar" name="document" onchange="this.form.submit();"
                                           accept=".txt, .doc, .pdf, .xls, .xlsx, .docx, .jpg, .gif, .png, .ppt, .pptx, .rtf"
                                           style="display: none;">

                                        <label class="btn btn-primary waves-effect waves-light mt-2" for="newAvatar"
                                               style="margin:0px!important;">
                                            <i class="bx bx-upload font-size-16 align-middle"></i> Attach Document
                                        </label>

                                </form>
                            </div>

                        </div>

                    </div>
                </div>
                <!-- end: Tab panes -->
            </div>
        </div>
    </div>

    @include('popups.family_edit')
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
            
            @hasrole('admin')
            @else
                $('#demographics input').attr('disabled', '');
                $('#demographics select').attr('disabled', '');
            @endhasrole

            // profile avatar --------------

            $("#edit-profile-avatar").change(function (e) {
                var avatar = e.target.files[0];

                if (!iEdit.open(avatar, true, function (res) {
                    $('.avatar-photo-notransparent').hide();
                    $("#edit-profile-avatar").val('');
                    $("#profile-avatar").attr("src", res);
                    $('#edit-profile-avatar-data').val(res);
                    $('#edit-profile-avatar-name').val(avatar.name);
                    $('input[name="avatar_delete"]').val('');
                    $('.profile-avatar-delete__btn').show();
                })) {
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

            @if(data_get($client, 'photo'))
            $('.profile-avatar-delete__btn').show();
            @else
            $('.profile-avatar-delete__btn').hide();
            @endif

            if ($('input[name="avatar_delete"]').val()) {
                $('.avatar-photo-notransparent').hide();
                $("#profile-avatar").attr("src", "{{ asset('/assets/images/default-user.jpg') }}");
                $('input[name="photo"]').val('');
                $('.profile-avatar-delete__btn').hide();
            } else {
                let p = $('input[name="photo"]').val();
                if (p) {
                    $('.avatar-photo-notransparent').show();
                    $("#profile-avatar").attr("src", p);
                    $('.profile-avatar-delete__btn').show();
                }
            }

            // end. profile avatar -----------

            $('a.client_delete_href').on('click', function () {
                if (confirm('Are you sure?')) {
                    window.location.href = "{{ route('client.delete', '') }}/" + $(this).data('id');
                }
                return false;
            });

            //modify buttons style
            $.fn.editableform.buttons =
                '<button id="editableformsave" class="btn btn-success editable-submit btn-sm waves-effect waves-light"><i class="mdi mdi-check"></i></button>' +
                '<button type="button" class="btn btn-danger editable-cancel btn-sm waves-effect waves-light"><i class="mdi mdi-close"></i></button>';

            var table = $('#document-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('document.client') }}",
                    data: function (d) {
                        d.id = "{{ data_get($client, "id") }}";
                        d.name = $('#search_document_name').val();
                        d.type = $('#search_document_type').val();
                    }
                },
                sDom: "rtipr", //lfrti
                pageLength: "10",
                columns: [
                    {"data": "name", "name": "name"},
                    {"data": "updated_at", "name": "updated_at"},
                    {"data": "size", "name": "size"},
                    {"data": "action", "name": "", "orderable": false, "searchable": false}
                ]
            });

            $('#search_document_name').keyup(function () {
                table.draw();
            });

            $('#search_document_type').change(function () {
                table.draw();
            });

            // main patient
            $('select[name="family_id"]').on('change', function () {
                let mainPatient = $('option[value="' + $(this).val() + '"]', this).data('main-patient');
                if (mainPatient > 0 && mainPatient != {{ $client->id }}) {
                    let rel = $('select[name="relationship_status"]');
                    if (rel.val() == 1) {
                        rel.val(2).trigger('change');
                    }
                    $('select[name="relationship_status"] option[value="1"]').attr('disabled', '');
                } else {
                    $('select[name="relationship_status"] option[value="1"]').removeAttr('disabled');
                }
                $('select[name="relationship_status"]').trigger('change');
            }).trigger('change');
            
            $('#note_find_btn').on('click', function () {
                let phrase = $('#note_find').val();
                
                if (phrase) {
                    phrase = escapeHtml(phrase.trim());
                }
                
                if (phrase) {
                    $('.note_editor').each(function () {
                        let text = $(this).data('index');
                        let reg = new RegExp(phrase, 'i');
                        let a = text.split(reg);
                        
                        let new_text = new Array();
                        let start = 0;
                        for (let i = 0; i < a.length; i++) {
                            start += a[i].length;
                            new_text.push(a[i]);
                            let original_phrase = text.substr(start, phrase.length);
                            if (i < a.length - 1) {
                                new_text.push('<span class="note_find_text">' + original_phrase + '</span>');
                                start += phrase.length;
                            }
                        }
                        
                        $(this).html(new_text.join(''));
                    });
                } else {
                    $('.note_editor').each(function () {
                        $(this).html($(this).data('index'));
                    });
                }
                
                if ($('.note_find_text').length) {
                    $('#note_find_empty').hide();
                    let first = $($('.note_find_text')[0]);
                    let top = first.offset().top;
                    $(document).scrollTop(top - 80);
                } else {
                    $('#note_find_empty').show();
                }
            });
            
            $('#note_find').on('keydown', function (e) {
                if (e.code == 'Enter') {
                    e.preventDefault();
                    $('#note_find_btn').click();
                }
            });
        });
        
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
    
    <script>
        $(function () {
            "use strict";
            $('#note_add_form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    method: 'post',
                    url: '{{ route("note.save", 0) }}',
                    data: {
                        _token: '{{ @csrf_token() }}',
                        clinician_id: {{ \Auth::user()->userable_id }},
                        client_id: {{ data_get($client, 'id') }},
                        text: $('#note_add_form textarea[name="text"]').val(),
                    },
                    success: function (data) {
                        $('#note_add_form textarea[name="text"]').val('').trigger('input');
                        $('#clientNotesList').html(data);
                        clientNotesInit();
                    },
                    error: function (err) {
                        console.log(err);
                    },
                });
            });
            
            $('#note_add_form textarea[name="text"]').on('input', function () {
                if ($(this).val()) {
                    $('#note_add_form button[type="submit"]').removeClass('disabled');
                } else {
                    $('#note_add_form button[type="submit"]').addClass('disabled');
                }
            }).trigger('input');
        });
        
        function clientNotesInit() {
            $('.note_edit_btn').on('click', function () {            
                $('#clinicalNote-' + $(this).data('id')).editable('toggle');
                $("#editableformsave").attr("noteid", $(this).data('id'));
                return false;
            });
            
            $('.note_editor').each(function () {
                $(this).editable({
                    showbuttons: 'bottom',
                    mode: 'inline',
                    inputclass: 'form-control note-id-' + $(this).data('id'),
                    toggle: 'manual'
                });
                $(this).data('index', $(this).html());
            });
            
            $('.note_delete_btn').on('click', function () {
                let id = $(this).data('id');
                if (confirm('Are you sure?')) {
                    $.ajax({
                        url: '{{ route("note.delete", "") }}/' + id,
                        success: function (data) {
                            $('#clientNotesList').html(data);
                            clientNotesInit();
                        },
                        error: function (err) {
                            console.log(err);
                        },
                     });
                }
            });
        }
        
        clientNotesInit();
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
                    data: {text: comment},
                    success: function (data) {
                        $('#clientNotesList').html(data);
                        clientNotesInit();
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function () {
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
            // end. File input

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

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#newFile").change(function () {
                readURL(this);
            });
        });
    </script>

    <script>
        $("input[name='document']").change(function () {
            this.form.submit();
        });
    </script>



@endsection
