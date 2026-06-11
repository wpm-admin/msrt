<?php
class ModelAccountGarage extends Model {
	public function addGarage($data) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "garage WHERE customer_id = '" . (int)$this->customer->getId() . "' AND garage_id = '" . (int)$garage_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "garage SET customer_id = '" . (int)$this->customer->getId() . "',
						 modeli = '" . (int)$data['modeli'] . "',
						 mark = '" . (int)$data['mark'] . "',
						 year = '" . (int)$data['year'] . "',
						 sort_order = '" . (int)$data['sort_order'] . "',
						 image = '" . $this->db->escape($data['image']) . "',
						 volume = '" . $this->db->escape($data['volume']) . "',
						 tipo = '" . $this->db->escape($data['tipo']) . "',
						 vin = '" . $this->db->escape($data['vin']) . "',
						 status = '" . (int)$data['status'] . "'
						 ");
	}
	
	public function deleteGarage($garage_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "garage WHERE customer_id = '" . (int)$this->customer->getId() . "' AND garage_id = '" . (int)$garage_id . "'");
	}

	public function getGarage() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "garage WHERE customer_id = '" . (int)$this->customer->getId() . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getTotalGarage() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "garage WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}
