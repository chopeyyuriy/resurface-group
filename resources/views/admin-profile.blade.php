@extends('layouts.master')

@section('title') Admin Profile @endsection

@section('css')
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Admins @endslot
        @slot('title') Profile @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal" method="POST" action="{{ route('users.save', ['id' => data_get($user, 'id', 0)]) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', data_get($user, 'name')) }}" id="name" name="name" autofocus
                                   placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" value="{{ old('email', data_get($user, 'email')) }}" name="email"
                                   placeholder="Enter email" autofocus>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password"
                                   placeholder="Enter password">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="active" class="form-label">Status</label>
                            <select class="form-control select2" id="active" name="status">
                                <option value="0" @if(old('status', data_get($user, 'status')) == 0) selected @endif>Unactive</option>
                                <option value="1" @if(old('status', data_get($user, 'status')) == 1) selected @endif>Active</option>
                            </select>
                        </div>

                        <div class="mt-3 d-grid">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

@endsection

@section('script')
@endsection
