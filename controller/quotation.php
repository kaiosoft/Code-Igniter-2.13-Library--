<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quotation extends CI_Controller {
	private $c = 'quotation';

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
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access QUOTATION page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index','refresh');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('QuotationModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/quotation/index/";
		$config['total_rows'] = $this->QuotationModel->jmlhdata();
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		$quotation = $this->QuotationModel->getlist($config['per_page'],$page,$perusahaan_id,$cabang_id);
		
		// prepare data
		$data['jmlh_data'] = $this->QuotationModel->jmlhdata();
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['results'] = $quotation;
		$data['action'] = site_url().'/quotation/edit';
		$data['file'] = 'marketing/quotation/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Quotation";
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
		$this->load->model('QuotationModel');
		$this->load->model('ConfigModel');

		//$id = $this->uri->segment(3);
		$data['results'] = null;
		$data['action'] = site_url().'/quotation/update';
		$data['listcustomer'] = $this->CustomerModel->getlist(0,0);

		if($id){ 
			$data['results'] = $this->CustomerModel->get_by_id($id); 
		} 
		
		$this->load->view('quotation/form_quotation',$data);
    }
   
    public function search(){
	  $this->load->view('quotation/search');
    }
   
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */