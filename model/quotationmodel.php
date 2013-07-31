<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QuotationModel extends CI_Model{

	private $tbl = 'kaio_quotation';

	public function _construct()
	{
		parent::_construct();
	}
	
	public  function getlist($start,$finish,$perusahaan_id,$cabang_id){ 
		$this->db->select('b.Company AS company, a.*');
		$this->db->from($this->tbl.' AS a');
		$this->db->join('kaio_buyers AS b','b.id=a.buyers_id');
		
		if($perusahaan_id > 0){
			$this->db->where('a.company_id',$perusahaan_id);
		}
		
		if($cabang_id > 0){
			$this->db->where('a.cabang_id',$cabang_id);
		}
		
		$this->db->order_by('a.qt_date','desc');
		$this->db->limit($start,$finish);
		return $this->db->get()->result();
	}
	
	public function jmlhdata(){
		return $this->db->count_all_results($this->tbl);
	}
	
	public function hapus($id=0){
		$this->db->where_in('id',$id);
		$this->db->delete($this->tbl);
		return true;
	}
	
	public function get_by_id($id){ 
		$this->db->select('*');
		$this->db->where('id', $id);
		return $this->db->get($this->tbl)->result_array();
	}
	
	public function saveData($data)
	{
		if(empty($data['id'])){
			$this->db->insert($this->tbl,$data);
		} else {
			$this->db->where('id',$data['id']);
			$this->db->update($this->tbl,$data);
		}
	}
	
	function listPerusahaan($perusahaan_id){
		$this->db->select('id,nama_perusahaan');
		if($perusahaan_id > 0){
			$this->db->where('id',$perusahaan_id);
		}
		return $this->db->get('kaio_perusahaan')->result_array();
	}
}

?>
