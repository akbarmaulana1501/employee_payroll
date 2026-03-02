<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tunjangan_jabatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_tunjangan_jabatan', 'jabatan');
        $this->load->model('M_unit_level', 'unit_level');
        $this->load->model('M_unit_usaha', 'unit_usaha');
    }

    public function index()
    {
        $this->data['title'] = 'Tunjangan Jabatan';
        $this->data['TombolCreate'] = '<h4 class="page-title">
            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="add()">
                <i class="fe-plus-square"></i> Create
            </button>
            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="reload_table()">
                <i class="fe-refresh-cw"></i> Reload
            </button>
        </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Tunjangan Jabatan';
        $this->data['ml'] = 'Daftar Tunjangan Jabatan';

        $this->data['list_unit_level'] = $this->unit_level->get_all();
        $this->data['list_unit_usaha'] = $this->unit_usaha->get_all();

        $this->template->load('templates/master', 'admin/master/jabatan/list', $this->data);
    }

    public function ajax_list()
    {
        $this->db->select('tj.*, ul.nm_unit_level, uu.nm_unit_usaha');
        $this->db->from('tunjangan_jabatan tj');
        $this->db->join('unit_level ul', 'ul.id_unit_level = tj.id_unit_level', 'left');
        $this->db->join('unit_usaha uu', 'uu.id_unit_usaha = tj.id_unit_usaha', 'left');

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $list = $this->db->get()->result();
        $no = $_POST['start'];
        $data = [];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '<a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_tunjangan_jabatan) . '\')">
                <i class="fas fa-edit"></i> Update</a>
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_tunjangan_jabatan) . '\')">
                <i class="fas fa-trash"></i> Delete</a>';
            $row[] = $item->nm_unit_level;
            $row[] = $item->nm_unit_usaha;
            $row[] = number_format($item->jumlah);
            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->jabatan->count_all(),
            "recordsFiltered" => $this->jabatan->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    private function id_otomatis()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(id_tunjangan_jabatan, 4) AS UNSIGNED)) AS maxKode FROM tunjangan_jabatan";
        $kode = $this->db->query($query)->row()->maxKode + 1;
        return 'TJ_' . str_pad($kode, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();

        if ($this->jabatan->is_duplicate(
            $this->input->post('id_unit_level'),
            $this->input->post('id_unit_usaha')
        )) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['id_unit_level', 'id_unit_usaha'],
                'error_string' => ['Kombinasi sudah ada', 'Kombinasi sudah ada']
            ]);
            return;
        }

        $data = [
            'id_tunjangan_jabatan' => $this->id_otomatis(),
            'id_unit_level' => $this->input->post('id_unit_level', true),
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'jumlah' => $this->input->post('jumlah', true),
        ];
        $this->jabatan->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->jabatan->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_tunjangan_jabatan', true);

        if ($this->jabatan->is_duplicate(
            $this->input->post('id_unit_level'),
            $this->input->post('id_unit_usaha')
        )) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['id_unit_level', 'id_unit_usaha'],
                'error_string' => ['Kombinasi sudah ada', 'Kombinasi sudah ada']
            ]);
            return;
        }
        $data = [
            'id_unit_level' => $this->input->post('id_unit_level', true),
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'jumlah' => $this->input->post('jumlah', true),
        ];
        $this->jabatan->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_tunjangan_jabatan'));
        $this->jabatan->delete(decrypt_url($id));
        echo json_encode(["status" => TRUE]);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('id_unit_level') == '') {
            $data['inputerror'][] = 'id_unit_level';
            $data['error_string'][] = 'Unit Level tidak boleh kosong';
            $data['status'] = FALSE;
        }

        if ($this->input->post('id_unit_usaha') == '') {
            $data['inputerror'][] = 'id_unit_usaha';
            $data['error_string'][] = 'Unit Usaha tidak boleh kosong';
            $data['status'] = FALSE;
        }

        if ($this->input->post('jumlah') == '') {
            $data['inputerror'][] = 'jumlah';
            $data['error_string'][] = 'Jumlah tidak boleh kosong';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
