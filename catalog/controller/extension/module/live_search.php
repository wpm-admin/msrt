<?php
class ControllerExtensionModuleLiveSearch extends Controller {
	
	public function index() {
		
      // Инициализируем счетчик запросов в сессии
        if (!isset($this->session->data['autocomplete'])) {
            $this->session->data['autocomplete'] = 0;
			sleep(1);
        }		
		
		$this->session->data['autocomplete']++;
		
		//Если часто долбит - пауза
		if($this->session->data['autocomplete'] > 15){
			$this->session->data['autocomplete'] = 0;
			sleep(3);
		}
		
		
		$json = array();
		if (isset($this->request->get['filter_name'])) {
			$search = $this->request->get['filter_name'];
			
			//Если слишком большой запрос
			if(strlen($search) > 20){
				sleep(5);
			}
			
		} else {
			$search = '';
		}
		if (isset($this->request->get['cat_id'])) {
			$cat_id = (int)$this->request->get['cat_id'];
		} else {
			$cat_id = 0;
		}

		$tag           = $search;
		$description   = '';
		$category_id   = $cat_id;
		$sub_category  = '';
		$sort          = 'p.sort_order';
		$order         = 'ASC';
		$page          = 1;
		$limit         = $this->config->get('live_search_limit');
		$search_result = 0;
		$error         = false;
		
		$currency_code = $this->session->data['currency'];
		
		if(!$error){
			if (isset($this->request->get['filter_name'])) {
				$this->load->model('catalog/product');
				$this->load->model('tool/image');
				$filter_data = array(
					'filter_name'         => $search,
					'filter_tag'          => $tag,
					'filter_description'  => $description,
					'filter_category_id'  => $category_id,
					'filter_sub_category' => $sub_category,
					'filter_image' => true,
					'sort' => 'pd.name',
					'order' => 'ASC',
					'start'               => 0,
					'limit'               => $limit
				);
				
				$key = md5(json_encode($filter_data)).'_'.$this->session->data['currency'].'_'.$this->config->get('config_language_id');
			
				$cache = false;//$this->cache->get($key);
				if(!$cache){
					$results = $this->getProducts($filter_data);
					$search_result = count($results); //$this->model_catalog_product->getTotalProducts($filter_data);
					$image_width        = $this->config->get('live_search_image_width');
					$image_height       = $this->config->get('live_search_image_height');
					$title_length       = $this->config->get('live_search_title_length');
					$description_length = $this->config->get('live_search_description_length');
	
					foreach ($results as $result) {
						if ($result['image']) {
							$image = $this->model_tool_image->resize($result['image'], $image_width, $image_height);
						} else {
							$image = $this->model_tool_image->resize('placeholder.png', $image_width, $image_height);
						}
	
						if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($result['price'], (isset($result['tax_class_id']) ? $result['tax_class_id'] : 0), $this->config->get('config_tax')), $currency_code);
						} else {
							$price = false;
						}
		
						$name = strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
						$leng = utf8_strlen($name);
						
						if($leng > $title_length){
							$name = utf8_substr($name, 0, $title_length) . '..';
						}
						
						$json['total'] = (int)$search_result;
						$json['products'][] = array(
							'product_id'  => $result['product_id'],
							'model'  => $result['model'],
							'image'       => $image,
							'name' => $name,
							//'extra_info' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $description_length) . '..',
							'price'       => $price,
							//'special'     => $special,
							'url'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
						);
					}
					
					$this->cache->set($key, $json);
				}else{
					$json = $cache;
				}
					
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getProducts($data = array()) {

		//$customer_group_id = $this->getCustomerGroup();
			
		$sql = "SELECT p.product_id, p.quantity, p.image, p.price, pd.name, p.model ";
		$sql .= " FROM " . DB_PREFIX . "product p";

		//$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_mark p2model ON (p.product_id = p2model.product_id) ";
		
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) ";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
		$sql .= "     AND p.status = '1' ";
		$sql .= "     AND p.diagram = '0'";

		
		if(isset($data['filter_mark_id']) AND $data['filter_mark_id']){
			$sql .= " AND p2model.mark_id = '". (int)$data['filter_mark_id'] ."' ";
		}elseif(isset($is_club) AND !$is_club AND (isset($data['filter_model']) AND $data['filter_model'])){
			if(isset($this->session->data['model_id'])){
				$data['model_id'] = (int)$this->session->data['model_id'];
			}
			if(isset($data['model_id']) AND $data['model_id'] > 0){
				$sql .= " AND p2model.mark_id = '". (int)$data['model_id'] ."' ";
			}
		}		
	
			
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
			
			$sql .= " OR LOWER(p.sku) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
			$sql .= " OR LOWER(p.upc) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
			$sql .= " OR LOWER(p.ean) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
			$sql .= " OR LOWER(p.jan) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
			$sql .= " OR LOWER(p.isbn) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
			$sql .= " OR LOWER(p.mpn) LIKE '%" . $this->db->escape(implode('%', $model)) . "%'";// = implode('%', $model);
		}

		$sql .= ")";

		if (isset($data['customer_id'])) {
			$sql .= " AND p.customer_id = '".(int)$data['customer_id']."'";
		}
	
	
	
		$sql .= " GROUP BY p.product_id";

		$sql .= " ORDER BY (CASE WHEN p.image <> '' THEN 0 ELSE 1 END) ASC, (CASE WHEN p.price > 0 THEN 0 ELSE 1 END) ASC ";

		$sql .= " LIMIT 20 ";

		$product_data = array();


		$query = $this->db->query($sql);

//die($sql);
		return $query->rows;
	}
	
	
	// Extensions Events
	public function injectLiveSearch(&$route, &$data, &$output) {
		if($this->config->get('live_search_ajax_status')){
			$this->load->language('product/search');
        	$text_view_all_results = $this->config->get('live_search_view_all_results');

        	$liveSearch = [
				'text_view_all_results'               => htmlspecialchars($text_view_all_results[$this->config->get('config_language_id')]['name']),
				'text_empty'                          => $this->language->get('text_empty'),
				'module_live_search_show_image'       => $this->config->get('live_search_show_image'),
				'module_live_search_show_price'       => $this->config->get('live_search_show_price'),
				'module_live_search_show_description' => $this->config->get('live_search_show_description'),
				'module_live_search_min_length'       => $this->config->get('live_search_min_length'),
				'module_live_search_show_add_button'  => $this->config->get('live_search_show_add_button'),
        	];
            // $this->document->addStyle('catalog/view/javascript/live_search/live_search.css');
            // $this->document->addScript('catalog/view/javascript/live_search/live_search.js');

            $liveSearchJS = '<link href="catalog/view/javascript/live_search/live_search.css" rel="stylesheet" type="text/css">'."\n";
            $liveSearchJS .= '<script src="catalog/view/javascript/live_search/live_search.js" type="text/javascript"></script>'."\n";
            $liveSearchJS .= '<script type="text/javascript"><!--'."\n";
			$liveSearchJS .= '$(document).ready(function() {'."\n";
			$liveSearchJS .= 'var options = '.json_encode($liveSearch).';'."\n";
			$liveSearchJS .= 'LiveSearchJs.init(options); '."\n";
			$liveSearchJS .= '});'."\n";
			$liveSearchJS .= '//--></script>'."\n";
			$liveSearchJS .= '</head>'."\n";

			$output = str_replace('</head>', $liveSearchJS, $output);
		}
	}
}
