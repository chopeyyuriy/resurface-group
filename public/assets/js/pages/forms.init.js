/******/
(function () { // webpackBootstrap
    /*!******************************************!*\
      !*** ./resources/js/pages/forms.init.js ***!
      \******************************************/
    $(function () {
        "use strict";

        $(".select2").select2({
            minimumResultsForSearch: 10,
            width: '100%'
        });

        $(".delete-alert").on('click', function() {
            return confirm('Are you sure?');
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#form-add-family, #form-update-family, #form-create-client").submit(function (event) {
            var formData = new FormData($(this)[0]);

            $.ajax({
                method: 'post',
                url: $(this).attr('action'),
                contentType: false,
                dataType: "json",
                processData: false,
                data: formData,
                success: function (data) {
                    location.reload();
                    console.log(data);
                },
                failure: function (errMsg) {
                    console.log(errMsg);
                }
            });

            event.preventDefault();
        });
        
        $('.js-add-client-modal').on('show.bs.modal', function () {
            $('#form-create-client select[name="family_id"]').on('change', function () {
                let mainPatient = $('option[value="' + $(this).val() + '"]', this).data('main-patient');
                if (mainPatient > 0) {
                    let rel = $('#form-create-client select[name="relationship_status"]');
                    if (rel.val() == 1) {
                        rel.val('').trigger('change');
                    }
                    rel.trigger('change');
                    $('#form-create-client select[name="relationship_status"] option[value="1"]').attr('disabled', '');
                } else {
                    $('#form-create-client select[name="relationship_status"] option[value="1"]').removeAttr('disabled');
                }
                $('#form-create-client select[name="relationship_status"]').trigger('change'); 
            }).trigger('change');
        });

        $(".delete-phone").on('click', function() {
            $.ajax({
                method: 'post',
                url: "/phone-delete/" + $(this).attr("data-id"),
                success: function (data) {
                    console.log(data);
                },
                failure: function (errMsg) {
                    console.log(errMsg);
                }
            });
        });

        $(".editfamily-details").on('click', function() {
            $.ajax({
                method: 'post',
                url: "/erm/json-data/" + $(this).attr("data-id"),
                success: function (data) {
                    $('#form-update-family input[name="id"]').val(data.data.id);
                    $('#form-update-family select[name="status"]').val(data.data.status).trigger('change');
                    $('#form-update-family input[name="title"]').val(data.data.title);
                    $('#form-update-family select[name="location"]').val(data.data.location).trigger('change');
                    $('#form-update-family input[name="admission"]').val(data.data.admission);
                },
                failure: function (errMsg) {
                    console.log(errMsg);
                }
            });
        });

    });
    /******/
})()
;
