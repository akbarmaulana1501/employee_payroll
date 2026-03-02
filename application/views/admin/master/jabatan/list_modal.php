<?php // VIEW: application/views/admin/master/jabatan/list_modal.php ?>
<div class="modal fade" id="modal_form" role="dialog" aria-hidden="true">
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
                    <input type="hidden" name="id_tunjangan_jabatan" />
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_level">Unit Level<span class="text-danger">*</span></label>
                                    <select name="id_unit_level" class="form-control" required>
                                        <option value="">-- Pilih Unit Level --</option>
                                        <?php foreach ($list_unit_level as $row): ?>
                                            <option value="<?= $row->id_unit_level ?>"><?= $row->nm_unit_level ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_usaha">Unit Usaha<span class="text-danger">*</span></label>
                                    <select name="id_unit_usaha" class="form-control" required>
                                        <option value="">-- Pilih Unit Usaha --</option>
                                        <?php foreach ($list_unit_usaha as $row): ?>
                                            <option value="<?= $row->id_unit_usaha ?>"><?= $row->nm_unit_usaha ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah">Jumlah<span class="text-danger">*</span></label>
                                    <input type="number" name="jumlah" placeholder="Masukkan jumlah" class="form-control" required>
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
