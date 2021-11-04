"use strict";

function removeEvent() {
    var event_id = $("#event_id").val();

    if (event_id) {

        var link = $('#remove_event').data('url');
        if (link) {
            var action = link.replace('%id%', event_id);
            $('#remove_event').attr('action', action);
            $('#remove_event').show();
        }

    } else {
        $('#remove_event').hide();
    }
}

function eventModal(date) {

    $.get('/calendar/event-modal?date=' + date, function (data, e) {


        $('#schedule-add').find('#modal-html').html(data.modal).promise().done(function () {

            removeEvent();

            $('.date-start').timepicker({
                icons: {
                    up: 'mdi mdi-chevron-up',
                    down: 'mdi mdi-chevron-down'
                },

                appendWidgetTo: ".parent-start"
            });

            $('.date-end').timepicker({
                icons: {
                    up: 'mdi mdi-chevron-up',
                    down: 'mdi mdi-chevron-down'
                },

                appendWidgetTo: ".parent-end"
            });

            $(".select2").select2({
                minimumResultsForSearch: 10,
                width: '100%'
            });

            $('#schedule-add').modal('show');
        });
    });
}

function eventModalById(event_id, date) {
    $.get('/calendar/event-modal', {event_id: event_id, date: date},
        function (data, e) {
            $('#schedule-add').find('#modal-html').html(data.modal).promise().done(function () {

                removeEvent();

                $('.date-start').timepicker({
                    icons: {
                        up: 'mdi mdi-chevron-up',
                        down: 'mdi mdi-chevron-down'
                    },

                    appendWidgetTo: ".parent-start"
                });

                $('.date-end').timepicker({
                    icons: {
                        up: 'mdi mdi-chevron-up',
                        down: 'mdi mdi-chevron-down'
                    },

                    appendWidgetTo: ".parent-end"
                });

                $(".select2").select2({
                    minimumResultsForSearch: 10,
                    width: '100%'
                });

                $('#schedule-add').modal('show');
            });
        });
}

var CalendarBasic = function () {

    return {
        //main function to initiate the module
        init: function () {
            var todayDate = moment().startOf('day');
            var TODAY = todayDate.format('YYYY-MM-DD');

            var calendarEl = document.getElementById('schedule-calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['bootstrap', 'interaction', 'dayGrid', 'timeGrid', 'list'],
                themeSystem: 'bootstrap',

                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,timeGridDay'
                },

                height: 800,
                contentHeight: 780,
                aspectRatio: 3,  // see: https://fullcalendar.io/docs/aspectRatio

                nowIndicator: true,
                now: TODAY + 'T09:25:00', // just for demo

                defaultView: 'dayGridMonth',
                defaultDate: TODAY,
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                navLinks: true,
                events: '/calendar/view-events/' + $('#user_id').val(),
                dateClick: function (date, jsEvent, view) {
                    var event_id = $(this).data('id');
                    if (event_id || window.calendarMainPageView) {
                        eventModalById(event_id, date.dateStr);
                    }
                },
                eventRender: function (info) {
                    var element = $(info.el);
                    var id = info.event.id;
                    if (info.event.extendedProps && info.event.extendedProps.description) {
                        $(element).data('id', id);
                        $(element).data('date', info.event.title);
                        
                        if (info.event.extendedProps.event_type == 2) {
                            $(element).addClass('btn-secondary');
                        } else {
                            $(element).addClass('btn-primary');
                        }
                    }
                },
            });

            calendar.render();
            return calendar;
        }
    };
}();

jQuery(document).ready(function () {

    var calendar = CalendarBasic.init();

    $(".close").on('click', function () {
        $("#schedule-add").modal('hide');
    });

    $(document).on('click', '.create-event', function (e) {
        eventModal();
    });

    $(document).on('mouseover', '.fc-day-grid-event', function (e) {
        $(this).css('cursor', 'pointer');
    });

    $(document).on('click', '.fc-day-grid-event', function (e) {
        e.preventDefault();

        $(this).css('cursor', 'pointer');

        var event_id = $(this).data('id');
        eventModalById(event_id);

    });

    $(document).on('change', '#startTimepicker', function (e) {

        var start = $('#startTimepicker').val();
        var end = $('#endTimepicker').val();

        if (end === "") {
            $('#endTimepicker').timepicker({
                defaultTime: moment(start, "hh:mm TT").add(30, 'minutes').format("hh:mm P"),
                icons: {
                    up: 'mdi mdi-chevron-up',
                    down: 'mdi mdi-chevron-down'
                },
            });
        }

    });

    $("#usersTypeFilter").on("select2:select", function (e) {
        window.location.replace(e.params.data.id);
    });

});



