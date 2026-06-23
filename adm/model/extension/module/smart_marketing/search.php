<?php
class ModelExtensionModuleSmartMarketingSearch extends Model {
	public function getLatestProductsIds($data) {
		$products_ids = array();

		$sql = "SELECT DISTINCT p.product_id ";

		if (!empty($data['filter_category'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(p.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_category'])) {
			$categories = array();

			foreach ($data['filter_category'] as $category_id) {
				$categories[] = (int)$category_id;
			}

			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id IN (" . implode(",", $categories) . ")";
			} else {
				$sql .= " AND p2c.category_id IN (" . implode(",", $categories) . ")";
			}
		}

		$sql .= " ORDER BY " . $this->db->escape($data['sort']) . " " . $this->db->escape($data['order']);
		$sql .= " LIMIT 0," . (int)$data['limit'];

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $product) {
				$products_ids[] = $product['product_id'];
			}
		}

		return $products_ids;
	}

	public function getSpecialProductsIds($data) {
		$products_ids = array();

		$sql = "SELECT DISTINCT p.product_id, ROUND((1 - IFNULL((SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1), p.price) / p.price) * 100, 2) AS discount_percent";

		if (!empty($data['filter_category'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category'])) {
			$categories = array();

			foreach ($data['filter_category'] as $category_id) {
				$categories[] = (int)$category_id;
			}

			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id IN (" . implode(",", $categories) . ")";
			} else {
				$sql .= " AND p2c.category_id IN (" . implode(",", $categories) . ")";
			}
		}

		$sql .= " HAVING";

		if (!empty($data['filter_discount_min'])) {
			$sql .= " discount_percent >= '" . (float)$data['filter_discount_min'] . "'";
		} else {
			$sql .= " discount_percent > '0'";
		}

		if (!empty($data['filter_discount_max'])) {
			$sql .= " AND discount_percent <= '" . (float)$data['filter_discount_max'] . "'";
		}

		$sql .= " ORDER BY " . $this->db->escape($data['sort']) . " " . $this->db->escape($data['order']);
		$sql .= " LIMIT 0," . (int)$data['limit'];

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $product) {
				$products_ids[] = $product['product_id'];
			}
		}

