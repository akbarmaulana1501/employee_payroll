<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Tunjangan</a></li>
                    <li class="breadcrumb-item active">Fungsional</li>
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
                            <th>Nama Tunjangan</th>
                            <th>Nama Eselon</th>
                            <th>Jenis Eselon</th>
                            <th>Unit Usaha</th>
                            <th>Jumlah Tunjangan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- load modal -->
<?php $this->load->view('admin/master/fungsional/list_modal') ?>
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
                "url": "<?= site_url('master_tunjangan_fungsional/ajax_list') ?>",
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
        $('.modal-title').text('<?= $create . ' ' . $m ?>');

        $('.id_tunjangan_fungsional').hide();
        $('[name="id_tunjangan"]').val(null);
        $('[name="id_eselon"]').val(null);
        $('[name="id_unit_usaha"]').val(null);
        $('[name="tunjangan"]').val(null);
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    // Fungsi JS Format Rupiah
    function formatRupiah(angka, prefix = 'Rp ') {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
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

    // Event on keyup untuk auto-format rupiah saat input
    $('#tunjangan').on('input', function() {
        let value = $(this).val();
        value = value.replace(/^Rp\s?/, '');
        $(this).val(formatRupiah(value));
    });


    function save() {
        let rawNominal = $('#tunjangan').val().replace(/[^,\d]/g, '');
        $('#tunjangan').val(rawNominal);

        $('#btnSave').text('saving...');
        $('#btnSave').attr('disabled', true);
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('master_tunjangan_fungsional/insert') ?>";
        } else {
            url = "<?php echo site_url('master_tunjangan_fungsional/update') ?>";
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
                    url: "<?php echo site_url('master_tunjangan_fungsional/delete'); ?>",
                    type: "POST",
                    data: "id_tunjangan=" + id,
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

    function update(id) {
        save_method = 'update';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('.id_tunjangan').show();

        $.ajax({
            url: "<?php echo site_url('master_tunjangan_fungsional/get_by_id') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_tunjangan"]').val(data.id_tunjangan);
                $('[name="id_eselon"]').val(data.id_eselon);
                $('[name="id_unit_usaha"]').val(data.id_unit_usaha);
                $('[name="tunjangan"]').val(data.tunjangan);
                $('#modal_form').modal('show');
                $('.modal-title').text('<?= $edit . ' ' . $m ?>');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    }
</script>