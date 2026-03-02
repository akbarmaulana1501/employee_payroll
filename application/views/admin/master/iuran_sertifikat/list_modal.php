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
                            <div class="col-md-6 id_iuran_sertifikat">
                                <div class="form-group">
                                    <label for="id_iuran_sertifikat">ID Iuran Sertifikat <span class="text-danger">*</span></label>
                                    <input type="text" name="id_iuran_sertifikat" class="form-control" id="id_iuran_sertifikat" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_pegawai">Pegawai <span class="text-danger">*</span></label>
                                    <select name="id_pegawai" id="id_pegawai" class="form-control select2" required>
                                        <option value="">-- Pilih Pegawai --</option>
                                        <?php foreach ($pegawai as $p): ?>
                                            <option value="<?= $p->id_pegawai ?>"><?= $p->nama ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <span class="help-block text-danger"></span>
                                </div>
                                <input type="hidden" id="id_unit_usaha" name="id_unit_usaha">
                                <input type="hidden" id="id_unit_level" name="id_unit_level">
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nominal">Nominal Iuran Sertifikat <span class="text-danger">*</span></label>
                                    <input type="text" name="nominal" class="form-control" id="nominal" placeholder="Masukkan nominal" required>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary"><i class="fe-save"></i> Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
