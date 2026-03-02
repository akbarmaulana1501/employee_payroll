<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_tunjangan_insentif extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_tunjangan_insentif', 'insentif');
    }

    public function index()
    {
        $this->data['title'] = 'Tunjangan Insentif';
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
        $this->data['m'] = 'Tunjangan Insentif';
        $this->data['ml'] = 'Daftar Tunjangan Insentif';

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
                <a href="javascript:void(0)" class="btn-xs btn-primary" onclick="update(\'' . encrypt_url($item->id_tunjangan) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" class="btn-xs btn-danger" onclick="deletedata(\'' . encrypt_url($item->id_tunjangan) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->id_tunjangan;
            $row[] = $item->nm_tunjangan_insentif;
            // $row[] = number_format($item->tunjangan);
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

    public function insert()
    {
        $this->_validate();

        if ($this->insentif->is_duplicate($this->input->post('nm_tunjangan_insentif'))) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['nm_tunjangan_insentif'],
                'error_string' => ['Nama Tunjangan Insentif sudah ada']
            ]);
            return;
        }

        $data = [
            'id_tunjangan' => $this->generate_id(),
            'nm_tunjangan_insentif' => $this->input->post('nm_tunjangan_insentif', true),
            // 'tunjangan' => $this->input->post('tunjangan', true),
        ];
        $this->insentif->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_tunjangan', true);

        if ($this->insentif->is_duplicate($this->input->post('nm_tunjangan_insentif'), $id)) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['nm_tunjangan_insentif'],
                'error_string' => ['Nama Tunjangan Insentif sudah ada']
            ]);
            return;
        }

        $data = [
            'nm_tunjangan_insentif' => $this->input->post('nm_tunjangan_insentif', true),
            // 'tunjangan' => $this->input->post('tunjangan', true),
        ];
        $this->insentif->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = decrypt_url($this->input->post('id_tunjangan'));
        $this->insentif->delete($id);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->insentif->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    private function generate_id()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_tunjangan, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('master_tunjangan_insentif')->row();
        $max_id = $data->max_id + 1;
        return 'TIN' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('nm_tunjangan_insentif') == '') {
            $data['inputerror'][] = 'nm_tunjangan_insentif';
            $data['error_string'][] = 'Nama Tunjangan Insentif wajib diisi';
            $data['status'] = FALSE;
        }

        // if ($this->input->post('tunjangan') == '') {
        //     $data['inputerror'][] = 'tunjangan';
        //     $data['error_string'][] = 'Jumlah tunjangan wajib diisi';
        //     $data['status'] = FALSE;
        // }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
