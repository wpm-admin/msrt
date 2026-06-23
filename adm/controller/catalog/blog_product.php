<?php
class ControllerCatalogBlogProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/blog_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_product');

		/*
		$blog_products = $this->model_catalog_blog_product->getProducts();
		foreach($blog_products as $blog_product){
			$this->model_catalog_blog_product->deleteProduct($blog_product['blog_product_id']);
		}
		*/
		
		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/blog_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$blog_product_id = $this->model_catalog_blog_product->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}


			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$url .= '&blog_product_id=' . $blog_product_id;
			
			if (isset($this->request->post['return_back_tab'])) {
				
				$tmp = explode('#', $this->request->post['return_back_tab']);
			
				$url .= '&back_tab=' . (isset($tmp[1]) ? $tmp[1] : 'tab-general');
			}

			if(isset($this->request->post['return_back']) AND $this->request->post['return_back']){
				$this->response->redirect($this->url->link('catalog/blog_product/edit', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}else{
				$this->response->redirect($this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/blog_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_blog_product->editProduct($this->request->get['blog_product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			if (isset($this->request->get['blog_product_id'])) {
				$url .= '&blog_product_id=' . $this->request->get['blog_product_id'];
			}
			
			if (isset($this->request->post['return_back_tab'])) {
				
				$tmp = explode('#', $this->request->post['return_back_tab']);
			
				$url .= '&back_tab=' . (isset($tmp[1]) ? $tmp[1] : 'tab-general');
			}

			if(isset($this->request->post['return_back']) AND $this->request->post['return_back']){
				$this->response->redirect($this->url->link('catalog/blog_product/edit', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}else{
				$this->response->redirect($this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/blog_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_product');

			
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $blog_product_id) {
				$this->model_catalog_blog_product->deleteProduct($blog_product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function copy() {
		$this->load->language('catalog/blog_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $blog_product_id) {
				$this->model_catalog_blog_product->copyProduct($blog_product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = '';
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = '';
		}
			
			$filter_sku = $filter_manufacturer = $filter_blog_category = $filter_mark = false;
			if (isset($this->request->get['filter_sku'])) {
				$filter_sku = $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$filter_manufacturer = $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$filter_blog_category = $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$filter_mark = $this->request->get['filter_mark'];
			}


		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/blog_product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('catalog/blog_product/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/blog_product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['blog_products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_sku' 	=> $filter_sku,
			'filter_manufacturer' => $filter_manufacturer,
			'filter_blog_category' => $filter_blog_category,
			'filter_mark' => $filter_mark,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/blog_category');
		$this->load->model('catalog/mark');

		$blog_product_total = $this->model_catalog_blog_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_blog_product->getProducts($filter_data);

		
		$filter_data = array();
		$filter_data['limit'] = 10000;
		$filter_data['start'] = 0;
		$filter_data['sort'] = 'name';
		$data['marks'] = $this->model_catalog_mark->getMarks();
		
		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		
		$filter_data = array();
		$filter_data['limit'] = 10000;
		$filter_data['start'] = 0;
		$filter_data['sort'] = 'name';
		$data['categories'] = $this->model_catalog_blog_category->getCategories($filter_data);
				
		

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			
			$special = false;

			$blog_product_specials = $this->model_catalog_blog_product->getProductSpecials($result['blog_product_id']);

			foreach ($blog_product_specials  as $blog_product_special) {
				if (($blog_product_special['date_start'] == '0000-00-00' || strtotime($blog_product_special['date_start']) < time()) && ($blog_product_special['date_end'] == '0000-00-00' || strtotime($blog_product_special['date_end']) > time())) {
					$special = $this->currency->format($blog_product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			//Получим бренд
			
			$manufacturer_name = '. . .';
			$manufacturer_query = $this->model_catalog_manufacturer->getManufacturer($result['manufacturer_id']);
			if($manufacturer_query){
				$manufacturer_name = $manufacturer_query->row['name'];
			}

			//Получим категории товара
			$blog_category_names = array();
			
			$blog_categoryes = $this->model_catalog_blog_product->getProductCategories($result['blog_product_id']);
			foreach($blog_categoryes as $categ_id){
				$blog_category_names[] = $data['categories'][$categ_id]['name'];
			}
			
				
			$data['blog_products'][] = array(
				'blog_product_id' => $result['blog_product_id'],
				'image'      => $image,
				'name'       => $result['blog_product_name'],
				'model'      => $result['model'],
				'sku'      => $result['sku'],
				'blog_category'      => implode('<br>', $blog_category_names),
				'manufacturer_id'      => $result['manufacturer_id'],
				'manufacturer_name'      => $manufacturer_name,
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'sort_order'   => $result['sort_order'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('catalog/blog_product/edit', 'user_token=' . $this->session->data['user_token'] . '&blog_product_id=' . $result['blog_product_id'] . $url, true)
			);
		}

			
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_sku'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sku' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=md.name' . $url, true);
		$data['sort_blog_category'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.name' . $url, true);
		$data['sort_mark'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=mode.name' . $url, true);
		
		
		$data['sort_name'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $blog_product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($blog_product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($blog_product_total - $this->config->get('config_limit_admin'))) ? $blog_product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $blog_product_total, ceil($blog_product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;
	$data['filter_sku'] = $filter_sku;
	$data['filter_manufacturer'] = $filter_manufacturer;
	$data['filter_blog_category'] = $filter_blog_category;
	$data['filter_mark'] = $filter_mark;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog_catalog/blog_product_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['blog_product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_blog_category'])) {
				$url .= '&filter_blog_category=' . $this->request->get['filter_blog_category'];
			}

			if (isset($this->request->get['filter_mark'])) {
				$url .= '&filter_mark=' . $this->request->get['filter_mark'];
			}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['back_tab'] = false;
		if (isset($this->request->get['back_tab'])) {
			$data['back_tab'] = $this->request->get['back_tab'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['blog_product_id'])) {
			$data['action'] = $this->url->link('catalog/blog_product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/blog_product/edit', 'user_token=' . $this->session->data['user_token'] . '&blog_product_id=' . $this->request->get['blog_product_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/blog_product', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['blog_product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$blog_product_info = $this->model_catalog_blog_product->getProduct($this->request->get['blog_product_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['blog_product_description'])) {
			$data['blog_product_description'] = $this->request->post['blog_product_description'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$data['blog_product_description'] = $this->model_catalog_blog_product->getProductDescriptions($this->request->get['blog_product_id']);
		} else {
			$data['blog_product_description'] = array();
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($blog_product_info)) {
			$data['model'] = $blog_product_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($blog_product_info)) {
			$data['sku'] = $blog_product_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($blog_product_info)) {
			$data['upc'] = $blog_product_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($blog_product_info)) {
			$data['ean'] = $blog_product_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($blog_product_info)) {
			$data['jan'] = $blog_product_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($blog_product_info)) {
			$data['isbn'] = $blog_product_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($blog_product_info)) {
			$data['mpn'] = $blog_product_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($blog_product_info)) {
			$data['location'] = $blog_product_info['location'];
		} else {
			$data['location'] = '';
		}

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['blog_product_store'])) {
			$data['blog_product_store'] = $this->request->post['blog_product_store'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$data['blog_product_store'] = $this->model_catalog_blog_product->getProductStores($this->request->get['blog_product_id']);
		} else {
			$data['blog_product_store'] = array(0);
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($blog_product_info)) {
			$data['shipping'] = $blog_product_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($blog_product_info)) {
			$data['price'] = $blog_product_info['price'];
		} else {
			$data['price'] = '';
		}

		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (isset($this->request->post['blog_product_recurrings'])) {
			$data['blog_product_recurrings'] = $this->request->post['blog_product_recurrings'];
		} elseif (!empty($blog_product_info)) {
			$data['blog_product_recurrings'] = $this->model_catalog_blog_product->getRecurrings($blog_product_info['blog_product_id']);
		} else {
			$data['blog_product_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($blog_product_info)) {
			$data['tax_class_id'] = $blog_product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($blog_product_info)) {
			$data['date_available'] = ($blog_product_info['date_available'] != '0000-00-00') ? $blog_product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($blog_product_info)) {
			$data['quantity'] = $blog_product_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($blog_product_info)) {
			$data['minimum'] = $blog_product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($blog_product_info)) {
			$data['subtract'] = $blog_product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($blog_product_info)) {
			$data['sort_order'] = $blog_product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($blog_product_info)) {
			$data['stock_status_id'] = $blog_product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($blog_product_info)) {
			$data['status'] = $blog_product_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($blog_product_info)) {
			$data['weight'] = $blog_product_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($blog_product_info)) {
			$data['weight_class_id'] = $blog_product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($blog_product_info)) {
			$data['length'] = $blog_product_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($blog_product_info)) {
			$data['width'] = $blog_product_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($blog_product_info)) {
			$data['height'] = $blog_product_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($blog_product_info)) {
			$data['length_class_id'] = $blog_product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($blog_product_info)) {
			$data['manufacturer_id'] = $blog_product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($blog_product_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($blog_product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Categories
		$this->load->model('catalog/blog_category');

		if (isset($this->request->post['blog_product_blog_category'])) {
			$categories = $this->request->post['blog_product_blog_category'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$categories = $this->model_catalog_blog_product->getProductCategories($this->request->get['blog_product_id']);
		} else {
			$categories = array();
		}

		$data['blog_product_categories'] = array();

		foreach ($categories as $blog_category_id) {
			$blog_category_info = $this->model_catalog_blog_category->getCategory($blog_category_id);

			if ($blog_category_info) {
				$data['blog_product_categories'][] = array(
					'blog_category_id' => $blog_category_info['blog_category_id'],
					'name'        => ($blog_category_info['path']) ? $blog_category_info['path'] . ' &gt; ' . $blog_category_info['name'] : $blog_category_info['name']
				);
			}
		}

			// Categories
		// Filters
		$this->load->model('catalog/filter');

		if (isset($this->request->post['blog_product_filter'])) {
			$filters = $this->request->post['blog_product_filter'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$filters = $this->model_catalog_blog_product->getProductFilters($this->request->get['blog_product_id']);
		} else {
			$filters = array();
		}

		$data['blog_product_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['blog_product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->post['blog_product_attribute'])) {
			$blog_product_attributes = $this->request->post['blog_product_attribute'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$blog_product_attributes = $this->model_catalog_blog_product->getProductAttributes($this->request->get['blog_product_id']);
		} else {
			$blog_product_attributes = array();
		}

		$data['blog_product_attributes'] = array();

		foreach ($blog_product_attributes as $blog_product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($blog_product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['blog_product_attributes'][] = array(
					'attribute_id'                  => $blog_product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'blog_product_attribute_description' => $blog_product_attribute['blog_product_attribute_description']
				);
			}
		}

		// Options
		$this->load->model('catalog/option');

		if (isset($this->request->post['blog_product_option'])) {
			$blog_product_options = $this->request->post['blog_product_option'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$blog_product_options = $this->model_catalog_blog_product->getProductOptions($this->request->get['blog_product_id']);
		} else {
			$blog_product_options = array();
		}

		$data['blog_product_options'] = array();

		foreach ($blog_product_options as $blog_product_option) {
			$blog_product_option_value_data = array();

			if (isset($blog_product_option['blog_product_option_value'])) {
				foreach ($blog_product_option['blog_product_option_value'] as $blog_product_option_value) {
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
			}

			$data['blog_product_options'][] = array(
				'blog_product_option_id'    => $blog_product_option['blog_product_option_id'],
				'blog_product_option_value' => $blog_product_option_value_data,
				'option_id'            => $blog_product_option['option_id'],
				'name'                 => $blog_product_option['name'],
				'type'                 => $blog_product_option['type'],
				'value'                => isset($blog_product_option['value']) ? $blog_product_option['value'] : '',
				'required'             => $blog_product_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['blog_product_options'] as $blog_product_option) {
			if ($blog_product_option['type'] == 'select' || $blog_product_option['type'] == 'radio' || $blog_product_option['type'] == 'checkbox' || $blog_product_option['type'] == 'image') {
				if (!isset($data['option_values'][$blog_product_option['option_id']])) {
					$data['option_values'][$blog_product_option['option_id']] = $this->model_catalog_option->getOptionValues($blog_product_option['option_id']);
				}
			}
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['blog_product_discount'])) {
			$blog_product_discounts = $this->request->post['blog_product_discount'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$blog_product_discounts = $this->model_catalog_blog_product->getProductDiscounts($this->request->get['blog_product_id']);
		} else {
			$blog_product_discounts = array();
		}

		$data['blog_product_discounts'] = array();

		foreach ($blog_product_discounts as $blog_product_discount) {
			$data['blog_product_discounts'][] = array(
				'customer_group_id' => $blog_product_discount['customer_group_id'],
				'quantity'          => $blog_product_discount['quantity'],
				'priority'          => $blog_product_discount['priority'],
				'price'             => $blog_product_discount['price'],
				'date_start'        => ($blog_product_discount['date_start'] != '0000-00-00') ? $blog_product_discount['date_start'] : '',
				'date_end'          => ($blog_product_discount['date_end'] != '0000-00-00') ? $blog_product_discount['date_end'] : ''
			);
		}

		if (isset($this->request->post['blog_product_special'])) {
			$blog_product_specials = $this->request->post['blog_product_special'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$blog_product_specials = $this->model_catalog_blog_product->getProductSpecials($this->request->get['blog_product_id']);
		} else {
			$blog_product_specials = array();
		}

		$data['blog_product_specials'] = array();

		foreach ($blog_product_specials as $blog_product_special) {
			$data['blog_product_specials'][] = array(
				'customer_group_id' => $blog_product_special['customer_group_id'],
				'priority'          => $blog_product_special['priority'],
				'price'             => $blog_product_special['price'],
				'date_start'        => ($blog_product_special['date_start'] != '0000-00-00') ? $blog_product_special['date_start'] : '',
				'date_end'          => ($blog_product_special['date_end'] != '0000-00-00') ? $blog_product_special['date_end'] :  ''
			);
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($blog_product_info)) {
			$data['image'] = $blog_product_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
			$data['thumb_coordiname'] = $this->model_tool_image->resize($this->request->post['image'], 1000, 800);
		} elseif (!empty($blog_product_info) && is_file(DIR_IMAGE . $blog_product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($blog_product_info['image'], 100, 100);
			$data['thumb_coordiname'] = $this->model_tool_image->resize($blog_product_info['image'], 1000, 800);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			$data['thumb_coordiname'] = $this->model_tool_image->resize('no_image.png', 1000, 800);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['blog_product_image'])) {
			$blog_product_images = $this->request->post['blog_product_image'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$blog_product_images = $this->model_catalog_blog_product->getProductImages($this->request->get['blog_product_id']);
		} else {
			$blog_product_images = array();
		}

		$data['blog_product_images'] = array();

		foreach ($blog_product_images as $blog_product_image) {
			if (is_file(DIR_IMAGE . $blog_product_image['image'])) {
				$image = $blog_product_image['image'];
				$thumb = $blog_product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['blog_product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $blog_product_image['sort_order']
			);
		}

		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->post['blog_product_download'])) {
			$blog_product_downloads = $this->request->post['blog_product_download'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$blog_product_downloads = $this->model_catalog_blog_product->getProductDownloads($this->request->get['blog_product_id']);
		} else {
			$blog_product_downloads = array();
		}

		$data['blog_product_downloads'] = array();

		foreach ($blog_product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['blog_product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['blog_product_related'])) {
			$blog_products = $this->request->post['blog_product_related'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$blog_products = $this->model_catalog_blog_product->getProductRelated($this->request->get['blog_product_id']);
		} else {
			$blog_products = array();
		}

		$data['blog_product_relateds'] = array();

		foreach ($blog_products as $blog_product_id) {
			$related_info = $this->model_catalog_blog_product->getProduct($blog_product_id);

			if ($related_info) {
				$data['blog_product_relateds'][] = array(
					'blog_product_id' => $related_info['blog_product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($blog_product_info)) {
			$data['points'] = $blog_product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['blog_product_reward'])) {
			$data['blog_product_reward'] = $this->request->post['blog_product_reward'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$data['blog_product_reward'] = $this->model_catalog_blog_product->getProductRewards($this->request->get['blog_product_id']);
		} else {
			$data['blog_product_reward'] = array();
		}

		if (isset($this->request->post['blog_product_seo_url'])) {
			$data['blog_product_seo_url'] = $this->request->post['blog_product_seo_url'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$data['blog_product_seo_url'] = $this->model_catalog_blog_product->getProductSeoUrls($this->request->get['blog_product_id']);
		} else {
			$data['blog_product_seo_url'] = array();
		}

		if (isset($this->request->post['blog_product_layout'])) {
			$data['blog_product_layout'] = $this->request->post['blog_product_layout'];
		} elseif (isset($this->request->get['blog_product_id'])) {
			$data['blog_product_layout'] = $this->model_catalog_blog_product->getProductLayouts($this->request->get['blog_product_id']);
		} else {
			$data['blog_product_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog_catalog/blog_product_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/blog_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['blog_product_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if (false and $this->request->post['blog_product_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['blog_product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['blog_product_id']) || (($seo_url['query'] != 'blog_product_id=' . $this->request->get['blog_product_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

								break;
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/blog_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/blog_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/blog_product');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_blog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$blog_product_options = $this->model_catalog_blog_product->getProductOptions($result['blog_product_id']);

				foreach ($blog_product_options as $blog_product_option) {
					$option_info = $this->model_catalog_option->getOption($blog_product_option['option_id']);

					if ($option_info) {
						$blog_product_option_value_data = array();

						foreach ($blog_product_option['blog_product_option_value'] as $blog_product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($blog_product_option_value['option_value_id']);

							if ($option_value_info) {
								$blog_product_option_value_data[] = array(
									'blog_product_option_value_id' => $blog_product_option_value['blog_product_option_value_id'],
									'option_value_id'         => $blog_product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$blog_product_option_value['price'] ? $this->currency->format($blog_product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $blog_product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'blog_product_option_id'    => $blog_product_option['blog_product_option_id'],
							'blog_product_option_value' => $blog_product_option_value_data,
							'option_id'            => $blog_product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $blog_product_option['value'],
							'required'             => $blog_product_option['required']
						);
					}
				}

				$json[] = array(
					'blog_product_id' => $result['blog_product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}


