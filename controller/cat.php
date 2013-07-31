<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cat extends CI_Controller {
	private $c = 'cat';

	public function _construct(){
		parent::_construct();	
	}

	public function index($page=0)
	{
		if(!$this->session->userdata('logged_in')){
			redirect('auth/login');
		}
		
		$user_id = $this->session->userdata('user_id');
		$perusahaan_id = $this->session->userdata('perusahaan_id');
		
		// cek acl
		if(!$this->acl->cek_acl($this->c,'index',$user_id)){
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access CATEGORY page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index','refresh');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('CatModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/cat/index/";
		$config['total_rows'] = $this->CatModel->jmlhdata($perusahaan_id,1);
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		$cat = $this->CatModel->getlist($config['per_page'],$page,$perusahaan_id,1);
		
		// prepare data
		$data['jmlh_data'] = $this->CatModel->jmlhdata($perusahaan_id,1);
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['action'] = site_url().'/cat/edit';
		$data['results'] = $cat;
		$data['file'] = 'cat/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Category";
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
		$this->load->model('CatModel');
		
		$data['results'] = null;
		$data['action'] = site_url().'/cat/update';

		if($id){ 
			$data['results'] = $this->CatModel->get_by_id($id); 
		} 
		
		$this->load->view('cat/form_cat',$data);
		
	}
	
    public function update(){
		if($this->input->post('button')=='Save'){
			if($this->input->post('id')){
				$data['updatetype'] = "edit";
			} else {
				$data['updatetype'] = "new";
			}
			$this->save();
		} else {
			$this->hapus();
		}
		
		redirect(site_url('cat'));	
    }
	
    public function save(){ 
		// load model
		$this->load->model('CatModel');
				
		$data['id'] = $this->input->post('id');
		$data['cat_name'] = $this->input->post('cat_name');
		$data['company_id'] = $this->session->userdata('perusahaan_id');
		$data['jenis_cat_id'] = 1;
		
		$this->CatModel->saveData($data); 
		
		$text = '<div class="notification info nopic">Data Category telah berhasil di simpan. </div>';
		$this->session->set_flashdata('msg',$text);
	}  
	
	/*
	*	Next version data tdk bisa di hapus jika sdng di gunakan di tabel lain
	*/
	private function hapus($id=0){
		// load model
		$this->load->model('CatModel');
		
		$id = $this->input->post('id');
		
		$this->CatModel->hapus($id);

		$text = '<div class="notification info nopic">Data Category telah berhasil di hapus. </div>';
		$this->session->set_flashdata('msg',$text);
	}
	
    public function confirm(){
		$data['id'] = $this->input->post('id');
		$data['action'] = site_url().'/cat/update';
		$this->load->view('cat/confirm-delete',$data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */