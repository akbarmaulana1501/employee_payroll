<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Master</a></li>
                    <li class="breadcrumb-item active">Imbalan Jasa Produksi</li>
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
                            <th>Pegawai</th>
                            <th>Unit Usaha</th>
                            <th>Unit Level</th>
                            <th>Tunjangan Bonus</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Load Modal -->
<?php $this->load->view('admin/master/imbalan_jasa_produksi/list_modal') ?>
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
                "url": "<?= site_url('imbalan_jasa_produksi/ajax_list') ?>",
                "type": "POST"
            }
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

        $('.id_tunjangan').hide();
        $('[name="id_tunjangan"]').val('');
        $('[name="id_unit_usaha"]').val('');
        $('[name="id_unit_level"]').val('');
        $('[name="id_pegawai"]').val('');
        $('[name="nominal"]').val('');
    }

    function reload_table() {
        table.ajax.reload(null, false);
    }

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

    $('#nominal').on('input', function() {
        let value = $(this).val();
        value = value.replace(/^Rp\s?/, '');
        $(this).val(formatRupiah(value));
    });

    function save() {
        let rawNominal = $('#nominal').val().replace(/[^,\d]/g, '');
        $('#nominal').val(rawNominal);
        $('#btnSave').text('saving...').attr('disabled', true);
        let url = (save_method === 'add') ? "<?= site_url('imbalan_jasa_produksi/insert') ?>" : "<?= site_url('imbalan_jasa_produksi/update') ?>";

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
                    url: "<?= site_url('imbalan_jasa_produksi/delete') ?>",
                    type: "POST",
                    data: {
                        id_tunjangan: id
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
        $('.id_tunjangan').show();

        $.get("<?= site_url('imbalan_jasa_produksi/get_by_id') ?>/" + id, function(data) {
            $('[name="id_tunjangan"]').val(data.id_tunjangan_imbalan);
            $('[name="id_unit_usaha"]').val(data.id_unit_usaha);
            $('[name="id_unit_level"]').val(data.id_unit_level);

            // Pastikan select2 update
            $('[name="id_pegawai"]').val(data.id_pegawai).trigger('change.select2');

            $('[name="nominal"]').val(data.nominal);
            $('#modal_form').modal('show');
            $('.modal-title').text('<?= $edit . ' ' . $m ?>');
        }, 'json');
    }


    $(document).ready(function() {
        $('#id_pegawai').select2({
            dropdownParent: $('#modal_form'),
            placeholder: "-- Pilih Pegawai --",
            allowClear: true,
            width: '100%'
        });

        $('#id_pegawai').on('change', function() {
            var id_pegawai = $(this).val();
            $('#id_unit_usaha').val('');
            $('#id_unit_level').val('');
            if (id_pegawai) {
                $.ajax({
                    url: "<?= site_url('imbalan_jasa_produksi/get_penempatan_by_pegawai') ?>",
                    type: "POST",
                    data: {
                        id_pegawai: id_pegawai
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data && data.id_unit_usaha && data.id_unit_level) {
                            $('#id_unit_usaha').val(data.id_unit_usaha);
                            $('#id_unit_level').val(data.id_unit_level);
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