<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *      Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Carpenter extends CI_Controller {
	private $c = 'carpenter';

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
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access SUPPLIER page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index','refresh');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('CarpenterModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/carpenter/index/";
		$config['total_rows'] = $this->CarpenterModel->jmlhdata($perusahaan_id);
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		$carpenter = $this->CarpenterModel->getlist($config['per_page'],$page,$perusahaan_id);
		
		// prepare data
		$data['jmlh_data'] = $this->CarpenterModel->jmlhdata($perusahaan_id);
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['action'] = site_url().'/carpenter/edit';
		$data['results'] = $carpenter;
		$data['file'] = 'carpenter/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "CARPENTER";
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
		$this->load->model('CarpenterModel');
		
		$data['results'] = null;
		$data['action'] = site_url().'/carpenter/update';

		if($id){ 
			$data['results'] = $this->CarpenterModel->get_by_id($id); 
		} 
		
		$this->load->view('carpenter/form_carpenter',$data);
		
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
		
		redirect(site_url('carpenter'));	
    }
	
    public function save(){ 
		// load model
		$this->load->model('CarpenterModel');
		
		$data['id'] = $this->input->post('id');
		$data['company_id'] = $this->session->userdata('perusahaan_id');
		$data['company_name'] = $this->input->post('company_name');
		$data['address'] = $this->input->post('address');
		$data['phone'] = $this->input->post('phone');
		$data['fax'] = $this->input->post('fax');
		$data['email'] = $this->input->post('email');
		$data['contact'] = $this->input->post('contact');
		
		$this->CarpenterModel->saveData($data); 
		
		$text = '<div class="notification info nopic">Data Supplier telah berhasil di simpan. </div>';
		$this->session->set_flashdata('msg',$text);
	}  
	
	/*
	*	Next version data tdk bisa di hapus jika sdng di gunakan di tabel lain
	*/
	private function hapus($id=0){
		// load model
		$this->load->model('CarpenterModel');
		
		$id = $this->input->post('Id');
		
		$this->CarpenterModel->hapus($id);

		$text = '<div class="notification info nopic">Data Supplier telah berhasil di hapus. </div>';
		$this->session->set_flashdata('msg',$text);
	}
	
    public function confirm(){
		$data['id'] = $this->uri->segment(3);
		$data['action'] = site_url().'/carpenter/update';
		$this->load->view('carpenter/confirm-delete',$data);
    }
   
    public function search(){
	  $this->load->view('carpenter/search');
   }
   
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
