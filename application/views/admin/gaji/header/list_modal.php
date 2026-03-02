<div class="modal fade" id="modal_form" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header badge-primary">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="id_gaji_header" class="id_gaji_header">
                    <div class="form-group">
                        <label for="id_unit_kerja">Unit Kerja</label>
                        <select name="id_unit_kerja" id="id_unit_kerja" class="form-control" required>
                            <option value="">-- Pilih Unit Kerja --</option>
                            <?php foreach ($unit_kerja as $uk): ?>
                                <option value="<?= $uk->id_unit_kerja ?>"><?= $uk->nm_unit_kerja ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_pegawai">Pegawai</label>
                        <select name="id_pegawai" id="id_pegawai" class="form-control" required disabled>
                            <option value="">-- Pilih Pegawai --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="periode">Periode (YYYY-MM) <span class="text-danger">*</span></label>
                        <input type="month" name="periode" class="form-control" required>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label for="status_konfirmasi">Status Konfirmasi <span class="text-danger">*</span></label>
                        <select name="status_konfirmasi" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="btnSave" onclick="save()" class="btn btn-primary"><i class="fe-save"></i> Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Gaji (baru, tambahin di bawah modal_form) -->
<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header badge-info">
                <h4 class="modal-title">Detail Gaji</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="infoPegawaiContainer" class="mb-3">
                    <table class="table table-sm table-borderless">
                        <tbody id="infoPegawai"></tbody>
                    </table>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Jenis Komponen</th>
                            <th>Nama Komponen</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="detailBody">
                        <!-- Data detail akan diisi lewat AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Potongan Detail -->
<div class="modal fade" id="modal_potongan" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header badge-warning">
                <h5 class="modal-title">Detail Potongan Gaji</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Potongan Tetap</h6>
                <table class="table table-bordered table-striped mb-4">
                    <thead>
                        <tr>
                            <th>Nama Potongan</th>
                            <th>Persentase</th>
                            <th>Dasar Hitung</th>
                            <th>Nilai Potongan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="potonganTetapBody">
                        <tr>
                            <td colspan="5" class="text-center">Memuat data potongan tetap...</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mb-3">
                    <button type="button" class="btn btn-success btn-sm" id="btnTambahPotonganModal">
                        <i class="fas fa-plus-circle"></i> Tambah Potongan Opsional
                    </button>
                </div>

                <h6>Potongan Opsional</h6>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama Potongan</th>
                            <th>Persentase</th>
                            <th>Dasar Hitung</th>
                            <th>Nilai Potongan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="potonganOpsionalBody">
                        <tr>
                            <td colspan="5" class="text-center">Memuat data potongan opsional...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Potongan -->
<div id="modalTambahPotongan" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalTambahPotonganLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formTambahPotongan">
            <div class="modal-content">
                <div class="modal-header badge-success">
                    <h5 class="modal-title" id="modalTambahPotonganLabel">Tambah Potongan Opsional</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_gaji_header" id="id_gaji_header">

                    <div class="form-group">
                        <label for="nama_komponen">Nama Potongan</label>
                        <input type="text" class="form-control" id="nama_komponen" name="nama_komponen" placeholder="Misal: Hutang Koperasi" required>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah Potongan (Rp)</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan nominal potongan" min="0" step="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success waves-effect waves-light">Simpan Potongan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Potongan Opsional -->
<div class="modal fade" id="modalEditPotongan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formEditPotongan">
            <input type="hidden" name="id_gaji_detail" id="edit_id_gaji_detail">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Potongan Opsional</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Potongan</label>
                        <input type="text" name="nama_komponen" id="edit_nama_komponen" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Potongan (Rp)</label>
                        <input type="number" name="jumlah" id="edit_jumlah" class="form-control" required min="0" step="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modal_form_detail" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header badge-success">
                <h4 class="modal-title">Tambah Detail Gaji</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="form_detail" autocomplete="off">
                <input type="hidden" name="id_gaji_header" id="id_gaji_header_detail" value="">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="jenis_komponen">Jenis Komponen <span class="text-danger">*</span></label>
                        <select name="jenis_komponen" id="jenis_komponen" class="form-control" required>
                            <option value="">-- Pilih Jenis Komponen --</option>
                            <option value="gapok">Gaji Pokok</option>
                            <option value="tunjangan_tetap">Tunjangan Tetap</option>
                            <option value="insentif">Insentif</option>
                            <option value="potongan">Potongan</option>
                            <option value="potongan_opsional">Potongan Opsional</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_komponen">Nama Komponen <span class="text-danger">*</span></label>
                        <input type="text" name="nama_komponen" id="nama_komponen" class="form-control" placeholder="Masukkan nama komponen" required>
                    </div>

                    <div class="form-group">
                        <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" step="0.01" min="0" class="form-control" placeholder="Masukkan jumlah" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="btnSaveDetail" onclick="saveDetail()" class="btn btn-success">
                        <i class="fe-save"></i> Simpan Detail
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                </div>
            </form>

        </div>
    </div>
</div>