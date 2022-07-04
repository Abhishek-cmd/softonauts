<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class All_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database('default');
	}

	private function getConn($conn) {
		if(!is_bool($conn)) {
			return $conn;
		} else {
			return $this->db;
		}
	}

	public function insert($table, $data, $conn = false) {
		$conn = $this->getConn($conn);
		$conn->insert($table, $data);
		return $conn->insert_id();
	}
}
?>