		return $products_ids;
	}

	public function getTopSalesProductsIds($data) {
		$products_ids = array();

		$sql = "SELECT op.product_id, SUM(op.quantity) AS sold_quantity, SUM(op.total) AS amount_paid";

		if (!empty($data['filter_category'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "order_product op ON (p2c.product_id = op.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "order_product op";
		}

		$sql .= " LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		$sql .= " AND o.order_status_id IN (" . implode(",", $this->getPendingCompleteOrderStatuses()) . ")";

		if (!empty($data['filter_category'])) {
			$categories = array();

			foreach ($data['filter_category'] as $category_id) {
				$categories[] = (int)$category_id;
			}

			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id IN (" . implode(",", $categories) . ")";
			} else {
				$sql .= " AND p2c.category_id IN (" . implode(",", $categories) . ")";
			}
		}

		$sql .= " GROUP BY op.product_id";

		$sql .= " ORDER BY " . $this->db->escape($data['sort']) . " " . $this->db->escape($data['order']);
		$sql .= " LIMIT 0," . (int)$data['limit'];

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $product) {
				$products_ids[] = $product['product_id'];
			}
		}

		return $products_ids;
	}

	public function getRelatedProductsIds($data) {
		$products_ids = array();

		$sql = "SELECT DISTINCT pr.related_id FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		$products = array();

		foreach ($data['filter_related'] as $product_id) {
			$products[] = (int)$product_id;
		}


		$sql .= " AND pr.product_id IN (" . implode(",", $products) . ")";


		$sql .= " ORDER BY " . $this->db->escape($data['sort']) . " " . $this->db->escape($data['order']);
		$sql .= " LIMIT 0," . (int)$data['limit'];

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $product) {
				$products_ids[] = $product['related_id'];
			}
		}

		return $products_ids;
	}

	public function getBoughtTogetherProductsIds($data) {
		$products_ids = array();

		$sql = "SELECT op2.product_id, SUM(op2.quantity) AS sold_quantity, SUM(op2.total) AS amount_paid FROM " . DB_PREFIX . "order_product op1 JOIN " . DB_PREFIX . "order_product op2 ON (op1.order_id = op2.order_id) LEFT JOIN " . DB_PREFIX . "product p ON (op2.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		$products = array();

		foreach ($data['filter_also_bought'] as $product_id) {
			$products[] = (int)$product_id;
		}

		$sql .= " AND op1.product_id IN (" . implode(",", $products) . ")";
		$sql .= " AND op2.product_id NOT IN (" . implode(",", $products) . ")";
		//$sql .= " AND o.order_status_id IN (" . implode(",", $this->getPendingCompleteOrderStatuses()) . ")";

		$sql .= " GROUP BY op2.product_id";


		$sql .= " ORDER BY " . $this->db->escape($data['sort']) . " " . $this->db->escape($data['order']);
		$sql .= " LIMIT 0," . (int)$data['limit'];

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $product) {
				$products_ids[] = $product['product_id'];
			}
		}

		return $products_ids;
	}

	public function getAdvancedSearchProductsIds($data) {
		$products_ids = array();

		$sql = "SELECT p.product_id, p.quantity, p.price,  (IFNULL((SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1), p.price)) AS special";

		if ($this->isKeyInSearchConditions('p2c.category_id', $data['filter_condition'])) {
			$sql .= " FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		$sql .= $this->getSQLPartsConditionsBased($data);
		$sql .= " GROUP BY p.product_id";
		$sql .= " HAVING 1 " . $this->getSQLPartsConditionsBased($data, true);

		$sql .= " ORDER BY " . $this->db->escape($this->getComputedKeyTranslation($data['sort'])) . " " . $this->db->escape($data['order']);
		$sql .= " LIMIT 0," . (int)$data['limit'];

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach ($query->rows as $product) {
				$products_ids[] = $product['product_id'];
			}
		}

		return $products_ids;
	}

	public function getProductsByIds($products_ids) {
		$query = $this->db->query("SELECT p.product_id, pd.name as name, p.model, p.quantity,  p.price, p.tax_class_id, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.product_id IN (" . implode(",", $products_ids) . ") ORDER BY FIELD(p.product_id, " . implode(",", $products_ids) . ")");

		return $query->rows;
	}

	private function getSQLPartsConditionsBased($data, $compute = false) {
		$sql = '';

		$implode = array();

		$sql_parts = $this->getSQLPartsOperatorsBased();

		foreach ($data['filter_condition'] as $condition) {
			if (($compute && $condition['compute']) || (!$compute && !$condition['compute'])) {
				$sql_part = $sql_parts[$condition['operator']];

				if ($compute && $condition['compute']) {
					$condition['key'] = $this->getComputedKeyTranslation($condition['key']);
				}

				$find = array(
					'{key}',
					'{value}'
				);

				$replace = array(
					'key'   => $this->db->escape($condition['key']),
					'value' => $this->db->escape($condition['value'])
				);

				if (strpos($sql_part, '{key}') !== false && strpos($sql_part, '{value}') !== false) {
					$implode[] = str_replace($find, $replace, $sql_part);
				} elseif (strpos($sql_part, '{value}') !== false) {
					$implode[] = $condition['key'] . ' ' . str_replace($find, $replace, $sql_part);
				} else {
					$operator = $sql_part;

					$implode[] = $condition['key'] . ' ' . $operator . ' \'' . $condition['value'] . '\'';
				}
			}
		}

		if ($implode) {
			$sql = " AND ( " . implode(utf8_strtoupper(' ' . $data['filter_match']) . ' ', $implode) . " )";
		}

		return $sql;
	}

	private function getSQLPartsOperatorsBased() {
		return array(
			'equal'              => '=',
			'not-equal'          => '!=',
			'greater'            => '>',
			'less'               => '<',
			'contain'            => 'LOWER({key}) LIKE LOWER(\'%{value}%\')',
			'not-contain'        => 'LOWER({key}) NOT LIKE LOWER(\'%{value}%\')',
			'start-with'         => 'LOWER({key}) LIKE LOWER(\'{value}%\')',
			'end-with'           => 'LOWER({key}) NOT LIKE LOWER(\'%{value}\')',
			'is-before'          => 'DATE({key}) < DATE(\'{value}\')',
			'is-after'           => 'DATE({key}) > DATE(\'{value}\')',
			'is-before-or-equal' => 'DATE({key}) <= DATE(\'{value}\')',
			'is-after-or-equal'  => 'DATE({key}) => DATE(\'{value}\')',
			'is-in'              => '{key} IN ({value})',
			'is-not-in'          => '{key} NOT IN ({value})'
		);
	}

	private function getSQLPartsComputedKeys() {
		return array(
			'stock'            => "(p.quantity > 0)",
			'discount_status'  => "(p.price - special > 0)",
			'discount_amount'  => "(p.price - special)",
			'discount_percent' => "ROUND((1 - special / p.price) * 100, 2)"
		);
	}

	private function getComputedKeyTranslation($key) {
		$computed_keys = $this->getSQLPartsComputedKeys();

		if (isset($computed_keys[$key])) {
			return $computed_keys[$key];
		} else {
			return $key;
		}
	}

	private function isKeyInSearchConditions($key, $search_conditions) {
		$status = false;

		foreach ($search_conditions as $search_condition) {
			if ($search_condition['key'] == $key) {
				$status = true;

				break;
			}
		}

		return $status;
	}

	public function getPendingCompleteOrderStatuses() {
		$order_statuses =  array_unique(array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')));

		return $order_statuses;
	}
}
