<?php
class ModelAccountAuction extends Model {
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

	public function getAuctionHistorys($auction_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "auction_history ah
				LEFT JOIN " . DB_PREFIX . "product p ON (ah.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (ah.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "customer c ON (ah.customer_id = c.customer_id)
				WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
			$sql .= " AND ah.auction_id LIKE '" . (int)$auction_id . "%'";
		
			$sql .= " ORDER BY ah.auction_history_id";
			$sql .= " DESC";

	
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
	public function addAuction($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "auction SET
						 lot_id = '" . (int)$data['lot_id'] . "',
						 product_id = '" . (int)$data['product_id'] . "',
						 customer_id = '" . (int)$data['customer_id'] . "',
						 price_start = '" . (float)$data['price_start'] . "',
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

		
			$sql .= " AND a.customer_id = '" . $this->customer->isLogged() . "'";
		
			$sql .= " ORDER BY a.auction_id DESC";
		

		$query = $this->db->query($sql);

		return $query->rows;
	}

	
	public function getCustomerAuctionProduct($data = array()) {
		$sql = "SELECT DISTINCT a.auction_id, a.*, p.*, pd.*, c.* FROM " . DB_PREFIX . "auction a
				LEFT JOIN " . DB_PREFIX . "product p ON (a.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "auction_history ah ON (ah.auction_id = a.auction_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (a.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "customer c ON (a.customer_id = c.customer_id)
				WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		
			$sql .= " AND ah.customer_id = '" . $this->customer->isLogged() . "'";
		
			$sql .= " ORDER BY a.auction_id DESC";
		

		$query = $this->db->query($sql);

		return $query->rows;
	}

	
	public function getCustomerAuctionProductTotal($data = array()) {
		$sql = "SELECT  count(DISTINCT a.auction_id) AS total FROM " . DB_PREFIX . "auction a
				LEFT JOIN " . DB_PREFIX . "product p ON (a.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "auction_history ah ON (ah.auction_id = a.auction_id)
				LEFT JOIN " . DB_PREFIX . "customer c ON (a.customer_id = c.customer_id)
				WHERE ";

		
			$sql .= " ah.customer_id = '" . $this->customer->isLogged() . "'";
		
			$sql .= " ORDER BY a.auction_id DESC";
		

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	

	public function getTotalAuctions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "auction WHERE customer_id = '" . $this->customer->isLogged() . "'");

		return $query->row['total'];
	}

	public function getTotalAuctionsByAuctionGroupId($auction_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "auction WHERE auction_group_id = '" . (int)$auction_group_id . "'");

		return $query->row['total'];
	}
}
