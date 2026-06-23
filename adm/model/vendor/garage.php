<?php
class ModelCustomerGarage extends Model {
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

	public function getGarage($customer_id) {
        $query = $this->db->query("SELECT g.*,
								  (SELECT md1.name FROM " . DB_PREFIX . "mark_description md1 WHERE md1.mark_id = g.mark AND md1.language_id='".$this->config->get('config_language_id')."') AS model_name,
								  (SELECT md2.name FROM " . DB_PREFIX . "mark_description md2 WHERE md2.mark_id = g.modeli AND md2.language_id='".$this->config->get('config_language_id')."') AS mark_name
								   FROM " . DB_PREFIX . "garage g
								  
								  WHERE g.customer_id = '" . (int)$customer_id . "' ORDER BY sort_order LIMIT 1");

		return $query->row;
	}

	public function getGarages($customer_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "garage WHERE customer_id = '" . (int)$customer_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getTotalGarage($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "garage WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}
}
