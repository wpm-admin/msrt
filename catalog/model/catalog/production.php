<?php
class ModelCatalogProduction extends Model {
	
	public function getProduction($production_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "production a LEFT JOIN " . DB_PREFIX . "production_description ad ON (a.production_id = ad.production_id) WHERE a.production_id = '" . (int)$production_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProductions($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "production a
			LEFT JOIN " . DB_PREFIX . "production_description ad ON (a.production_id = ad.production_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

	
		$sort_data = array(
			'ad.name',
			'production_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY  ad.name";
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

	public function getProductionDescriptions($production_id) {
		$production_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "production_description WHERE production_id = '" . (int)$production_id . "'");

		foreach ($query->rows as $result) {
			$production_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $production_data;
	}

	public function getTotalProductions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "production");

		return $query->row['total'];
	}

}
