<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller {
	private $c = 'customer';

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
		$cabang_id = $this->session->userdata('cabang_id');
		
		// cek acl
		if(!$this->acl->cek_acl($this->c,'index',$user_id)){
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access CUSTOMER page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index','refresh');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('CustomerModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/customer/index/";
		$config['total_rows'] = $this->CustomerModel->jmlhdata($perusahaan_id,$cabang_id);
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		// jika user dr kntr pusat maka set cabang id menjadi nol
		if($cabang_id ==$cfg[0]['id_pusat']){
			$cabang_id = 0;
		}
		
		$customer = $this->CustomerModel->getlist($config['per_page'],$page,$perusahaan_id,$cabang_id);
		
		// prepare data
		$data['jmlh_data'] = $this->CustomerModel->jmlhdata($perusahaan_id,$cabang_id);
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['action'] = site_url().'/customer/edit';
		$data['results'] = $customer;
		$data['file'] = 'customer/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Customer";
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
		$this->load->model('CustomerModel');
		$this->load->model('CountryModel');
		
		//$id = $this->uri->segment(3);
		$data['results'] = null;
		$data['action'] = site_url().'/customer/update';
		$data['listmember'] = $this->CustomerModel->getlistmember();
		$data['listcountry'] = $this->CountryModel->getlist(0,0);

		if($id){ 
			$data['results'] = $this->CustomerModel->get_by_id($id); 
		} 
		
		$this->load->view('customer/formcustomer',$data);
		
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
		
		redirect(site_url('customer'));	
    }
   
   public function save(){ 
		// load model
		$this->load->model('CustomerModel');
		
		$data['id'] = $this->input->post('id');
		$data['buyers_id'] = $this->input->post('buyers_id');
		$data['cabang_id'] = $this->session->userdata('cabang_id');
		$data['company_id'] = $this->session->userdata('perusahaan_id');
		$data['company'] = $this->input->post('company');
		$data['contact'] = $this->input->post('contact');
		$data['email'] = $this->input->post('email');
		$data['has_child'] = $this->input->post('has_child');
		$data['master_buyers'] = $this->input->post('master_buyers');
		$data['phone'] = $this->input->post('phone');
		$data['fax'] = $this->input->post('fax');
		$data['address'] = $this->input->post('address');
		$data['city'] = $this->input->post('city');
		$data['state'] = $this->input->post('state');
		$data['country_id'] = $this->input->post('country_id');
		$data['zip'] = $this->input->post('zip');
		
		$this->CustomerModel->saveData($data); 
		
		$text = '<div class="notification info nopic">Data Customer telah berhasil di simpan. </div>';
		$this->session->set_flashdata('msg',$text);
	}  
   
	/*
	*	Next version data tdk bisa di hapus jika sdng di gunakan di tabel lain
	*/
	private function hapus($id=0){
		// load model
		$this->load->model('CustomerModel');
		
		$id = $this->input->post('id');
		
		// cek apakah customer mempunyai member ?
		if($this->CustomerModel->have_member($id)==0){
			$this->CustomerModel->hapus($id);
		} else {
			// krn mempunyai member maka tdk dpt di hapus
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> Customer tidak dapat di hapus karena masih memilik member. </div><p></p';
			$this->session->set_flashdata('msg',$text);
		}
	}
   
    public function search(){
		// load model
		$this->load->model('CustomerModel');
		$this->load->model('CountryModel');
		
		$data['action'] = site_url().'/customer/index';
		$data['listmember'] = $this->CustomerModel->getlistmember();
		$data['listcountry'] = $this->CountryModel->getlist(0,0);
		
		$this->load->view('customer/search', $data);
    }
	
    public function confirm(){
		$data['id'] = $this->uri->segment(3);
		$data['action'] = site_url().'/customer/update';
		$this->load->view('customer/confirm-delete',$data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */