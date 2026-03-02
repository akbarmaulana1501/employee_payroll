<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pegawai</a></li>
                    <li class="breadcrumb-item active">Insentif Pegawai</li>
                </ol>
            </div>
            <?= $TombolCreate ?>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="card card-border">
            <div class="card-header border-primary pb-0"></div>
            <div class="card-body table-responsive">

                <form id="form" class="form-horizontal" onsubmit="return false;">
                    <input type="hidden" name="id_pegawai_penggajian" id="id_pegawai_penggajian">

                    <div class="row form-section">
                        <div class="col-md-6">

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Bulan</label>
                                <div class="col-sm-8">
                                    <select name="bulan" id="bulan" class="form-control" required>
                                        <option value="">-- Pilih Bulan --</option>
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            $namaBulan = date('F', mktime(0, 0, 0, $i, 10));
                                            echo "<option value='$i'>$namaBulan</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Tahun</label>
                                <div class="col-sm-8">
                                    <input type="number" name="tahun" id="tahun" class="form-control" value="<?= date('Y') ?>" required>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label form-label">Nama Pegawai</label>
                                <div class="col-sm-8">
                                    <select name="id_pegawai" id="id_pegawai" class="form-control select2" required>
                                        <option value="">-- Pilih Pegawai --</option>
                                        <?php foreach ($list_pegawai as $p): ?>
                                            <option value="<?= $p->id_pegawai ?>"
                                                data-eselon="<?= $p->id_eselon ?>">
                                                <?= $p->nama ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>

                                    <span class="help-block text-danger"></span>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label form-label">Pegawai Eselon</label>
                                <div class="col-sm-8">
                                    <input type="text" name="id_eselon" id="id_eselon" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Radiasi</label>
                                <div class="col-sm-6">
                                    <input type="text" id="tunjangan_radiasi_view" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_radiasi" id="id_tunjangan_radiasi">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Perumahan</label>
                                <div class="col-sm-6">
                                    <input type="text" id="tunjangan_perumahan_view" class="form-control uang" readonly>
                                    <input type="hidden" name="id_tunjangan_perumahan" id="id_tunjangan_perumahan">
                                </div>
                            </div>


                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Gaji Pokok</label>
                                <div class="col-sm-6">
                                    <input type="text" name="gaji_pokok_view" id="gaji_pokok_view" class="form-control" readonly>
                                    <input type="hidden" name="id_gaji_pokok" id="id_gaji_pokok">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Fungsional</label>
                                <div class="col-sm-6">
                                    <input type="text" name="tunjangan_fungsional_view" id="tunjangan_fungsional_view" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_fungsional" id="id_tunjangan_fungsional">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Kesra</label>
                                <div class="col-sm-6">
                                    <input type="text" name="tunjangan_kesra_view" id="tunjangan_kesra_view" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_kesra" id="id_tunjangan_kesra">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Komunikasi</label>
                                <div class="col-sm-6">
                                    <input type="text" name="tunjangan_komunikasi_view" id="tunjangan_komunikasi_view" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_komunikasi" id="id_tunjangan_komunikasi">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Daerah</label>
                                <div class="col-sm-6">
                                    <input type="text" name="tunjangan_daerah_view" id="tunjangan_daerah_view" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_daerah" id="id_tunjangan_daerah">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Penugasan</label>
                                <div class="col-sm-6">
                                    <input type="text" name="tunjangan_penugasan" id="tunjangan_penugasan" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_penugasan" id="id_tunjangan_penugasan">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Kasir</label>
                                <div class="col-sm-6">
                                    <input type="text" name="tunjangan_kasir" id="tunjangan_kasir" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_kasir" id="id_tunjangan_kasir">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Transport</label>
                                <div class="col-sm-6">
                                    <input type="text" name="tunjangan_transport" id="tunjangan_transport" class="form-control" readonly>
                                    <input type="hidden" name="id_tunjangan_transport" id="id_tunjangan_transport">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Kompensasi</label>
                                <div class="col-sm-6">
                                    <input type="text" name="kompensasi" id="kompensasi" class="form-control" readonly>
                                    <input type="hidden" name="id_kompensasi" id="id_kompensasi">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-section">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-6 col-form-label form-label">Tunjangan Insentif & Kode</label>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-info btn-block" onclick="addTunjangan()">
                                        Input Tunjangan Insentif & Lain lain
                                    </button>

                                    <input type="hidden" name="insentif_data" id="insentif_data">
                                    <input type="hidden" name="kode_data" id="kode_data">
                                    <div id="previewTunjangan"></div> <!-- opsional: buat preview -->
                                </div>
                            </div>

                            <!-- 
                    <div class="form-group row">
                        <label class="col-sm-6 col-form-label form-label">Tunjangan Insentif</label>
                        <div class="col-sm-6">
                            <div class="row">
                                <?php foreach ($insentif as $i => $item): ?>
                                    <div class="col-md-12 mb-2 d-flex align-items-center">
                                        <label class="flex-grow-1 mb-0" style="font-weight:400">
                                            <?= $item->nm_tunjangan_insentif ?>
                                        </label>
                                        <input
                                            type="text"
                                            min="0"
                                            class="form-control ms-2 uang"
                                            style="max-width:120px;"
                                            name="insentif[<?= $i ?>][nominal]"
                                            placeholder="Nominal">

                                        <input type="hidden" name="insentif[<?= $i ?>][id]" value="<?= $item->id_tunjangan ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div> -->
                        </div>
                        <div class="col-md-6">
                            <!-- <div class="form-group row">
                        <label class="col-sm-6 col-form-label form-label">Tunjangan Lain Lain</label>
                        <div class="col-sm-6">
                            <div class="row">
                                <?php foreach ($kode as $i => $item): ?>
                                    <div class="col-md-12 mb-2 d-flex align-items-center">
                                        <label class="flex-grow-1 mb-0" style="font-weight:400">
                                            <?= $item->nm_tunjangan ?>
                                        </label>
                                        <input
                                            type="text"
                                            min="0"
                                            class="form-control ms-2 uang"
                                            style="max-width:120px;"
                                            name="kode[<?= $i ?>][nominal]"
                                            placeholder="Nominal">

                                        <input type="hidden" name="kode[<?= $i ?>][id]" value="<?= $item->id_master ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div> -->
                        </div>
                    </div>


                    <div class="form-group mt-3 text-right">
                        <button type="button" id="btnSave" class="btn btn-primary">
                            <i class="fe-save"></i> Simpan
                        </button>
                        <a href="<?= base_url('pegawai_penggajian') ?>" class="btn btn-danger">Batal</a>
                    </div>

                    <div id="debugSimpan" style="margin-top:10px;color:red"></div>

                </form>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('admin/master/pegawai_penggajian/list_modal') ?>
