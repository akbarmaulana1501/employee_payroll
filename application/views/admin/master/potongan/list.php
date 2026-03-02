<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Potongan</li>
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
                            <th width="2%">No</th>
                            <th class="text-center" width="8%">Action</th>
                            <th>Nama Potongan</th>
                            <th>Tipe Potongan</th>
                            <th>Persentase</th>
                            <th>Maksimal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<!-- load modal -->
<?php $this->load->view('admin/master/potongan/list_modal') ?>
<?php $this->load->view('templates/includes/footer') ?>

<script type="text/javascript">
    var save_method;
    var table;
    var base_url = '<?= base_url(); ?>';

    $(document).ready(function() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?= site_url('master_potongan/ajax_list') ?>",
                "type": "POST"
            },
        });

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

        $('.id_potongan').hide();
        $('[name="nama_potongan"]').val(null);
        $('[name="tipe_potongan"]').val(null);
        $('[name="persentase"]').val(null);
        $('[name="maksimal"]').val(null);
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function save() {
        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('master_potongan/insert') ?>";
        } else {
            url = "<?php echo site_url('master_potongan/update') ?>";
        }

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
                $('#btnSave').html('<i class="fe-save"> </i> Save');
                $('#btnSave').attr('disabled', false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('error');
                $('#btnSave').text('save');
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
                    url: "<?php echo site_url('master_potongan/delete'); ?>",
                    type: "POST",
                    data: "id_potongan=" + id,
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

    function update(id_potongan) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('.id_potongan').show();

        $.ajax({
            url: "<?php echo site_url('master_potongan/get_by_id') ?>/" + id_potongan,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_potongan"]').val(data.id_potongan);
                $('[name="nama_potongan"]').val(data.nama_potongan);
                $('[name="tipe_potongan"]').val(data.tipe_potongan);
                $('[name="persentase"]').val(data.persentase);
                $('[name="maksimal"]').val(data.maksimal);
                $('#modal_form').modal('show');
                $('.modal-title').text('<?= $edit . ' ' . $m ?>');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>