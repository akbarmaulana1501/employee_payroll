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


<div class="col-md-3">
    <div class="form-group">
        <label for="pilih_rekap">Pilih Rekap</label>
        <select name="pilih_rekap" id="pilih_rekap" class="form-control" required>
            <option value="">-- Pilih Rekap --</option>
            <option value="gaji">Gaji</option>
            <option value="thr">THR</option>
            <option value="bonus">Bonus</option>
        </select>
    </div>
</div>


<div class="row" id="table_gaji_wrapper">
    <div class="col-sm-12">
        <div class="card card-border">
            <div class="card-header border-primary pb-0"></div>
            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Bulan Awal</label>
                        <input type="month" id="start_month" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Bulan Akhir</label>
                        <input type="month" id="end_month" class="form-control">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button id="filter_btn" class="btn btn-primary">Filter</button>
                        <button id="reset_btn" class="btn btn-secondary">Reset</button>
                    </div>
                </div>

                <table id="table_terkonfirmasi" class="table table-striped table-bordered dt-responsive nowrap"
                    style="border-collapse:collapse;border-spacing:0;width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Total Pendapatan</th>
                            <th>Total Potongan</th>
                            <th>Gaji Kotor</th>
                            <th>Gaji Bersih</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row" id="table_thr_wrapper" style="display:none;">
    <div class="col-sm-12">
        <div class="card card-border">
            <div class="card-header border-primary pb-0"></div>
            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Bulan Awal</label>
                        <input type="month" id="start_month_thr" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Bulan Akhir</label>
                        <input type="month" id="end_month_thr" class="form-control">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button id="filter_btn_thr" class="btn btn-primary">Filter</button>
                        <button id="reset_btn_thr" class="btn btn-secondary">Reset</button>
                    </div>
                </div>

                <table id="table_thr" class="table table-striped table-bordered dt-responsive nowrap"
                    style="border-collapse:collapse;border-spacing:0;width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <!-- <th>Bonus</th> -->
                            <th>THR</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="row" id="table_bonus_wrapper" style="display:none;">
    <div class="col-sm-12">
        <div class="card card-border">
            <div class="card-header border-primary pb-0"></div>
            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Bulan Awal</label>
                        <input type="month" id="start_month_bonus" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Bulan Akhir</label>
                        <input type="month" id="end_month_bonus" class="form-control">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button id="filter_btn_bonus" class="btn btn-primary">Filter</button>
                        <button id="reset_btn_bonus" class="btn btn-secondary">Reset</button>
                    </div>
                </div>

                <table id="table_bonus" class="table table-striped table-bordered dt-responsive nowrap"
                    style="border-collapse:collapse;border-spacing:0;width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Bonus</th>
                            <!-- <th>THR</th> -->
                            <th>Status</th>
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
<!-- pdfmake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<!-- SheetJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


