<div class="modal-header">
    <h5 class="modal-title">
        <label class="form-label">Event Name</label>
        <input type="text" form="form-new-event" name="subject" class="form-control" required
               value="{{ $event->subject }}">
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="form-new-event" action="{{ route('calendar.add') }}" method="POST">
        @csrf
        <input id="clinician_id" type="hidden" name="clinician_id" value="{{ Auth::id() }}">
        <input id="event_id" type="hidden" name="event_id" value="{{ $event->id }}">

        <div class="row">
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <div class="input-group input-group-icon-end" id="eventDate">
                        <input type="text" id="event-date" class="form-control" placeholder="mm/dd/yyyy"
                               data-date-format="mm/dd/yyyy" data-date-container='#eventDate' data-provide="datepicker"
                               data-date-autoclose="true" name="date" value="{{ date('m/d/Y', strtotime($event->date ? $event->date : date('m/d/Y'))) }}" required>
                        <span class="input-group-text input-group-icon bg-transparent border-0"><i
                                    class="bx bx-calendar-alt font-size-16"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">Event Type</label>
                    <select class="form-control select2" name="type" required>
                        <option value="1" @if($event->type == 1) selected @endif>Business</option>
                        <option value="2" @if($event->type == 2) selected @endif >Session</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">Start</label>
                    <div class="input-group input-group-icon-end timepicker-parent parent-start"
                         id="startTimepickerGroup">
                        <input type="text" class="form-control date-start" id="startTimepicker" name="from" required
                               value="{{ $event->from }}">
                        <span class="input-group-text input-group-icon bg-transparent border-0"><i
                                    class="bx bx-time-five font-size-16"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">End</label>
                    <div class="input-group input-group-icon-end timepicker-parent parent-end" id="endTimepickerGroup">
                        <input type="text" class="form-control date-end" id="endTimepicker" name="to" required
                               value="{{ $event->to }}">
                        <span class="input-group-text input-group-icon bg-transparent border-0"><i
                                    class="bx bx-time-five font-size-16"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" required value="{{ $event->location }}">
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Host</label>
                    <select class="form-control select2" name="host_id" @if(Auth::user()->hasRole('admin')) @else disabled @endif required>
                        @foreach($clinicians_hosts as $host)
                            @if($event_type == 'old_event')
                                <option value="{{ $host->id }}" @if($event->host_id == $host->id) selected @endif>{{ $host->name }}</option>
                            @else
                                <option value="{{ $host->id }}" @if(Auth::user()->userable_id == $host->id) selected @endif>{{ $host->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Participants</label>
                    <select class="form-control select2" name="participants_id[]" title="Participants" multiple
                            required>
                        @foreach($clinicians as $clinician)
                            <option value="{{ $clinician['id'] }}"
                                    @if(isset($clinician['selected'])) selected @endif>{{ $clinician['first_name'] . ' ' . $clinician['last_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Commentary</label>
                    <textarea name="commentary" class="form-control" rows="4" placeholder="Enter a few words about this entry" required>{{ $event->commentary }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between;">
                        <label class="form-label">Notes</label>
                        <a id="notes_button" href="#" style="margin: -4px 0;">
                            <i class="mdi mdi-chevron-down accor-down-icon ms-auto" style="font-size: 20px;"></i>
                        </a>
                    </div>
                    <textarea name="notes" class="form-control" style="{{ $event->notes ? '' : 'display: none;' }}" rows="4" placeholder="Enter a few words about this entry">{{ $event->notes }}</textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('#notes_button').on('click', function () {
            if ($('#form-new-event textarea[name="notes"]').css('display') == 'none') {
                $('#form-new-event textarea[name="notes"]').show();
            } else {
                $('#form-new-event textarea[name="notes"]').hide();
            }
            eventNotesUpdateButton();
            
            return false;
        });
        eventNotesUpdateButton();
    });
    
    function eventNotesUpdateButton() {
        if ($('#form-new-event textarea[name="notes"]').css('display') == 'none') {
            $('#notes_button i').removeClass('mdi-chevron-up');
            $('#notes_button i').addClass('mdi-chevron-down');
        } else {
            $('#notes_button i').removeClass('mdi-chevron-down');
            $('#notes_button i').addClass('mdi-chevron-up');
        }
    }
</script>