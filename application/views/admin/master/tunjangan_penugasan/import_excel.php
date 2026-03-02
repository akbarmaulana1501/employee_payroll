<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Import Excel</li>
                </ol>
            </div>
            <!-- <?= $TombolCreate ?> -->
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6">
        <div class="card card-border">
            <div class="card-header border-primary pb-0"></div>
            <div class="card-body table-responsive">
                <form id="excelForm" enctype="multipart/form-data">
                    <input type="file" class="form-control" id="data_excel" name="data_excel" accept=".xlsx">
                    <div class="mt-2">
                        <button type="button" id="btnTampilkan" class="btn btn-sm btn-success">
                            <i class="fe-file-text"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="tampil_data_excel" style="display: none;">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-border">
                <div class="card-header border-primary pb-0"></div>
                <div class="card-body table-responsive">
                    <table id="table" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse:collapse;border-spacing:0;width:100%;">
                        <thead>
                            <tr>
                                <!-- <th width="2%">No</th>
                                <th class="text-center" width="8%">Action</th>
                                <th>Pegawai</th>
                                <th>Unit Usaha</th>
                                <th>Unit Level</th>
                                <th>Gaji Pokok</th> -->
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button id="btnSimpan" class="btn btn-sm btn-success"><i class="fe-download"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Load Modal -->
<?php $this->load->view('admin/master/gaji_pokok/list_modal') ?>
<?php $this->load->view('templates/includes/footer') ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script type="text/javascript">
    var save_method;
    var table;
    var base_url = '<?= base_url(); ?>';

    $(document).ready(function() {

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            orientation: "auto",
            locale: "id",
        });
    });

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('<?= $create . ' ' . $m ?>');

        $('.id_gaji_pokok').hide();
        $('[name="id_gaji_pokok"]').val('');
        $('[name="id_unit_usaha"]').val('');
        $('[name="id_unit_level"]').val('');
        $('[name="id_pegawai"]').val('');
        $('[name="nominal"]').val('');
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    document.getElementById('btnTampilkan').addEventListener('click', function() {
        var fileInput = document.getElementById('data_excel');
        var file = fileInput.files[0];

        if (!file) {
            Swal.fire({
                title: "Kesalahan!",
                text: "Masukan file Excel terlebih dahulu!",
                icon: "error"
            });
            return;
        }

        var reader = new FileReader();

        reader.onload = function(e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, {
                type: 'array'
            });

            // Ambil sheet pertama
            var sheetName = workbook.SheetNames[0];
            var worksheet = workbook.Sheets[sheetName];

            // Konversi ke array of objects
            var jsonData = XLSX.utils.sheet_to_json(worksheet, {
                header: 1
            });

            if (jsonData.length === 0) {
                alert("File kosong.");
                return;
            }

            // Kosongkan isi tabel
            const tableHead = document.querySelector("#table thead");
            const tableBody = document.querySelector("#table tbody");
            tableHead.innerHTML = '';
            tableBody.innerHTML = '';

            // Header
            var headerRow = document.createElement("tr");
            jsonData[0].forEach(header => {
                var th = document.createElement("th");
                th.textContent = header;
                headerRow.appendChild(th);
            });
            tableHead.appendChild(headerRow);

            // Data
            for (let i = 1; i < jsonData.length; i++) {
                var row = document.createElement("tr");
                jsonData[i].forEach(cell => {
                    var td = document.createElement("td");
                    td.textContent = cell;
                    row.appendChild(td);
                });
                tableBody.appendChild(row);
            }

            // Tampilkan kontainer
            document.getElementById("tampil_data_excel").style.display = "block";
        };

        reader.readAsArrayBuffer(file);
    });

    document.getElementById('btnSimpan').addEventListener('click', function() {
        const table = document.getElementById('table');
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText);
        const rows = Array.from(table.querySelectorAll('tbody tr'));

        const data = rows.map(row => {
            const cells = row.querySelectorAll('td');
            let obj = {};
            cells.forEach((cell, i) => {
                obj[headers[i]] = cell.innerText;
            });
            return obj;
        });

        fetch("<?= base_url('tunjangan_penugasan/simpan') ?>", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    data: data
                }) // <--- fix di sini
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect setelah Swal selesai
                        window.location.href = "<?= base_url('tunjangan_penugasan') ?>";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal menyimpan data.'
                });
            });
    });
</script>