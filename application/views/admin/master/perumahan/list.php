<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Tunjangan</a></li>
                    <li class="breadcrumb-item active">Perumahan</li>
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
                            <th>ID Tunjangan</th>
                            <th>Eselon</th>
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
<?php $this->load->view('admin/master/perumahan/list_modal') ?>
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
                "url": "<?= site_url('master_tunjangan_perumahan/ajax_list') ?>",
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
        $('[name="id_tunjangan"]').val(null);
        $('[name="id_eselon"]').val(null);
        $('[name="tunjangan"]').val(null);
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

    // Event on keyup untuk auto-format rupiah saat input
    $('#tunjangan').on('input', function() {
        let value = $(this).val();
        value = value.replace(/^Rp\s?/, '');
        $(this).val(formatRupiah(value));
    });


    function save() {
        let rawTunjangan = $('#tunjangan').val().replace(/[^,\d]/g, '');
        $('#tunjangan').val(rawTunjangan);

        $('#btnSave').text('saving...').attr('disabled', true);
        let url = (save_method === 'add') ?
            "<?= site_url('master_tunjangan_perumahan/insert') ?>" :
            "<?= site_url('master_tunjangan_perumahan/update') ?>";

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
                    url: "<?= site_url('master_tunjangan_perumahan/delete') ?>",
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

        $.get("<?= site_url('master_tunjangan_perumahan/get_by_id') ?>/" + id, function(data) {
            $('[name="id_tunjangan"]').val(data.id_tunjangan);
            $('[name="id_eselon"]').val(data.id_eselon);
            $('[name="tunjangan"]').val(data.tunjangan);
            $('#modal_form').modal('show');
            $('.modal-title').text('<?= $edit . ' ' . $m ?>');
        }, 'json');
    }
</script>