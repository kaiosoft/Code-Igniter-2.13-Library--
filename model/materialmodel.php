<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MaterialModel extends CI_Model{

	private $tbl = 'kaio_material';

	public function _construct()
	{
		parent::_construct();
	}
	
	public  function getlist($start,$finish){ 
		$this->db->select('c.nama_satuan,b.cat_name,a.*');
		$this->db->from($this->tbl.' AS a');
		$this->db->join('kaio_cat AS b','b.id=a.cat_Id');
		$this->db->join('kaio_satuan AS c','c.id=a.satuan_Id');
		$this->db->order_by('a.nama','desc');
		
		if($start > 0 OR $finish > 0){
			$this->db->limit($start,$finish);
		}
		
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
	
	public function check_kode($kode){
		$this->db->where('kode',$kode);
		return $this->db->count_all_results($this->tbl);
	}
}

?>
