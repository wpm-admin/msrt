<?php
class ModelExtensionModuleSmartMarketingProduct extends Model {
	public function getProduct($product_id, $language_switch = false) {
		if ($language_switch && isset($this->session->data['smart_marketing_language_id'])) {
			$language_id = $this->session->data['smart_marketing_language_id'];
		} else {
			$language_id = $this->config->get('config_language_id');
		}

		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$language_id . "' AND p.status = '1' AND p.date_available <= NOW()");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'model'            => $query->row['model'],
				'quantity'         => $query->row['quantity'],
				'stock_status_id'  => $query->row['stock_status_id'],
				'image'            => $query->row['image'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'keyword'          => $this->getKeyword($product_id, $language_id)
			);
		} else {
			return false;
		}
	}

	private function getKeyword($product_id, $language_id = 1) {
		$keyword = '';

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

		if ($query->num_rows) {
			// check case multilanguage keywords (custom seo extension)
			if ($query->num_rows > 1) {
				foreach ($query->rows as $keyword_info) {
					if (isset($keyword_info['language_id']) && $keyword_info['language_id'] == $language_id) {
						$keyword = $keyword_info['keyword'];

						break;
					}
				}
			} else {
				$keyword = $query->row['keyword'];
			}
		}

		return $keyword;
	}

	public function getLink($product_id, $keyword) {
		$store_base_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;

		if ($this->config->get('config_seo_url') && !empty($keyword)) {
			return $store_base_url . $keyword;
		} else {
			return $store_base_url . 'index.php?route=product/product&product_id=' . $product_id;
		}
	}

	public function getAutocomplete($data = array()) {
		$sql = "SELECT p.product_id, pd.name as name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1'";

		if (!empty($data['filter_search'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_search']) . "%'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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

	public function getSegmentationProducts($segment_conditions) {
		$product_data = array();

		$products_ids = $this->getProductsIdsFromSegmentationConditions($segment_conditions);

		if ($products_ids) {
			$query = $this->db->query("SELECT p.product_id, pd.name as name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id IN (" . implode(",", $products_ids) . ") AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1'");

			if ($query->num_rows) {
				$product_data = $query->rows;
			}
		}

		return $product_data;
	}

	private function getProductsIdsFromSegmentationConditions($segment_conditions) {
		$products_ids = array();

		if ($segment_conditions) {
			foreach ($segment_conditions as $segment_condition) {
				if ($segment_condition['key'] == 'product_id') {
					$products_ids[] = $segment_condition['value'];
				}
			}
		}

		return $products_ids;
	}
}
