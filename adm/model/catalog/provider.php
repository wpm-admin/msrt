<?php
class ModelCatalogProvider extends Model {
	public function addProvider($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "provider SET name = '" . $this->db->escape($data['name']) . "', comment = '" . $this->db->escape($data['comment']) . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$provider_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "provider SET image = '" . $this->db->escape($data['image']) . "' WHERE provider_id = '" . (int)$provider_id . "'");
		}

		
		$this->cache->delete('provider');

		return $provider_id;
	}

	public function editProvider($provider_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "provider SET name = '" . $this->db->escape($data['name']) . "', comment = '" . $this->db->escape($data['comment']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE provider_id = '" . (int)$provider_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "provider SET image = '" . $this->db->escape($data['image']) . "' WHERE provider_id = '" . (int)$provider_id . "'");
		}


		$this->cache->delete('provider');
	}

	public function deleteProvider($provider_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "provider` WHERE provider_id = '" . (int)$provider_id . "'");

		$this->cache->delete('provider');
	}

	public function getProvider($provider_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "provider WHERE provider_id = '" . (int)$provider_id . "'");

		return $query->row;
	}

	public function getProducts($provider_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "provider_to_product p2v
								  LEFT JOIN " . DB_PREFIX . "product p ON (p2v.product_id = p.product_id)
								  LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
								  WHERE p2v.provider_id = '" . (int)$provider_id . "' AND pd.language_id = '".$this->config->get('config_language_id')."'");

		return $query->rows;
	}

	public function getProviders($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "provider";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
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

	
	public function getTotalProviders() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "provider");

		return $query->row['total'];
	}
}
