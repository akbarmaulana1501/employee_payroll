<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Gaji</a></li>
                    <li class="breadcrumb-item active">Detail Gaji</li>
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
                            <th class="text-center" width="12%">Action</th>
                            <th>Jenis Komponen</th>
                            <th>Nama Komponen</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Load modal form -->
<?php $this->load->view('admin/gaji/detail/list_modal'); ?>
<?php $this->load->view('templates/includes/footer'); ?>

<script>
    var save_method;
    var table;
    var base_url = '<?= base_url(); ?>';
    var id_gaji_header = '<?= $id_gaji_header ?>';

    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": base_url + "gaji_detail/ajax_list/" + id_gaji_header,
                "type": "POST"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 1] }
            ]
        });
    });

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('[name="id_gaji_detail"]').val('');
        $('[name="id_gaji_header"]').val(id_gaji_header); // set otomatis id_gaji_header
        $('#modal_form').modal('show');
        $('.modal-title').text('Tambah Detail Gaji');
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function save() {
        $('#btnSave').text('Saving...');
        $('#btnSave').attr('disabled', true);

        var url = save_method == 'add' ? base_url + 'gaji_detail/insert' : base_url + 'gaji_detail/update';

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                    $.toast({
                        text: "Data berhasil disimpan",
                        heading: 'Success',
                        icon: 'success',
                        position: 'bottom-right',
                        hideAfter: 3000,
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
            error: function() {
                alert('Error saat menyimpan data');
                $('#btnSave').text('Save');
                $('#btnSave').attr('disabled', false);
            }
        });
    }

    function update(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();

        $.ajax({
            url: base_url + 'gaji_detail/get_by_id/' + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_gaji_detail"]').val(data.id_gaji_detail);
                $('[name="jenis_komponen"]').val(data.jenis_komponen);
                $('[name="nama_komponen"]').val(data.nama_komponen);
                $('[name="jumlah"]').val(data.jumlah);
                $('[name="id_gaji_header"]').val(data.id_gaji_header);
                $('#modal_form').modal('show');
                $('.modal-title').text('Edit Detail Gaji');
            },
            error: function() {
                alert('Error mengambil data');
            }
        });
    }

    function deletedata(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'gaji_detail/delete',
                    type: "POST",
                    data: "id_gaji_detail=" + id,
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status) {
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
                        alert('Error menghapus data');
                    }
                });
            }
        });
    }
</script>
