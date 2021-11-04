<!-- Edit File Modal -->
<div class="modal fade js-rename-file-modal" role="dialog" aria-hidden="true">
    <form id="rename_file_form" action="" method="POST">
        <input type="hidden" name="document_id" value="">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename file</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Name</label>
                        <input id="rename_file_name" class="form-control" name="name" placeholder="file-name" type="text" value="" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" id="rename_file_btn" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div>
<!-- end: Edit File Modal -->
