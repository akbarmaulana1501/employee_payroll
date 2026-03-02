<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pegawai</a></li>
                    <li class="breadcrumb-item active">Kode Pegawai</li>
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
                            <th>ID Pegawai Kode</th>
                            <th>Pegawai</th>
                            <th>Kode Tunjangan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- load modal -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<?php $this->load->view('admin/master/pegawai_kode/list_modal') ?>
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
                "url": "<?= site_url('pegawai_kode/ajax_list') ?>",
                "type": "POST"
            },
        });

        $('#id_pegawai').select2({
            dropdownParent: $('#modal_form'),
            placeholder: "-- Pilih Pegawai --",
            allowClear: true,
            width: '100%'
        });

        $('#id_master').select2({
            dropdownParent: $('#modal_form'),
            placeholder: "-- Pilih Kode Tunjangan --",
            allowClear: true,
            width: '100%'
        });

        // Format rupiah saat input
        $('#nominal').on('keyup', function() {
            this.value = formatRupiah(this.value, 'Rp ');
        });
    });

    function add() {
        save_method = 'add';
        $('#form')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.help-block').empty();
        $('#modal_form').modal('show');
        $('.modal-title').text('<?= $create . ' ' . $m ?>');
        $('#id_pegawai').val('').trigger('change');
        $('#id_master').val('').trigger('change');
        $('#nominal').val('');
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

    function import_excel() {
        window.location.href = "<?= base_url('pegawai_kode/import_excel') ?>"
    }


    function save() {
        var rawNominal = $('#nominal').val().replace(/[^,\d]/g, '');
        $('#nominal').val(rawNominal);

        $('#btnSave').text('saving...').attr('disabled', true);
        let url = (save_method === 'add') ?
            "<?= site_url('pegawai_kode/insert') ?>" :
            "<?= site_url('pegawai_kode/update') ?>";

        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {
                $('#nominal').val(formatRupiah($('#nominal').val(), 'Rp '));
                if (data.status) {
                    $('#modal_form').modal('hide');
                    reload_table();
                    $.toast({
                        text: "Data berhasil disimpan",
                        heading: 'Success',
                        icon: 'success',
                        showHideTransition: 'fade',
                        hideAfter: 3000,
                        position: 'bottom-right',
                    });
                } else {
                    for (let i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error');
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
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
                    url: "<?= site_url('pegawai_kode/delete') ?>",
                    type: "POST",
                    data: "id_pegawai_kode=" + id,
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

        $.get("<?= site_url('pegawai_kode/get_by_id') ?>/" + id, function(data) {
            $('[name="id_pegawai_kode"]').val(data.id_pegawai_kode);
            $('#id_pegawai').val(data.id_pegawai).trigger('change');
            $('#id_master').val(data.id_master).trigger('change');
            $('#nominal').val(formatRupiah(data.nominal, 'Rp '));
            $('#modal_form').modal('show');
            $('.modal-title').text('<?= $edit . ' ' . $m ?>');
        }, 'json');
    }

    // Format rupiah JS helper
    function formatRupiah(angka, prefix) {
        let number_string = angka ? angka.toString().replace(/[^,\d]/g, '') : '',
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    $(document).ready(function() {
        $('#id_master').on('change', function() {
            let id_master = $(this).val();
            let id_pegawai = $('#id_pegawai').val();

            if (id_master && id_pegawai) {
                $.ajax({
                    url: '<?= site_url("pegawai_kode/get_potongan_by_kode") ?>',
                    type: 'POST',
                    data: {
                        id_master: id_master,
                        id_pegawai: id_pegawai
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#nominal').val(res.nominal);
                        if (res.auto) {
                            $('#nominal').prop('readonly', true);
                        } else {
                            $('#nominal').prop('readonly', false).val('');
                        }
                    }
                });
            } else {
                $('#nominal').prop('readonly', false).val('');
            }
        });
    });
</script>