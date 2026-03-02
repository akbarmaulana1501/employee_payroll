<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Penggajian</a></li>
                    <li class="breadcrumb-item active">Pegawai</li>
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
                <table id="table" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse:collapse;border-spacing:0;width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Action</th>
                            <th>Pegawai</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Total Pendapatan</th>
                            <th>Total Potongan</th>
                            <th>Gaji Kotor</th>
                            <th>Gaji Bersih</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('admin/master/pegawai_penggajian/list_modal') ?>
<?php $this->load->view('templates/includes/footer') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script type="text/javascript">
    var save_method;
    var table;
    var base_url = '<?= base_url(); ?>';

    function formatRupiah(x) {
        x = parseFloat(x);
        if (isNaN(x) || x == 0) return '-';
        return 'Rp. ' + x.toLocaleString('id-ID');
    }

    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('pegawai_penggajian/ajax_list') ?>",
                "type": "POST"
            },
        });

        // Satu event untuk semua tombol detail
        $('#table').on('click', '.detail-trigger', function() {
            let type = $(this).data('type');
            let nama = $(this).data('nama');

            if (type === 'pendapatan') {
                let data = {
                    "Gaji Pokok": $(this).data('gaji_pokok'),
                    "Tunjangan Fungsional": $(this).data('fungsional'),
                    "Tunjangan Kesra": $(this).data('kesra'),
                    "Tunjangan Komunikasi": $(this).data('komunikasi'),
                    "Tunjangan Daerah": $(this).data('daerah'),
                    "Tunjangan Radiasi": $(this).data('radiasi'),
                    "Tunjangan Perumahan": $(this).data('perumahan'),
                    "Tunjangan Lain Lain": $(this).data('kode'),
                    "Tunjangan Insentif": $(this).data('insentif'),
                    "Tunjangan Penugasan": $(this).data('penugasan'),
                    "Tunjangan Kasir": $(this).data('kasir'),
                    "Tunjangan transport": $(this).data('transport'),
                    "Kompensasi": $(this).data('kompensasi')
                };
                let total = $(this).data('total');

                let html = `<table class="table table-sm table-borderless mb-0">`;
                $.each(data, function(label, val) {
                    html += `<tr>
                    <td>${label}</td>
                    <td class="text-right font-weight-semibold">${formatRupiah(val)}</td>
                </tr>`;
                });
                html += `<tr class="bg-light">
                <th class="text-right pr-3">TOTAL</th>
                <th class="text-right">${formatRupiah(total)}</th>
            </tr>`;
                html += `</table>`;

                $('#modalPendapatanNama').html(`<span class="badge badge-light ml-2">${nama}</span>`);
                $('#modalPendapatanBody').html(html);
                $('#modalPendapatan').modal('show');

            } else if (type === 'potongan') {
                let potongan = $(this).data('potongan');
                let total = $(this).data('total');
                // Jika potongan dari PHP itu string JSON, harus di-parse
                if (typeof potongan === "string") potongan = JSON.parse(potongan);

                let html = `<table class="table table-sm table-borderless mb-0">`;
                if (potongan && Object.keys(potongan).length > 0) {
                    $.each(potongan, function(label, val) {
                        html += `<tr>
                        <td>${label}</td>
                        <td class="text-right font-weight-semibold">${formatRupiah(val)}</td>
                    </tr>`;
                    });
                } else {
                    html += `<tr><td colspan="2" class="text-center text-muted">Tidak ada potongan</td></tr>`;
                }
                html += `<tr class="bg-light">
                <th class="text-right pr-3">TOTAL</th>
                <th class="text-right">${formatRupiah(total)}</th>
            </tr>`;
                html += `</table>`;

                $('#modalPotonganNama').html(`<span class="badge badge-light ml-2">${nama}</span>`);
                $('#modalPotonganBody').html(html);
                $('#modalPotongan').modal('show');
            }
        });
    });


    function numberWithCommas(x) {
        if (!x) return '-';
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        sessionStorage.removeItem('id_penggajian_edit');
        window.location.href = "<?= site_url('pegawai_penggajian/page'); ?>"
    }


    function update(id_encrypted) {
        sessionStorage.setItem('id_penggajian_edit', id_encrypted);
        window.location.href = "<?= site_url('pegawai_penggajian/page') ?>";
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function generate_pdf() {
        fetch('<?= base_url('pegawai_penggajian/generate_pdf') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        footer: `<a href="${data.download_url}" target="_blank">Download PDF</a>`
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan: ' + error
                });
            });
    }

    function kirim_email(id_encrypted) {
        fetch("<?= base_url('pegawai_penggajian/kirim_email'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id_pegawai_penggajian=" + encodeURIComponent(id_encrypted)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: data.message
                    }).then(() => {
                        if (typeof reload_table === "function") reload_table();
                        if (typeof reload_table_terkonfirmasi === "function") reload_table_terkonfirmasi();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi error: ' + error
                });
            });
    }

    function kirim_slip_thr(id_encrypted) {
        fetch("<?= base_url('pegawai_penggajian/kirim_slip_thr'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id_pegawai_penggajian=" + encodeURIComponent(id_encrypted)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: data.message
                    }).then(() => {
                        if (typeof reload_table === "function") reload_table();
                        if (typeof reload_table_terkonfirmasi === "function") reload_table_terkonfirmasi();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi error: ' + error
                });
            });
    }

    function kirim_slip_bonus(id_encrypted) {
        fetch("<?= base_url('pegawai_penggajian/kirim_slip_bonus'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id_pegawai_penggajian=" + encodeURIComponent(id_encrypted)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: data.message
                    }).then(() => {
                        if (typeof reload_table === "function") reload_table();
                        if (typeof reload_table_terkonfirmasi === "function") reload_table_terkonfirmasi();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi error: ' + error
                });
            });
    }


    function download_excel() {
        const header = [
            ["Nama", "Email", "Tanggal"]
        ];

        // 2. Jika ingin isi data juga, tambahkan baris berikutnya
        // const data = [
        //     ["John Doe", "john@example.com", "2024-07-18"],
        //     ["Jane Doe", "jane@example.com", "2024-07-18"]
        // ];
        // const ws_data = header.concat(data);

        const ws_data = header; // Hanya header saja

        // 3. Buat worksheet dan workbook
        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // 4. Download file
        XLSX.writeFile(wb, "template.xlsx");
    }

    function generate_penggajian() {
        var id_pegawai = <?php echo json_encode($id_pegawai); ?>;

        $.ajax({
            url: "<?= site_url('pegawai_penggajian/generate_penggajian') ?>",
            type: "POST",
            data: {
                id_pegawai: id_pegawai
            },
            dataType: "json",
            success: function(response) {
                console.log(response);

                response.forEach(function(msg) {
                    if (msg.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: msg.message,
                            showConfirmButton: false,
                            timer: 2500
                        }).then(() => {
                            location.reload(); // refresh halaman setelah swal sukses
                        });
                    } else if (msg.status === "warning") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplikat',
                            text: msg.message,
                            showConfirmButton: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg.message
                        });
                    }
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Terjadi kesalahan server!"
                });
                console.log("Error:", xhr.responseText);
            }
        });
    }

    function deletedata(id) {
        Swal.fire({
            title: 'Apa kamu yakin?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= site_url('pegawai_penggajian/delete') ?>",
                    type: "POST",
                    data: {
                        id_pegawai_penggajian: id
                    },
                    dataType: 'json',
                    success: function(respone) {
                        if (respone.status === true) {
                            reload_table();
                            $.toast({
                                text: "Data berhasil dihapus",
                                heading: 'Success',
                                icon: 'success',
                                hideAfter: 3000,
                                position: 'bottom-right'
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
            }
        });
    }


    // function save() {
    //     $('#btnSave').text('saving...').attr('disabled', true);
    //     let url = (save_method === 'add') ?
    //         "<?= site_url('pegawai_penggajian/insert') ?>" :
    //         "<?= site_url('pegawai_penggajian/update') ?>";

    //     $.ajax({
    //         url: url,
    //         type: "POST",
    //         data: $('#form').serialize(),
    //         dataType: "JSON",
    //         success: function(data) {
    //             if (data.status) {
    //                 $('#modal_form').modal('hide');
    //                 reload_table();
    //                 $.toast({
    //                     text: "Data berhasil disimpan",
    //                     heading: 'Success',
    //                     icon: 'success',
    //                     hideAfter: 3000,
    //                     position: 'bottom-right',
    //                 });
    //             } else {
    //                 for (let i = 0; i < data.inputerror.length; i++) {
    //                     $('[name="' + data.inputerror[i] + '" ]').parent().addClass('has-error');
    //                     $('[name="' + data.inputerror[i] + '" ]').next().text(data.error_string[i]);
    //                 }
    //             }
    //             $('#btnSave').html('<i class="fe-save"></i> Save').attr('disabled', false);
    //         },
    //         error: function() {
    //             alert('Error saving data');
    //             $('#btnSave').text('Save').attr('disabled', false);
    //         }
    //     });
    // }
</script>