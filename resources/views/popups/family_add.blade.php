<!-- Add Family Modal -->
<div class="modal fade js-add-family-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="{{ route('erm.add.family') }}" id="form-add-family">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Family</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please enter the family name and location below. You will be redirected to the Main Patient
                        creation page once you save this.</p>
                    <div class="mb-3">
                        <label class="form-label">Enter Family Name</label>
                        <input name="title" class="form-control" required type="text" placeholder="The Smith Family">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Set Location</label>
                        <select name="location" class="form-control select2">
                            @foreach(App\Models\Directories::rootList() as $row)
                                <option value="{{ $row->id }}">{{ $row->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Set Admission Date</label>
                        <div class="input-group input-group-icon-end" id="setAdmissionDate">
                            <input name="admission" type="text" class="form-control"
                                   data-date-format="mm/dd/yyyy" data-date-container='#setAdmissionDate'
                                   data-provide="datepicker" data-date-autoclose="true">
                            <span class="input-group-text input-group-icon bg-transparent border-0"><i
                                        class="bx bx-calendar-alt font-size-16"></i></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div>
<!-- end: Add Family Modal -->
