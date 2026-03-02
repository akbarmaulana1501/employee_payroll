<?php // VIEW: application/views/admin/master/jabatan/list.php 
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Tunjangan</a></li>
                    <li class="breadcrumb-item active">Jabatan</li>
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
                            <th>No</th>
                            <th>Action</th>
                            <th>Unit Level</th>
                            <th>Unit Usaha</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/master/jabatan/list_modal') ?>
<?php $this->load->view('templates/includes/footer') ?>

<script type="text/javascript">
    var save_method;
    var table;

    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('tunjangan_jabatan/ajax_list') ?>",
                "type": "POST"
            }
        });

        $('#form').on('submit', function(e) {
            e.preventDefault();
        });
        $('#btnSave').attr('type', 'button');
    });


    function reload_table() {
        table.ajax.reload(null, false);
    }

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('[name="id_tunjangan_jabatan"]').val('');
        $('#modal_form').modal('show');
        $('.modal-title').text('<?= $create . ' ' . $m ?>');
    }

    function save() {
        $('#btnSave').text('saving...').attr('disabled', true);
        var url = (save_method === 'add') ?
            "<?= site_url('tunjangan_jabatan/insert') ?>" :
            "<?= site_url('tunjangan_jabatan/update') ?>";

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
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').addClass('is-invalid');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
                    }
                }
                $('#btnSave').text('Save').attr('disabled', false);
            },
            error: function() {
                alert('Gagal menyimpan data');
                $('#btnSave').text('Save').attr('disabled', false);
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
                    url: "<?= site_url('tunjangan_jabatan/delete') ?>",
                    type: "POST",
                    data: "id_tunjangan_jabatan=" + id,
                    cache: false,
                    dataType: 'json',
                    success: function(respone) {
                        if (respone.status === true) {
                            reload_table();
                        } else {
                            alert('Data gagal dihapus');
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
        $.get("<?= site_url('tunjangan_jabatan/get_by_id') ?>/" + id, function(data) {
            $('[name="id_tunjangan_jabatan"]').val(data.id_tunjangan_jabatan);
            $('[name="id_unit_level"]').val(data.id_unit_level);
            $('[name="id_unit_usaha"]').val(data.id_unit_usaha);
            $('[name="jumlah"]').val(data.jumlah);
            $('#modal_form').modal('show');
            $('.modal-title').text('<?= $edit . ' ' . $m ?>');
        }, 'json');
    }
</script>