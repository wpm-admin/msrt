<?php
class ControllerProductSearch extends Controller {
	public function index() {
		$this->load->language('product/search');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} elseif (isset($this->request->get['search'])) {
			$tag = $this->request->get['search'];
		} else {
			$tag = '';
		}

		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = 0;
		}

		if (isset($this->request->get['sub_category'])) {
			$sub_category = $this->request->get['sub_category'];
		} else {
			$sub_category = '';
		}

		if (isset($this->request->get['filter_mark'])) {
			$data['filter_mark'] = (int)$this->request->get['filter_mark'];
		} else {
			$data['filter_mark'] = false;
		}

		if (isset($this->request->get['filter_model'])) {
			$data['filter_model'] = (int)$this->request->get['filter_model'];
		} else {
			$data['filter_model'] = false;
		}

		if (isset($this->request->get['filter_category'])) {
			$data['filter_category'] = (int)$this->request->get['filter_category'];
		} else {
			$data['filter_category'] = false;
		}

		if (isset($this->request->get['filter_diagram'])) {
			$data['filter_diagram'] = (int)$this->request->get['filter_diagram'];
		} else {
			$data['filter_diagram'] = false;
		}

		if (isset($this->request->get['picture'])) {
			$picture_sort = false;
		}else{
			$picture_sort = true;
		}

		if (isset($this->request->get['price'])) {
			$price_sort = false;
		}else{
			$price_sort = true;
		}

		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.stock_status_id=7';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		if (isset($this->request->get['search'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['search']);
		} elseif (isset($this->request->get['tag'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->language->get('heading_tag') . $this->request->get['tag']);
		} else {
			$this->document->setTitle($this->language->get('heading_title'));
		}
		
		
		$data['club_href'] =  $this->url->link('product/category', 'path=' . CLUB_CATEGORY_ID);
		//$data['model'] = $product_info['model'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$url = '';

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['description'])) {
			$url .= '&description=' . $this->request->get['description'];
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
		}

		if (isset($this->request->get['sub_category'])) {
			$url .= '&sub_category=' . $this->request->get['sub_category'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['picture'])) {
			$data['picture_sort_actice'] = false;
			$url .= '&picture';
		}else{
			$data['picture_sort_actice'] = true;
		}

		if (isset($this->request->get['price'])) {
			$data['price_sort_actice'] = false;
			$url .= '&price';
		}else{
			$data['price_sort_actice'] = true;
		}

		if (isset($this->request->get['picture'])) {
			$data['picture_sort'] = $this->url->link('product/search', str_replace('&picture', '',$url));
		}else{
			$data['picture_sort'] = $this->url->link('product/search', 'picture' . $url);
		}
		
		if (isset($this->request->get['price'])) {
			$data['price_sort'] = $this->url->link('product/search', str_replace('&price', '',$url));
		}else{
			$data['price_sort'] = $this->url->link('product/search', 'price' . $url);
		}
		
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('product/search', $url)
		);

		if (isset($this->request->get['search'])) {
			$data['heading_title'] = $this->language->get('heading_title') .  ' - ' . $this->request->get['search'];
		} else {
			$data['heading_title'] = $this->language->get('heading_title');
		}

		$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

		$data['compare'] = $this->url->link('product/compare');

		// 3 Level Category Search
		$data['categories'] = array();

		$categories_1 = $this->model_catalog_category->getCategories(0);

		foreach ($categories_1 as $category_1) {
			$level_2_data = array();

			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

			foreach ($categories_2 as $category_2) {
				$level_3_data = array();

				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'category_id' => $category_3['category_id'],
						'name'        => $category_3['name'],
					);
				}

				$level_2_data[] = array(
					'category_id' => $category_2['category_id'],
					'name'        => $category_2['name'],
					'children'    => $level_3_data
				);
			}

			$data['categories'][] = array(
				'category_id' => $category_1['category_id'],
				'name'        => $category_1['name'],
				'children'    => $level_2_data
			);
		}

		$data['products'] = array();

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			
			//Обнулим пока марку - она у нас одна. Потом надо будет сделать поиск по вложеностям марки
			//$data['filter_mark'] = false;
			
			$filter_data = array(
				'filter_name'         => $search,
				'filter_diagram'          => 2,
				'filter_model'          => false,
				'filter_tag'          => $tag,
				'filter_image'          => $data['picture_sort_actice'],
				'filter_price'          => $data['price_sort_actice'],
				'filter_description'  => $description,
				'filter_category_id'  => $data['filter_category'],
				'filter_diagram_id'  => $data['filter_diagram'],
				'filter_mark_id'  => $data['filter_model'],
				'filter_sub_category' => $sub_category,
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit
			);

			//unset($filter_data['filter_diagram']);
			
			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$results = $this->model_catalog_product->getProducts($filter_data);
	
			$filter_data_1 = $filter_data;
			unset($filter_data_1['filter_mark_id']);
			unset($filter_data_1['filter_category_id']);
			unset($filter_data_1['filter_diagram_id']);
			$s_results = $this->model_catalog_product->getSearchProducts($filter_data_1);

			//Отфильтруем списка Модели и Категорий без учета их выбора
			if(count($s_results) > 1){
		
				$this->load->model('catalog/category');
				$this->load->model('catalog/mark');
		
				$model_ids = $this->model_catalog_product->getProductsModels($s_results);
				$mark_ids = $this->model_catalog_product->getMarksOnModels($model_ids);
				$category_ids = $this->model_catalog_product->getProductsCategorys($s_results);
				$diagram_ids = $this->model_catalog_product->getProductsDiagrams($s_results);
				
				$data['marks'] = array();
				$data['models'] = array();
				$data['categories'] = array();
				$data['diagrams'] = array();
				
				$data['marks'][] = array(
						 'id' => 0,
						 'name' => 'all',
						 'href' => $this->url->link('product/search', $url),
						 'sort' => $this->url->link('product/search', 'filter_model=0' . $url),
						 );

				
				foreach($mark_ids as $row_id){
					$info = $this->model_catalog_mark->getMark($row_id);
					if($info){
						$data['marks'][] = array(
											 'id' => $info['mark_id'],
											 'name' => $info['name'],
											 'href' => $this->url->link('product/mark', 'mark_id=' .  $info['mark_id'] ),
											 'sort' => $this->url->link('product/search', 'filter_model=' .  $info['mark_id'] . $url),
											 );
					}
				}
				
				foreach($model_ids as $row_id){
					$info = $this->model_catalog_mark->getMark($row_id);
					
					if($info){
						$data['models'][] = array(
											 'id' => $info['mark_id'],
											 'name' => $info['name'],
											 'href' => $this->url->link('product/mark', 'mark_id=' .  $info['mark_id'] ),
											 );
					}
				}
				
				foreach($category_ids as $row_id){
					$info = $this->model_catalog_category->getCategory($row_id);
					
					if($info){
						$data['categories'][] = array(
											 'id' => $info['category_id'],
											 'name' => $info['name'],
											 'href' => $this->url->link('product/category', 'path=' .  $info['category_id'] ),
											 );
					}
				}
					
				foreach($diagram_ids as $row_id){
					$info = $this->model_catalog_product->getProduct($row_id);
					
					if($info){
						$data['diagrams'][] = array(
											 'id' => $info['product_id'],
											 'name' => $info['name'],
											 'href' => $this->url->link('product/product', 'product_id=' .  $info['product_id'] ),
											 );
					}else{
						
					}
				}

			}
		
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'), 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
	
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ($result['special'] && !is_null($result['special']) && (float)$result['special'] >= 0) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$tax_price = (float)$result['special'];
				} else {
					$special = false;
					$tax_price = (float)$result['price'];
				}
	
				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format($tax_price, $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false; 
				}


					$query_manufacturer_img = $this->db->query("select image from " . DB_PREFIX . "manufacturer where manufacturer_id = '" .(int)$result['manufacturer_id'] . "'");
					if($query_manufacturer_img->row){
						$manufacturer_img = $this->model_tool_image->resize($query_manufacturer_img->row['image'], 50,50);
					}else{
						$manufacturer_img = $this->model_tool_image->resize('placeholder.png', 50,50);
					}
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'model'  => $result['model'],
					'sku'  => ($result['sku'] != '') ? $result['sku'] : $result['model'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
		
				
					'special'     => $special,
					'manufacturer_img' => $manufacturer_img,
					'stock_status_id'        => $result['stock_status_id'],				
					'tax'         => $tax,
					'minimum'     => ($result['minimum'] > 0) ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
				);
			}
			
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			if (isset($this->request->get['picture'])) {
				$url .= '&picture';
			}

			if (isset($this->request->get['price'])) {
				$url .= '&price';
			}

			$data['sorts'] = array();

			$statuses = $this->model_catalog_product->getProductStatuses();
			
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.sort_order-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.price&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $statuses[5]['name'], //$this->language->get('stock_status_id=5'),
				'value' => 'p.stock_status_id=5',
				'href'  => $this->url->link('product/search', 'sort=p.stock_status_id=5&order=ASC' . $url)
			);
			$data['sorts'][] = array(
				'text'  => $statuses[6]['name'], //$this->language->get('stock_status_id=6'),
				'value' => 'p.stock_status_id=6',
				'href'  => $this->url->link('product/search', 'sort=p.stock_status_id=6&order=ASC' . $url)
			);
			$data['sorts'][] = array(
				'text'  => $statuses[7]['name'], //$this->language->get('stock_status_id=7'),
				'value' => 'p.stock_status_id=7',

				'href'  => $this->url->link('product/search', 'sort=p.stock_status_id=7&order=ASC' . $url)
			);
	
			$data['sorts'][] = array(
				'text'  => $statuses[9]['name'], //$this->language->get('stock_status_id=9'),
				'value' => 'p.stock_status_id=9',
				'href'  => $this->url->link('product/search', 'sort=p.stock_status_id=9&order=ASC' . $url)
			);
	
			$url = '';
	 		if (isset($this->request->get['search'])) {
				$url .= 'search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}
			$data['href']  = $this->url->link('product/search', $url);
 

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['picture'])) {
				$url .= '&picture';
			}
			if (isset($this->request->get['price'])) {
				$url .= '&price';
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 40, 60, 100, 200));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/search', $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['picture'])) {
				$url .= '&picture';
			}
			if (isset($this->request->get['price'])) {
				$url .= '&price';
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/search', $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			if (isset($this->request->get['search']) && $this->config->get('config_customer_search')) {
				$this->load->model('account/search');

				if ($this->customer->isLogged()) {
					$customer_id = $this->customer->getId();
				} else {
					$customer_id = 0;
				}

				if (isset($this->request->server['REMOTE_ADDR'])) {
					$ip = $this->request->server['REMOTE_ADDR'];
				} else {
					$ip = '';
				}

				$search_data = array(
					'keyword'       => $search,
					'category_id'   => $category_id,
					'sub_category'  => $sub_category,
					'description'   => $description,
					'products'      => $product_total,
					'customer_id'   => $customer_id,
					'ip'            => $ip
				);

				$this->model_account_search->addSearch($search_data);
			}
		}

		$data['search'] = $search;
		$data['description'] = $description;
		$data['category_id'] = $category_id;
		$data['sub_category'] = $sub_category;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		
		$data['breadcrumbs_twig'] = $this->load->view('includes/_breadcrumbs', $data);
		$this->response->setOutput($this->load->view('product/search', $data));
	}
}
