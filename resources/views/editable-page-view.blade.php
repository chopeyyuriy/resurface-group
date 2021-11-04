@extends('layouts.master')

@section('title') Editable Pages @endsection

@section('css')
<style>
    .faq_page_top {
        margin-bottom: 24px;
    }
    
    .faq_page_top > span {
        display: block;
    }
</style>
@endsection

@section('content')    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 id="faqPageTitle" class="mb-sm-0 font-size-18">{{ $item->title }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('faq') }}">FAQ / Support</a></li>
                        <li class="breadcrumb-item active">{{ $item->title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="faq_page_top">
                <span>Author: {{ $item->user->name }}</span>
                <span>Created: {{ \Carbon\Carbon::parse($item->created_at)->format('m/d/Y H:i') }}</span>
                <span>Updated: {{ \Carbon\Carbon::parse($item->updated_at)->format('m/d/Y H:i') }}</span>
                @if($isAuthor)
                <span>Status: {{ $item->status() }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>{!! $item->data !!}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('li.breadcrumb-item a').attr('href', "{{ route('faq') }}");
    });
</script>
@endsection