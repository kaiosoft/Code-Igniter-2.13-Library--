<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CabangModel extends CI_Model{

	private $tbl = 'kaio_cabang';

	public function _construct()
	{
		parent::_construct();
	}
	
	public  function getlist($start,$finish,$perusahaan_id){ 
		$this->db->select('*');
		$this->db->from($this->tbl);
		$this->db->where('company_id',$perusahaan_id);
		$this->db->order_by('cabang_name','asc');
		
		if($start > 0 OR $finish > 0){
			$this->db->limit($start,$finish);
		}
		
		return $this->db->get()->result();
	}
	
	public function jmlhdata($perusahaan_id){
		$this->db->where('company_id',$perusahaan_id);
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
	
}

?>
