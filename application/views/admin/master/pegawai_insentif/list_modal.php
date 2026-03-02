<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header badge-primary">
                <h4 class="modal-title mt-0"></h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal" onsubmit="return false;">
                    <input type="hidden" name="id_pegawai_insentif">



                    <div class="form-group">
                        <label for="id_pegawai">Pegawai <span class="text-danger">*</span></label>
                        <select name="id_pegawai" id="id_pegawai" class="form-control" required>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($list_pegawai as $p): ?>
                                <option value="<?= $p->id_pegawai ?>">
                                    <?= $p->nama ?>
                                </option>
                            <?php endforeach ?>
                        </select>

                        <span class=" help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="id_tunjangan">Insentif <span class="text-danger">*</span></label>
                        <select name="id_tunjangan" id="id_tunjangan" class="form-control" required>
                            <option value="">-- Pilih Insentif --</option>
                            <?php foreach ($list_insentif as $t): ?>
                                <option value="<?= $t->id_tunjangan ?>"><?= $t->nm_tunjangan_insentif ?></option>
                            <?php endforeach ?>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Nominal</label>
                        <input type="text" name="nominal" id="nominal" class="form-control" placeholder="Masukkan nominal">
                        <span class="help-block text-danger"></span>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">
                    <i class="fe-save"></i> Save
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>