<?php
class ModelCatalogProduct extends Model {
	public function getConsignment($consignment_id) {
		$query = $this->db->query("SELECT * FROM  " . DB_PREFIX . "consignment WHERE consignment_id = '" . (int)$consignment_id . "'");
		
		return $query->row;
	}
	
	public function getConsigments($category_id, $page = 1, $filter = false) {
	
		$sql_mark = '';
		
		if(isset($this->session->data['model_id']) AND (int)$this->session->data['model_id'] > 0){
			$sql_mark .= " AND mark_id = " . (int)$this->session->data['model_id'];
		}elseif(isset($this->session->data['mark_id']) AND (int)$this->session->data['mark_id'] > 0){
			$sql_mark = " AND mark_id IN (SELECT mark_id FROM " . DB_PREFIX . "mark WHERE parent_id = " . (int)$this->session->data['mark_id'] . ")";
		}
		
		if($filter){
			$sql_mark .= " AND category_id IN (" . implode(',', $category_id) . ") ";
		}elseif($category_id != CONSIGNMENT_CATEGORY_ID){
			$sql_mark .= " AND category_id = '" . (int)$category_id . "' ";
		}
			
		$sql = "SELECT * FROM  " . DB_PREFIX . "consignment WHERE status = 3 ".$sql_mark." ORDER BY date_added DESC LIMIT " . (($page - 1) * 20) . ", 20";
		//echo $sql;
			
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function getTotalConsigments($category_id, $filter = false) {
		
		$sql_mark = '';
		
		if(isset($this->session->data['model_id']) AND (int)$this->session->data['model_id'] > 0){
			$sql_mark .= " AND mark_id = " . (int)$this->session->data['model_id'];
		}elseif(isset($this->session->data['mark_id']) AND (int)$this->session->data['mark_id'] > 0){
			$sql_mark .= " AND mark_id IN (SELECT mark_id FROM " . DB_PREFIX . "mark WHERE parent_id = " . (int)$this->session->data['mark_id'] . ")";
		}
		
		if($filter){
			$sql_mark .= " AND category_id IN (" . implode(',', $category_id) . ") ";
		}elseif($category_id != CONSIGNMENT_CATEGORY_ID){
			$sql_mark .= " AND category_id = '" . (int)$category_id . "' ";
		}
				
		
		$sql = "SELECT COUNT(DISTINCT consignment_id) AS total FROM  " . DB_PREFIX . "consignment WHERE status = 3" . $sql_mark;
		
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}

	
	public function updateViewed($product_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int)$product_id . "'");
	}
	
	public function getProductCoordinate($product_id) {
		$query = $this->db->query("SELECT pc.*, pd.name, p.* FROM " . DB_PREFIX . "product_coordinate pc
								  LEFT JOIN " . DB_PREFIX . "product p ON (pc.list_product_id = p.product_id)
								  LEFT JOIN " . DB_PREFIX . "product_description pd ON (pc.list_product_id = pd.product_id)
								  WHERE pc.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
								  ORDER BY pc.num");
		
		$rows = $query->rows;
		
		foreach($rows as $index => $row){
			$special = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = ".(int)$row['product_id']." AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1");
		
			$rows[$index]['special'] = false;

			if ($special->num_rows) {
				if($special->row['price_prefix'] == '='){
					$rows[$index]['special'] = $special->row['price'];
				}elseif($special->row['price_prefix'] == '+'){
					$rows[$index]['special'] =(float)$row['price'] + $special->row['price'];
				}elseif($special->row['price_prefix'] == '-'){
					$rows[$index]['special'] = (float)$row['price'] - $special->row['price'];
				}elseif($special->row['price_prefix'] == '%'){
					$rows[$index]['special'] = (float)($row['price'] / 100 * (100 - $special->row['price']));
				}
			}
		
		}
		
		return $rows;
		
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductSeoUrls($product_id) {
		$product_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $product_seo_url_data;
	}
	
	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}
	
	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}
	
	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}
	
	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}
	
	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => htmlspecialchars($result['name'], ENT_QUOTES),
				'description'      => $result['description'],
				'meta_title'       => htmlspecialchars($result['meta_title'], ENT_QUOTES),
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => htmlspecialchars($result['tag'], ENT_QUOTES)
			);
		}

		return $product_description_data;
	}

	public function getProductMarks($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_mark WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['mark_id'];
		}

		return $product_category_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}



	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if((int)$query->num_rows == 0) return false;
		if(!isset($query->row['name'])) return false;
		
		$query->row['name'] = html_entity_decode($query->row['name'], ENT_QUOTES, 'UTF-8');
		$query->row['name'] = html_entity_decode($query->row['name'], ENT_QUOTES, 'UTF-8');
		$query->row['name'] = html_entity_decode($query->row['name'], ENT_QUOTES, 'UTF-8');
		$query->row['name'] = html_entity_decode($query->row['name'], ENT_QUOTES, 'UTF-8');
		$query->row['name'] = html_entity_decode($query->row['name'], ENT_QUOTES, 'UTF-8');
		
		$query->row['description'] = html_entity_decode($query->row['description'], ENT_QUOTES, 'UTF-8');
		$query->row['description'] = html_entity_decode($query->row['description'], ENT_QUOTES, 'UTF-8');
		$query->row['description'] = html_entity_decode($query->row['description'], ENT_QUOTES, 'UTF-8');
	
		if ($query->num_rows) {

			$discount = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = ".(int)$product_id." AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . 	"' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1");

			if ($discount->num_rows) {
				if($discount->row['price_prefix'] == '='){
					$query->row['discount'] = $discount->row['price'];
				}elseif($discount->row['price_prefix'] == '+'){
					$query->row['discount'] += $discount->row['price'];
				}elseif($discount->row['price_prefix'] == '-'){
					$query->row['discount'] -= $discount->row['price'];
				}elseif($discount->row['price_prefix'] == '%'){
					$query->row['discount'] = (float)($query->row['price'] / 100 * (100 - $discount->row['price']));
				}
			}

			$special = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = ".(int)$product_id." AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1");

			if ($special->num_rows) {
				if($special->row['price_prefix'] == '='){
					$query->row['special'] = $special->row['price'];
				}elseif($special->row['price_prefix'] == '+'){
					$query->row['special'] += $special->row['price'];
				}elseif($special->row['price_prefix'] == '-'){
					$query->row['special'] -= $special->row['price'];
				}elseif($special->row['price_prefix'] == '%'){
					$query->row['special'] = (float)($query->row['price'] / 100 * (100 - $special->row['price']));
				}
			}

			
			if(isset($this->session->data['show_as_diagram']) AND $this->session->data['show_as_diagram']){
				$query->row['image'] = $query->row['image'];
			}elseif($query->row['image2']){
				$query->row['image'] = $query->row['image2'];
			}
			
			if($query->row['image'] == '' AND $query->row['image2'] != ''){
				$query->row['image'] = $query->row['image2'];
			}
			
			//echo '<br>'."\n".$query->row['product_id'];
			
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => htmlentities($query->row['name']),
				'product_name'             => $query->row['name'],
				'diagram'          => $query->row['diagram'],
				'year_manuf'          => $query->row['year_manuf'],
				'condition_status_id'          => $query->row['condition_status_id'],
				'description'      => $query->row['description'],
				'product_description'      => $query->row['description'],
				'mark'       => $query->row['mark'],
				//'uom'       => $query->row['upc'],
				'uom'       => $query->row['uom'],
				'modeli'       => $query->row['modeli'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status_id'     => $query->row['stock_status_id'],
				'stock_status'     => $query->row['stock_status'],	
				
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ((isset($query->row['discount']) AND $query->row['discount']) ? $query->row['discount'] : $query->row['price']),
				'special'          => (isset($query->row['special']) ? $query->row['special'] : false),
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'date_off'   => $query->row['date_off'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}

	public function getNeiboProducts($product_id, $category_id=false, $mark_id, $is_diagram = false) {
		$sql = "SELECT p.*, pd.name AS name
					FROM " . DB_PREFIX . "product p
					LEFT JOIN " . DB_PREFIX . "product_to_mark p2model ON (p.product_id = p2model.product_id)
					LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
					LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
					WHERE p.status='1' AND p2model.mark_id = '".(int)$mark_id."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";//AND p2c.category_id = '". (int)$category_id ."' ";
		
		if($is_diagram){
			$sql .= " AND p.diagram='1'";
		}
		
		
		if(isset($this->session->data['show_as_diagram']) AND $this->session->data['show_as_diagram'] == 1){
			$sql .= " AND (p.image2 = '' OR p.image2 IS NULL) ";
		}else{
			$sql .= " AND (p.image2 <> '' AND p.image2 IS NOT NULL) ";
		}		
		
		if($category_id){
			$sql .= " AND p2c.category_id = '". (int)$category_id ."'";
		}
		
		$sql .= " GROUP BY p.product_id ORDER BY p.sort_order ASC, p.product_id";
		
		//die($sql);
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function getProductMark($product_id) {
		$sql = "SELECT *
					FROM " . DB_PREFIX . "product_to_mark p2model
					LEFT JOIN " . DB_PREFIX . "mark m ON (p2model.mark_id = m.mark_id)
					LEFT JOIN " . DB_PREFIX . "mark_description pd ON (p2model.mark_id = pd.mark_id)
					WHERE m.status='1' AND p2model.product_id = '".(int)$product_id."' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";//AND p2c.category_id = '". (int)$category_id ."' ";
	
		//$sql .= " GROUP BY p.product_id ORDER BY p.sort_order ASC, p.product_id";
		
		//die($sql);
		
		$query = $this->db->query($sql);
		
		return $query->row;
	}
	
	public function getAuctionProducts($limit) {

		if(isset($this->request->get['mark_id']) AND $this->request->get['mark_id']){
			$marks = array();
			
			$mark_id = (int)$this->request->get['mark_id'];
			
			
			$query = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "mark WHERE mark_id = '".(int)$mark_id."' LIMIT 1");
			
			if((int)$query->row['parent_id'] > 0){
				$marks[] = (int)$query->row['parent_id'];
			}
			
			if((int)$query->row['parent_id'] > 0){
				$sql = "SELECT mark_id FROM " . DB_PREFIX . "mark WHERE parent_id = '". (int)$query->row['parent_id'] ."'";
			}else{
				$sql = "SELECT mark_id FROM " . DB_PREFIX . "mark WHERE parent_id = '". (int)$mark_id ."'";
			}
		
			$query = $this->db->query($sql);
			
			
			foreach($query->rows as $row){
				$marks[] = $row['mark_id'];
			}
			
			$marks[] = $mark_id;
			
			if(count($marks) > 0){
				$sql = "SELECT * FROM " . DB_PREFIX . "auction a
					LEFT JOIN " . DB_PREFIX . "product_to_mark p2m ON (p2m.product_id = a.product_id)
					WHERE a.status='1' AND a.date_start <= NOW() AND a.date_end >= NOW() AND a.product_id > 0 AND p2m.mark_id IN (" . implode(',',$marks) . ")
					ORDER BY a.auction_id DESC LIMIT " . $limit;
			}else{
				$sql = "SELECT * FROM " . DB_PREFIX . "auction a
					LEFT JOIN " . DB_PREFIX . "product_to_mark p2m ON (p2m.product_id = a.product_id)
					WHERE a.status='1' AND a.date_start <= NOW() AND a.date_end >= NOW() AND a.product_id > 0 AND p2m.mark_id = '" . (int)$mark_id . "'
					ORDER BY a.auction_id DESC LIMIT " . $limit;
			}
			
		}else{
		
			$sql = "SELECT * FROM " . DB_PREFIX . "auction
					WHERE status='1' AND date_start <= NOW() AND date_end >= NOW() AND product_id > 0
					ORDER BY auction_id DESC LIMIT " . $limit;
		}		
		$product_data = array();

	$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			$product_data[$result['product_id']]['auction'] = $result;
		}
//die($sql);
		return $product_data;
	}
	
	public function getProductDiagrams($product_id){
		$sql = "SELECT p.product_id
					FROM " . DB_PREFIX . "product_coordinate p
					WHERE p.list_product_id = '".(int)$product_id."'";
		
		$sql .= " GROUP BY p.product_id";
		
		$query = $this->db->query($sql);
		
		$product_data = array();
		
		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}
	
	public function getProducts($data = array()) {
		$sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

		if(!isset($data['filter_mark_id'])) $data['filter_mark_id'] = false;
		
		if (!empty($data['filter_mark_id'])) {
			$sql .= " FROM " . DB_PREFIX . "mark_path cp LEFT JOIN " . DB_PREFIX . "product_to_mark p2c ON (cp.mark_id = p2c.mark_id)";
		
			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		}elseif (!empty($data['filter_category_id']) OR !empty($data['filter_mark_id'])) {
			if (!empty($data['filter_mark_id'])) {
				$sql .= " FROM " . DB_PREFIX . "mark_path cp LEFT JOIN " . DB_PREFIX . "mark_to_category p2c ON (cp.mark_id = p2c.mark_id)";
			}elseif (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_mark p2model ON (p.product_id = p2model.product_id) ";
		
		if(isset($data['filter_diagram_id']) AND $data['filter_diagram_id']){
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_coordinate coord ON (p.product_id = coord.list_product_id) ";
		}
		
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND (p.date_off = '0000-00-00' OR p.date_off <= NOW())";

		
		//Проверка или категория не клубная
		$is_club = false;
		if (!empty($data['filter_category_id']) ){
			$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path cp
										LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('category_id=', cp.path_id)
										WHERE cp.category_id='" . (int)$data['filter_category_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
			if($r->num_rows){
				foreach($r->rows as $row){
					if((int)$row['path_id'] == CLUB_CATEGORY_ID){ // Клубная категория
						$is_club = true;
					}
				}
			}
		}
		
		if(isset($data['filter_diagram_id']) AND $data['filter_diagram_id']){
		
			$sql .= " AND coord.product_id = '". (int)$data['filter_diagram_id'] ."' ";
		
		}
		
		//Проверяем картинки только если это не поиск
		if (empty($data['filter_name'])){
			if(isset($data['filter_second_photos']) AND $data['filter_second_photos']){
				$sql .= " AND (p.image2 <> '' AND p.image2 IS NOT NULL) ";
			}elseif(isset($data['filter_image']) AND $data['filter_image']){ //Выводим товары только с картинками
				$sql .= " AND (p.image <> '' AND p.image IS NOT NULL) ";
			}else{
				$sql .= " AND (p.image2 = '' OR p.image2 IS NULL) ";
			}
		}
	
		if($data['filter_mark_id']){
		
			//$sql .= " AND p2model.mark_id = '". (int)$data['filter_mark_id'] ."' ";
		
		}elseif(!$is_club AND (isset($data['filter_model']) AND $data['filter_model'])){
			if(isset($this->session->data['model_id'])){
				$data['model_id'] = (int)$this->session->data['model_id'];
			}
		
			if(isset($data['model_id']) AND $data['model_id'] > 0){
				$sql .= " AND p2model.mark_id = '". (int)$data['model_id'] ."' ";
			}
		}			
			
				
		if (!empty($data['filter_category_id']) OR !empty($data['filter_mark_id'])) {
			if (!empty($data['filter_mark_id'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_mark_id'] . "'";
			}elseif (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();
				$implode2 = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					
					$word = utf8_strtolower($word);
					
					$implode[] = "LOWER(pd.name_search) LIKE '%" . $this->db->escape($word) . "%'";
					
					$model = str_split(utf8_strtolower($word));
					$implode2[] = "LOWER(p.model) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
				
				}

				if ($implode) {
					$sql .= " (" . implode(" AND ", $implode) . ")";
					$sql .= " OR (" . implode(" AND ", $implode2) . ")";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$model = str_split(utf8_strtolower($data['filter_name']));
				
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";
				$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.upc) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.ean) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.jan) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.isbn) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.mpn) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		if (isset($data['filter_diagram'])) {
			if((int)$data['filter_diagram'] == 2){
				$sql .= " AND p.diagram = '0'";
			}elseif($data['filter_diagram'] == true){
				$sql .= " AND p.diagram = '1'";
			}else{
				//$sql .= " AND p.diagram = '1'";	
			}
		}
	
		if (isset($data['customer_id'])) {
			$sql .= " AND p.customer_id = '".(int)$data['customer_id']."'";
		}
	
	
	
		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.stock_status_id',	
			'p.quantity',
			'p.image',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);
		
		if (isset($data['sort']) AND strpos($data['sort'], 'p.stock_status_id=') !== false){
			
			$tmp = explode('=', $data['sort']);
			
			
			$stock_status_id = (int)$tmp[1];
			
			$sql .= " ORDER BY (CASE WHEN p.stock_status_id = '".$stock_status_id."' THEN 0 ELSE 1 END) ";
			unset($data['sort']);
		}

		

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.image') {
				$sql .= " ORDER BY (CASE WHEN p.image <> '' THEN 0 ELSE 1 END)";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			//$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}
		
		
		//Всуним сорт по ценам
		if(isset($data['filter_price']) AND $data['filter_price']){
			
			if(strpos($sql, 'ORDER BY') !== false){
				$replace = " ORDER BY (CASE WHEN p.price > 0 THEN 0 ELSE 1 END) ASC, ";
				$sql = str_replace('ORDER BY', $replace, $sql);
			}else{
				$sql = $sql . " ORDER BY (CASE WHEN p.price > 0 THEN 0 ELSE 1 END) ASC ";
			}
			
		}
		
		//Всуним сорт по картинкам
		if(isset($data['filter_image']) AND $data['filter_image']){
			
			if(strpos($sql, 'ORDER BY') !== false){
				$replace = " ORDER BY (CASE WHEN p.image <> '' THEN 0 ELSE 1 END) ASC, (CASE WHEN p.price > 0 THEN 0 ELSE 1 END) ASC,  ";
				$sql = str_replace('ORDER BY', $replace, $sql);
			}else{
				$sql = $sql . " ORDER BY (CASE WHEN p.image <> '' THEN 0 ELSE 1 END) ASC, (CASE WHEN p.price > 0 THEN 0 ELSE 1 END) ASC  ";
			}
			
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

		$product_data = array();
//die($sql);
//echo '<br>'.$sql.'<br>';

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getProductSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

		$sort_data = array(
			'pd.name',

			'p.stock_status_id',	
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);

		
		
 
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}
 

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getLatestProducts($limit) {
		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) {
		$product_data = $this->cache->get('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
	
		if (!$product_data) {
			$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			
			$this->cache->set('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}
		
		return $product_data;
	}

	public function getBestSellerProducts($limit) {
		$product_data = false;//$this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			$product_data = array();

			$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total
									  FROM " . DB_PREFIX . "order_product op
									  LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)
									  LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id)
									  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
									  WHERE o.order_status_id > '0' AND
									  p.status = '1' AND
									  p.image <> '' AND
									  p.date_available <= NOW() AND
									  (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND
									  p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
									  GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}
			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_group_data = array();

		$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);
		}

		return $product_attribute_group_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT pd.*, p.price AS origin_price
								  FROM " . DB_PREFIX . "product_discount pd
								  LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pd.product_id)
								  WHERE pd.product_id = '" . (int)$product_id . "' AND pd.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd.quantity > 1 AND ((pd.date_start = '0000-00-00' OR pd.date_start < NOW()) AND (pd.date_end = '0000-00-00' OR pd.date_end > NOW())) ORDER BY pd.quantity ASC, pd.priority ASC, pd.price ASC");

		$return = array();
		
		if ($query->num_rows) {
			
			foreach($query->rows as $row){
				
				if($row['price_prefix'] == '='){
			
				}elseif($row['price_prefix'] == '+'){
					$row['price'] = $row['origin_price'] + $row['price'];
				}elseif($row['price_prefix'] == '-'){
					$row['price'] = $row['origin_price'] - $row['price'];
				}elseif($row['price_prefix'] == '%'){
					$row['price'] = (int)($row['origin_price'] / 100 * (100 - $row['price']));
				}
				
				$return[] = $row;
				
			}
		
		}
		
		return $return;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductStatuses() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `name` ASC");

		$return = array();
		
		foreach($query->rows as $row){
			$return[(int)$row['stock_status_id']] = $row;
		}
		
		return $return;
	}

	public function getProductConditions() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "condition_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `name` ASC");

		$return = array();
		
		foreach($query->rows as $row){
			$return[(int)$row['condition_status_id']] = $row;
		}
		
		return $return;
	}

	public function getProductRelated($product_id) {
		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function getProductLayoutId($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getProductsDiagrams($products = array()) {
		$sql = "SELECT distinct product_id FROM " . DB_PREFIX . "product_coordinate WHERE list_product_id IN (".implode(',', $products).")";
		
		$query = $this->db->query($sql);
		
		$return = array();
		foreach($query->rows as $row){
			$return[] = $row['product_id'];
		}
		
		return $return;
	}
	
	public function getProductsCategorys($products = array()) {
		$sql = "SELECT distinct category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id IN (".implode(',', $products).")";
		
		$query = $this->db->query($sql);
		
		$return = array();
		foreach($query->rows as $row){
			$return[] = $row['category_id'];
		}
		
		return $return;
	}
	
	public function getProductsModels($products = array()) {
		$sql = "SELECT distinct mark_id FROM " . DB_PREFIX . "product_to_mark WHERE product_id IN (".implode(',', $products).")";
		
		$query = $this->db->query($sql);
		
		$return = array();
		foreach($query->rows as $row){
			$return[] = $row['mark_id'];
		}
		
		return $return;
	}
	public function getMarksOnModels($marks = array()) {
		
		if(count($marks) == 0) return false;
		
		$sql = "SELECT distinct parent_id FROM " . DB_PREFIX . "mark WHERE mark_id IN (".implode(',', $marks).") AND parent_id > 0";
		
		$query = $this->db->query($sql);
		
		$return = array();
		foreach($query->rows as $row){
			$return[] = $row['parent_id'];
		}
		
		return $return;
	}
	//Тут код сторінки
	public function getSearchProducts($data = array()) {
		
		if(!isset($data['filter_mark_id'])) $data['filter_mark_id'] = false;
		
		$sql = "SELECT p.product_id ";

		if (!empty($data['filter_category_id']) OR !empty($data['filter_mark_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			}elseif (!empty($data['filter_mark_id'])) {
				$sql .= " FROM " . DB_PREFIX . "mark_path cp LEFT JOIN " . DB_PREFIX . "product_to_mark p2c ON (cp.mark_id = p2c.mark_id) ";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_mark p2model ON (p.product_id = p2model.product_id) ";
		
		if(isset($data['filter_diagram_id']) AND $data['filter_diagram_id']){
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_coordinate coord ON (p.product_id = coord.list_product_id) ";
		}
		
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		
		//Проверка или категория не клубная
		$is_club = false;
		if (!empty($data['filter_category_id']) ){
			$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path cp
										LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('category_id=', cp.path_id)
										WHERE cp.category_id='" . (int)$data['filter_category_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
			if($r->num_rows){
				foreach($r->rows as $row){
					if((int)$row['path_id'] == CLUB_CATEGORY_ID){ // Клубная категория
						$is_club = true;
					}
				}
			}
		}
		
		if(isset($data['filter_diagram_id']) AND $data['filter_diagram_id']){
		
			$sql .= " AND coord.product_id = '". (int)$data['filter_diagram_id'] ."' ";
		
		}
				
		if($data['filter_mark_id']){
		
			//$sql .= " AND p2model.mark_id = '". (int)$data['filter_mark_id'] ."' ";
		
		}elseif(!$is_club AND (isset($data['filter_model']) AND $data['filter_model'])){
	
			if(isset($this->session->data['model_id'])){
				$data['model_id'] = (int)$this->session->data['model_id'];
			}
		
			if(isset($data['model_id']) AND $data['model_id'] > 0){
				$sql .= " AND p2model.mark_id = '". (int)$data['model_id'] ."' ";
			}
		}			
		

					
		if (!empty($data['filter_category_id']) OR !empty($data['filter_mark_id'])) {
			if (!empty($data['filter_mark_id'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_mark_id'] . "'";
			}elseif (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();
				$implode2 = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					
					$word = utf8_strtolower($word);
					
					$implode[] = "LOWER(pd.name_search) LIKE '%" . $this->db->escape($word) . "%'";
					
					$model = str_split(utf8_strtolower($word));
					$implode2[] = "LOWER(p.model) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
				
				}

				if ($implode) {
					$sql .= " (" . implode(" AND ", $implode) . ")";
					$sql .= " OR (" . implode(" AND ", $implode2) . ")";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$model = str_split(utf8_strtolower($data['filter_name']));
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";
				$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.upc) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.ean) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.jan) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.isbn) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.mpn) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			$sql .= ")";
		}

		
		if (isset($data['filter_diagram'])) {
			if((int)$data['filter_diagram'] == 2){
				$sql .= " AND p.diagram = '0'";
			}elseif($data['filter_diagram'] == true){
				$sql .= " AND p.diagram = '1'";
			}else{
				//$sql .= " AND p.diagram = '1'";	
			}
		}
	
		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
//die($sql);
		$query = $this->db->query($sql);

		$return = array();
		foreach($query->rows as $row){
			$return[] = $row['product_id'];
		}
		
		return $return;
	}
	public function getTotalProducts($data = array()) {
		
		if(!isset($data['filter_mark_id'])) $data['filter_mark_id'] = false;
		
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['filter_category_id']) OR !empty($data['filter_mark_id'])) {
			if (!empty($data['filter_mark_id'])) {
				$sql .= " FROM " . DB_PREFIX . "mark_path mp LEFT JOIN " . DB_PREFIX . "product_to_mark p2m ON (mp.mark_id = p2m.mark_id) ";
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2m.product_id = p.product_id)";
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)";
			}elseif (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp ";
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
//				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_mark p2model ON (p.product_id = p2model.product_id) ";
		
		if(isset($data['filter_diagram_id']) AND $data['filter_diagram_id']){
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_coordinate coord ON (p.product_id = coord.list_product_id) ";
		}
		
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		
		//Проверка или категория не клубная
		$is_club = false;
		if (!empty($data['filter_category_id']) ){
			$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path cp
										LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('category_id=', cp.path_id)
										WHERE cp.category_id='" . (int)$data['filter_category_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
			if($r->num_rows){
				foreach($r->rows as $row){
					if((int)$row['path_id'] == CLUB_CATEGORY_ID){ // Клубная категория
						$is_club = true;
					}
				}
			}
		}
		
		
		if(isset($data['filter_diagram_id']) AND $data['filter_diagram_id']){
		
			$sql .= " AND coord.product_id = '". (int)$data['filter_diagram_id'] ."' ";
		
		}
				
		if($data['filter_mark_id']){
	
			//$sql .= " AND p2model.mark_id = '". (int)$data['filter_mark_id'] ."' ";
	
		}elseif(!$is_club AND (isset($data['filter_model']) AND $data['filter_model'])){
	
			if(isset($this->session->data['model_id'])){
				$data['model_id'] = (int)$this->session->data['model_id'];
			}
		
			if(isset($data['model_id']) AND $data['model_id'] > 0){
				$sql .= " AND p2model.mark_id = '". (int)$data['model_id'] ."' ";
			}
		}			
		

					
		if (!empty($data['filter_category_id']) OR !empty($data['filter_mark_id'])) {
			if (!empty($data['filter_mark_id'])) {
				$sql .= " AND mp.path_id = '" . (int)$data['filter_mark_id'] . "'";
			}elseif (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			}
			
			if (!empty($data['filter_category_id'])){
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();
				$implode2 = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					
					$word = utf8_strtolower($word);
					
					$implode[] = "LOWER(pd.name_search) LIKE '%" . $this->db->escape($word) . "%'";
					
					$model = str_split(utf8_strtolower($word));
					$implode2[] = "LOWER(p.model) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
				
				}

				if ($implode) {
					$sql .= " (" . implode(" AND ", $implode) . ")";
					$sql .= " OR (" . implode(" AND ", $implode2) . ")";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$model = str_split(utf8_strtolower($data['filter_name']));
				$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";
				$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.upc) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.ean) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.jan) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.isbn) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				$sql .= " OR LCASE(p.mpn) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			$sql .= ")";
		}

		
		if (isset($data['filter_diagram'])) {
			if((int)$data['filter_diagram'] == 2){
				$sql .= " AND p.diagram = '0'";
			}elseif($data['filter_diagram'] == true){
				$sql .= " AND p.diagram = '1'";
			}else{
				//$sql .= " AND p.diagram = '1'";	
			}
		}
	
		if (isset($data['customer_id'])) {
			$sql .= " AND p.customer_id = '".(int)$data['customer_id']."'";
		}
	
		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
