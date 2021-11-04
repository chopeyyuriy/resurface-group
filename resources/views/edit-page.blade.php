@extends('layouts.master')

@section('title') Edit - Page Title @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 id="faqPageTitle" class="mb-sm-0 font-size-18">This Page Title</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('faq') }}">FAQ / Support</a></li>
                        <li class="breadcrumb-item active">Edit page</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="form_faq_page_edit" method="post" action="{{ route('faq.page_edit', $item->id) }}">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-sm-9 col-xxl-10 text-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light mb-3 me-2">Save</button>
                                <a href="{{ route('faq') }}" class="btn btn-secondary waves-effect waves-light mb-3 me-2">Cancel</a>
                            </div>
                            <div class="col-sm-3 col-xxl-2">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control select2">
                                    @foreach(App\Models\FaqPage::STATUSES as $key => $val)
                                    <option value="{{ $key }}" {{ $item->status == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" name="title" required="" type="text" maxlength="250" placeholder="Page Title" value="{{ $item->title }}">
                        </div>
                        <textarea id="elm1" name="data">{!! $item->data !!}</textarea>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <!--tinymce js-->
    <script src="{{ URL::asset('/assets/libs/tinymce/tinymce.min.js') }} "></script>
    <!-- forms init -->
    <script src="{{ URL::asset('/assets/js/pages/forms.init.js') }}"></script>

    <script>
    $(document).ready(function () {
        $('#form_faq_page_edit input[name="title"]').on('input', function () {
            $('#faqPageTitle').text($(this).val());
        }).trigger('input');
        
        if($("#elm1").length > 0){
            tinymce.init({
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                selector: "textarea#elm1",
                height: 530,
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor"
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
                style_formats: [
                    {title: 'Bold text', inline: 'b'},
                    {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                    {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                    {title: 'Example 1', inline: 'span', classes: 'example1'},
                    {title: 'Example 2', inline: 'span', classes: 'example2'},
                    {title: 'Table styles'},
                    {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                ],
                images_upload_handler: function (blobInfo, success, failure) {
                    var xhr, formData;
                    xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', "{{ route('faq.page_upload_file') }}");
                    var token = '{{ csrf_token() }}';
                    xhr.setRequestHeader("X-CSRF-Token", token);
                    xhr.onload = function() {
                        var json;
                        if (xhr.status != 200) {
                            failure('HTTP Error: ' + xhr.status);
                            return;
                        }
                        json = JSON.parse(xhr.responseText);

                        if (!json || typeof json.location != 'string') {
                            failure('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        success(json.location);
                    };
                    formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                },
            });
        }

        $('.note-toolbar  [data-toggle=dropdown]').attr("data-bs-toggle", "dropdown");
    });
    </script>
@endsection
