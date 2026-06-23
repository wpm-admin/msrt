<?php
class ModelCatalogBlogProduct extends Model {
	public function addProduct($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

		$blog_product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "blog_product SET image = '" . $this->db->escape($data['image']) . "' WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		}

		foreach ($data['blog_product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_description SET blog_product_id = '" . (int)$blog_product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['blog_product_store'])) {
			foreach ($data['blog_product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_store SET blog_product_id = '" . (int)$blog_product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['blog_product_attribute'])) {
			foreach ($data['blog_product_attribute'] as $blog_product_attribute) {
				if ($blog_product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_attribute WHERE blog_product_id = '" . (int)$blog_product_id . "' AND attribute_id = '" . (int)$blog_product_attribute['attribute_id'] . "'");

					foreach ($blog_product_attribute['blog_product_attribute_description'] as $language_id => $blog_product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_attribute WHERE blog_product_id = '" . (int)$blog_product_id . "' AND attribute_id = '" . (int)$blog_product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_attribute SET blog_product_id = '" . (int)$blog_product_id . "', attribute_id = '" . (int)$blog_product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($blog_product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['blog_product_option'])) {
			foreach ($data['blog_product_option'] as $blog_product_option) {
				if ($blog_product_option['type'] == 'select' || $blog_product_option['type'] == 'radio' || $blog_product_option['type'] == 'checkbox' || $blog_product_option['type'] == 'image') {
					if (isset($blog_product_option['blog_product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_option SET blog_product_id = '" . (int)$blog_product_id . "', option_id = '" . (int)$blog_product_option['option_id'] . "', required = '" . (int)$blog_product_option['required'] . "'");

						$blog_product_option_id = $this->db->getLastId();

						foreach ($blog_product_option['blog_product_option_value'] as $blog_product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_option_value SET blog_product_option_id = '" . (int)$blog_product_option_id . "', blog_product_id = '" . (int)$blog_product_id . "', option_id = '" . (int)$blog_product_option['option_id'] . "', option_value_id = '" . (int)$blog_product_option_value['option_value_id'] . "', quantity = '" . (int)$blog_product_option_value['quantity'] . "', subtract = '" . (int)$blog_product_option_value['subtract'] . "', price = '" . (float)$blog_product_option_value['price'] . "', price_prefix = '" . $this->db->escape($blog_product_option_value['price_prefix']) . "', points = '" . (int)$blog_product_option_value['points'] . "', points_prefix = '" . $this->db->escape($blog_product_option_value['points_prefix']) . "', weight = '" . (float)$blog_product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($blog_product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_option SET blog_product_id = '" . (int)$blog_product_id . "', option_id = '" . (int)$blog_product_option['option_id'] . "', value = '" . $this->db->escape($blog_product_option['value']) . "', required = '" . (int)$blog_product_option['required'] . "'");
				}
			}
		}

		if (isset($data['blog_product_recurring'])) {
			foreach ($data['blog_product_recurring'] as $recurring) {

				$query = $this->db->query("SELECT `blog_product_id` FROM `" . DB_PREFIX . "blog_product_recurring` WHERE `blog_product_id` = '" . (int)$blog_product_id . "' AND `customer_group_id = '" . (int)$recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "blog_product_recurring` SET `blog_product_id` = '" . (int)$blog_product_id . "', customer_group_id = '" . (int)$recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");
				}
			}
		}
		
		if (isset($data['blog_product_discount'])) {
			foreach ($data['blog_product_discount'] as $blog_product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_discount SET blog_product_id = '" . (int)$blog_product_id . "', customer_group_id = '" . (int)$blog_product_discount['customer_group_id'] . "', quantity = '" . (int)$blog_product_discount['quantity'] . "', priority = '" . (int)$blog_product_discount['priority'] . "', price = '" . (float)$blog_product_discount['price'] . "', date_start = '" . $this->db->escape($blog_product_discount['date_start']) . "', date_end = '" . $this->db->escape($blog_product_discount['date_end']) . "'");
			}
		}

		if (isset($data['blog_product_special'])) {
			foreach ($data['blog_product_special'] as $blog_product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_special SET blog_product_id = '" . (int)$blog_product_id . "', customer_group_id = '" . (int)$blog_product_special['customer_group_id'] . "', priority = '" . (int)$blog_product_special['priority'] . "', price = '" . (float)$blog_product_special['price'] . "', date_start = '" . $this->db->escape($blog_product_special['date_start']) . "', date_end = '" . $this->db->escape($blog_product_special['date_end']) . "'");
			}
		}

		if (isset($data['blog_product_image'])) {
			foreach ($data['blog_product_image'] as $blog_product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_image SET blog_product_id = '" . (int)$blog_product_id . "', image = '" . $this->db->escape($blog_product_image['image']) . "', sort_order = '" . (int)$blog_product_image['sort_order'] . "'");
			}
		}

		if (isset($data['blog_product_download'])) {
			foreach ($data['blog_product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_download SET blog_product_id = '" . (int)$blog_product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['blog_product_blog_category'])) {
			foreach ($data['blog_product_blog_category'] as $blog_category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_category SET blog_product_id = '" . (int)$blog_product_id . "', blog_category_id = '" . (int)$blog_category_id . "'");
			}
		}

	
		if (isset($data['blog_product_filter'])) {
			foreach ($data['blog_product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_filter SET blog_product_id = '" . (int)$blog_product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['blog_product_related'])) {
			foreach ($data['blog_product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE blog_product_id = '" . (int)$blog_product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_related SET blog_product_id = '" . (int)$blog_product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE blog_product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$blog_product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_related SET blog_product_id = '" . (int)$related_id . "', related_id = '" . (int)$blog_product_id . "'");
			}
		}

		if (isset($data['blog_product_reward'])) {
			foreach ($data['blog_product_reward'] as $customer_group_id => $blog_product_reward) {
				if ((int)$blog_product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_reward SET blog_product_id = '" . (int)$blog_product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$blog_product_reward['points'] . "'");
				}
			}
		}
		
		// SEO URL
		if (isset($data['blog_product_seo_url'])) {
			foreach ($data['blog_product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'blog_product_id=" . (int)$blog_product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		if (isset($data['blog_product_layout'])) {
			foreach ($data['blog_product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_layout SET blog_product_id = '" . (int)$blog_product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}


		$this->cache->delete('blog_product');

		return $blog_product_id;
	}

	public function editProduct($blog_product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "blog_product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "blog_product SET image = '" . $this->db->escape($data['image']) . "' WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_description WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($data['blog_product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_description SET blog_product_id = '" . (int)$blog_product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_store WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_store'])) {
			foreach ($data['blog_product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_store SET blog_product_id = '" . (int)$blog_product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_attribute WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (!empty($data['blog_product_attribute'])) {
			foreach ($data['blog_product_attribute'] as $blog_product_attribute) {
				if ($blog_product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_attribute WHERE blog_product_id = '" . (int)$blog_product_id . "' AND attribute_id = '" . (int)$blog_product_attribute['attribute_id'] . "'");

					foreach ($blog_product_attribute['blog_product_attribute_description'] as $language_id => $blog_product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_attribute SET blog_product_id = '" . (int)$blog_product_id . "', attribute_id = '" . (int)$blog_product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($blog_product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_option WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_option_value WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_option'])) {
			foreach ($data['blog_product_option'] as $blog_product_option) {
				if ($blog_product_option['type'] == 'select' || $blog_product_option['type'] == 'radio' || $blog_product_option['type'] == 'checkbox' || $blog_product_option['type'] == 'image') {
					if (isset($blog_product_option['blog_product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_option SET blog_product_option_id = '" . (int)$blog_product_option['blog_product_option_id'] . "', blog_product_id = '" . (int)$blog_product_id . "', option_id = '" . (int)$blog_product_option['option_id'] . "', required = '" . (int)$blog_product_option['required'] . "'");

						$blog_product_option_id = $this->db->getLastId();

						foreach ($blog_product_option['blog_product_option_value'] as $blog_product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_option_value SET blog_product_option_value_id = '" . (int)$blog_product_option_value['blog_product_option_value_id'] . "', blog_product_option_id = '" . (int)$blog_product_option_id . "', blog_product_id = '" . (int)$blog_product_id . "', option_id = '" . (int)$blog_product_option['option_id'] . "', option_value_id = '" . (int)$blog_product_option_value['option_value_id'] . "', quantity = '" . (int)$blog_product_option_value['quantity'] . "', subtract = '" . (int)$blog_product_option_value['subtract'] . "', price = '" . (float)$blog_product_option_value['price'] . "', price_prefix = '" . $this->db->escape($blog_product_option_value['price_prefix']) . "', points = '" . (int)$blog_product_option_value['points'] . "', points_prefix = '" . $this->db->escape($blog_product_option_value['points_prefix']) . "', weight = '" . (float)$blog_product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($blog_product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_option SET blog_product_option_id = '" . (int)$blog_product_option['blog_product_option_id'] . "', blog_product_id = '" . (int)$blog_product_id . "', option_id = '" . (int)$blog_product_option['option_id'] . "', value = '" . $this->db->escape($blog_product_option['value']) . "', required = '" . (int)$blog_product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "blog_product_recurring` WHERE blog_product_id = " . (int)$blog_product_id);

		if (isset($data['blog_product_recurring'])) {
			foreach ($data['blog_product_recurring'] as $blog_product_recurring) {
				$query = $this->db->query("SELECT `blog_product_id` FROM `" . DB_PREFIX . "blog_product_recurring` WHERE `blog_product_id` = '" . (int)$blog_product_id . "' AND `customer_group_id` = '" . (int)$blog_product_recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$blog_product_recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "blog_product_recurring` SET `blog_product_id` = '" . (int)$blog_product_id . "', `customer_group_id` = '" . (int)$blog_product_recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$blog_product_recurring['recurring_id'] . "'");
				}				
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_discount WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_discount'])) {
			foreach ($data['blog_product_discount'] as $blog_product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_discount SET blog_product_id = '" . (int)$blog_product_id . "', customer_group_id = '" . (int)$blog_product_discount['customer_group_id'] . "', quantity = '" . (int)$blog_product_discount['quantity'] . "', priority = '" . (int)$blog_product_discount['priority'] . "', price = '" . (float)$blog_product_discount['price'] . "', date_start = '" . $this->db->escape($blog_product_discount['date_start']) . "', date_end = '" . $this->db->escape($blog_product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_special WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_special'])) {
			foreach ($data['blog_product_special'] as $blog_product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_special SET blog_product_id = '" . (int)$blog_product_id . "', customer_group_id = '" . (int)$blog_product_special['customer_group_id'] . "', priority = '" . (int)$blog_product_special['priority'] . "', price = '" . (float)$blog_product_special['price'] . "', date_start = '" . $this->db->escape($blog_product_special['date_start']) . "', date_end = '" . $this->db->escape($blog_product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_image WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_image'])) {
			foreach ($data['blog_product_image'] as $blog_product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_image SET blog_product_id = '" . (int)$blog_product_id . "', image = '" . $this->db->escape($blog_product_image['image']) . "', sort_order = '" . (int)$blog_product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_download WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_download'])) {
			foreach ($data['blog_product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_download SET blog_product_id = '" . (int)$blog_product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_category WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_blog_category'])) {
			foreach ($data['blog_product_blog_category'] as $blog_category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_category SET blog_product_id = '" . (int)$blog_product_id . "', blog_category_id = '" . (int)$blog_category_id . "'");
			}
		}

		
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_filter WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_filter'])) {
			foreach ($data['blog_product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_filter SET blog_product_id = '" . (int)$blog_product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE related_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_related'])) {
			foreach ($data['blog_product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE blog_product_id = '" . (int)$blog_product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_related SET blog_product_id = '" . (int)$blog_product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE blog_product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$blog_product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_related SET blog_product_id = '" . (int)$related_id . "', related_id = '" . (int)$blog_product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_reward WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_reward'])) {
			foreach ($data['blog_product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_reward SET blog_product_id = '" . (int)$blog_product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}
		
		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'blog_product_id=" . (int)$blog_product_id . "'");
		
		if (isset($data['blog_product_seo_url'])) {
			foreach ($data['blog_product_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'blog_product_id=" . (int)$blog_product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_layout WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		if (isset($data['blog_product_layout'])) {
			foreach ($data['blog_product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "blog_product_to_layout SET blog_product_id = '" . (int)$blog_product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
		
		$this->cache->delete('blog_product');
	}

	public function copyProduct($blog_product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "blog_product p WHERE p.blog_product_id = '" . (int)$blog_product_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';

			$data['blog_product_attribute'] = $this->getProductAttributes($blog_product_id);
			$data['blog_product_description'] = $this->getProductDescriptions($blog_product_id);
			$data['blog_product_discount'] = $this->getProductDiscounts($blog_product_id);
			$data['blog_product_filter'] = $this->getProductFilters($blog_product_id);
			$data['blog_product_image'] = $this->getProductImages($blog_product_id);
			$data['blog_product_option'] = $this->getProductOptions($blog_product_id);
			$data['blog_product_related'] = $this->getProductRelated($blog_product_id);
			$data['blog_product_reward'] = $this->getProductRewards($blog_product_id);
			$data['blog_product_special'] = $this->getProductSpecials($blog_product_id);
			$data['blog_product_blog_category'] = $this->getProductCategories($blog_product_id);
			$data['blog_product_download'] = $this->getProductDownloads($blog_product_id);
			$data['blog_product_layout'] = $this->getProductLayouts($blog_product_id);
			$data['blog_product_store'] = $this->getProductStores($blog_product_id);
			$data['blog_product_recurrings'] = $this->getRecurrings($blog_product_id);

			$this->addProduct($data);
		}
	}

	public function deleteProduct($blog_product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_attribute WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_description WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_discount WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_filter WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_image WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_option WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_option_value WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_related WHERE related_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_reward WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_special WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_category WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_download WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_layout WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_to_store WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_product_recurring WHERE blog_product_id = " . (int)$blog_product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE blog_product_id = '" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'blog_product_id=" . (int)$blog_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_blog_product WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		$this->cache->delete('blog_product');
	}

	public function getProduct($blog_product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "blog_product p LEFT JOIN " . DB_PREFIX . "blog_product_description pd ON (p.blog_product_id = pd.blog_product_id) WHERE p.blog_product_id = '" . (int)$blog_product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProducts($data = array()) {
		$sql = "SELECT *, pd.name AS blog_product_name, p.blog_product_id AS blog_product_id, p.sort_order AS sort_order, p.* FROM " . DB_PREFIX . "blog_product p
				LEFT JOIN " . DB_PREFIX . "blog_product_description pd ON (p.blog_product_id = pd.blog_product_id)
				LEFT JOIN " . DB_PREFIX . "manufacturer md ON (p.manufacturer_id = md.manufacturer_id)
				LEFT JOIN " . DB_PREFIX . "blog_product_to_category p2c ON (p.blog_product_id = p2c.blog_product_id)
				LEFT JOIN " . DB_PREFIX . "blog_category_description cd ON (cd.blog_category_id = p2c.blog_category_id AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "')
				WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '%" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_sku']) && $data['filter_sku']) {
			$sql .= " AND p.sku LIKE '%" . $this->db->escape($data['filter_sku']) . "%'";
		}

		if (isset($data['filter_manufacturer']) && $data['filter_manufacturer']) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer'] . "'";
		}

		if (isset($data['filter_blog_category']) && $data['filter_blog_category']) {
			$sql .= " AND p2c.blog_category_id = '" . (int)$data['filter_blog_category'] . "'";
		}

		$sql .= " GROUP BY p.blog_product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.sku',
			'md.manufacturer',
			'cd.blog_category',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
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
//die($sql);
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsByCategoryId($blog_category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product p LEFT JOIN " . DB_PREFIX . "blog_product_description pd ON (p.blog_product_id = pd.blog_product_id) LEFT JOIN " . DB_PREFIX . "blog_product_to_category p2c ON (p.blog_product_id = p2c.blog_product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.blog_category_id = '" . (int)$blog_category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($blog_product_id) {
		$blog_product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_description WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $blog_product_description_data;
	}

	public function getProductCategories($blog_product_id) {
		$blog_product_blog_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_to_category WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_blog_category_data[] = $result['blog_category_id'];
		}

		return $blog_product_blog_category_data;
	}


	public function getProductFilters($blog_product_id) {
		$blog_product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_filter WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_filter_data[] = $result['filter_id'];
		}

		return $blog_product_filter_data;
	}

	public function getProductAttributes($blog_product_id) {
		$blog_product_attribute_data = array();

		$blog_product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "blog_product_attribute WHERE blog_product_id = '" . (int)$blog_product_id . "' GROUP BY attribute_id");

		foreach ($blog_product_attribute_query->rows as $blog_product_attribute) {
			$blog_product_attribute_description_data = array();

			$blog_product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_attribute WHERE blog_product_id = '" . (int)$blog_product_id . "' AND attribute_id = '" . (int)$blog_product_attribute['attribute_id'] . "'");

			foreach ($blog_product_attribute_description_query->rows as $blog_product_attribute_description) {
				$blog_product_attribute_description_data[$blog_product_attribute_description['language_id']] = array('text' => $blog_product_attribute_description['text']);
			}

			$blog_product_attribute_data[] = array(
				'attribute_id'                  => $blog_product_attribute['attribute_id'],
				'blog_product_attribute_description' => $blog_product_attribute_description_data
			);
		}

		return $blog_product_attribute_data;
	}

	public function getProductOptions($blog_product_id) {
		$blog_product_option_data = array();

		$blog_product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "blog_product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.blog_product_id = '" . (int)$blog_product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order ASC");

		foreach ($blog_product_option_query->rows as $blog_product_option) {
			$blog_product_option_value_data = array();

			$blog_product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.blog_product_option_id = '" . (int)$blog_product_option['blog_product_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($blog_product_option_value_query->rows as $blog_product_option_value) {
				$blog_product_option_value_data[] = array(
					'blog_product_option_value_id' => $blog_product_option_value['blog_product_option_value_id'],
					'option_value_id'         => $blog_product_option_value['option_value_id'],
					'quantity'                => $blog_product_option_value['quantity'],
					'subtract'                => $blog_product_option_value['subtract'],
					'price'                   => $blog_product_option_value['price'],
					'price_prefix'            => $blog_product_option_value['price_prefix'],
					'points'                  => $blog_product_option_value['points'],
					'points_prefix'           => $blog_product_option_value['points_prefix'],
					'weight'                  => $blog_product_option_value['weight'],
					'weight_prefix'           => $blog_product_option_value['weight_prefix']
				);
			}

			$blog_product_option_data[] = array(
				'blog_product_option_id'    => $blog_product_option['blog_product_option_id'],
				'blog_product_option_value' => $blog_product_option_value_data,
				'option_id'            => $blog_product_option['option_id'],
				'name'                 => $blog_product_option['name'],
				'type'                 => $blog_product_option['type'],
				'value'                => $blog_product_option['value'],
				'required'             => $blog_product_option['required']
			);
		}

		return $blog_product_option_data;
	}

	public function getProductOptionValue($blog_product_id, $blog_product_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "blog_product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.blog_product_id = '" . (int)$blog_product_id . "' AND pov.blog_product_option_value_id = '" . (int)$blog_product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProductImages($blog_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_image WHERE blog_product_id = '" . (int)$blog_product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductDiscounts($blog_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_discount WHERE blog_product_id = '" . (int)$blog_product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($blog_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_special WHERE blog_product_id = '" . (int)$blog_product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($blog_product_id) {
		$blog_product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_reward WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $blog_product_reward_data;
	}

	public function getProductDownloads($blog_product_id) {
		$blog_product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_to_download WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_download_data[] = $result['download_id'];
		}

		return $blog_product_download_data;
	}

	public function getProductStores($blog_product_id) {
		$blog_product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_to_store WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_store_data[] = $result['store_id'];
		}

		return $blog_product_store_data;
	}
	
	public function getProductSeoUrls($blog_product_id) {
		$blog_product_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'blog_product_id=" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $blog_product_seo_url_data;
	}
	
	public function getProductLayouts($blog_product_id) {
		$blog_product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_to_layout WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $blog_product_layout_data;
	}

	public function getProductRelated($blog_product_id) {
		$blog_product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_related WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		foreach ($query->rows as $result) {
			$blog_product_related_data[] = $result['related_id'];
		}

		return $blog_product_related_data;
	}

	public function getRecurrings($blog_product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "blog_product_recurring` WHERE blog_product_id = '" . (int)$blog_product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.blog_product_id) AS total
						FROM " . DB_PREFIX . "blog_product p
						LEFT JOIN " . DB_PREFIX . "blog_product_description pd ON (p.blog_product_id = pd.blog_product_id)
						LEFT JOIN " . DB_PREFIX . "blog_product_to_category p2c ON (p.blog_product_id = p2c.blog_product_id)
						";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}
		
	
		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '%" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_sku']) && $data['filter_sku']) {
			$sql .= " AND p.sku LIKE '%" . $this->db->escape($data['filter_sku']) . "%'";
		}

		if (isset($data['filter_manufacturer']) && $data['filter_manufacturer']) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer'] . "'";
		}

		if (isset($data['filter_blog_category']) && $data['filter_blog_category']) {
			$sql .= " AND p2c.blog_category_id = '" . (int)$data['filter_blog_category'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog_product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}


