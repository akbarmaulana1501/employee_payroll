<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_insentif extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_insentif', 'insentif');
    }

    public function index()
    {
        $this->data['title'] = 'Master Insentif';
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
        $this->data['m'] = 'Master Insentif';
        $this->data['ml'] = 'Daftar Insentif';

        $this->template->load('templates/master', 'admin/master/insentif/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->insentif->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_insentif) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_insentif) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->nama_insentif;
            $row[] = $item->kriteria;
            $data[] = $row;
        }
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->insentif->count_all(),
            "recordsFiltered" => $this->insentif->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    private function id_insentifOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_insentif, 4) AS UNSIGNED)) AS maxKode FROM master_insentif";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'IS_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_insentif' => $this->id_insentifOtomatis(),
            'nama_insentif' => $this->input->post('nama_insentif', true),
            'kriteria' => $this->input->post('kriteria', true),
        ];
        $this->insentif->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->insentif->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_insentif', true);
        $data = [
            'nama_insentif' => $this->input->post('nama_insentif', true),
            'kriteria' => $this->input->post('kriteria', true),
        ];
        $this->insentif->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_insentif'));
        $this->insentif->delete(decrypt_url($id));
        echo json_encode(array("status" => TRUE));
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];
        if ($this->input->post('nama_insentif') == '') {
            $data['inputerror'][] = 'nama_insentif';
            $data['error_string'][] = 'Nama Insentif tidak boleh kosong';
            $data['status'] = FALSE;
        }
        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
