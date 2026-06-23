<?php
class ModelVendorVendorGroup extends Model {
	public function addVendorGroup($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_group SET approval = '" . (int)$data['approval'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$vendor_group_id = $this->db->getLastId();

		foreach ($data['vendor_group_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_group_description SET vendor_group_id = '" . (int)$vendor_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
		
		return $vendor_group_id;
	}

	public function editVendorGroup($vendor_group_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "vendor_group SET approval = '" . (int)$data['approval'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_group_description WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");

		foreach ($data['vendor_group_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "vendor_group_description SET vendor_group_id = '" . (int)$vendor_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
	}

	public function deleteVendorGroup($vendor_group_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_group WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "vendor_group_description WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "tax_rate_to_vendor_group WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");
	}

	public function getVendorGroup($vendor_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "vendor_group cg LEFT JOIN " . DB_PREFIX . "vendor_group_description cgd ON (cg.vendor_group_id = cgd.vendor_group_id) WHERE cg.vendor_group_id = '" . (int)$vendor_group_id . "' AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getVendorGroups($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "vendor_group cg LEFT JOIN " . DB_PREFIX . "vendor_group_description cgd ON (cg.vendor_group_id = cgd.vendor_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'cgd.name',
			'cg.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cgd.name";
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

	public function getVendorGroupDescriptions($vendor_group_id) {
		$vendor_group_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_group_description WHERE vendor_group_id = '" . (int)$vendor_group_id . "'");

		foreach ($query->rows as $result) {
			$vendor_group_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}

		return $vendor_group_data;
	}

	public function getTotalVendorGroups() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_group");

		return $query->row['total'];
	}
}