<?php $this->load->view('templates/includes/footer') ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    var save_method = 'add';
    var table;
    var base_url = '<?= base_url(); ?>';
    var lastSelectedPegawai = '';



    function formatRupiah(angka, prefix = 'Rp ') {
        angka = angka.replace(/[^,\d]/g, '').toString();
        let split = angka.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix + rupiah;
    }

    function unformatRupiah(str) {
        return str.replace(/[^0-9]/g, '');
    }

    $(document).on('input', '.uang', function() {
        let val = $(this).val();
        let number = val.replace(/[^0-9]/g, '');
        if (number === '' || number === '0') {
            $(this).val('');
            return;
        }
        $(this).val(formatRupiah(number));
    });

    function addTunjangan() {
        let id_pegawai = $('#id_pegawai').val();
        let id_pegawai_penggajian = $('[name="id_pegawai_penggajian"]').val(); // <--- pastikan ada field hidden ini di form!

        $('input[data-type="insentif"]').val('');
        $('input[data-type="kode"]').val('');
        $('#previewTunjangan').empty();
        $('#modal_id_pegawai').val(id_pegawai);

        if (id_pegawai) {
            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_detail_pegawai') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai,
                    id_pegawai_penggajian: id_pegawai_penggajian // <--- kirim juga di sini!
                },
                dataType: "json",
                success: function(data) {
                    // Isi insentif
                    $('input[data-type="insentif"]').each(function() {
                        let id_tunjangan = $(this).data('id');
                        if (data.insentif[id_tunjangan]) {
                            $(this).val(formatRupiah(data.insentif[id_tunjangan].toString()));
                        } else {
                            $(this).val('');
                        }
                    });

                    // Isi kode
                    $('input[data-type="kode"]').each(function() {
                        let id_master = $(this).data('id');
                        if (data.kode[id_master]) {
                            $(this).val(formatRupiah(data.kode[id_master].toString()));
                        } else {
                            $(this).val('');
                        }
                    });
                }
            });
        }

        $('#modalTunjangan').modal('show');
    }

    $('#btnSimpanModalTunjangan').on('click', function() {
        let insentif = [];
        $('input[data-type="insentif"]').each(function() {
            let nominal = $(this).val().replace(/[^0-9]/g, '');
            let id = $(this).data('id');
            if (nominal !== '' && nominal !== '0') {
                insentif.push({
                    id: id,
                    nominal: nominal
                });
            }
        });
        $('#insentif_data').val(JSON.stringify(insentif));

        let kode = [];
        $('input[data-type="kode"]').each(function() {
            let nominal = $(this).val().replace(/[^0-9]/g, '');
            let id = $(this).data('id');
            if (nominal !== '' && nominal !== '0') {
                kode.push({
                    id: id,
                    nominal: nominal
                });
            }
        });
        $('#kode_data').val(JSON.stringify(kode));
        // let prevHtml = '';
        // insentif.forEach(x => prevHtml += `<div><b>Insentif:</b> ${x.id} - Rp ${formatRupiah(x.nominal, '')}</div>`);
        // kode.forEach(x => prevHtml += `<div><b>Kode:</b> ${x.id} - Rp ${formatRupiah(x.nominal, '')}</div>`);
        // $('#previewTunjangan').html(prevHtml);
        $('#modalTunjangan').modal('hide');
    });


    $('#btnSave').on('click', function() {
        console.log('Insentif:', $('#insentif_data').val());
        console.log('Kode:', $('#kode_data').val());
        $('.uang').each(function() {
            let val = $(this).val();
            $(this).val(unformatRupiah(val));
        });
        save();
    });



    function save() {
        $('#btnSave').text('saving...').attr('disabled', true);
        let url = (save_method === 'add') ?
            "<?= site_url('pegawai_penggajian/insert') ?>" :
            "<?= site_url('pegawai_penggajian/update') ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    window.location.href = "<?= base_url('pegawai_penggajian') ?>";
                    return;
                } else {
                    let errorMsg = '';
                    for (let i = 0; i < data.inputerror.length; i++) {
                        // Ini jika kamu pakai form help-block
                        $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                        // Gabung error ke satu alert (jika mau alert juga)
                        if (data.error_string[i]) {
                            errorMsg += data.error_string[i] + '\n';
                        }
                    }
                    // Tampilkan alert kalau ada pesan error (bisa pakai swal/fire/sweetalert juga)
                    if (errorMsg !== '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                        }).then(() => {
                            window.location.href = "<?= base_url('pegawai_penggajian') ?>";
                        });
                    }
                }
                $('#btnSave').html('<i class="fe-save"></i> Save').attr('disabled', false);
            },


            error: function() {
                alert('Error saving data');
                $('#btnSave').text('Save').attr('disabled', false);
            }
        });
    }


    $(document).ready(function() {

        var id_encrypted = sessionStorage.getItem('id_penggajian_edit');
        if (id_encrypted) {
            $.get("<?= site_url('pegawai_penggajian/get_by_id') ?>/" + id_encrypted, function(data) {
                if (data) {
                    save_method = 'update';
                    $('[name="id_pegawai_penggajian"]').val(data.id_pegawai_penggajian);

                    // select2 wajib trigger change
                    $('[name="id_pegawai"]').val(data.id_pegawai).trigger('change');
                    $('[name="id_gaji_pokok"]').val(data.id_gaji_pokok).trigger('change');
                    $('[name="bulan"]').val(data.bulan).trigger('change');
                    $('[name="tahun"]').val(data.tahun);

                    // Hidden + label view
                    $('[name="id_tunjangan_fungsional"]').val(data.id_tunjangan_fungsional).trigger('change');
                    $('#tunjangan_fungsional_view').val(getLabelByValue('id_tunjangan_fungsional', data.id_tunjangan_fungsional));

                    $('[name="id_tunjangan_kesra"]').val(data.id_tunjangan_kesra).trigger('change');
                    $('#tunjangan_kesra_view').val(getLabelByValue('id_tunjangan_kesra', data.id_tunjangan_kesra));

                    $('[name="id_tunjangan_komunikasi"]').val(data.id_tunjangan_komunikasi).trigger('change');
                    $('#tunjangan_komunikasi_view').val(getLabelByValue('id_tunjangan_komunikasi', data.id_tunjangan_komunikasi));

                    $('[name="id_tunjangan_daerah"]').val(data.id_tunjangan_daerah).trigger('change');
                    $('#tunjangan_daerah_view').val(getLabelByValue('id_tunjangan_daerah', data.id_tunjangan_daerah));

                    $('[name="id_tunjangan_penugasan"]').val(data.id_tunjangan_penugasan).trigger('change');
                    $('#tunjangan_penugasan').val(getLabelByValue('id_tunjangan_penugasan', data.id_tunjangan_penugasan));

                    $('[name="id_tunjangan_kasir"]').val(data.id_tunjangan_kasir).trigger('change');
                    $('#tunjangan_kasir').val(getLabelByValue('id_tunjangan_kasir', data.id_tunjangan_kasir));

                    $('[name="kompensasi"]').val(data.id_kompensasi).trigger('change');
                    $('#kompensasi').val(getLabelByValue('id_kompensasi', data.id_kompensasi));

                    $('[name="id_tunjangan_transport"]').val(data.id_tunjangan_transport).trigger('change');
                    $('#tunjangan_transport').val(getLabelByValue('id_tunjangan_transport', data.id_tunjangan_transport));

                    $('[name="id_tunjangan_radiasi"]').val(data.id_tunjangan_radiasi).trigger('change');
                    $('#tunjangan_radiasi_view').val(getLabelByValue('id_tunjangan_radiasi', data.id_tunjangan_radiasi));

                    $('[name="id_tunjangan_perumahan"]').val(data.id_tunjangan_perumahan).trigger('change');
                    $('#tunjangan_perumahan_view').val(getLabelByValue('id_tunjangan_perumahan', data.id_tunjangan_perumahan));

                    // Gaji pokok nominal view (kalau ada)
                    $('#gaji_pokok_view').val(formatRupiah(data.nominal));

                    // Tahun
                    $('[name="tahun"]').val(data.tahun);

                    // Eselon (readonly)
                    $('#id_eselon').val(data.id_eselon);

                    // Insentif & kode ke hidden
                    if (data.insentif_data) {
                        $('[name="insentif_data"]').val(JSON.stringify(data.insentif_data));
                    }
                    if (data.kode_data) {
                        $('[name="kode_data"]').val(JSON.stringify(data.kode_data));
                    }
                    // Option: render insentif/kode ke preview
                    renderInsentifPreview(data.insentif_data || []);
                    renderKodePreview(data.kode_data || []);

                    // Hapus sessionStorage supaya ga dobel
                    sessionStorage.removeItem('id_penggajian_edit');
                }
            }, 'json');
        }

        $('.select2').select2({
            width: '100%'
        });

        $('#id_pegawai').on('change', function() {
            let id_pegawai = $(this).val();
            lastSelectedPegawai = id_pegawai;
            let selectedOption = $(this).find(':selected');
            let id_eselon = selectedOption.data('eselon') || '';
            $('#id_eselon').val(id_eselon);

            // --- Tunjangan fungsional
            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_fungsional_otomatis') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai,
                    // id_unit_usaha: $('#id_unit_usaha').val() // opsional, hapus jika tidak butuh
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_fungsional_view').val(data.tunjangan);
                        $('#id_tunjangan_fungsional').val(data.id_tunjangan);
                    } else {
                        $('#tunjangan_fungsional_view').val('');
                        $('#id_tunjangan_fungsional').val('');
                    }
                }
            });

            // --- Tunjangan kesra
            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_kesra_otomatis') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_kesra_view').val(data.tunjangan);
                        $('#id_tunjangan_kesra').val(data.id_tunjangan);
                    } else {
                        $('#tunjangan_kesra_view').val('');
                        $('#id_tunjangan_kesra').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_komunikasi_otomatis') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_komunikasi_view').val(data.tunjangan);
                        $('#id_tunjangan_komunikasi').val(data.id_tunjangan);
                    } else {
                        $('#tunjangan_komunikasi_view').val('');
                        $('#id_tunjangan_komunikasi').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_daerah_otomatis') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai,
                    id_unit_usaha: $('#id_unit_usaha').val()
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_daerah_view').val(data.tunjangan);
                        $('#id_tunjangan_daerah').val(data.id_tunjangan);
                    } else {
                        $('#tunjangan_daerah_view').val('-');
                        $('#id_tunjangan_daerah').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_radiasi_otomatis') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_radiasi_view').val(data.tunjangan);
                        $('#id_tunjangan_radiasi').val(data.id_tunjangan);
                    } else {
                        $('#tunjangan_radiasi_view').val('-');
                        $('#id_tunjangan_radiasi').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_perumahan_otomatis') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_perumahan_view').val(data.tunjangan);
                        $('#id_tunjangan_perumahan').val(data.id_tunjangan);
                    } else {
                        $('#tunjangan_perumahan_view').val('-');
                        $('#id_tunjangan_perumahan').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_gaji_pokok_by_pegawai') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#gaji_pokok_view').val(data.nominal);
                        $('#id_gaji_pokok').val(data.id_gaji_pokok);
                    } else {
                        $('#gaji_pokok_view').val('-');
                        $('#id_gaji_pokok').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_penugasan_by_pegawai') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_penugasan').val(data.nominal);
                        $('#id_tunjangan_penugasan').val(data.id_tunjangan_penugasan);
                    } else {
                        $('#tunjangan_penugasan').val('-');
                        $('#id_tunjangan_penugasan').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_kasir_by_pegawai') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_kasir').val(data.nominal);
                        $('#id_tunjangan_kasir').val(data.id_tunjangan_kasir);
                    } else {
                        $('#tunjangan_kasir').val('-');
                        $('#id_tunjangan_kasir').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_tunjangan_transport_by_pegawai') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#tunjangan_transport').val(data.nominal);
                        $('#id_tunjangan_transport').val(data.id_tunjangan_transport);
                    } else {
                        $('#tunjangan_transport').val('-');
                        $('#id_tunjangan_transport').val('');
                    }
                }
            });

            $.ajax({
                url: "<?= site_url('pegawai_penggajian/get_kompensasi_by_pegawai') ?>",
                type: "POST",
                data: {
                    id_pegawai: id_pegawai
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#kompensasi').val(data.nominal);
                        $('#id_kompensasi').val(data.id_kompensasi);
                    } else {
                        $('#kompensasi').val('-');
                        $('#id_kompensasi').val('');
                    }
                }
            });
        });

    });

