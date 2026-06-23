<?php
class ModelExtensionModuleSmartMarketingCategory extends Model {
	public function getCategory($category_id, $language_switch = false) {
		if ($language_switch && isset($this->session->data['smart_marketing_language_id'])) {
			$language_id = $this->session->data['smart_marketing_language_id'];
		} else {
			$language_id = $this->config->get('config_language_id');
		}

		$query = $this->db->query("SELECT c.*, cd.name, cd.description FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$language_id . "' AND c.status = '1'");

		if ($query->num_rows) {
			return array(
				'category_id' => $query->row['category_id'],
				'name'        => $query->row['name'],
				'description' => $query->row['description'],
				'image'       => $query->row['image'],
				'keyword'     => $this->getKeyword($category_id, $language_id)
			);
		} else {
			return false;
		}
	}

	private function getKeyword($category_id, $language_id) {
		$keyword = '';

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'");

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

	public function getLink($category_id, $keyword) {
		$store_base_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;

		if ($this->config->get('config_seo_url') && !empty($keyword)) {
			return $store_base_url . $keyword;
		} else {
			return $store_base_url . 'index.php?route=product/category&path=' . $category_id;
		}
	}

	public function getCategories() {
		$cache_key = 'smart.marketing.category';

		$category_data = $this->cache->get($cache_key);

		if (!$category_data) {
			$category_data = array();

			$query = $this->db->query("SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id ORDER BY name ASC");

			if ($query->num_rows) {
				foreach ($query->rows as $row) {
					$category_data[] = array(
						'category_id' => $row['category_id'],
						'name'        => $row['name']
					);
				}

				$this->cache->set($cache_key, $category_data);
			}
		}

		return $category_data;
	}

	public function getAutocomplete($data = array()) {
		$sql = "SELECT c.category_id, cd.name as name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = '1'";

		if (!empty($data['filter_search'])) {
			$sql .= " AND cd.name LIKE '" . $this->db->escape($data['filter_search']) . "%'";
		}

		$sql .= " GROUP BY c.category_id";

		$sort_data = array(
			'cd.name',
			'c.category_id'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cd.name";
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
}
