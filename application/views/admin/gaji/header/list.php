<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Gaji</a></li>
                    <li class="breadcrumb-item active">Data Gaji Header</li>
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
                <table id="table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%;">
                    <thead>
                        <tr>
                            <th width="2%">No</th>
                            <th class="text-center" width="10%">Action</th>
                            <th>Nama Pegawai</th>
                            <th>Periode</th>
                            <th>Total Pendapatan Tetap</th>
                            <th>Total Pendapatan Kotor</th>
                            <th>Total Potongan</th>
                            <th>Gaji Bersih</th>
                            <th>Status Konfirmasi</th>
                            <th>Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<!-- load modal -->
<?php $this->load->view('admin/gaji/header/list_modal') ?>
<?php $this->load->view('templates/includes/footer') ?>

<script>
    var save_method;
    var table;
    var base_url = '<?= base_url(); ?>';

    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?= site_url('gaji_header/ajax_list') ?>",
                "type": "POST"
            },
        });
    });

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('Tambah Data Gaji Header');
        $('.id_gaji_header').hide();
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);

        var url = (save_method == 'add') ? "<?php echo site_url('gaji_header/insert') ?>" : "<?php echo site_url('gaji_header/update') ?>";

        var formData = new FormData($('#form')[0]);
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                    $.toast({
                        text: "Data berhasil disimpan",
                        heading: 'Success',
                        icon: 'success',
                        showHideTransition: 'fade',
                        allowToastClose: false,
                        hideAfter: 3000,
                        stack: 5,
                        position: 'bottom-right',
                        textAlign: 'left',
                    });
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSave').text('Save');
                $('#btnSave').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error saat menyimpan data');
                $('#btnSave').text('Save');
                $('#btnSave').attr('disabled', false);
            }
        });
    }

    function deletedata(id) {

        Swal.fire({
            title: 'Apa kamu yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.value) {

                $.ajax({
                    url: "<?php echo site_url('gaji_header/delete'); ?>",
                    type: "POST",
                    data: "id_gaji_header=" + id,
                    cache: false,
                    dataType: 'json',
                    success: function(respone) {
                        if (respone.status === true) {
                            reload_table();
                            $.toast({
                                text: "Data berhasil dihapus",
                                heading: 'Success',
                                icon: 'success',
                                showHideTransition: 'fade',
                                allowToastClose: false,
                                hideAfter: 3000,
                                stack: 5,
                                position: 'bottom-right',
                                textAlign: 'left',
                                // loader: true, 
                                // bgColor: '#0040e0',
                            });
                        } else {
                            $.toast({
                                text: "Data gagal dihapus",
                                heading: 'Error',
                                icon: 'error',
                            });
                        }
                    }
                });

            } else if (result.dismiss === swal.DismissReason.cancel) {
                reload_table();
                $.toast({
                    text: "Data batal dihapus",
                    heading: 'Note',
                    icon: 'info',
                    showHideTransition: 'fade',
                    allowToastClose: false,
                    hideAfter: 3000,
                    stack: 5,
                    position: 'bottom-left',
                });
            }
        })

    }

    function update(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('.id_gaji_header').show();

        $.ajax({
            url: "<?php echo site_url('gaji_header/get_by_id') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_gaji_header"]').val(data.id_gaji_header);
                $('[name="id_pegawai"]').val(data.id_pegawai);
                $('[name="periode"]').val(data.periode);
                $('[name="status_konfirmasi"]').val(data.status_konfirmasi);
                $('#modal_form').modal('show');
                $('.modal-title').text('Edit Data Gaji Header');
            },
            error: function() {
                alert('Error mengambil data');
            }
        });
    }

    function addDetail(encryptedId) {
        $('#form_detail')[0].reset();
        $('#id_gaji_header_detail').val(encryptedId);
        $('#modal_form_detail').modal('show');
    }

    function saveDetail() {
        $('#btnSaveDetail').text('Menyimpan...');
        $('#btnSaveDetail').attr('disabled', true);

        $.ajax({
            url: '<?= base_url("gaji_detail/insert") ?>',
            type: 'POST',
            data: $('#form_detail').serialize(),
            dataType: 'JSON',
            success: function(data) {
                if (data.status) {
                    $('#modal_form_detail').modal('hide');
                    $.toast({
                        text: "Detail gaji berhasil disimpan",
                        heading: 'Success',
                        icon: 'success',
                        position: 'bottom-right',
                        hideAfter: 3000,
                    });
                } else {
                    alert('Gagal menyimpan detail gaji');
                }
                $('#btnSaveDetail').text('Simpan Detail');
                $('#btnSaveDetail').attr('disabled', false);
            },
            error: function() {
                alert('Error koneksi server');
                $('#btnSaveDetail').text('Simpan Detail');
                $('#btnSaveDetail').attr('disabled', false);
            }
        });
    }

    // function viewDetail(encryptedId) {
    //     const jenisKomponenText = {
    //         'gapok': 'Gaji Pokok',
    //         'tunjangan_tetap': 'Tunjangan Tetap',
    //         'tunjangan_non_tetap': 'Tunjangan Non Tetap',
    //         'insentif': 'Insentif',
    //         'potongan': 'Potongan',
    //         'potongan_opsional': 'Potongan Opsional'
    //     };

    //     $.ajax({
    //         url: '<?= base_url('gaji_detail/get_gaji_detail/') ?>' + encryptedId,
    //         type: 'GET',
    //         dataType: 'json',
    //         success: function(res) {
    //             let rows = '';
    //             if (res.data.length > 0) {
    //                 $.each(res.data, function(i, item) {
    //                     // Ambil teks yang sudah dimapping, kalau gak ada default ke item.jenis_komponen asli
    //                     const jenis = jenisKomponenText[item.jenis_komponen] || item.jenis_komponen;

    //                     rows += '<tr>' +
    //                         '<td>' + jenis + '</td>' +
    //                         '<td>' + item.nama_komponen + '</td>' +
    //                         '<td class="text-right">' + Number(item.jumlah).toLocaleString('id-ID', {
    //                             minimumFractionDigits: 2
    //                         }) + '</td>' +
    //                         '</tr>';
    //                 });
    //             } else {
    //                 rows = '<tr><td colspan="3" class="text-center">Tidak ada detail</td></tr>';
    //             }
    //             $('#detailBody').html(rows);
    //             $('#modal_detail').modal('show');
    //         },
    //         error: function() {
    //             alert('Gagal mengambil data detail');
    //         }
    //     });
    // }

    function viewDetail(encryptedId) {
        const jenisKomponenText = {
            'gapok': 'Gaji Pokok',
            'tunjangan_tetap': 'Tunjangan Tetap',
            'tunjangan_non_tetap': 'Tunjangan Non Tetap',
            'insentif': 'Insentif',
            'potongan': 'Potongan',
            'potongan_opsional': 'Potongan Opsional'
        };

        $.ajax({
            url: '<?= base_url('gaji_detail/get_gaji_detail/') ?>' + encryptedId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.pegawai && res.penempatan) {
                    let infoRows = `
                  <tr>
                    <td><strong>Nama</strong></td>
                    <td>${res.pegawai.nama || '-'}</td>
                    <td><strong>Unit Level</strong></td>
                    <td>${res.penempatan.nm_unit_level  || '-'}</td>
                  </tr>
                  <tr>
                    <td><strong>NIK</strong></td>
                    <td>${res.pegawai.nik || '-'}</td>
                    <td><strong>Unit Usaha</strong></td>
                    <td>${res.penempatan.nm_unit_usaha || '-'}</td>
                  </tr>
                  <tr>
                    <td><strong>Email</strong></td>
                    <td>${res.pegawai.email || '-'}</td>

                    <td><strong>No Rekening</strong></td>
                    <td>${res.pegawai.no_rek || '-'}</td>
                  </tr>
                  <tr>
                    <td><strong>Unit Kerja</strong></td>
                    <td>${res.penempatan.nm_unit_kerja || '-'}</td>
                    <td><strong>Atas Nama</strong></td>
                    <td>${res.pegawai.atas_nm || '-'}</td>
                  </tr>
                  <tr>
                    <td><strong>Unit Bisnis</strong></td>
                    <td>${res.penempatan.nm_unit_bisnis || '-'}</td>
                    <td><strong>Nama Bank</strong></td>
                    <td>${res.bank ? res.bank.nm_bank : '-'}</td>
                  </tr>
                `;
                    $('#infoPegawai').html(infoRows);
                } else {
                    $('#infoPegawai').html('<tr><td colspan="4" class="text-center">Info pegawai tidak tersedia.</td></tr>');
                }

                let rows = '';
                if (res.data.length > 0) {
                    $.each(res.data, function(i, item) {
                        const jenis = jenisKomponenText[item.jenis_komponen] || item.jenis_komponen;
                        rows += '<tr>' +
                            '<td>' + jenis + '</td>' +
                            '<td>' + item.nama_komponen + '</td>' +
                            '<td class="text-right">' + Number(item.jumlah).toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                            }) + '</td>' +
                            '</tr>';
                    });
                } else {
                    rows = '<tr><td colspan="3" class="text-center">Tidak ada detail</td></tr>';
                }
                $('#detailBody').html(rows);
                $('#modal_detail').modal('show');
            },
            error: function() {
                alert('Gagal mengambil data detail');
            }
        });
    }



    $('#id_unit_kerja').change(function() {
        var idKerja = $(this).val();
        $('#id_pegawai').prop('disabled', true).empty().append('<option value="">-- Pilih Pegawai --</option>');

        if (idKerja) {
            $.ajax({
                url: '<?= site_url('gaji_header/get_pegawai_by_unit_kerja') ?>',
                type: 'POST',
                data: {
                    id_unit_kerja: idKerja
                },
                dataType: 'json',
                success: function(data) {
                    $('#id_pegawai').prop('disabled', false);
                    $.each(data, function(i, item) {
                        $('#id_pegawai').append('<option value="' + item.id_pegawai + '">' + item.nama + ' (' + item.nik + ')</option>');
                    });
                }
            });
        }
    });

    function viewPotonganDetail(encryptedId) {
        currentEncryptedId = encryptedId;
        $.ajax({
            url: '<?= base_url("gaji_detail/get_potongan_detail/") ?>' + encryptedId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                // Render potongan tetap
                let rowsTetap = '';
                if (res.potongan_tetap && res.potongan_tetap.length > 0) {
                    $.each(res.potongan_tetap, function(i, item) {
                        let nilaiDasar = Number(item.dasar_hitung);
                        let dasarHitungDisplay = !isNaN(nilaiDasar) ?
                            nilaiDasar.toLocaleString('id-ID', {
                                minimumFractionDigits: 2
                            }) :
                            item.dasar_hitung;

                        let persentaseDisplay = (item.persentase !== null && item.persentase !== undefined) ?
                            item.persentase + '%' : '';

                        rowsTetap += `<tr>
                            <td>${item.nama_potongan}</td>
                            <td>${persentaseDisplay}</td>
                            <td>${dasarHitungDisplay}</td>
                            <td>${Number(item.jumlah_potongan).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                            <td>${item.keterangan}</td>
                        </tr>`;
                    });

                } else {
                    rowsTetap = '<tr><td colspan="5" class="text-center">Tidak ada potongan tetap</td></tr>';
                }
                $('#potonganTetapBody').html(rowsTetap);
                let rowsOpsional = '';
                if (res.potongan_opsional && res.potongan_opsional.length > 0) {
                    $.each(res.potongan_opsional, function(i, item) {
                        let dasarHitungDisplay = Number(item.dasar_hitung);
                        if (isNaN(dasarHitungDisplay)) dasarHitungDisplay = item.dasar_hitung;
                        else dasarHitungDisplay = dasarHitungDisplay.toLocaleString('id-ID', {
                            minimumFractionDigits: 2
                        });

                        rowsOpsional += `<tr>
                        <td>${item.nama_potongan}</td>
                        <td>-</td>
                        <td>${dasarHitungDisplay}</td>
                        <td>${Number(item.jumlah_potongan).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                        <td>${item.keterangan}</td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit-potongan mb-1" data-id="${item.id_gaji_detail}">
                                <i class="fas fa-edit"></i> Update
                            </button><br>
                             <button class="btn btn-sm btn-danger btn-delete-potongan" data-id="${item.id_gaji_detail}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                        </tr>`;
                    });

                } else {
                    rowsOpsional = '<tr><td colspan="5" class="text-center">Tidak ada potongan opsional</td></tr>';
                }
                $('#potonganOpsionalBody').html(rowsOpsional);

                $('#modal_potongan').modal('show');
            },
            error: function() {
                alert('Gagal mengambil data potongan');
            }
        });
    }


    // function addPotongan(encryptedIdGajiHeader) {
    //     $('#formTambahPotongan')[0].reset(); // reset form
    //     $('#id_gaji_header').val(encryptedIdGajiHeader);
    //     $('#modalTambahPotongan').modal('show');
    // }

    $('#btnTambahPotonganModal').on('click', function() {
        if (!currentEncryptedId) {
            alert('ID Gaji Header belum tersedia');
            return;
        }
        $('#formTambahPotongan')[0].reset();
        $('#id_gaji_header').val(currentEncryptedId);
        $('#modalTambahPotongan').modal('show');
    });


    $('#formTambahPotongan').submit(function(e) {
        e.preventDefault();

        let formData = $(this).serialize();
        $('#btnSavePotongan').text('Menyimpan...');
        $('#btnSavePotongan').attr('disabled', true);

        $.ajax({
            url: "<?= site_url('gaji_detail/insert_potongan_opsional') ?>",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $('#modalTambahPotongan').modal('hide');
                    $('#modal_potongan').modal('hide');
                    reload_table();
                    $.toast({
                        text: "Potongan Opsional berhasil disimpan",
                        heading: 'Success',
                        icon: 'success',
                        position: 'bottom-right',
                        hideAfter: 3000,
                    });
                } else {
                    alert('Gagal menyimpan detail gaji');
                }
                $('#btnSavePotongan').text('Simpan Detail');
                $('#btnSavePotongan').attr('disabled', false);
            },
            error: function() {
                alert('Error koneksi server');
                $('#btnSavePotongan').text('Simpan Detail');
                $('#btnSavePotongan').attr('disabled', false);
            }
        });
    });


    $('#modal_potongan').on('click', '.btn-edit-potongan', function() {
        let id = $(this).data('id');

        $.ajax({
            url: base_url + 'gaji_detail/get_by_id/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res) {
                    $('#edit_id_gaji_detail').val(res.id_gaji_detail);
                    $('#edit_nama_komponen').val(res.nama_komponen);
                    $('#edit_jumlah').val(res.jumlah);
                    $('#edit_jenis_komponen').val('potongan_opsional');
                    $('#modalEditPotongan').modal('show');
                } else {
                    alert('Data tidak ditemukan!');
                }
            },
            error: function() {
                alert('Gagal mengambil data potongan');
            }
        });

    });

    $('#formEditPotongan').submit(function(e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: '<?= base_url("gaji_detail/update") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    $('#modalEditPotongan').modal('hide');
                    $('#modal_potongan').modal('hide');
                    reload_table();
                    $.toast({
                        text: "Potongan berhasil diupdate",
                        heading: 'Success',
                        icon: 'success',
                        position: 'bottom-right',
                        hideAfter: 3000,
                    });
                } else {
                    alert('Gagal update potongan');
                }
            },
            error: function() {
                alert('Error server saat update potongan');
            }
        });
    });

    $('#modal_potongan').on('click', '.btn-delete-potongan', function() {
        if (!confirm('Yakin hapus potongan ini?')) return;

        let id = $(this).data('id');

        $.ajax({
            url: '<?= base_url("gaji_detail/delete") ?>',
            type: 'POST',
            data: {
                id_gaji_detail: id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    $('#modal_potongan').modal('hide');
                    reload_table();
                    $.toast({
                        text: "Data berhasil dihapus",
                        heading: 'Success',
                        icon: 'success',
                        position: 'bottom-right',
                        hideAfter: 3000,
                    });
                } else {
                    $.toast({
                        text: "Data gagal dihapus",
                        heading: 'Error',
                        icon: 'error',
                    });
                }
            },
            error: function() {
                alert('Error server saat hapus potongan');
            }
        });
    });
</script>