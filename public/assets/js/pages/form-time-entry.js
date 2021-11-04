$(document).ready(function () {
    $("#form-new-time-entry .select2").select2({
        minimumResultsForSearch: 10,
        width: '100%'
    });    
    
    $('#form-new-time-entry input[name="time"], #form-edit-time-entry input[name="time"]').inputmask('99:99');
    
    $('.js-new-time-entry-modal').on('show.bs.modal', function () {
        $('#form-new-time-entry input[name="time"]').val('');
        //$("#form-new-time-entry select").val('').trigger('change');
        $("#form-new-time-entry textarea").val('');
                
        if ($('#form-new-time-entry select[name="clinicians[]"]').data('admin')) {
            //
        } else {
            let val = $('#form-new-time-entry select[name="clinicians[]"] option').val();
            let a = new Array();
            a.push(val);
            console.log(val);
            $('#form-new-time-entry select[name="clinicians[]"]').val(val).trigger('change');
        }
        
        $('#form-new-time-entry input[name="date"]').datepicker().datepicker("setDate", new Date());
    });
    
    $("#form-new-time-entry").submit(function (event) {
        var formData = new FormData($(this)[0]);

        $.ajax({
            method: 'post',
            url: $(this).attr('action'),
            contentType: false,
            dataType: "json",
            processData: false,
            data: formData,
            success: function (data) {
                $(".js-new-time-entry-modal").modal('hide');
                if (location.href.indexOf('time-reporting') > 0) {
                    timeReportTable.draw();
                } else {
                    location.reload();
                }
            },
            failure: function (errMsg) {
                console.log(errMsg);
            }
        });

        event.preventDefault();
    });
    
    $("#form-edit-time-entry").submit(function (event) {
        var formData = new FormData($(this)[0]);

        $.ajax({
            method: 'post',
            url: $(this).attr('action'),
            contentType: false,
            dataType: "json",
            processData: false,
            data: formData,
            success: function (data) {
                $(".js-edit-time-entry-modal").modal('hide');
                timeReportTable.draw();
            },
            failure: function (errMsg) {
                console.log(errMsg);
            }
        });

        event.preventDefault();
    });
});