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
                            <div class="col-md-6 id_gaji_pokok">
                                <div class="form-group">
                                    <label for="id_gaji_pokok">ID Gaji Pokok <span class="text-danger">*</span></label>
                                    <input type="text" name="id_gaji_pokok" class="form-control" id="id_gaji_pokok" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_usaha">Unit Usaha <span class="text-danger">*</span></label>
                                    <select name="id_unit_usaha" id="id_unit_usaha" class="form-control" required>
                                        <option value="">-- Pilih Unit Usaha --</option>
                                        <?php foreach ($unit_usaha as $u) : ?>
                                            <option value="<?= $u->id_unit_usaha ?>"><?= $u->nm_unit_usaha ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_kerja">Unit Kerja <span class="text-danger">*</span></label>
                                    <select name="id_unit_kerja" id="id_unit_kerja" class="form-control" required>
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        <?php foreach ($unit_kerja as $k) : ?>
                                            <option value="<?= $k->id_unit_kerja ?>"><?= $k->nm_unit_kerja ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nm_unit_level">Unit Level <span class="text-danger">*</span></label>
                                    <input type="text" name="nm_unit_level" id="nm_unit_level" class="form-control" placeholder="Otomatis" readonly>
                                    <input type="hidden" name="id_unit_level" id="id_unit_level">
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div> -->



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

                                
                                
                                <!-- <input type="hidden" id="id_unit_usaha" name="id_unit_usaha">
                                <input type="hidden" id="id_unit_level" name="id_unit_level"> -->
                            </div>

                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_usaha">Unit Usaha</label>
                                    <input type="text" name="id_unit_usaha" class="form-control" id="id_unit_usaha" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div> -->

                            <input type="hidden" name="id_unit_usaha" class="form-control" id="id_unit_usaha" readonly>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_usaha">Unit Usaha</label>
                                    <input type="text" name="nm_unit_usaha" class="form-control" id="nm_unit_usaha" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_level">Unit Level</label>
                                    <input type="text" name="id_unit_level" class="form-control" id="id_unit_level" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div> -->

                            <input type="hidden" name="id_unit_level" class="form-control" id="id_unit_level" readonly>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_unit_level">Unit Level</label>
                                    <input type="text" name="nm_unit_level" class="form-control" id="nm_unit_level" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nominal">Nominal Gaji Pokok <span class="text-danger">*</span></label>
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