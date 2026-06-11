<?php
class ModelLocalisationConditionStatus extends Model {

	public function getConditionStatus($condition_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "condition_status WHERE condition_status_id = '" . (int)$condition_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getConditionStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "condition_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

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
		} else {
			$condition_status_data = $this->cache->get('condition_status.' . (int)$this->config->get('config_language_id'));

			if (!$condition_status_data) {
				$query = $this->db->query("SELECT condition_status_id, name FROM " . DB_PREFIX . "condition_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$condition_status_data = $query->rows;

				$this->cache->set('condition_status.' . (int)$this->config->get('config_language_id'), $condition_status_data);
			}

			return $condition_status_data;
		}
	}

	public function getConditionStatusDescriptions($condition_status_id) {
		$condition_status_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "condition_status WHERE condition_status_id = '" . (int)$condition_status_id . "'");

		foreach ($query->rows as $result) {
			$condition_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $condition_status_data;
	}

	public function getTotalConditionStatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "condition_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
}