<script type="text/javascript">
    var tableTerkonfirmasi;

    function formatRupiah(x) {
        x = parseFloat(x);
        if (isNaN(x) || x == 0) return '-';
        return 'Rp. ' + x.toLocaleString('id-ID');
    }

    $(document).ready(function() {
        tableTerkonfirmasi = $('#table_terkonfirmasi').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= site_url('pegawai_penggajian/ajax_list_terkonfirmasi') ?>",
                "type": "POST",
                "data": function(d) {
                    d.start_month = $('#start_month').val(); // format: yyyy-mm
                    d.end_month = $('#end_month').val(); // format: yyyy-mm
                }
            }
        });

        $('#filter_btn').on('click', function() {
            tableTerkonfirmasi.ajax.reload();
        });

        $('#reset_btn').on('click', function() {
            $('#start_month').val('');
            $('#end_month').val('');
            tableTerkonfirmasi.ajax.reload();
        });
    });


    $(document).ready(function() {
        tableThr = $('#table_thr').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= site_url('pegawai_penggajian/ajax_list_thr') ?>",
                "type": "POST",
                "data": function(d) {
                    d.start_month = $('#start_month_thr').val(); // format: yyyy-mm
                    d.end_month = $('#end_month_thr').val(); // format: yyyy-mm
                }
            }
        });

        $('#filter_btn_thr').on('click', function() {
            tableThr.ajax.reload();
        });

        $('#reset_btn_thr').on('click', function() {
            $('#start_month_thr').val('');
            $('#end_month_thr').val('');
            tableThr.ajax.reload();
        });
    });


    $(document).ready(function() {
        tableThr = $('#table_bonus').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= site_url('pegawai_penggajian/ajax_list_bonus') ?>",
                "type": "POST",
                "data": function(d) {
                    d.start_month = $('#start_month_bonus').val(); // format: yyyy-mm
                    d.end_month = $('#end_month_bonus').val(); // format: yyyy-mm
                }
            }
        });

        $('#filter_btn_bonus').on('click', function() {
            tableThr.ajax.reload();
        });

        $('#reset_btn_bonus').on('click', function() {
            $('#start_month_bonus').val('');
            $('#end_month_bonus').val('');
            tableThr.ajax.reload();
        });
    });



    $(document).on('click', '.filter-tahun-option', function(e) {
        e.preventDefault();
        let val = $(this).data('value');
        $('#filter-tahun').val(val).trigger('change');
        let text = $(this).text();
        $(this).closest('.btn-group').find('.dropdown-toggle').html('<i class="fe-calendar"></i> ' + text);
    });

    $(document).on('click', '.filter-bulan-option', function(e) {
        e.preventDefault();
        let val = $(this).data('value');
        $('#filter-bulan').val(val).trigger('change');
        let text = $(this).text();
        $(this).closest('.btn-group').find('.dropdown-toggle').html('<i class="fe-clock"></i> ' + text);
    });

    $('#filter-tahun, #filter-bulan').on('change', function() {
        tableTerkonfirmasi.ajax.reload();
    });

    $(document).on('click', '.detail-trigger', function() {
        let type = $(this).data('type');
        let nama = $(this).data('nama');

        if (type === 'pendapatan') {
            let pendapatan = $(this).data('pendapatan'); // otomatis object di jQuery
            if (typeof pendapatan === "string") pendapatan = JSON.parse(pendapatan);

            let total = $(this).data('total');
            let html = `<table class="table table-sm table-borderless mb-0">`;

            if (pendapatan && Object.keys(pendapatan).length > 0) {
                $.each(pendapatan, function(label, val) {
                    html += `<tr>
                <td>${label}</td>
                <td class="text-right font-weight-semibold">${formatRupiah(val)}</td>
            </tr>`;
                });
            } else {
                html += `<tr><td colspan="2" class="text-center text-muted">Tidak ada pendapatan</td></tr>`;
            }

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
            // Potongan bisa string (json) atau objek, parse jika perlu
            if (typeof potongan === "string" && potongan !== "") potongan = JSON.parse(potongan);
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

    function reload_table_terkonfirmasi() {
        tableTerkonfirmasi.ajax.reload(null, false);
    }


    // Ambil data dari DataTables
    function fetchAllData(callback) {
        $.ajax({
            url: "<?= site_url('pegawai_penggajian/ajax_list_terkonfirmasi') ?>",
            type: "POST",
            data: {
                bulan: $('#filter-bulan').val(),
                tahun: $('#filter-tahun').val(),
                length: -1, // ambil semua data
                start: 0
            },
            dataType: "json",
            success: function(res) {
                // res.data biasanya array dari server
                callback(res.data);
            }
        });
    }

    function stripHTML(input) {
        if (!input) return ''; // aman untuk undefined/null
        return String(input).replace(/<[^>]*>?/gm, '');
    }


    // PDF Export
    function print_excel() {
        // Ambil semua data dari server
        function fetchAllData(callback) {
            fetch(`<?= base_url() ?>pegawai_penggajian/get_terkonfirmasi_excel`)
                .then(res => res.json())
                .then(data => callback(data))
                .catch(err => {
                    console.error('Gagal ambil data Excel:', err);
                    callback([]);
                });
        }

        fetchAllData(function(data) {
            console.log(data)
            if (!data || !Array.isArray(data) || data.length === 0) {
                alert('Data kosong, tidak bisa membuat laporan.');
                return;
            }

            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Laporan Gaji');

            // Cari header unik untuk pendapatan & potongan
            let pendapatanHeaders = new Set();
            let potonganHeaders = new Set();

            data.forEach(row => {
                if (row.pendapatan_rincian) Object.keys(row.pendapatan_rincian).forEach(k => pendapatanHeaders.add(k));
                if (row.potongan_rincian) Object.keys(row.potongan_rincian).forEach(k => potonganHeaders.add(k));
            });

            pendapatanHeaders = Array.from(pendapatanHeaders);
            potonganHeaders = Array.from(potonganHeaders);

            // Header Excel
            const headers = [
                'No', 'Pegawai', 'Bulan', 'Tahun',
                ...pendapatanHeaders,
                'Total Pendapatan',
                ...potonganHeaders,
                'Total Potongan',
                'Gaji Kotor',
                'Gaji Bersih'
            ];

            const headerRow = worksheet.addRow(headers);
            headerRow.font = {
                bold: true
            };
            headerRow.alignment = {
                horizontal: 'center'
            };
            headerRow.eachCell(cell => {
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {
                        argb: 'DDDDDD'
                    }
                };
                cell.border = {
                    top: {
                        style: 'thin'
                    },
                    left: {
                        style: 'thin'
                    },
                    bottom: {
                        style: 'thin'
                    },
                    right: {
                        style: 'thin'
                    }
                };
            });

            // Isi data per pegawai
            data.forEach((row, index) => {
                const rowData = [
                    index + 1,
                    row.nama || '',
                    row.bulan || '',
                    row.tahun || ''
                ];

                // Pendapatan
                pendapatanHeaders.forEach(h => {
                    rowData.push(row.pendapatan_rincian[h] != null ? row.pendapatan_rincian[h] : 0);
                });
                rowData.push(row.total_pendapatan || 0);

                // Potongan
                potonganHeaders.forEach(h => {
                    rowData.push(row.potongan_rincian[h] != null ? row.potongan_rincian[h] : 0);
                });
                rowData.push(row.total_potongan || 0);

                // Gaji Kotor & Bersih
                rowData.push(row.gaji_kotor || 0);
                rowData.push(row.gaji_bersih || 0);

                const newRow = worksheet.addRow(rowData);

                newRow.eachCell(cell => {
                    cell.border = {
                        top: {
                            style: 'thin'
                        },
                        left: {
                            style: 'thin'
                        },
                        bottom: {
                            style: 'thin'
                        },
                        right: {
                            style: 'thin'
                        }
                    };
                });
            });

            // Simpan file Excel
            workbook.xlsx.writeBuffer()
                .then(buffer => saveAs(new Blob([buffer]), 'laporan_gaji.xlsx'))
                .catch(err => console.error('Gagal menyimpan file Excel:', err));
        });
    }


    function print_pdf() {
        fetch("<?= base_url('pegawai_penggajian/get_terkonfirmasi_excel'); ?>")
            .then(res => res.json())
            .then(data => {
                if (!data || !Array.isArray(data) || data.length === 0) {
                    alert("Data kosong, tidak bisa membuat laporan.");
                    return;
                }

                // Cari header unik
                let pendapatanHeaders = new Set();
                let potonganHeaders = new Set();

                data.forEach(row => {
                    if (row.pendapatan_rincian) {
                        Object.keys(row.pendapatan_rincian).forEach(k => pendapatanHeaders.add(k));
                    }
                    if (row.potongan_rincian) {
                        Object.keys(row.potongan_rincian).forEach(k => potonganHeaders.add(k));
                    }
                });

                pendapatanHeaders = Array.from(pendapatanHeaders);
                potonganHeaders = Array.from(potonganHeaders);

                // ------------------------
                // TABEL PENDAPATAN
                // ------------------------
                let bodyPendapatan = [
                    [
                        "No", "Pegawai", "Bulan", "Tahun",
                        ...pendapatanHeaders,
                        "Total Pendapatan"
                    ]
                ];

                data.forEach((row, index) => {
                    let rowData = [
                        index + 1,
                        row.nama || "",
                        row.bulan || "",
                        row.tahun || ""
                    ];

                    pendapatanHeaders.forEach(h => {
                        rowData.push(row.pendapatan_rincian[h] != null ? row.pendapatan_rincian[h] : 0);
                    });

                    rowData.push(row.total_pendapatan || 0);
                    bodyPendapatan.push(rowData);
                });

                // ------------------------
                // TABEL POTONGAN
                // ------------------------
                let bodyPotongan = [
                    [
                        "No", "Pegawai", "Bulan", "Tahun",
                        ...potonganHeaders,
                        "Total Potongan",
                        "Gaji Kotor",
                        "Gaji Bersih"
                    ]
                ];

                data.forEach((row, index) => {
                    let rowData = [
                        index + 1,
                        row.nama || "",
                        row.bulan || "",
                        row.tahun || ""
                    ];

                    potonganHeaders.forEach(h => {
                        rowData.push(row.potongan_rincian[h] != null ? row.potongan_rincian[h] : 0);
                    });

                    rowData.push(row.total_potongan || 0);
                    rowData.push(row.gaji_kotor || 0);
                    rowData.push(row.gaji_bersih || 0);

                    bodyPotongan.push(rowData);
                });

                // ------------------------
                // Definisi PDF
                // ------------------------
                var docDefinition = {
                    pageOrientation: "landscape",
                    pageMargins: [10, 10, 10, 10], // margin kecil
                    content: [{
                            text: "Laporan Gaji Pegawai",
                            style: "header"
                        },

                        {
                            text: "Rincian Pendapatan",
                            style: "subheader",
                            margin: [0, 10, 0, 5]
                        },
                        {
                            table: {
                                headerRows: 1,
                                widths: Array(bodyPendapatan[0].length).fill('*'), // kolom rata
                                body: bodyPendapatan
                            },
                            layout: {
                                paddingLeft: () => 1,
                                paddingRight: () => 1,
                                paddingTop: () => 1,
                                paddingBottom: () => 1
                            },
                            fontSize: 6 // kecilkan font tabel
                        },

                        {
                            text: "Rincian Potongan",
                            style: "subheader",
                            margin: [0, 15, 0, 5]
                        },
                        {
                            table: {
                                headerRows: 1,
                                widths: Array(bodyPotongan[0].length).fill('*'),
                                body: bodyPotongan
                            },
                            layout: {
                                paddingLeft: () => 1,
                                paddingRight: () => 1,
                                paddingTop: () => 1,
                                paddingBottom: () => 1
                            },
                            fontSize: 6
                        }
                    ],
                    styles: {
                        header: {
                            fontSize: 12,
                            bold: true,
                            margin: [0, 0, 0, 10]
                        },
                        subheader: {
                            fontSize: 10,
                            bold: true
                        }
                    },
                    defaultStyle: {
                        fontSize: 6
                    }
                };

                pdfMake.createPdf(docDefinition).download("laporan_gaji.pdf");
            })
            .catch(err => {
                console.error("Gagal ambil data PDF:", err);
            });
    }
</script>

<script>
    document.getElementById('pilih_rekap').addEventListener('change', function() {
        var pilihan = this.value;

        // sembunyikan dulu semua wrapper
        document.getElementById('table_gaji_wrapper').style.display = 'none';
        document.getElementById('table_thr_wrapper').style.display = 'none';
        document.getElementById('table_bonus_wrapper').style.display = 'none';

        // reset range date setiap kali ganti pilihan
        document.getElementById('start_month').value = "";
        document.getElementById('end_month').value = "";
        document.getElementById('start_month_thr').value = "";
        document.getElementById('end_month_thr').value = "";
        document.getElementById('start_month_bonus').value = "";
        document.getElementById('end_month_bonus').value = "";

        if (pilihan === 'gaji') {
            document.getElementById('table_gaji_wrapper').style.display = 'block';
        } else if (pilihan === 'thr') {
            document.getElementById('table_thr_wrapper').style.display = 'block';
        } else if (pilihan === 'bonus') {
            document.getElementById('table_bonus_wrapper').style.display = 'block';

        }
    });
</script>