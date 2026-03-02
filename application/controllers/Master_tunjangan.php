<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_tunjangan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_tunjangan', 'tunjangan');
    }

    public function index()
    {
        $this->data['title'] = 'Master Tunjangan';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
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
        $this->data['m'] = 'Master Tunjangan';
        $this->data['ml'] = 'Daftar Tunjangan';

        $this->template->load('templates/master', 'admin/master/tunjangan/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->tunjangan->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_tunjangan) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_tunjangan) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->nama_tunjangan;
            $data[] = $row;
        }
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tunjangan->count_all(),
            "recordsFiltered" => $this->tunjangan->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    private function id_tunjanganOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_tunjangan, 3) AS UNSIGNED)) AS maxKode FROM master_tunjangan";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'T_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_tunjangan' => $this->id_tunjanganOtomatis(),
            'nama_tunjangan' => $this->input->post('nama_tunjangan', true),
        ];
        $this->tunjangan->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->tunjangan->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_tunjangan', true);
        $data = [
            'nama_tunjangan' => $this->input->post('nama_tunjangan', true),
        ];
        $this->tunjangan->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_tunjangan'));
        $this->tunjangan->delete(decrypt_url($id));
        echo json_encode(array("status" => TRUE));
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];
        if ($this->input->post('nama_tunjangan') == '') {
            $data['inputerror'][] = 'nama_tunjangan';
            $data['error_string'][] = 'Nama Tunjangan tidak boleh kosong';
            $data['status'] = FALSE;
        }
        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
