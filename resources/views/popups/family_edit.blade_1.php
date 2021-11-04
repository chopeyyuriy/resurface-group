<!-- Edit Family Details Modal -->
<div class="modal fade js-edit-family-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="{{ route('erm.update.family') }}" id="form-update-family">
                <input type="hidden" name="id" value="{{ data_get($client, 'family.id', 0) }}" />
                <div class="modal-header">
                    <h5 class="modal-title">Edit Family Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <h4 class="card-title mb-3 pb-2 border-bottom">Administrative controls</h4>
                    <div>
                        <label class="form-label">Family Status</label>
                        <select name="status" class="form-control select2">
                            @foreach(config('client.status') as $idStatus => $valStatus)
                                <option value="{{ $idStatus }}"
                                        @if($idStatus == data_get($client, 'family.status', 0)) selected @endif>{{ $valStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Enter Family Name</label>
                        <input name="title" class="form-control" type="text" placeholder="" value="{{ data_get($client, 'family.title') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Set Location</label>
                        <select name="location" class="form-control select2">
                            @foreach($locationForSelect as $idLocation => $valLocation)
                                <option value="{{ $idLocation }}}"
                                    @if(data_get($client, 'family.location') == $idLocation) selected  @endif>{{ $valLocation }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Set Admission Date</label>
                        <div class="input-group input-group-icon-end" id="editAdmissionDate">
                            <input name="admission" type="text" class="form-control"
                                   data-date-format="mm/dd/yyyy" data-date-container='#editAdmissionDate'
                                   data-provide="datepicker" data-date-autoclose="true" value="{{ data_get($client, 'family.admission') }}">
                            <span class="input-group-text input-group-icon bg-transparent border-0"><i class="bx bx-calendar-alt font-size-16"></i></span>
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
<!-- end: Edit Family Details Modal -->