<!-- JAVASCRIPT -->
<script src="{{ URL::asset('assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/metismenu/metismenu.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js')}}"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<script src="{{ URL::asset('/assets/libs/bootstrap-autocomplete/bootstrap-autocomplete.min2.js') }}"></script>
<script>

    $(document).ready(function () {
        $('.basicAutoComplete').autoComplete({
            bootstrapVersion: 5,
            resolver: 'custom',
            formatResult: function (item) {
                return {
                    value: item.id,
                    text: item.text,
                    html: item.text
                };
            },
            events: {
                search: function (keyword, callback) {
                    $.ajax(
                        '/search',
                        {
                            data: {'keyword': keyword}
                        }
                    ).done(function (res) {
                        callback(res.results)
                    });
                }
            }
        }).on('autocomplete.select', function (env, item) {
            window.location.href = item.url;
        });
    });

    $('#change-password').on('submit', function (event) {
        event.preventDefault();
        var Id = $('#data_id').val();
        var current_password = $('#current-password').val();
        var password = $('#password').val();
        var password_confirm = $('#password-confirm').val();
        $('#current_passwordError').text('');
        $('#passwordError').text('');
        $('#password_confirmError').text('');
        $.ajax({
            url: "{{ url('update-password') }}" + "/" + Id,
            type: "POST",
            data: {
                "current_password": current_password,
                "password": password,
                "password_confirmation": password_confirm,
                "_token": "{{ csrf_token() }}",
            },
            success: function (response) {
                $('#current_passwordError').text('');
                $('#passwordError').text('');
                $('#password_confirmError').text('');
                if (response.isSuccess == false) {
                    $('#current_passwordError').text(response.Message);
                } else if (response.isSuccess == true) {
                    setTimeout(function () {
                        window.location.href = "{{ route('root') }}";
                    }, 1000);
                }
            },
            error: function (response) {
                $('#current_passwordError').text(response.responseJSON.errors.current_password);
                $('#passwordError').text(response.responseJSON.errors.password);
                $('#password_confirmError').text(response.responseJSON.errors.password_confirmation);
            }
        });
    });
</script>

<script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@auth
    <script src="{{ URL::asset('/assets/js/pages/chat-observer.js') }}"></script>
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script>
        @if(env('ONESIGNAL_APP_ID'))
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "{{ env('ONESIGNAL_APP_ID') }}",
                safari_web_id: "{{ env('ONESIGNAL_SAFARI_APP_ID') }}",
                notifyButton: {
                    enable: true,
                },
            });
            
            OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                if (isEnabled) {
                    OneSignal.getUserId(function(userId) {
                        $.ajax({
                            method: 'post',
                            url: '{{ route("onesignal.player_id") }}',
                            data: {
                                _token: '{{ csrf_token() }}',
                                player_id: userId,
                            },
                            success: function (data) {
                                console.log(data);
                            },
                            error: function (err) {
                                console.log(err);
                            },
                        });
                    });
                }
            });
        });
        @endif
    </script>
@endauth

@yield('script')

<script src="{{ URL::asset('/assets/js/pages/form-time-entry.js') }}"></script>

<!-- App js -->
<script src="{{ URL::asset('assets/js/app.min.js')}}"></script>

@yield('script-bottom')
