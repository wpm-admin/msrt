<?php
class ModelCatalogAuction extends Model {
	public function addAuction($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "auction SET
						 lot_id = '" . (int)$data['lot_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 customer_id = '" . (int)$data['customer_id'] . "',
						 price_start = '" . (float)$data['price_start'] . "',
						 reserv_price = '" . (float)$data['reserv_price'] . "',
						 price_end = '" . (float)$data['price_end'] . "',
						 price_step = '" . (float)$data['price_step'] . "',
						 `key` = '" . $this->db->escape($data['key']) . "',
						 status = '" . (int)$data['status'] . "',
						 date_added = NOW(),
						 date_start = '" . $this->db->escape($data['date_start']) . "',
						 date_end = '" . $this->db->escape($data['date_end']) . "'");

		$auction_id = $this->db->getLastId();

		return $auction_id;
	}

	public function editAuction($auction_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "auction SET
						 lot_id = '" . (int)$data['lot_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 customer_id = '" . (int)$data['customer_id'] . "',
						 price_start = '" . (float)$data['price_start'] . "',
						 reserv_price = '" . (float)$data['reserv_price'] . "',
						 price_end = '" . (float)$data['price_end'] . "',
						 price_step = '" . (float)$data['price_step'] . "',
						 `key` = '" . $this->db->escape($data['key']) . "',
						 status = '" . (int)$data['status'] . "',
						 date_added = NOW(),
						 date_start = '" . $this->db->escape($data['date_start']) . "',
						 date_end = '" . $this->db->escape($data['date_end']) . "' WHERE auction_id = '" . (int)$auction_id . "'");

	}

	public function deleteAuction($auction_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "auction WHERE auction_id = '" . (int)$auction_id . "'");
	}

	public function getAuction($auction_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction a WHERE a.auction_id = '" . (int)$auction_id . "'");

		return $query->row;
	}

	public function getAuctions($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "auction a
				LEFT JOIN " . DB_PREFIX . "product p ON (a.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (a.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "customer c ON (a.customer_id = c.customer_id)
				WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

	
		$sort_data = array(
			'ad.name',
			'auction_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.auction_id";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
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

	

	public function getTotalAuctions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "auction");

		return $query->row['total'];
	}

	public function getTotalAuctionsByAuctionGroupId($auction_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "auction WHERE auction_group_id = '" . (int)$auction_group_id . "'");

		return $query->row['total'];
	}
}
