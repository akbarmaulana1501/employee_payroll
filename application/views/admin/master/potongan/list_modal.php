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

                            <div class="col-md-6 id_potongan">
                                <div class="form-group">
                                    <label for="id_potongan">ID Potongan<span class="text-danger">*</span></label>
                                    <input type="text" name="id_potongan" placeholder="Auto" class="form-control" id="id_potongan" readonly>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_potongan">Nama Potongan<span class="text-danger">*</span></label>
                                    <input type="text" name="nama_potongan" placeholder="Masukkan Nama Potongan" class="form-control" id="nama_potongan" required>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipe_potongan">Tipe Potongan<span class="text-danger">*</span></label>
                                    <select name="tipe_potongan" id="tipe_potongan" class="form-control" required>
                                        <option value="">-- Pilih Tipe Potongan --</option>
                                        <option value="persen">Persentase (%)</option>
                                        <option value="fixed">Fixed (Nominal)</option>
                                    </select>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="persentase">Persentase / Nominal<span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" name="persentase" placeholder="Masukkan Persentase atau Nominal" class="form-control" id="persentase" required>
                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="maksimal">Maksimal (boleh kosong)</label>
                                    <input type="number" step="0.01" min="0" name="maksimal" placeholder="Masukkan Batas Maksimal Potongan" class="form-control" id="maksimal">
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
