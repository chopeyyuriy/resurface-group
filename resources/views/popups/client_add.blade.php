<!-- Add Client Modal -->
<div class="modal fade js-add-client-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('client.add') }}" id="form-create-client">
                @csrf
                <div class="modal-body">
                    <p>Client needs to be associated to an existing family. Please select an existing family record below or <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target=".js-add-family-modal">create a new family record</a>. You will be redirected to the client detail page once you save this.</p>
                    <div class="mb-3">
                        <label class="form-label">Family Name</label>
                        <select name="family_id" class="form-control select2" data-placeholder="Search / Select" data-minimum-results-for-search="1">
                            @foreach($familyAll as $v_familyAll)
                            <option value="{{ data_get($v_familyAll, 'id') }}" data-main-patient="{{ $v_familyAll->mainPatient('id') }}">{{ data_get($v_familyAll, 'title') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relationship to Main Patient</label>
                        <select name="relationship_status" class="form-control select2" data-placeholder="Please Select" required="">
                            <option></option>
                            @foreach(config('client.relationship') as $idRelationship => $valRelationship)
                            <option value="{{ $idRelationship }}">{{ $valRelationship }}</option>
                            @endforeach
                        </select>
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
<!-- end: Add Client Modal -->
