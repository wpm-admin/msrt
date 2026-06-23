<?php
class ModelCatalogMark extends Model {
	public function addMark($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "mark SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()");

		$mark_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mark SET image = '" . $this->db->escape($data['image']) . "' WHERE mark_id = '" . (int)$mark_id . "'");
		}

		foreach ($data['mark_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mark_description SET mark_id = '" . (int)$mark_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "mark_path` SET `mark_id` = '" . (int)$mark_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "mark_path` SET `mark_id` = '" . (int)$mark_id . "', `path_id` = '" . (int)$mark_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['mark_filter'])) {
			foreach ($data['mark_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mark_filter SET mark_id = '" . (int)$mark_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['mark_store'])) {
			foreach ($data['mark_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mark_to_store SET mark_id = '" . (int)$mark_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (isset($data['mark_seo_url'])) {
			foreach ($data['mark_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mark_id=" . (int)$mark_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		// Set which layout to use with this mark
		if (isset($data['mark_layout'])) {
			foreach ($data['mark_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mark_to_layout SET mark_id = '" . (int)$mark_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('mark');

		return $mark_id;
	}

	public function editMark($mark_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mark SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE mark_id = '" . (int)$mark_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "mark SET image = '" . $this->db->escape($data['image']) . "' WHERE mark_id = '" . (int)$mark_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_description WHERE mark_id = '" . (int)$mark_id . "'");

		foreach ($data['mark_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "mark_description SET mark_id = '" . (int)$mark_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mark_path` WHERE path_id = '" . (int)$mark_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $mark_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$mark_path['mark_id'] . "' AND level < '" . (int)$mark_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$mark_path['mark_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "mark_path` SET mark_id = '" . (int)$mark_path['mark_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$mark_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "mark_path` SET mark_id = '" . (int)$mark_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "mark_path` SET mark_id = '" . (int)$mark_id . "', `path_id` = '" . (int)$mark_id . "', level = '" . (int)$level . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_filter WHERE mark_id = '" . (int)$mark_id . "'");

		if (isset($data['mark_filter'])) {
			foreach ($data['mark_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mark_filter SET mark_id = '" . (int)$mark_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_to_store WHERE mark_id = '" . (int)$mark_id . "'");

		if (isset($data['mark_store'])) {
			foreach ($data['mark_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mark_to_store SET mark_id = '" . (int)$mark_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'mark_id=" . (int)$mark_id . "'");

		if (isset($data['mark_seo_url'])) {
			foreach ($data['mark_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'mark_id=" . (int)$mark_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_to_layout WHERE mark_id = '" . (int)$mark_id . "'");

		if (isset($data['mark_layout'])) {
			foreach ($data['mark_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "mark_to_layout SET mark_id = '" . (int)$mark_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('mark');
	}

	public function deleteMark($mark_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_path WHERE mark_id = '" . (int)$mark_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_path WHERE path_id = '" . (int)$mark_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteMark($result['mark_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "mark WHERE mark_id = '" . (int)$mark_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_description WHERE mark_id = '" . (int)$mark_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_filter WHERE mark_id = '" . (int)$mark_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_to_store WHERE mark_id = '" . (int)$mark_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "mark_to_layout WHERE mark_id = '" . (int)$mark_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_mark WHERE mark_id = '" . (int)$mark_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'mark_id=" . (int)$mark_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_mark WHERE mark_id = '" . (int)$mark_id . "'");

		$this->cache->delete('mark');
	}

	public function repairMarks($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $mark) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$mark['mark_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mark_path` WHERE mark_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "mark_path` SET mark_id = '" . (int)$mark['mark_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "mark_path` SET mark_id = '" . (int)$mark['mark_id'] . "', `path_id` = '" . (int)$mark['mark_id'] . "', level = '" . (int)$level . "'");

			$this->repairMarks($mark['mark_id']);
		}
	}

	public function getMark($mark_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "mark_path cp LEFT JOIN " . DB_PREFIX . "mark_description cd1 ON (cp.path_id = cd1.mark_id AND cp.mark_id != cp.path_id) WHERE cp.mark_id = c.mark_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.mark_id) AS path FROM " . DB_PREFIX . "mark c LEFT JOIN " . DB_PREFIX . "mark_description cd2 ON (c.mark_id = cd2.mark_id) WHERE c.mark_id = '" . (int)$mark_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}

	public function getMarks($data = array()) {
		$sql = "SELECT cp.mark_id AS mark_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "mark_path cp LEFT JOIN " . DB_PREFIX . "mark c1 ON (cp.mark_id = c1.mark_id) LEFT JOIN " . DB_PREFIX . "mark c2 ON (cp.path_id = c2.mark_id) LEFT JOIN " . DB_PREFIX . "mark_description cd1 ON (cp.path_id = cd1.mark_id) LEFT JOIN " . DB_PREFIX . "mark_description cd2 ON (cp.mark_id = cd2.mark_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY cp.mark_id";

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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

		$return = array();
		foreach($query->rows as $row){
			$return[$row['mark_id']] = $row;
		}
		
		return $return;
	}

	public function getMarkDescriptions($mark_id) {
		$mark_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_description WHERE mark_id = '" . (int)$mark_id . "'");

		foreach ($query->rows as $result) {
			$mark_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $mark_description_data;
	}
	
	public function getMarkPath($mark_id) {
		$query = $this->db->query("SELECT mark_id, path_id, level FROM " . DB_PREFIX . "mark_path WHERE mark_id = '" . (int)$mark_id . "'");

		return $query->rows;
	}
	
	public function getMarkFilters($mark_id) {
		$mark_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_filter WHERE mark_id = '" . (int)$mark_id . "'");

		foreach ($query->rows as $result) {
			$mark_filter_data[] = $result['filter_id'];
		}

		return $mark_filter_data;
	}

	public function getMarkStores($mark_id) {
		$mark_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_to_store WHERE mark_id = '" . (int)$mark_id . "'");

		foreach ($query->rows as $result) {
			$mark_store_data[] = $result['store_id'];
		}

		return $mark_store_data;
	}
	
	public function getMarkSeoUrls($mark_id) {
		$mark_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'mark_id=" . (int)$mark_id . "'");

		foreach ($query->rows as $result) {
			$mark_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $mark_seo_url_data;
	}
	
	public function getMarkLayouts($mark_id) {
		$mark_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_to_layout WHERE mark_id = '" . (int)$mark_id . "'");

		foreach ($query->rows as $result) {
			$mark_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $mark_layout_data;
	}

	public function getTotalMarks() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mark");

		return $query->row['total'];
	}
	
	public function getTotalMarksByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "mark_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}	
}