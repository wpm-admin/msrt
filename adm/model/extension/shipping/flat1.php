<?php
class ModelExtensionShippingFlat1 extends Model {
	
	public function getCosts() {
      	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping_cost ORDER BY type_id ASC, weight ASC");
		
		return $query->rows;
	}
	
	public function saveCosts($data) {
     	
		$this->db->query("DELETE FROM " . DB_PREFIX . "shipping_cost");
		
		foreach($data['costs'] as $row){
			$this->db->query("INSERT INTO " . DB_PREFIX . "shipping_cost SET
							  shipping_cost_id = '" . ( ((int)$row['shipping_cost_id'] > 0) ? (int)$row['shipping_cost_id'] : '') . "',
							  type_id = '" . (int)$row['type_id'] . "',
							  weight_class_id = '" . (int)$row['weight_class_id'] . "',
							  weight = '" . (float)$row['weight'] . "',
							  price = '" . (float)$row['price'] . "',
							  name = '" . $this->db->escape($row['name']) . "'
							  ");	
		}
		
	}
	
}