//die($sql);
//echo '<br><br>'.$sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProfile($product_id, $recurring_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r JOIN " . DB_PREFIX . "product_recurring pr ON (pr.recurring_id = r.recurring_id AND pr.product_id = '" . (int)$product_id . "') WHERE pr.recurring_id = '" . (int)$recurring_id . "' AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

		return $query->row;
	}

	public function getProfiles($product_id) {
		$query = $this->db->query("SELECT rd.* FROM " . DB_PREFIX . "product_recurring pr JOIN " . DB_PREFIX . "recurring_description rd ON (rd.language_id = " . (int)$this->config->get('config_language_id') . " AND rd.recurring_id = pr.recurring_id) JOIN " . DB_PREFIX . "recurring r ON r.recurring_id = rd.recurring_id WHERE pr.product_id = " . (int)$product_id . " AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getTotalProductSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND (p.date_off = '0000-00-00' OR p.date_off <= NOW()) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function checkProductCategory($product_id, $category_ids) {
		
		$implode = array();

		foreach ($category_ids as $category_id) {
			$implode[] = (int)$category_id;
		}
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id IN(" . implode(',', $implode) . ")");
  	    return $query->row;
	}
	
	public function addProduct($data) {
		
		$customer_id = (int)$this->customer->isLogged();
		
		if((int)$customer_id < 1){
			return false;
		}
		
		if(trim($data['model']) == ''){
			$data['model'] = md5(date('Y-m-d H:i:s'));
		}
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

		$product_id = $this->db->getLastId();

		$this->db->query("UPDATE " . DB_PREFIX . "product SET customer_id = '" . $customer_id . "' WHERE product_id = '" . (int)$product_id . "'");

		
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		if (isset($data['cost'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET cost = '" . (float)$data['cost'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		if (isset($data['year_manuf'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET year_manuf = '" . (float)$data['year_manuf'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		if (isset($data['diagram'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET diagram = '" . (int)$data['diagram'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		
		if (isset($data['condition_status_id'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET condition_status_id = '" . (int)$data['condition_status_id'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $recurring) {

				$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product_recurring` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id = '" . (int)$recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = '" . (int)$product_id . "', customer_group_id = '" . (int)$recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");
				}
			}
		}
		
		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_mark'])) {
			foreach ($data['product_mark'] as $mark_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_mark SET product_id = '" . (int)$product_id . "', mark_id = '" . (int)$mark_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}
		
		// SEO URL
		$languages = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
		
		foreach ($languages as $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '0', language_id = '" . (int)$language['language_id'] . "', query = 'product_id=" . (int)$product_id . "', keyword = 'market_" . (int)$product_id . "'");
		}
		
		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if(isset($data['product_coordinate'])){
			foreach($data['product_coordinate'] as $product_coordinate){
				
				if((int)$product_coordinate['list_product_id'] > 0){
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_coordinate SET
									 product_id = '" . (int)$product_id . "',
									 list_product_id = '" . (int)$product_coordinate['list_product_id'] . "',
									 num = '" . (int)$product_coordinate['num'] . "',
									 coordiname = '" . $this->db->escape($product_coordinate['coordiname']) . "'");
				}
			}
		}
	

		$this->cache->delete('product');

		return $product_id;
	}

	public function editProduct($product_id, $data) {
		
		$customer_id = (int)$this->customer->isLogged();
		
		if((int)$customer_id < 1){
			return false;
		}
	
		$update = false;
	
		$res = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if($res->num_rows){
			$row = $res->row;
			
			if((int)$row['customer_id'] == (int)$this->customer->isLogged() AND (int)$row['customer_id'] > 0){
				$update = true;
			}
		}
		
		if(!$update){
			return false;
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "product SET customer_id = '" . $customer_id . "' WHERE product_id = '" . (int)$product_id . "'");
	
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		
		if (isset($data['cost'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET cost = '" . (float)$data['cost'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		
		if (isset($data['year_manuf'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET year_manuf = '" . (float)$data['year_manuf'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		if (isset($data['diagram'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET diagram = '" . (int)$data['diagram'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		if (isset($data['condition_status_id'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET condition_status_id = '" . (int)$data['condition_status_id'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product_recurring` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = '" . (int)$product_recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$product_recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = '" . (int)$product_id . "', `customer_group_id` = '" . (int)$product_recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$product_recurring['recurring_id'] . "'");
				}				
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_mark WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_mark'])) {
			foreach ($data['product_mark'] as $mark_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_mark SET product_id = '" . (int)$product_id . "', mark_id = '" . (int)$mark_id . "'");
			}
		}
		
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}
		
		// SEO URL
		$languages = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
		
		foreach ($languages as $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '0', language_id = '" . (int)$language['language_id'] . "', query = 'product_id=" . (int)$product_id . "', keyword = 'market_" . (int)$product_id . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_coordinate WHERE product_id = '" . (int)$product_id . "'");
		if(isset($data['product_coordinate'])){
			foreach($data['product_coordinate'] as $product_coordinate){
				
				if((int)$product_coordinate['list_product_id'] > 0){
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_coordinate SET
									 product_id = '" . (int)$product_id . "',
									 list_product_id = '" . (int)$product_coordinate['list_product_id'] . "',
									 num = '" . (int)$product_coordinate['num'] . "',
									 coordiname = '" . $this->db->escape($product_coordinate['coordiname']) . "'");
				}
			}
		}
		
		
		$this->cache->delete('product');
	}

	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';

			$data['product_attribute'] = $this->getProductAttributes($product_id);
			$data['product_description'] = $this->getProductDescriptions($product_id);
			$data['product_discount'] = $this->getProductDiscounts($product_id);
			$data['product_filter'] = $this->getProductFilters($product_id);
			$data['product_image'] = $this->getProductImages($product_id);
			$data['product_option'] = $this->getProductOptions($product_id);
			$data['product_related'] = $this->getProductRelated($product_id);
			$data['product_reward'] = $this->getProductRewards($product_id);
			$data['product_special'] = $this->getProductSpecials($product_id);
			$data['product_category'] = $this->getProductCategories($product_id);
			$data['product_mark'] = $this->getProductMarks($product_id);
			$data['product_download'] = $this->getProductDownloads($product_id);
			$data['product_layout'] = $this->getProductLayouts($product_id);
			$data['product_store'] = $this->getProductStores($product_id);
			$data['product_recurrings'] = $this->getRecurrings($product_id);
			$data['product_coordinate'] = $this->getProductCoordinate($product_id);
			
			

			$this->addProduct($data);
		}
	}

	public function updateProduct($product_id, $data) {
		
		if(isset($data['change_status'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '".(int)$data['change_status']."' WHERE product_id = '" . (int)$product_id . "'");
		}
		if(isset($data['product_image'])){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '".$data['product_image']."' WHERE product_id = '" . (int)$product_id . "'");
		}

	}
	
	public function getAdminCategories($data = array()) {
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY cp.category_id";

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
		foreach( $query->rows as $category){
			$return[$category['category_id']] = $category;
		}
		
		return $return;
	}
	
	public function deleteProduct($product_id) {
		
		$delete = false;
		
		$res = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if($res->num_rows){
			$row = $res->row;
			
			if((int)$row['customer_id'] == (int)$this->customer->isLogged() AND (int)$row['customer_id'] > 0){
				$delete = true;
			}
		}
		
		if(!$delete){
			return false;
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_mark WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}

}
