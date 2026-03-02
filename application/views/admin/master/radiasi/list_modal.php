<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header badge-primary">
                <h4 class="modal-title mt-0"></h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal" onsubmit="return false;">
                    <input type="hidden" name="id_tunjangan" />

                    <div class="form-group">
                        <label>Unit Usaha</label>
                        <select name="id_unit_usaha" class="form-control">
                            <option value="">-- Pilih Unit Usaha --</option>
                            <?php foreach ($list_unit_usaha as $row): ?>
                                <option value="<?= $row->id_unit_usaha ?>"><?= $row->nm_unit_usaha ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Unit Kerja Sub</label>
                        <select name="id_unit_kerja_sub" class="form-control">
                            <option value="">-- Pilih Unit Kerja Sub --</option>
                            <?php foreach ($list_unit_kerja_sub as $row): ?>
                                <option value="<?= $row->id_unit_kerja_sub ?>"><?= $row->nm_unit_kerja_sub ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="text" name="tunjangan" id="tunjangan" class="form-control" placeholder="Masukkan Nominal Tunjangan">
                        <span class="help-block text-danger"></span>
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