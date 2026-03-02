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
        // table = $('#table').DataTable({
        //     "processing": true,
        //     "serverSide": true,
        //     "order": [],
        //     "ajax": {
        //         "url": "<?= site_url('master_gaji_pokok/') ?>",
        //         "type": "POST"
        //     }
        // });

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

        fetch("<?= base_url('master_gaji_pokok/simpan') ?>", {
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


    function save() {
        $('#btnSave').text('saving...').attr('disabled', true);
        let url = (save_method === 'add') ? "<?= site_url('master_gaji_pokok/insert') ?>" : "<?= site_url('master_gaji_pokok/update') ?>";

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
                        hideAfter: 3000,
                        position: 'bottom-right',
                    });
                } else {
                    for (let i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSave').html('<i class="fe-save"> </i> Save').attr('disabled', false);
            },
            error: function() {
                alert('Error saving data');
                $('#btnSave').text('Save').attr('disabled', false);
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
                    url: "<?= site_url('master_gaji_pokok/delete') ?>",
                    type: "POST",
                    data: {
                        id_gaji_pokok: id
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

    function update(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('.id_gaji_pokok').show();

        $.get("<?= site_url('master_gaji_pokok/get_by_id') ?>/" + id, function(data) {
            $('[name="id_gaji_pokok"]').val(data.id_gaji_pokok);
            $('[name="id_unit_usaha"]').val(data.id_unit_usaha);
            $('[name="id_unit_level"]').val(data.id_unit_level);
            $('[name="id_pegawai"]').val(data.id_pegawai);
            $('[name="nominal"]').val(data.nominal);
            $('#modal_form').modal('show');
            $('.modal-title').text('<?= $edit . ' ' . $m ?>');
        }, 'json');
    }

    $(document).ready(function() {
        $('#id_unit_usaha, #id_unit_kerja').on('change', function() {
            const id_usaha = $('#id_unit_usaha').val();
            const id_kerja = $('#id_unit_kerja').val();
            $('#id_pegawai').html('<option value="">-- Pilih Pegawai --</option>');

            if (id_usaha && id_kerja) {
                $.ajax({
                    url: '<?= site_url('master_gaji_pokok/get_pegawai_by_unit') ?>',
                    type: 'POST',
                    data: {
                        id_unit_usaha: id_usaha,
                        id_unit_kerja: id_kerja
                    },
                    dataType: 'json',
                    success: function(data) {
                        let options = '<option value="">-- Pilih Pegawai --</option>';
                        $.each(data, function(i, val) {
                            options += `<option value="${val.id_pegawai}">${val.nama}</option>`;
                        });
                        $('#id_pegawai').html(options);
                    }
                });
            }
        });

        $('#id_pegawai').on('change', function() {
            const id_pegawai = $(this).val();
            if (id_pegawai) {
                $.ajax({
                    url: "<?= site_url('master_gaji_pokok/get_unit_level_by_pegawai') ?>",
                    type: "POST",
                    data: {
                        id_pegawai: id_pegawai
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data); // Tambahkan ini untuk debugging
                        if (data && data.id_unit_level) {
                            $('#id_unit_level').val(data.id_unit_level);
                            $('#nm_unit_level').val(data.nm_unit_level); // <- pastikan nama ini benar
                        } else {
                            $('#id_unit_level').val('');
                            $('#nm_unit_level').val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }
        });


    });
</script>