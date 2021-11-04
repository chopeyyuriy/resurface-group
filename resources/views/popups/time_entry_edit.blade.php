<!-- New Time Entry Modal -->
<div class="modal fade js-edit-time-entry-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Time Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="form-edit-time-entry" action="{{ route('edit_time_entry') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="">

                <div class="modal-body">
                   <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <div class="input-group input-group-icon-end" id="timeEntryDatepickerEdit">
                                    <input name="date" type="text" class="form-control" placeholder="mm/dd/yyyy"
                                        data-date-format="mm/dd/yyyy" data-date-container='#timeEntryDatepickerEdit'
                                        data-provide="datepicker" data-date-autoclose="true">
                                    <span class="input-group-text input-group-icon bg-transparent border-0"><i class="bx bx-calendar-alt font-size-16"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Hour / Minutes Spent</label>
                                <input name="time" type="text" class="form-control" placeholder="HH:MM">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Clinician</label>
                                <select name="clinicians[]" class="form-control select2" 
                                        {{ Auth::user()->hasRole('admin') ? '' : 'disabled' }}
                                        multiple="" required="">
                                    @foreach(App\Models\Clinician::getSortList() as $row)
                                    <option value="{{ $row->id }}">{{ $row->getNameAttribute() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Project / Client</label>
                                <select name="client" class="form-control select2">
                                    @foreach(App\Models\Client::getSortList() as $row)
                                    <option value="{{ $row->id }}">{{ $row->getNameAttribute() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Activity Type</label>
                                <select name="activity_type" class="form-control select2">
                                    <option value="1">Travel</option>
                                    <option value="2">Session</option>
                                    <option value="3">Virtual Session</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Enter a few words about this entry"></textarea>
                            </div>
                        </div>
                   </div>
                </div>

                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- end: New Time Entry Modal -->