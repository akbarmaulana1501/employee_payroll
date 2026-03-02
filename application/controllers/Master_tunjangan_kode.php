<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_tunjangan_kode extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_tunjangan_kode', 'kode');
    }

    public function index()
    {
        $this->data['title'] = 'Tunjangan Kode';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Tunjangan Kode';
        $this->data['ml'] = 'Daftar Tunjangan Kode';

        $this->template->load('templates/master', 'admin/master/kode/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->kode->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" class="btn-xs btn-primary" onclick="update(\'' . encrypt_url($item->id_master) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" class="btn-xs btn-danger" onclick="deletedata(\'' . encrypt_url($item->id_master) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->id_master;
            $row[] = $item->nm_tunjangan;
            // $row[] = number_format($item->nominal);
            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kode->count_all(),
            "recordsFiltered" => $this->kode->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    public function insert()
    {
        $this->_validate();

        if ($this->kode->is_duplicate($this->input->post('nm_tunjangan'))) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['nm_tunjangan'],
                'error_string' => ['Nama Tunjangan sudah ada']
            ]);
            return;
        }

        $data = [
            'id_master' => $this->generate_id(),
            'nm_tunjangan' => $this->input->post('nm_tunjangan', true),
            // 'nominal' => $this->input->post('nominal', true),
        ];
        $this->kode->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_master', true);

        if ($this->kode->is_duplicate($this->input->post('nm_tunjangan'), $id)) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['nm_tunjangan'],
                'error_string' => ['Nama Tunjangan sudah ada']
            ]);
            return;
        }

        $data = [
            'nm_tunjangan' => $this->input->post('nm_tunjangan', true),
            // 'nominal' => $this->input->post('nominal', true),
        ];
        $this->kode->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = decrypt_url($this->input->post('id_master'));
        $this->kode->delete($id);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->kode->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    private function generate_id()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_master, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('master_tunjangan_kode')->row();
        $max_id = $data->max_id + 1;
        return 'TKD' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('nm_tunjangan') == '') {
            $data['inputerror'][] = 'nm_tunjangan';
            $data['error_string'][] = 'Nama tunjangan wajib diisi';
            $data['status'] = FALSE;
        }

        // if ($this->input->post('nominal') == '') {
        //     $data['inputerror'][] = 'nominal';
        //     $data['error_string'][] = 'Jumlah nominal wajib diisi';
        //     $data['status'] = FALSE;
        // }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
