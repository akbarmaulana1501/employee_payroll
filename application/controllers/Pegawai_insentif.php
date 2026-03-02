<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai_insentif extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_pegawai_insentif', 'pegawai_insentif');
    }

    public function index()
    {
        $this->data['title'] = 'Pegawai Insentif';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
                <button type="button" class="btn btn-sm btn-success waves-effect waves-light" onclick="import_excel()">
                    <i class="fe-file-text"></i> Excel
                </button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Pegawai Insentif';
        $this->data['ml'] = 'Daftar Pegawai Insentif';

        $this->data['list_pegawai'] = $this->db->get('pegawai')->result();
        $this->data['list_insentif'] = $this->db->get('master_tunjangan_insentif')->result();

        $this->template->load('templates/master', 'admin/master/pegawai_insentif/list', $this->data);
    }

    public function import_excel()
    {
        $this->data['title'] = 'Import Excel';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
                <button type="button" class="btn btn-sm btn-success waves-effect waves-light" onclick="import_excel()">
                    <i class="fe-download"></i> Excel
                </button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Import Excel';
        // $this->data['ml'] = 'Daftar Gaji Pokok';
        // $this->data['unit_levels'] = $this->M_unit_level->get_all();
        // $this->data['unit_usaha'] = $this->M_unit_usaha->get_all();
        // $this->data['unit_kerja'] = $this->M_unit_kerja->get_all();
        // $this->data['pegawai'] = $this->M_pegawai->get_all();

        $this->template->load('templates/master', 'admin/master/pegawai_insentif/import_excel', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->pegawai_insentif->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" class="btn-xs btn-primary" onclick="update(\'' . encrypt_url($item->id_pegawai_insentif) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" class="btn-xs btn-danger" onclick="deletedata(\'' . encrypt_url($item->id_pegawai_insentif) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->id_pegawai_insentif;
            $row[] = $item->nama ?: $item->id_pegawai;
            $row[] = $item->nm_tunjangan_insentif ?: $item->id_tunjangan;
            $row[] = 'Rp ' . number_format($item->nominal ?? 0, 0, ',', '.');
            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pegawai_insentif->count_all(),
            "recordsFiltered" => $this->pegawai_insentif->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    public function simpan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
        }

        $json = file_get_contents('php://input');
        $decoded = json_decode($json, true);
        $excel_data = isset($decoded['data']) ? $decoded['data'] : [];

        // var_dump($excel_data);
        // die;

        if (empty($excel_data) || !is_array($excel_data)) {
            echo json_encode(['status' => 'error', 'message' => 'Data kosong atau tidak valid.']);
            return;
        }

        // Ambil max ID saat ini hanya sekali
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_pegawai_insentif, 4) AS UNSIGNED)) AS maxKode FROM pegawai_insentif";
        $data = $ci->db->query($query)->row();
        $last_kode = $data->maxKode ?? 0;

        $counter = 1;
        $data_insert = [];

        foreach ($excel_data as $row) {
            $nama_pegawai = trim($row['Pegawai']);
            $nama_insentif = trim($row['Insentif']);
            $gaji_pokok = str_replace(['Rp.', '.', ',', ' '], '', $row['Nominal'] ?? '0');

            // Ambil ID pegawai dan tunjangan dari nama
            $ref_pegawai = $this->db->select('id_pegawai')->where('nama', $nama_pegawai)->get('pegawai')->row();
            $ref_tunjangan = $this->db->select('id_tunjangan')->where('nm_tunjangan_insentif', $nama_insentif)->get('master_tunjangan_insentif')->row();

            if ($ref_pegawai && $ref_tunjangan) {
                $new_id_number = $last_kode + $counter;
                $new_id = 'GP_' . str_pad($new_id_number, 3, '0', STR_PAD_LEFT);
                $counter++;

                $data_insert[] = [
                    'id_pegawai_insentif' => $new_id,
                    'id_tunjangan' => $ref_tunjangan->id_tunjangan,
                    'id_pegawai' => $ref_pegawai->id_pegawai,
                    'nominal' => $gaji_pokok
                ];
            }
        }

        if (empty($data_insert)) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau tidak valid.']);
            return;
        }

        $saved = $this->db->insert_batch('pegawai_insentif', $data_insert);

        if ($saved) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
        }
    }



    public function insert()
    {
        $this->_validate();

        $id_pegawai = $this->input->post('id_pegawai', true);
        $id_tunjangan = $this->input->post('id_tunjangan', true);

        if ($this->pegawai_insentif->is_duplicate($id_pegawai, $id_tunjangan)) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['id_pegawai', 'id_tunjangan'],
                'error_string' => ['Kombinasi Pegawai & Insentif sudah ada', 'Kombinasi Pegawai & Insentif sudah ada']
            ]);
            return;
        }

        $data = [
            'id_pegawai_insentif' => $this->generate_id(),
            'id_pegawai' => $id_pegawai,
            'id_tunjangan' => $id_tunjangan,
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->pegawai_insentif->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_pegawai_insentif', true);
        $id_pegawai = $this->input->post('id_pegawai', true);
        $id_tunjangan = $this->input->post('id_tunjangan', true);

        if ($this->pegawai_insentif->is_duplicate($id_pegawai, $id_tunjangan, $id)) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['id_pegawai', 'id_tunjangan'],
                'error_string' => ['Kombinasi Pegawai & Insentif sudah ada', 'Kombinasi Pegawai & Insentif sudah ada']
            ]);
            return;
        }

        $data = [
            'id_pegawai' => $id_pegawai,
            'id_tunjangan' => $id_tunjangan,
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->pegawai_insentif->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = decrypt_url($this->input->post('id_pegawai_insentif'));
        $this->pegawai_insentif->delete($id);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->pegawai_insentif->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    private function generate_id()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_pegawai_insentif, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('pegawai_insentif')->row();
        $max_id = $data->max_id + 1;
        return 'PIN' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('id_pegawai') == '') {
            $data['inputerror'][] = 'id_pegawai';
            $data['error_string'][] = 'Pegawai wajib dipilih';
            $data['status'] = FALSE;
        }
        if ($this->input->post('id_tunjangan') == '') {
            $data['inputerror'][] = 'id_tunjangan';
            $data['error_string'][] = 'Insentif wajib dipilih';
            $data['status'] = FALSE;
        }
        if ($this->input->post('nominal') == '' || !is_numeric(str_replace('.', '', $this->input->post('nominal')))) {
            $data['inputerror'][] = 'nominal';
            $data['error_string'][] = 'Nominal wajib diisi dan berupa angka';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
