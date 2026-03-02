<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header badge-primary">
                <h4 class="modal-title mt-0"></h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal" onsubmit="return false;">
                    <input type="hidden" name="id_pegawai_penggajian">

                    <!-- Unit Usaha -->
                    <div class="form-group">
                        <label>Unit Usaha</label>
                        <select name="id_unit_usaha" id="id_unit_usaha" class="form-control" required>
                            <option value="">-- Pilih Unit Usaha --</option>
                            <?php foreach ($unit_usaha as $u): ?>
                                <option value="<?= $u->id_unit_usaha ?>"><?= $u->nm_unit_usaha ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <!-- Unit Kerja -->
                    <div class="form-group">
                        <label>Unit Kerja</label>
                        <select name="id_unit_kerja" id="id_unit_kerja" class="form-control" required>
                            <option value="">-- Pilih Unit Kerja --</option>
                            <?php foreach ($unit_kerja as $k): ?>
                                <option value="<?= $k->id_unit_kerja ?>"><?= $k->nm_unit_kerja ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Pegawai</label>
                        <select name="id_pegawai" id="id_pegawai" class="form-control" required>
                            <option value="">-- Pilih Pegawai --</option>
                        </select>

                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Eselon</label>
                        <input type="text" name="id_eselon" id="id_eselon" class="form-control" readonly>
                    </div>


                    <div class="form-group">
                        <label>Gaji Pokok</label>
                        <input type="text" name="gaji_pokok_view" id="gaji_pokok_view" class="form-control" readonly>
                        <input type="hidden" name="id_gaji_pokok" id="id_gaji_pokok">
                    </div>

                    <div class="form-group">
                        <label>Tunjangan Fungsional</label>
                        <input type="text" name="tunjangan_fungsional_view" id="tunjangan_fungsional_view" class="form-control" readonly>
                        <input type="hidden" name="id_tunjangan_fungsional" id="id_tunjangan_fungsional">
                    </div>


                    <div class="form-group">
                        <label>Tunjangan Kesra</label>
                        <input type="text" name="tunjangan_kesra_view" id="tunjangan_kesra_view" class="form-control" readonly>
                        <input type="hidden" name="id_tunjangan_kesra" id="id_tunjangan_kesra">
                    </div>

                    <div class="form-group">
                        <label>Tunjangan Komunikasi</label>
                        <input type="text" name="tunjangan_komunikasi_view" id="tunjangan_komunikasi_view" class="form-control" readonly>
                        <input type="hidden" name="id_tunjangan_komunikasi" id="id_tunjangan_komunikasi">
                    </div>

                    <div class="form-group">
                        <label>Tunjangan Daerah</label>
                        <input type="text" name="tunjangan_daerah_view" id="tunjangan_daerah_view" class="form-control" readonly>
                        <input type="hidden" name="id_tunjangan_daerah" id="id_tunjangan_daerah">
                    </div>

                    <div class="form-group">
                        <label>Tunjangan Radiasi</label>
                        <input type="text" id="tunjangan_radiasi_view" class="form-control" readonly>
                        <input type="hidden" name="id_tunjangan_radiasi" id="id_tunjangan_radiasi">
                    </div>


                    <div class="form-group">
                        <label>Tunjangan Perumahan</label>
                        <input type="text" id="tunjangan_perumahan_view" class="form-control" readonly>
                        <input type="hidden" name="id_tunjangan_perumahan" id="id_tunjangan_perumahan">
                    </div>


                    <div class="form-group">
                        <label>Tunjangan Insentif</label>
                        <div class="row">
                            <?php foreach ($insentif as $item): ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input insentif-checkbox" type="checkbox" value="<?= $item->id_tunjangan ?>" id="insentif_<?= $item->id_tunjangan ?>">
                                        <label class="form-check-label" for="insentif_<?= $item->id_tunjangan ?>">
                                            <?= $item->nm_tunjangan_insentif ?> - Rp <?= number_format($item->tunjangan, 0, ',', '.') ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="id_tunjangan_insentif" id="id_tunjangan_insentif">
                    </div>


                    <div class="form-group">
                        <label>Tunjangan Kode</label>
                        <div class="row">
                            <?php foreach ($kode as $item): ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input kode-checkbox" type="checkbox" value="<?= $item->id_master ?>" id="kode_<?= $item->id_master ?>">
                                        <label class="form-check-label" for="kode_<?= $item->id_master ?>">
                                            <?= $item->nm_tunjangan ?> - Rp <?= number_format($item->nominal, 0, ',', '.') ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="id_tunjangan_kode" id="id_tunjangan_kode">
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

<div class="modal fade" id="modalPendapatan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-wallet mr-1"></i> Detail Pendapatan <span id="modalPendapatanNama"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4" id="modalPendapatanBody">
                <!-- ISI DETAIL TUNJANGAN/Gaji akan diinject JS -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i class="fe-x"></i> Tutup</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalPotongan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-receipt mr-1"></i> Detail Potongan <span id="modalPotonganNama"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4" id="modalPotonganBody">
                <!-- ISI DETAIL POTONGAN akan diinject JS -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal"><i class="fe-x"></i> Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTunjangan" tabindex="-1" aria-labelledby="modalTunjanganLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Tunjangan Insentif & Kode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_id_pegawai" name="id_pegawai" value="">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Tunjangan Insentif</h6>
                        <?php foreach ($insentif as $i => $item): ?>
                            <div class="mb-2 d-flex align-items-center">
                                <label class="flex-grow-1 mb-0"><?= $item->nm_tunjangan_insentif ?></label>
                                <input
                                    type="text"
                                    class="form-control ms-2 uang"
                                    style="max-width:120px;"
                                    name="modal_insentif[<?= $i ?>][nominal]"
                                    placeholder="Nominal"
                                    data-id="<?= $item->id_tunjangan ?>"
                                    data-type="insentif">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-md-6">
                        <h6>Tunjangan Lain lain</h6>
                        <?php foreach ($kode as $i => $item): ?>
                            <div class="mb-2 d-flex align-items-center">
                                <label class="flex-grow-1 mb-0"><?= $item->nm_tunjangan ?></label>
                                <input
                                    type="text"
                                    class="form-control ms-2 uang"
                                    style="max-width:120px;"
                                    name="modal_kode[<?= $i ?>][nominal]"
                                    placeholder="Nominal"
                                    data-id="<?= $item->id_master ?>"
                                    data-type="kode">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSimpanModalTunjangan" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>