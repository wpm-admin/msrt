<?php
class ModelCatalogConsignment extends Model {
	public function addConsignment($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "consignment SET
						 customer_id = '" . (int)$data['customer_id'] . "',
						 category_id = '" . (int)$data['category_id'] . "',
						 mark_id = '" . (int)$data['mark_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 price = '" . (float)$data['price'] . "',
						 my_price = '" . (float)$data['my_price'] . "',
						 status = '" . (int)$data['status'] . "',
						 name = '" . $this->db->escape($data['name']) . "',
						 telephone = '" . $this->db->escape($data['telephone']) . "',
						 email = '" . $this->db->escape($data['email']) . "',
						 year = '" . $this->db->escape($data['year']) . "',
						 description = '" . $this->db->escape($data['description']) . "',
						 images = '" . $this->db->escape(implode(';', $data['images'])) . "'
						 ");

		$consignment_id = $this->db->getLastId();

		return $consignment_id;
	}

	public function editConsignment($consignment_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "consignment SET
						 customer_id = '" . (int)$data['customer_id'] . "',
						 category_id = '" . (int)$data['category_id'] . "',
						 mark_id = '" . (int)$data['mark_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 price = '" . (float)$data['price'] . "',
						 my_price = '" . (float)$data['my_price'] . "',
						 status = '" . (int)$data['status'] . "',
						 name = '" . $this->db->escape($data['name']) . "',
						 telephone = '" . $this->db->escape($data['telephone']) . "',
						 email = '" . $this->db->escape($data['email']) . "',
						 year = '" . $this->db->escape($data['year']) . "',
						 description = '" . $this->db->escape($data['description']) . "',
						 images = '" . $this->db->escape(implode(';', $data['images'])) . "'
						 WHERE consignment_id = '" . (int)$consignment_id . "'");

	}

	public function deleteConsignment($consignment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "consignment WHERE consignment_id = '" . (int)$consignment_id . "'");
	}

	public function getConsignment($consignment_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "consignment WHERE consignment_id = '" . (int)$consignment_id . "'");

		return $query->row;
	}

	public function getConsignments($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "consignment 
					 WHERE 1 = 1";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_consignment_group_id'])) {
			$sql .= " AND consignment_group_id = '" . $this->db->escape($data['filter_consignment_group_id']) . "'";
		}

		$sort_data = array(
			'name',
			'consignment_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}


	public function getTotalConsignments() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "consignment");

		return $query->row['total'];
	}

	public function getTotalConsignmentsByConsignmentGroupId($consignment_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "consignment WHERE consignment_group_id = '" . (int)$consignment_group_id . "'");

		return $query->row['total'];
	}
}
