<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_gaji_pokok extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_gaji_pokok', 'gaji_pokok');
        $this->load->model('M_potongan', 'potongan');
        $this->load->model('M_pegawai_kode', 'pegawai_kode');
        $this->load->model('M_unit_level');
        $this->load->model('M_unit_usaha');
        $this->load->model('M_unit_kerja');
        $this->load->model('M_pegawai');
    }

    public function index()
    {
        $this->data['title'] = 'Master Gaji Pokok';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
                <button type="button" class="btn btn-sm btn-success waves-effect waves-light" onclick="import_excel()">
                    <i class="fe-file-text"></i> Excel
                </button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Master Gaji Pokok';
        $this->data['ml'] = 'Daftar Gaji Pokok';
        $this->data['unit_levels'] = $this->M_unit_level->get_all();
        $this->data['unit_usaha'] = $this->M_unit_usaha->get_all();
        $this->data['unit_kerja'] = $this->M_unit_kerja->get_all();
        $this->data['pegawai'] = $this->M_pegawai->get_all();

        $this->template->load('templates/master', 'admin/master/gaji_pokok/list', $this->data);
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

        $this->template->load('templates/master', 'admin/master/gaji_pokok/import_excel', $this->data);
    }

    public function simpan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
        }

        $json = file_get_contents('php://input');
        $decoded = json_decode($json, true);
        $excel_data = isset($decoded['data']) ? $decoded['data'] : [];

        if (empty($excel_data) || !is_array($excel_data)) {
            echo json_encode(['status' => 'error', 'message' => 'Data kosong atau tidak valid.']);
            return;
        }

        // Ambil max ID saat ini hanya sekali
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_gaji_pokok, 4) AS UNSIGNED)) AS maxKode FROM master_gaji_pokok";
        $data = $ci->db->query($query)->row();
        $last_kode = $data->maxKode ?? 0;

        $data_insert = [];
        $counter = 1;

        foreach ($excel_data as $row) {
            $nama_pegawai = trim($row['Pegawai']);
            $nama_usaha   = trim($row['unit usaha']);
            $nama_level   = trim($row['unit level']);
            $gaji_pokok   = str_replace(['Rp.', '.', ' '], '', $row['gaji pokok'] ?? '0');

            $ref_pegawai = $this->db->select('id_pegawai')->where('nama', $nama_pegawai)->get('pegawai')->row();
            $ref_usaha   = $this->db->select('id_unit_usaha')->where('nm_unit_usaha', $nama_usaha)->get('unit_usaha')->row();
            $ref_level   = $this->db->select('id_unit_level')->where('nm_unit_level', $nama_level)->get('unit_level')->row();

            if ($ref_pegawai && $ref_usaha && $ref_level) {
                $exists = $this->db
                    ->where('id_pegawai', $ref_pegawai->id_pegawai)
                    ->where('id_unit_usaha', $ref_usaha->id_unit_usaha)
                    ->where('id_unit_level', $ref_level->id_unit_level)
                    ->get('master_gaji_pokok')
                    ->row();

                if (!$exists) {
                    $new_id = 'GP_' . str_pad($last_kode + $counter, 3, '0', STR_PAD_LEFT);
                    $counter++;

                    $data_insert[] = [
                        'id_gaji_pokok' => $new_id,
                        'id_pegawai'     => $ref_pegawai->id_pegawai,
                        'id_unit_usaha'  => $ref_usaha->id_unit_usaha,
                        'id_unit_level'  => $ref_level->id_unit_level,
                        'nominal'        => $gaji_pokok
                    ];
                }
            }
        }

        if (empty($data_insert)) {
            echo json_encode(['status' => 'error', 'message' => 'Semua data sudah ada. Tidak ada data baru yang disimpan.']);
            return;
        }

        $saved = $this->db->insert_batch('master_gaji_pokok', $data_insert);

        if ($saved) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
        }
    }


    public function ajax_list()
    {
        $list = $this->gaji_pokok->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_gaji_pokok) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_gaji_pokok) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->nama;
            $row[] = $item->nm_unit_usaha;
            $row[] = $item->nm_unit_level;

            $row[] = 'Rp. ' . number_format($item->nominal, 0, ',', '.');

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->gaji_pokok->count_all(),
            "recordsFiltered" => $this->gaji_pokok->count_filtered(),
            "data" => $data,
        ];

        echo json_encode($output);
    }

    private function id_gaji_pokokOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_gaji_pokok, 4) AS UNSIGNED)) AS maxKode FROM master_gaji_pokok";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'GP_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_gaji_pokok' => $this->id_gaji_pokokOtomatis(),
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'id_unit_level' => $this->input->post('id_unit_level', true),
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->gaji_pokok->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->gaji_pokok->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_gaji_pokok', true);
        $data = [
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'id_unit_level' => $this->input->post('id_unit_level', true),
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'nominal' => $this->input->post('nominal', true),
        ];

        // var_dump($data);
        // die;
        $this->gaji_pokok->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_gaji_pokok'));
        $this->gaji_pokok->delete(decrypt_url($id));
        echo json_encode(["status" => TRUE]);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('id_unit_usaha') == '') {
            $data['inputerror'][] = 'id_unit_usaha';
            $data['error_string'][] = 'Unit Usaha wajib diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('id_unit_level') == '') {
            $data['inputerror'][] = 'id_unit_level';
            $data['error_string'][] = 'Unit Level wajib diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('id_pegawai') == '') {
            $data['inputerror'][] = 'id_pegawai';
            $data['error_string'][] = 'Pegawai wajib dipilih';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nominal') == '') {
            $data['inputerror'][] = 'nominal';
            $data['error_string'][] = 'Nominal gaji pokok tidak boleh kosong';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function get_pegawai_by_unit()
    {
        $id_usaha = $this->input->post('id_unit_usaha');
        $id_kerja = $this->input->post('id_unit_kerja');

        $this->db->select('p.id_pegawai, p.nama');
        $this->db->from('pegawai_penempatan pp');
        $this->db->join('pegawai p', 'p.id_pegawai = pp.id_pegawai');
        $this->db->where('pp.id_unit_usaha', $id_usaha);
        $this->db->where('pp.id_unit_kerja', $id_kerja);
        $pegawai = $this->db->get()->result();

        echo json_encode($pegawai);
    }

    public function get_penempatan_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('pp.id_unit_usaha, pp.id_unit_level, uu.nm_unit_usaha, ul.nm_unit_level');
        $this->db->from('pegawai_penempatan pp');
        $this->db->join('unit_usaha uu', 'uu.id_unit_usaha = pp.id_unit_usaha', 'left');
        $this->db->join('unit_level ul', 'ul.id_unit_level = pp.id_unit_level', 'left');
        $this->db->where('pp.id_pegawai', $id_pegawai);

        $data = $this->db->get()->row();

        if ($data) {
            echo json_encode([
                // 'id_unit_usaha' => $data->id_unit_usaha,
                'nm_unit_usaha' => $data->nm_unit_usaha,
                // 'id_unit_level' => $data->id_unit_level,
                'nm_unit_level' => $data->nm_unit_level
            ]);
        } else {
            echo json_encode(null);
        }
    }




    public function get_unit_level_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('pp.id_unit_level, ul.nm_unit_level');
        $this->db->from('pegawai_penempatan pp');
        $this->db->join('unit_level ul', 'ul.id_unit_level = pp.id_unit_level');
        $this->db->where('pp.id_pegawai', $id_pegawai);
        $result = $this->db->get()->row();

        echo json_encode($result);
    }

    public function excel() {}
}
