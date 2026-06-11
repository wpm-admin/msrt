<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			//Если это список категорий для Обьявлений
			if(isset($parts[0]) and $parts[0] == 'classified' and isset($parts[1]) and strpos($parts[1], '=') !== false){
				
				$this->request->get['filter_categories'] = array();
				
				$filter = explode('=', $parts[1]);
				foreach($filter as $keyword){
					
					$sql = "SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($keyword) . "' AND query LIKE 'category_id=%' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1";
		
					$query = $this->db->query($sql);
					
					if($query->num_rows){
						
						$info = explode('=', $query->row['query']);
						
						$this->request->get['filter_categories'][] = (int)$info[1];
					}
				}
				
				unset($parts[1]);
			}
			
			foreach ($parts as $part) {
				
				$sql = "SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1";
				
				$query = $this->db->query($sql);

				
				//Старые УРЛ редирект со строго урл на новый
				if ($query->num_rows == 0) {
				
					$sql = "SELECT * FROM " . DB_PREFIX . "seo_url WHERE old_keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' LIMIT 1";
				
					$query = $this->db->query($sql);
				
					if ($query->num_rows) {
						$url = $this->url->link('product/product', $query->row['query']);
						
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: " . $url);
						exit();						
					}
					
				}
				
				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);
					
					//<!-- Blogi * * * Start -->
					if ($url[0] == 'blog_product_id') {
						$this->request->get['blog_product_id'] = $url[1];
					}
	
					if ($url[0] == 'blog_category_id') {
						if (!isset($this->request->get['blogpath'])) {
							$this->request->get['blogpath'] = $url[1];
						} else {
							$this->request->get['blogpath'] .= '_' . $url[1];
						}
					}
					//<!-- Blogi * * * End -->

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'mark_id') {
						$this->request->get['mark_id'] = $url[1];
						
						$mark_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark WHERE mark_id = '" . (int)$this->request->get['mark_id'] . "' LIMIT 1");

						if($mark_query->num_rows){
							$mark_info = $mark_query->row;
							
							
							if((int)$mark_info['parent_id'] > 0){
								$this->session->data['mark_id'] = (int)$mark_info['parent_id'];
								$this->session->data['model_id'] = (int)$this->request->get['mark_id'];
							}else{
								$this->session->data['mark_id'] = (int)$this->request->get['mark_id'];
								unset($this->session->data['model_id']);
								
								
								//Если выбрана марка - выбираем дефолтную модель. Она вторая в слайдере
								$this->load->model('catalog/mark');
								$model = $this->model_catalog_mark->getMarks($this->request->get['mark_id'], 1);
						
								if($model){
									$model = array_shift($model);
									$this->session->data['auto_model_id'] = (int)$model['mark_id'];
								}
							}
							
						}
						
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($query->row['query'] && $url[0] != 'mark_id' && $query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id') {
						$this->request->get['route'] = $query->row['query'];
					}
					
				} else {
					$this->request->get['route'] = 'error/not_found';

					break;
				}
			}

			
			//Марки и модели в том случае если нет у ЧПУ продолжения. Иначе только определение для сессии
			//<!-- Blogi * * * Start -->
			/*
			if (isset($url[0]) && ($url[0] != 'blog_product_id' || $url[0] != 'blog_category_id' ) &&
				strpos($this->request->get['route'], 'account') === false
				) {
				unset($this->request->get['route']);
			}
			*/
			//<!-- Blogi * * * End -->		
			if (isset($this->request->get['blog_product_id'])) {
				$this->request->get['route'] = 'product/blog_product';
			}elseif (isset($this->request->get['blogpath'])) {
				$this->request->get['route'] = 'product/blog_category';
			//<!-- Blogi * * * End -->
			}
	
			if (!isset($this->request->get['route']) OR $this->request->get['route'] == 'product/mark') {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				//<!-- Blogi * * * Start -->
				} elseif (isset($this->request->get['blog_product_id'])) {
					$this->request->get['route'] = 'product/blog_product';
				} elseif (isset($this->request->get['blogpath'])) {
					$this->request->get['route'] = 'product/blog_category';
				//<!-- Blogi * * * End -->
				} elseif (isset($this->request->get['search'])) {
					$this->request->get['route'] = 'product/search';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				}elseif (isset($this->request->get['mark_id'])) {
					$this->request->get['route'] = 'product/mark';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				}
			}
		}
		
		
		//Сделаем проверку УРЛ для товара
		if (isset($this->request->get['product_id'])) {
			$seo = $this->rewrite(HTTPS_SERVER.'index.php?route=product/product&product_id=' . (int)$this->request->get['product_id']);
			
			
			if(trim(trim(HTTPS_SERVER, '/').$_SERVER["REQUEST_URI"]) != trim($seo)){
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: " . $seo);
				exit();
			}
			
		}


	}

	public function rewrite($link) {
		
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();
		$no_path = true;
		$path = '';
	
		$lang = '';
		$this->load->model('localisation/language');
		$language = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
		if($language['url'] != ''){
			$lang = '/'.$language['url'];
		}
		
		
		$path_mark = '';
		if(isset($this->session->data['mark_id']) AND (int)$this->session->data['mark_id'] > 0){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'mark_id=" . (int)$this->session->data['mark_id'] . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
			
			if($query->num_rows){
				$path .= '/'.$query->row['keyword'];
				$path_mark .= '/'.$query->row['keyword'];
				$no_path = true;
			}
			
		}

		if(isset($this->session->data['model_id']) AND (int)$this->session->data['model_id'] > 0){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'mark_id=" . (int)$this->session->data['model_id'] . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
			
			if($query->num_rows){
				$path .= '/'.$query->row['keyword'];
				$no_path = true;
			}
			
		}

		
		parse_str($url_info['query'], $data);
		
		$categ_path = '';
		
		$mark_from_link = false;
		
		$path_array = array();
		if (isset($data['path'])){
			$path_array = explode('_', $data['path']);
		}
		if(in_array(CONSIGNMENT_CATEGORY_ID, $path_array) ){
			$mark_from_link = true;
		}
		
		
		foreach ($data as $key => $value) {
			
			//echo '<br>'.$value;
			
			if (isset($data['route'])) {
				
				
				if($key == 'mark_id' OR $key == 'product_id' OR $key == 'path'){
					
					//Отдельно найдем путь
					$path = '';
				

					if(isset($data['mark_id'])){
						$curent_mark_id = (int)$data['mark_id'];
					}
					
					//Если это товар но нет у нас ни модели ни марки
					//Было раньше - сейчас товар всегда находит свою модель и марку.
					//Для обьявлений тоже категории строятся иначе
					if($key == 'product_id' ){// AND !isset($this->session->data['model_id'])){
						
						$sql = "SELECT * FROM " . DB_PREFIX . "product_to_mark p2m
												LEFT JOIN " . DB_PREFIX . "mark m ON (m.mark_id = p2m.mark_id)
												WHERE p2m.product_id='" . (int)$value . "' ORDER BY m.mark_id LIMIT 1";
					 
						$r = $this->db->query($sql);
					
						
						if($r->num_rows){
							//$this->session->data['mark_id'] = $r->row['parent_id'];
							//$this->session->data['model_id'] = $r->row['mark_id'];
							$curent_mark_id = $r->row['mark_id'];
						}
					}
					
					if(isset($this->session->data['auto_model_id']) AND !isset($this->session->data['model_id'])){
						$mark_id = $this->session->data['auto_model_id'];
					}elseif(isset($this->session->data['model_id'])){
						$mark_id = $this->session->data['model_id'];
					}elseif(isset($this->session->data['mark_id'])){
						$mark_id = $this->session->data['mark_id'];
					}
			
					//Если это товар то у него своя марка. А вот для категорий будет текущая
					//Для обьявлений тоже
					if (isset($data['product_id'])) {
						$mark_id = isset($curent_mark_id) ? $curent_mark_id : 0;
					}
					
					//Если нам надо получить путь модели из Урл
					if (isset($data['fix_mark_id'])) {
						$mark_id = (int)$data['fix_mark_id'];
						unset($data['fix_mark_id']);
					}
					if($mark_from_link){
						$mark_id = isset($curent_mark_id) ? $curent_mark_id : 0;
					}

				
					if(isset($mark_id) AND $mark_id > 0){
						$sql = "SELECT * FROM " . DB_PREFIX . "mark_path cp
												LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('mark_id=', cp.path_id)
												WHERE cp.mark_id='" . (int)$mark_id . "' AND
												language_id = '" . (int)$this->config->get('config_language_id') . "'";
					
					
						$r = $this->db->query($sql);
						
						
						if($r->num_rows){
							foreach($r->rows as $row){
								$path .= '/'.$row['keyword'];
							}
						}
						
					}	
					
				}else{
					
					//Тут добавляем ключи при котором пути не очищает - думаю будут проблемы с появлением фильтров! Надо будет переделать!!!
					$ignore = array(
									'page',
									'sort',
									'order',
									'limit',
									);
					
					if(!in_array($key, $ignore)){
						//echo '<br>'.$key;
						$path = '';
					}
				}
			
				if (($data['route'] == 'product/mark' && $key == 'mark_id') || ($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					
					//Отдельно найдем путь
					if($key == 'product_id'){
						$product_id = $value;
						
						$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id='" . (int)$product_id . "' LIMIT 1");
						//Убираем путь категорий из товара
						if(false and $r->num_rows){
							
							$category_id = (int)$r->row['category_id'];
							$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path cp
													LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('category_id=', cp.path_id)
													WHERE cp.category_id='" . (int)$category_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
							if($r->num_rows){
								foreach($r->rows as $row){
									$categ_path .= '/'.$row['keyword'];
									
									if((int)$row['path_id'] == CLUB_CATEGORY_ID){ // Клубная категория
										$path = '';
									}
								}
							}
							$path .= $categ_path;
						}
						
						unset($data['path']); //Удаляем путь у продукта - мы его уже составили
					}
				
					//Для моделей и марок пересоберем весь путь наново
					if($key == 'mark_id'){
						$path = '';
							
						$mark_id = (int)$value;
						$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "mark_path cp
												LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('mark_id=', cp.path_id)
												WHERE cp.mark_id='" . (int)$mark_id . "' AND
													language_id = '" . (int)$this->config->get('config_language_id') . "' AND
													path_id <> '" . (int)$mark_id . "'");
						if($r->num_rows){
							foreach($r->rows as $row){
								$path .= '/'.$row['keyword'];
							}
						}
						
					}
				
					
					
					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
			//<!-- Blogi * * * Start -->
				}elseif ($data['route'] == 'product/blog_product' && $key == 'blog_product_id') {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
 

					//Отдельно найдем путь
					if($key == 'blog_product_id'){
						unset($data['blogpath']);
						$path = '';
						$product_id = $value;
						
						$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_product_to_category WHERE blog_product_id='" . (int)$product_id . "' LIMIT 1");
						if($r->num_rows){
							
							$category_id = (int)$r->row['blog_category_id'];
							$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_category_path cp
													LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('blog_category_id=', cp.path_id)
													WHERE cp.blog_category_id='" . (int)$category_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
							
							if($r->num_rows){
								foreach($r->rows as $row){
									$path .= '/'.$row['keyword'];
								}
							}
						}
					}
 

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				}elseif ($key == 'blogpath') {
					
					if(isset($data['blog_product_id'])) continue;
					
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'blog_category_id=" . (int)$category . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				
				//<!-- Blogi * * * End -->

					//Отключаем пути у товара - он у него формируется отдельно
				} elseif ($key == 'path') {
					
					 if(isset($data['product_id'])) continue;
					
					$categories = explode('_', $value);
					//$no_path = false;
					$categ_path = '';

					
					$category_id = (int)array_pop($categories);
					$r = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_path cp
											LEFT JOIN " . DB_PREFIX . "seo_url ua ON ua.query = CONCAT('category_id=', cp.path_id)
											WHERE cp.category_id='" . (int)$category_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
					if($r->num_rows){
						foreach($r->rows as $row){
							$categ_path .= '/'.$row['keyword'];
							
							if((int)$row['path_id'] == CLUB_CATEGORY_ID){ // Клубная категория
								$path = '';
							}
							if((int)$row['path_id'] == CONSIGNMENT_CATEGORY_ID){ // Обьявления категория
								//$path = '';
								$path_first = true;
							}
							if((int)$row['path_id'] == 234){ // Аукцион категория
								$path = $path_mark;
							}
						}
					}

					
					//Если нам нужен путь перед марками
					if(isset($path_first)){
						//$path = $categ_path . $path;
					}else{
						$path .= $categ_path;	
					}
				
				
					/*
					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}
					.ru
					*/
					unset($data[$key]);
				}else{
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				}
			}
		}
		
		//Для некоторых категорий путь нужен перед марками
		if(isset($path_first)){
			$path = $categ_path . $path;
			unset($data['mark_id']);
		}
		
		if($no_path){
			$url = $path.$url;
		}
		//echo '<br>' . $path;
		
		if ($url) {
			unset($data['route']);

			$query = '';

			
			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

	
			if(strpos($link, 'route=common/home') !== false){
				$url = '';
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $lang . $url . $query;
		} else {
			
			if($lang != ''){
				$lang = ltrim($lang, '/').'/';
			}
			
			
			$link = str_replace(HTTPS_SERVER, HTTPS_SERVER.$lang, $link);
			$link = str_replace(HTTP_SERVER, HTTP_SERVER.$lang, $link);
			
			if(strpos($link, 'common/home') !== false){
				
				$link = str_replace('index.php?route=common/home', '', $link);
			}
			
			//echo '<br>---' . $link;
			
			return $link;
		}
	}
}