</script>
</body>
    <!-- // $(document).ready(function() {
    //     $('.select2').select2({
    //         width: '100%'
    //     });

    //     $('#id_unit_usaha, #id_unit_kerja').on('change', function() {
    //         let id_usaha = $('#id_unit_usaha').val();
    //         let id_kerja = $('#id_unit_kerja').val();

    //         if (id_usaha && id_kerja) {
    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_pegawai_by_unit') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_unit_usaha: id_usaha,
    //                     id_unit_kerja: id_kerja
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     pegawaiList = {};
    //                     let $pegawai = $('#id_pegawai');
    //                     $pegawai.empty().append('<option value="">-- Pilih Pegawai --</option>');

    //                     if (data.length === 0) {
    //                         $pegawai.append('<option value="">(Tidak ditemukan)</option>');
    //                     } else {
    //                         $.each(data, function(i, val) {
    //                             pegawaiList[val.id_pegawai] = val;
    //                             $pegawai.append(`<option value="${val.id_pegawai}">${val.nama}</option>`);
    //                         });
    //                     }
    //                     $pegawai.trigger('change.select2');
    //                 }



    //             });
    //         }
    //     });



    //     $('#id_pegawai').on('change', function() {
    //         let selectedId = $(this).val();
    //         if (pegawaiList[selectedId]) {
    //             $('#id_eselon').val(pegawaiList[selectedId].id_eselon || '');
    //         } else {
    //             $('#id_eselon').val('');
    //         }
    //     });

    //     $('#id_pegawai').on('change', function() {
    //         let id_pegawai = $(this).val();
    //         let id_usaha = $('#id_unit_usaha').val();

    //         if (id_pegawai && id_usaha) {
    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_tunjangan_fungsional_otomatis') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_pegawai: id_pegawai,
    //                     id_unit_usaha: id_usaha
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     if (data) {
    //                         $('#tunjangan_fungsional_view').val(data.tunjangan);
    //                         $('#id_tunjangan_fungsional').val(data.id_tunjangan);
    //                     } else {
    //                         $('#tunjangan_fungsional_view').val('');
    //                         $('#id_tunjangan_fungsional').val('');
    //                     }
    //                 }
    //             });
    //         }
    //     });

    //     $('#id_pegawai').on('change', function() {
    //         let id_pegawai = $(this).val();
    //         if (id_pegawai) {
    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_tunjangan_kesra_otomatis') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_pegawai: id_pegawai
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     if (data) {
    //                         $('#tunjangan_kesra_view').val(data.tunjangan);
    //                         $('#id_tunjangan_kesra').val(data.id_tunjangan);
    //                     } else {
    //                         $('#tunjangan_kesra_view').val('');
    //                         $('#id_tunjangan_kesra').val('');
    //                     }
    //                 }
    //             });

    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_tunjangan_komunikasi_otomatis') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_pegawai: id_pegawai
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     if (data) {
    //                         $('#tunjangan_komunikasi_view').val(data.tunjangan);
    //                         $('#id_tunjangan_komunikasi').val(data.id_tunjangan);
    //                     } else {
    //                         $('#tunjangan_komunikasi_view').val('');
    //                         $('#id_tunjangan_komunikasi').val('');
    //                     }
    //                 }
    //             });

    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_tunjangan_daerah_otomatis') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_pegawai: id_pegawai,
    //                     id_unit_usaha: $('#id_unit_usaha').val()
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     if (data) {
    //                         $('#tunjangan_daerah_view').val(data.tunjangan);
    //                         $('#id_tunjangan_daerah').val(data.id_tunjangan);
    //                     } else {
    //                         $('#tunjangan_daerah_view').val('-');
    //                         $('#id_tunjangan_daerah').val('');
    //                     }
    //                 }
    //             });

    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_tunjangan_radiasi_otomatis') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_pegawai: id_pegawai
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     if (data) {
    //                         $('#tunjangan_radiasi_view').val(data.tunjangan);
    //                         $('#id_tunjangan_radiasi').val(data.id_tunjangan);
    //                     } else {
    //                         $('#tunjangan_radiasi_view').val('-');
    //                         $('#id_tunjangan_radiasi').val('');
    //                     }
    //                 }
    //             });

    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_tunjangan_perumahan_otomatis') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_pegawai: id_pegawai
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     if (data) {
    //                         $('#tunjangan_perumahan_view').val(data.tunjangan);
    //                         $('#id_tunjangan_perumahan').val(data.id_tunjangan);
    //                     } else {
    //                         $('#tunjangan_perumahan_view').val('-');
    //                         $('#id_tunjangan_perumahan').val('');
    //                     }
    //                 }
    //             });

    //             $.ajax({
    //                 url: "<?= site_url('pegawai_penggajian/get_gaji_pokok_by_pegawai') ?>",
    //                 type: "POST",
    //                 data: {
    //                     id_pegawai: id_pegawai
    //                 },
    //                 dataType: "json",
    //                 success: function(data) {
    //                     if (data) {
    //                         $('#gaji_pokok_view').val(data.nominal);
    //                         $('#id_gaji_pokok').val(data.id_gaji_pokok);
    //                     } else {
    //                         $('#gaji_pokok_view').val('-');
    //                         $('#id_gaji_pokok').val('');
    //                     }
    //                 }
    //             });


    //         }
    //     });

    //     $(document).on('change', '.insentif-checkbox', function() {
    //         let selected = [];
    //         $('.insentif-checkbox:checked').each(function() {
    //             selected.push($(this).val());
    //         });
    //         $('#id_tunjangan_insentif').val(selected.join(','));
    //     });

    //     $(document).on('change', '.kode-checkbox', function() {
    //         let selected = [];
    //         $('.kode-checkbox:checked').each(function() {
    //             selected.push($(this).val());
    //         });
    //         $('#id_tunjangan_kode').val(selected.join(','));
    //     });
    // }); -->

</html>

