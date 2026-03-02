<div class="modal fade" id="modal_form" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header badge-primary">
                <h4 class="modal-title mt-0"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-body">
                        <div class="row">

                            <div class="col-md-6 id_tunjangan">
                                <div class="form-group">
                                    <label for="id_tunjangan">ID Tunjangan<span class="text-danger">*</span></label>
                                    <input type="text" name="id_tunjangan" placeholder="Auto" class="form-control" id="id_tunjangan" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_tunjangan">Nama Tunjangan<span class="text-danger">*</span></label>
                                    <input type="text" name="nama_tunjangan" placeholder="Masukkan Nama Tunjangan" class="form-control" id="nama_tunjangan" required>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary"><i class="fe-save"> </i> Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>