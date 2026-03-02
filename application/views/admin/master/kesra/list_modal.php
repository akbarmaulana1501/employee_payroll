<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header badge-primary">
                <h4 class="modal-title mt-0"></h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal" onsubmit="return false;">
                    <input type="hidden" name="id_tunjangan">

                    <div class="form-group">
                        <label>Status Pegawai</label>
                        <input type="text" name="status_pegawai" class="form-control" placeholder="Contoh: PWTT, PNS">
                        <span class="help-block text-danger"></span>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Tunjangan</label>
                        <input type="text" name="tunjangan" class="form-control" id="tunjangan" placeholder="Masukkan nominal tunjangan">
                        <span class="help-block text-danger"></span>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">
                    <i class="fe-save"></i> Save
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>