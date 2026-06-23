<?php
class ModelCatalogAuctionHistory extends Model {
	public function addAuctionHistory($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "auction_history SET auction_history_group_id = '" . (int)$data['auction_history_group_id'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$auction_history_id = $this->db->getLastId();

		foreach ($data['auction_history_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "auction_history_description SET auction_history_id = '" . (int)$auction_history_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		return $auction_history_id;
	}

	public function editAuctionHistory($auction_history_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "auction_history SET auction_history_group_id = '" . (int)$data['auction_history_group_id'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE auction_history_id = '" . (int)$auction_history_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "auction_history_description WHERE auction_history_id = '" . (int)$auction_history_id . "'");

		foreach ($data['auction_history_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "auction_history_description SET auction_history_id = '" . (int)$auction_history_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}

	public function deleteAuctionHistory($auction_history_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "auction_history WHERE auction_history_id = '" . (int)$auction_history_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "auction_history_description WHERE auction_history_id = '" . (int)$auction_history_id . "'");
	}

	public function getAuctionHistory($auction_history_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction_history a WHERE a.auction_history_id = '" . (int)$auction_id . "'");

		return $query->row;
	}

	public function getAuctionHistorys($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "auction_history ah
				LEFT JOIN " . DB_PREFIX . "product p ON (ah.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (ah.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "customer c ON (ah.customer_id = c.customer_id)
				WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}


		$sort_data = array(
			'ad.name',
			'auction_history_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ah.auction_history_id";
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


	public function getTotalAuctionHistorys() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "auction_history");

		return $query->row['total'];
	}

	public function getTotalAuctionHistorysByAuctionHistoryGroupId($auction_history_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "auction_history WHERE auction_history_group_id = '" . (int)$auction_history_group_id . "'");

		return $query->row['total'];
	}
}
