<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gaji_detail extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_gaji_detail', 'gaji_detail');
        $this->load->model('M_gaji_header', 'gaji_header'); 
    }

    public function index($encrypted_id = null)
    {
        if (!$encrypted_id) {
            show_404();
            return;
        }

        // Dekripsi ID dari URL
        $id_gaji_header = decrypt_url($encrypted_id);

        if (!$id_gaji_header) {
            show_error('ID Gaji Header tidak valid');
            return;
        }

        $this->data['title'] = 'Data Detail Gaji';
        $this->data['id_gaji_header'] = $id_gaji_header;
        $this->data['header'] = $this->gaji_header->get_by_id($id_gaji_header);

        $this->data['TombolCreate'] = '
        <h4 class="page-title">
            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="add()">
                <i class="fe-plus-square"></i> Create Detail
            </button>
            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="reload_table()">
                <i class="fe-refresh-cw"></i> Reload
            </button>
        </h4>';

        $this->template->load('templates/master', 'admin/gaji/detail/list', $this->data);
    }



    // DataTables AJAX list
    public function ajax_list($id_gaji_header)
    {
        $list = $this->gaji_detail->get_datatables($id_gaji_header);
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_gaji_detail) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_gaji_detail) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->jenis_komponen;
            $row[] = $item->nama_komponen;
            $row[] = number_format($item->jumlah, 2, ',', '.');
            $data[] = $row;
        }
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->gaji_detail->count_all($id_gaji_header),
            "recordsFiltered" => $this->gaji_detail->count_filtered($id_gaji_header),
            "data" => $data,
        ];
        echo json_encode($output);
    }


    public function get_gaji_detail($encrypted_id)
    {
        $id_gaji_header = decrypt_url($encrypted_id);

        // Ambil detail gaji tanpa potongan opsional
        $details = $this->gaji_detail->get_all_by_header($id_gaji_header);

        // Ambil data header untuk dapatkan id_pegawai
        $header = $this->db->get_where('gaji_header', ['id_gaji_header' => $id_gaji_header])->row();

        if (!$header) {
            echo json_encode(['data' => [], 'pegawai' => null, 'penempatan' => null, 'bank' => null]);
            return;
        }

        $id_pegawai = $header->id_pegawai;

        // Ambil data pegawai (data pribadi)
        $pegawai = $this->db->get_where('pegawai', ['id_pegawai' => $id_pegawai])->row();

        // Ambil data penempatan pegawai dengan join ke tabel unit untuk dapatkan nama lengkap unit
        $this->db->select('pp.*, 
        ub.nm_unit_bisnis, 
        uu.nm_unit_usaha, 
        ul.nm_unit_level, 
        uorg.nm_unit_organisasi, 
        uk.nm_unit_kerja, 
        uks.nm_unit_kerja_sub, 
        ulok.nm_unit_lokasi');
        $this->db->from('pegawai_penempatan pp');
        $this->db->join('unit_bisnis ub', 'pp.id_unit_bisnis = ub.id_unit_bisnis', 'left');
        $this->db->join('unit_usaha uu', 'pp.id_unit_usaha = uu.id_unit_usaha', 'left');
        $this->db->join('unit_level ul', 'pp.id_unit_level = ul.id_unit_level', 'left');
        $this->db->join('unit_organisasi uorg', 'pp.id_unit_organisasi = uorg.id_unit_organisasi', 'left');
        $this->db->join('unit_kerja uk', 'pp.id_unit_kerja = uk.id_unit_kerja', 'left');
        $this->db->join('unit_kerja_sub uks', 'pp.id_unit_kerja_sub = uks.id_unit_kerja_sub', 'left');
        $this->db->join('unit_lokasi ulok', 'pp.id_unit_lokasi = ulok.id_unit_lokasi', 'left');
        $this->db->where('pp.id_pegawai', $id_pegawai);
        $penempatan = $this->db->get()->row();

        // Ambil data bank pegawai dengan join ke bank_list untuk dapatkan nama bank
        $this->db->select('pegawai.id_bank, bank_list.nm_bank, bank_list.cabang, bank_list.kota');
        $this->db->from('pegawai');
        $this->db->join('bank_list', 'pegawai.id_bank = bank_list.id_bank', 'left');
        $this->db->where('pegawai.id_pegawai', $id_pegawai);
        $bank = $this->db->get()->row();

        echo json_encode([
            'data' => $details,
            'pegawai' => $pegawai,
            'penempatan' => $penempatan,
            'bank' => $bank
        ]);
    }






    // public function get_potongan_detail($encrypted_id_gaji_header)
    // {
    //     $result = $this->gaji_detail->get_potongan_detail($encrypted_id_gaji_header);
    //     echo json_encode($result['detail']);
    // }

    public function get_potongan_detail($encrypted_id_gaji_header)
    {
        $result = $this->gaji_detail->get_potongan_detail($encrypted_id_gaji_header);
        echo json_encode($result);
    }


    // public function get_by_id($id)
    // {
    //     $data = $this->gaji_detail->get_by_id(decrypt_url($id));
    //     echo json_encode($data);
    // }

    public function get_by_id($id)
    {
        $data = $this->gaji_detail->get_by_id($id);  // Ambil data berdasarkan id_gaji_detail yang dikirim

        if ($data) {
            echo json_encode($data);  // Kirimkan data dalam format JSON
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);  // Jika tidak ada data
        }
    }



    private function id_gaji_detailOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_gaji_detail, 4) AS UNSIGNED)) AS maxKode FROM gaji_detail";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'GD_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();

        $id_gaji_header_decrypted = decrypt_url($this->input->post('id_gaji_header', true));

        $data = [
            'id_gaji_detail' => $this->id_gaji_detailOtomatis(),
            'id_gaji_header' => $id_gaji_header_decrypted,
            'jenis_komponen' => $this->input->post('jenis_komponen', true),
            'nama_komponen' => $this->input->post('nama_komponen', true),
            'jumlah' => $this->input->post('jumlah', true),
        ];

        $this->gaji_detail->insert($data);
        $this->gaji_header->update_gaji_summary($id_gaji_header_decrypted);

        echo json_encode(["status" => TRUE]);
    }


    public function insert_potongan_opsional()
    {
        $this->_validate(false);

        $id_gaji_header_decrypted = decrypt_url($this->input->post('id_gaji_header', true));
        $nama_potongan = $this->input->post('nama_komponen', true);
        $jumlah = $this->input->post('jumlah', true);

        $this->load->model('M_gaji_detail', 'gaji_detail');
        $this->load->model('M_gaji_header', 'gaji_header');

        $this->gaji_detail->insert_potongan_opsional($id_gaji_header_decrypted, $nama_potongan, $jumlah);
        $this->gaji_header->update_gaji_summary($id_gaji_header_decrypted);

        echo json_encode(["status" => TRUE]);
    }

    // public function update()
    // {
    //     $this->_validate(false);
    //     $id = $this->input->post('id_gaji_detail', true);
    //     $data = [
    //         'jenis_komponen' => 'potongan_opsional',
    //         'nama_komponen' => $this->input->post('nama_komponen', true),
    //         'jumlah' => $this->input->post('jumlah', true),
    //     ];
    //     $this->gaji_detail->update($id, $data);
    //     $this->gaji_header->update_gaji_summary($data['id_gaji_header']);
    //     echo json_encode(["status" => TRUE]);
    // }

    public function update()
    {
        $this->_validate(false);
        $id = $this->input->post('id_gaji_detail', true);

        $gaji_detail = $this->gaji_detail->get_by_id($id);
        $id_gaji_header = $gaji_detail->id_gaji_header;
        $data = [
            'jenis_komponen' => 'potongan_opsional',
            'nama_komponen' => $this->input->post('nama_komponen', true),
            'jumlah' => $this->input->post('jumlah', true),
        ];
        $this->gaji_detail->update($id, $data);
        $this->gaji_header->update_gaji_summary($id_gaji_header);

        echo json_encode(["status" => TRUE]);
    }


    public function delete()
    {
        $id = $this->input->post('id_gaji_detail', true);
        $oldData = $this->gaji_detail->get_by_id($id);
        if (!$oldData) {
            echo json_encode(["status" => false, "error" => "Data tidak ditemukan"]);
            return;
        }
        $this->gaji_detail->delete($id);
        $this->gaji_header->update_gaji_summary($oldData->id_gaji_header);
        echo json_encode(["status" => true]);
    }


    private function _validate($checkJenisKomponen = true)
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($checkJenisKomponen && $this->input->post('jenis_komponen') == '') {
            $data['inputerror'][] = 'jenis_komponen';
            $data['error_string'][] = 'Jenis komponen harus dipilih';
            $data['status'] = FALSE;
        }
        if ($this->input->post('nama_komponen') == '') {
            $data['inputerror'][] = 'nama_komponen';
            $data['error_string'][] = 'Nama komponen harus diisi';
            $data['status'] = FALSE;
        }
        if ($this->input->post('jumlah') == '') {
            $data['inputerror'][] = 'jumlah';
            $data['error_string'][] = 'Jumlah harus diisi';
            $data['status'] = FALSE;
        }
        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
