<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Material extends CI_Controller {
	private $c = 'material';

	public function _construct(){
		parent::_construct();	
	}

	public function index($page=0)
	{
		if(!$this->session->userdata('logged_in')){
			redirect('auth/login');
		}
		
		$user_id = $this->session->userdata('user_id');
		
		// cek acl
		if(!$this->acl->cek_acl($this->c,'index',$user_id)){
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access MATERIAL page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index','refresh');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('MaterialModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/material/index/";
		$config['total_rows'] = $this->MaterialModel->jmlhdata();
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		$material = $this->MaterialModel->getlist($config['per_page'],$page);
		
		// prepare data
		$data['jmlh_data'] = $this->MaterialModel->jmlhdata();
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['results'] = $material;
		$data['action'] = site_url().'/material/edit';
		$data['file'] = 'material/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Material";
		// Write to $subtitle
		$this->template->write('subtitle', $subtitle);
		
		// Write to Header
		$this->template->write_view('header', 'templates/default/header.php'); 
		  
		// Write to Content
		$this->template->write_view('content', 'templates/default/content.php', $data);
					
		// Write to Footer
		$this->template->write_view('footer', 'templates/default/footer.php'); 
		  
		// Render the template
		$this->template->render();
	}
	
    public function edit($id=0){
		
		// load model
		$this->load->model('MaterialModel');
		$this->load->model('CatModel');
		$this->load->model('SatuanModel');
		
		$perusahaan_id = $this->session->userdata('perusahaan_id');
		
		$data['results'] = null;
		$data['action'] = site_url().'/material/update';
		$data['listcat'] = $this->CatModel->getlist(0,0,$perusahaan_id,2);
		$data['listsatuan'] = $this->SatuanModel->getlist(0,0);
		$data['jenis_cat_id'] = 2;

		if($id){ 
			$data['results'] = $this->MaterialModel->get_by_id($id); 
			$data['jenis_cat_id'] = $data['results'][0]['jenis_cat_id'];
		} 
		
		$this->load->view('material/form_material',$data);
    }
   
    public function update(){
		if($this->input->post('button')=='Save'){
			if($this->input->post('id')){
				$data['updatetype'] = "edit";
			} else {
				$data['updatetype'] = "new";
			}
			$this->save();
			redirect(site_url('material'));
			
		} else if($this->input->post('button')=='Delete'){
			$this->hapus($this->input->post('id'));
			redirect(site_url('material'));
			
		}	else if($this->input->post('button')=='Check'){
			// cek apakah kode sdh ada yg menggunakan ?
			$x = $this->check_kode($this->input->post('kode'));
			
			if($x > 0){
				$data['msg'] = "Kode sudah di gunakan";
			} else {
				$data['msg'] = "Kode dapat di gunakan";
			}
			
			$this->load->model('SatuanModel');
			$this->load->model('CatModel');
			
			$perusahaan_id = $this->session->userdata('perusahaan_id');
			
			// prepare data
			$data['kode'] = $this->input->post('kode');
			$data['action'] = site_url().'/material/update';
			$data['listcat'] = $this->CatModel->getlist(0,0,$perusahaan_id,2);
			$data['listsatuan'] = $this->SatuanModel->getlist(0,0);
			
			$this->load->view('material/form_material2',$data);
		}
		
    }
   
    public function save(){ 
		// load model
		$this->load->model('MaterialModel');
		
		$data['id'] = $this->input->post('id');
		$data['kode'] = $this->input->post('kode');
		$data['nama'] = $this->input->post('nama');
		$data['cat_id'] = $this->input->post('cat_id');
		$data['satuan_id'] = $this->input->post('satuan_id');
		$data['jenis_cat_id'] = $this->input->post('jenis_cat_id');
		
		// cek apakah kode sdh ada yg menggunakan ?
		if($data['updatetype'] = "new"){
			$x = $this->check_kode($data['kode']);
		}
		
		if($x > 0){
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> Material Code is used. </div><p></p>';
		} else {
			// jika blm ada yg menggunakan
			$this->MaterialModel->saveData($data); 
			$text = '<div class="notification info nopic">Data Material telah berhasil di simpan. </div>';
		}
		
		$this->session->set_flashdata('msg',$text);
	}  
   
	/*
	*	Next version data tdk bisa di hapus jika sdng di gunakan di tabel lain
	*/
	private function hapus($id=0){
		// load model
		$this->load->model('MaterialModel');
		
		//$id = $this->input->post('id');
		
		$this->MaterialModel->hapus($id);
		$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> Data material telah di hapus. </div><p></p>';
		$this->session->set_flashdata('msg',$text);
	}   
   
    public function search(){
	  $this->load->view('material/search');
    }
	
    public function confirm(){
		$data['id'] = $this->uri->segment(3);
		$data['action'] = site_url().'/material/update';
		$this->load->view('material/confirm-delete',$data);
    }
	
	public function check_kode($kode){
		
		// load model
		$this->load->model('MaterialModel');
		
		// cek apakah kode sdh ada yg menggunakan ?
		$is_kode = $this->MaterialModel->check_kode($kode);
		return $is_kode;
	}
	
	
   
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */