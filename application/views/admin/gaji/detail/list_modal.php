<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header badge-primary">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form" autocomplete="off">
                <input type="hidden" name="id_gaji_detail" class="id_gaji_detail">
                <input type="hidden" name="id_gaji_header" class="id_gaji_header">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="jenis_komponen">Jenis Komponen <span class="text-danger">*</span></label>
                        <select name="jenis_komponen" id="jenis_komponen" class="form-control" required>
                            <option value="">-- Pilih Jenis Komponen --</option>
                            <option value="Gaji Pokok">Gaji Pokok</option>
                            <option value="Tunjangan">Tunjangan</option>
                            <option value="Insentif">Insentif</option>
                            <option value="Potongan">Potongan</option>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="nama_komponen">Nama Komponen <span class="text-danger">*</span></label>
                        <input type="text" name="nama_komponen" id="nama_komponen" class="form-control" placeholder="Masukkan nama komponen" required>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="jumlah" id="jumlah" class="form-control" placeholder="Masukkan jumlah" required>
                        <span class="help-block text-danger"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="btnSave" onclick="save()" class="btn btn-primary"><i class="fe-save"></i> Simpan</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>