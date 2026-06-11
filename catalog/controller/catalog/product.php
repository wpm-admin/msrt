<?php
class ControllerCatalogProduct extends Controller {
	private $error = array();
	private $cols = array(
					 'image' => 1,
					 'name' => 1,
					 'description' => 1,
					 'manufacturer' => 0,
					 'category' => 1,
					 'uom' => 0,
					 'availability' => 0,
					 'dimensions' => 0,
					 'sales_tax' => 0,
					 'condition' => 0,
					 'weight' => 0,
					 'mark' => 1,
					 'code_manual' => 0,
					 'fishbowl' => 0,
					 'cost' => 0,
					 'price' => 1,
					 'quantity' => 0,
					 'status' => 0,
					 'sort_order' => 1,
					 );
	
	public function index() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		/*
		$products = $this->model_catalog_product->getProducts();
		foreach($products as $product){
			$this->model_catalog_product->deleteProduct($product['product_id']);
		}
		$this->db->query("DELETE FROM oc_product_coordinate");
		$this->db->query("ALTER TABLE oc_product AUTO_INCREMENT =1;");
		$this->db->query("ALTER TABLE oc_seo_url AUTO_INCREMENT =1;");
		$this->db->query("ALTER TABLE oc_image_compress_squeezeimg_replace AUTO_INCREMENT =1;");
		*/
		
		//$this->db->query("ALTER TABLE oc_category ADD COLUMN is_diagram INT(2) DEFAULT 1");
		
		
		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$product_id = $this->model_catalog_product->addProduct($this->request->post);

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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
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

			$url .= '&product_id=' . $product_id;
			
			if (isset($this->request->post['return_back_tab'])) {
				
				$tmp = explode('#', $this->request->post['return_back_tab']);
			
				$url .= '&back_tab=' . (isset($tmp[1]) ? $tmp[1] : 'tab-general');
			}

			if(isset($this->request->post['return_back']) AND $this->request->post['return_back']){
				$this->response->redirect($this->url->link('catalog/product/edit', $url, true));
			}else{
				$this->response->redirect($this->url->link('catalog/product', $url, true));
			}
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->editProduct($this->request->get['product_id'], $this->request->post);

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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
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

			if (isset($this->request->get['product_id'])) {
				$url .= '&product_id=' . $this->request->get['product_id'];
			}
			
			if (isset($this->request->post['return_back_tab'])) {
				
				$tmp = explode('#', $this->request->post['return_back_tab']);
			
				$url .= '&back_tab=' . (isset($tmp[1]) ? $tmp[1] : 'tab-general');
			}

			if(isset($this->request->post['return_back']) AND $this->request->post['return_back']){
				$this->response->redirect($this->url->link('catalog/product/edit', $url, true));
			}else{
				$this->response->redirect($this->url->link('catalog/product', $url, true));
			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

			
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->deleteProduct($product_id);
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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
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

			$this->response->redirect($this->url->link('catalog/product', $url, true));
		}

		$this->getList();
	}
	public function update() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		
		$count = 0;
				
		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $product_id) {
				
				//Пауза каждые 20 записей
				if($count++ > 20){
					$count = 0;
					sleep(2);
				}
				
				$this->model_catalog_product->updateProduct($product_id, $this->request->post);
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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
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

			if(isset($this->request->post['product_image'])){
				$this->load->model('tool/image');
				echo $this->model_tool_image->resize($this->request->post['product_image'], 40, 40);
				return false;
			}else{
				$this->response->redirect($this->url->link('catalog/product', $url, true));
			}
			
		}

		$this->getList();

	}

	public function copy() {
		$this->load->language('catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catalog_product->copyProduct($product_id);
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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
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

			$this->response->redirect($this->url->link('catalog/product', $url, true));
		}

		$this->getList();
	}

	public function session(){
		$this->session->data[$this->request->get['name']] = $this->request->get['value'];
	}
	public function cols() {
		
		
		$this->session->data['cols'] = array();
		
		foreach($this->cols as $name => $val){
			
			if(isset($this->request->post['cols'][$name])){
				$this->session->data['cols'][$name] = 1;
			}else{
				$this->session->data['cols'][$name] = 0;
			}
			
		}
		
	}
	
	protected function getList() {
		
		if(isset($this->session->data['cols'])){
			$data['cols'] = $this->session->data['cols'];
		}else{
			$data['cols'] = $this->cols;
		}
		
		
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
			
			$filter_sku = $filter_manufacturer = $filter_category = $filter_mark = false;
			if (isset($this->request->get['filter_sku'])) {
				$filter_sku = $this->request->get['filter_sku'];
			}

			if (isset($this->request->get['filter_manufacturer'])) {
				$filter_manufacturer = $this->request->get['filter_manufacturer'];
			}

			if (isset($this->request->get['filter_category'])) {
				$filter_category = $this->request->get['filter_category'];
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

		if (isset($this->request->get['filter_diagram'])) {
			$filter_diagram = (int)$this->request->get['filter_diagram'];
		}else{
			$filter_diagram = 0;
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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
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
			'href' => $this->url->link('catalog/product', $url, true)
		);

		$data['add'] = $this->url->link('catalog/product/add', $url, true);
		$data['copy'] = $this->url->link('catalog/product/copy', $url, true);
		$data['delete'] = $this->url->link('catalog/product/delete', $url, true);
		$data['update'] = $this->url->link('catalog/product/update', $url, true);

		if(isset($this->session->data['product_limit'])){
			$limit = (int)$this->session->data['product_limit'];
		}else{
			$limit = (int)$this->config->get('config_limit_admin');
		}
	
		$data['limit_value'] = $limit;
		$data['limits'] = array(
								10 => 'Limit 10',
								30 => 'Limit 30',
								50 => 'Limit 50',
								100 => 'Limit 100',
								150 => 'Limit 150',
								200 => 'Limit 200',
								300 => 'Limit 300',
								500 => 'Limit 500',
								1000 => 'Limit 1000',
							);
		
		$data['products'] = array();

		$customer_id = (int)$this->customer->isLogged();
		if($customer_id == 0){
			$customer_id = -1;
		}
		
		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_diagram'   => $filter_diagram,
			'filter_sku' 	=> $filter_sku,
			'filter_manufacturer' => $filter_manufacturer,
			'filter_category' => $filter_category,
			'filter_mark' => $filter_mark,
			'sort'            => $sort,
			'customer_id' => $customer_id,
			'order'           => $order,
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit
		);

		$this->load->model('tool/image');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/category');
		$this->load->model('catalog/mark');
		$this->load->model('catalog/product');
		$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_product->getProducts($filter_data);

		$filter_data = array();
		$filter_data['limit'] = 10000;
		$filter_data['start'] = 0;
		$filter_data['sort'] = 'name';
		$data['marks'] = $this->model_catalog_mark->getMarks();
		
		foreach($data['marks'] as $mark_id => $mark){
			
			$data['marks'] = array_merge($data['marks'], $this->model_catalog_mark->getMarks($mark['mark_id']));
		}
	
		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		
		$filter_data = array();
		$filter_data['limit'] = 10000;
		$filter_data['start'] = 0;
		$filter_data['sort'] = 'name';
		$data['categories'] = $this->model_catalog_product->getAdminCategories($filter_data);
		
				
		$this->load->model('localisation/stock_status');
		$stock_statuses = $this->model_localisation_stock_status->getStockStatuses();

		foreach($stock_statuses as $row){
			$data['stock_statuses'][$row['stock_status_id']] = $row;
		}
		
		$this->load->model('localisation/length_class');
		$length_classes = $this->model_localisation_length_class->getLengthClasses();

		foreach($length_classes as $row){
			$data['length_classes'][$row['length_class_id']] = $row;
		}
	
		$this->load->model('localisation/tax_class');
		$tax_classes = $this->model_localisation_tax_class->getTaxClasses();
	
		foreach($tax_classes as $row){
			$data['tax_classes'][$row['tax_class_id']] = $row;
		}
	
		$this->load->model('localisation/condition_status');
		$condition_statuses = $this->model_localisation_condition_status->getConditionStatuses();

		foreach($condition_statuses as $row){
			$data['condition_statuses'][$row['condition_status_id']] = $row;
		}
	
		$this->load->model('tool/image');
		$data['image'] = $this->model_tool_image->resize('no_image.png', 40, 40);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			//Марки
			$mark_names = array();
			$marks = $this->model_catalog_product->getProductMarks($result['product_id']);
			foreach($marks as $mark_id){
				$mark_names[] = $data['marks'][(int)$mark_id]['name'];
			}
		
			//Получим бренд
			$manufacturer_name = '. . .';
			$manufacturer_query = $this->model_catalog_manufacturer->getManufacturer($result['manufacturer_id']);
			if($manufacturer_query){
				$manufacturer_name = $manufacturer_query['name'];
			}

			
			//Получим категории товара
			$category_names = array();
			
			$categoryes = $this->model_catalog_product->getProductCategories($result['product_id']);
			foreach($categoryes as $categ_id){
				$category_names[] = $data['categories'][$categ_id]['name'];
			}
			
				
			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'href_image'       => $result['image'],
				'cost'       => $result['cost'],
				'name'       => $result['product_name'],
				'weight'       => $result['weight'],
				'description'       => trim(strip_tags(html_entity_decode($result['product_description'], ENT_QUOTES, 'UTF-8'))),
				'model'      => $result['model'],
				'uom'      => $result['uom'],
				'availability'      => $data['stock_statuses'][(int)$result['stock_status_id']]['name'],
				'dimensions'      => (int)$result['length'].' x '.(int)$result['width'].' x '.(int)$result['height'].'<br>'.$data['length_classes'][(int)$result['length_class_id']]['unit'],
				'sales_tax'      => $data['tax_classes'][(int)$result['tax_class_id']]['title'],
				'condition'      => $data['condition_statuses'][$result['condition_status_id']]['name'],
				'sku'      => $result['sku'],
				'category'      => implode('<br>', $category_names),
				'mark'      => implode('<br>', $mark_names),
				'manufacturer_id'      => $result['manufacturer_id'],
				'manufacturer_name'      => $manufacturer_name,
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'sort_order'   => $result['sort_order'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('catalog/product/edit', 'product_id=' . $result['product_id'] . $url, true)
			);
			
		}

		$categories = $this->model_catalog_category->getCategories(array('start'=>0,'limit'=>99999));	
	
		$data['my_categories'] = array();
		foreach($categories as $index => $row){
			if($row['parent_id'] == 0){
				$data['my_categories'][$index] = $row;
				unset($categories[$index]);

				foreach($categories as $index1 => $row1){
					if($row1['parent_id'] == $index){
						$data['my_categories'][$index]['parent'][$index1] = $row1;
						unset($categories[$index1]);

						foreach($categories as $index2 => $row2){
							if($row2['parent_id'] == $index1){
								$data['my_categories'][$index]['parent'][$index1]['parent'][$index2] = $row2;
								unset($categories[$index2]);

								foreach($categories as $index3 => $row3){
									if($row3['parent_id'] == $index2){
										$data['my_categories'][$index]['parent'][$index1]['parent'][$index2]['parent'][$index3] = $row3;
										unset($categories[$index3]);
		
										foreach($categories as $index4 => $row4){
											if($row4['parent_id'] == $index3){
												$data['my_categories'][$index]['parent'][$index1]['parent'][$index2]['parent'][$index3]['parent'][$index4] = $row4;
												unset($categories[$index4]);
				
												foreach($categories as $index5 => $row5){
													if($row5['parent_id'] == $index4){
														$data['my_categories'][$index]['parent'][$index1]['parent'][$index2]['parent'][$index3]['parent'][$index4]['parent'][$index5] = $row5;
														unset($categories[$index5]);
													
														// До 5 левела
														
													}
												}
												
											}
										}
										
									}
								}
								
							}
						}
						
					}
				}
			}
		}
		
		$marks = $this->model_catalog_mark->getMarks(array('start'=>0,'limit'=>99999));	
	
		$data['my_marks'] = array();
		foreach($marks as $index => $row){
			if($row['parent_id'] == 0){
				$data['my_marks'][$index] = $row;
				unset($marks[$index]);

				foreach($marks as $index1 => $row1){
					if($row1['parent_id'] == $index){
						$data['my_marks'][$index]['parent'][$index1] = $row1;
						unset($marks[$index1]);

						foreach($marks as $index2 => $row2){
							if($row2['parent_id'] == $index1){
								$data['my_marks'][$index]['parent'][$index1]['parent'][$index2] = $row2;
								unset($marks[$index2]);

								foreach($marks as $index3 => $row3){
									if($row3['parent_id'] == $index2){
										$data['my_marks'][$index]['parent'][$index1]['parent'][$index2]['parent'][$index3] = $row3;
										unset($marks[$index3]);
		
										foreach($marks as $index4 => $row4){
											if($row4['parent_id'] == $index3){
												$data['my_marks'][$index]['parent'][$index1]['parent'][$index2]['parent'][$index3]['parent'][$index4] = $row4;
												unset($marks[$index4]);
				
												foreach($marks as $index5 => $row5){
													if($row5['parent_id'] == $index4){
														$data['my_marks'][$index]['parent'][$index1]['parent'][$index2]['parent'][$index3]['parent'][$index4]['parent'][$index5] = $row5;
														unset($marks[$index5]);
													
														// До 5 левела
														
													}
												}
												
											}
										}
										
									}
								}
								
							}
						}
						
					}
				}
			}
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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
			}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_sku'] = $this->url->link('catalog/product', 'sort=p.sku' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('catalog/product', 'sort=md.name' . $url, true);
		$data['sort_category'] = $this->url->link('catalog/product', 'sort=cd.name' . $url, true);
		$data['sort_mark'] = $this->url->link('catalog/product', 'sort=mode.name' . $url, true);
		$data['sort_weight'] = $this->url->link('catalog/product', 'sort=p.weight' . $url, true);
		$data['sort_condition'] = $this->url->link('catalog/product', 'sort=p.condition' . $url, true);
		
		
		$data['sort_product_id'] = $this->url->link('catalog/product', 'sort=p.product_id' . $url, true);
		$data['sort_name'] = $this->url->link('catalog/product', 'sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/product', 'sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/product', 'sort=p.price' . $url, true);
		$data['sort_cost'] = $this->url->link('catalog/product', 'sort=p.cost' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/product', 'sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/product', 'sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/product', 'sort=p.sort_order' . $url, true);

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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
			}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/product', $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;
	$data['filter_diagram'] = $filter_diagram;
	$data['filter_sku'] = $filter_sku;
	$data['filter_manufacturer'] = $filter_manufacturer;
	$data['filter_category'] = $filter_category;
	$data['filter_mark'] = $filter_mark;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/product_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			if (isset($this->request->get['filter_diagram'])) {
				$url .= '&filter_diagram=' . $this->request->get['filter_diagram'];
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
			'href' => $this->url->link('catalog/product', $url, true)
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('catalog/product/add', $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/product/edit', 'product_id=' . $this->request->get['product_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/product', $url, true);

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['diagram'])) {
			$data['diagram'] = $this->request->post['diagram'];
		} elseif (!empty($product_info)) {
			$data['diagram'] = $product_info['diagram'];
		} else {
			$data['diagram'] = 0;
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($product_info)) {
			$data['upc'] = $product_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($product_info)) {
			$data['ean'] = $product_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['year_manuf'])) {
			$data['year_manuf'] = $this->request->post['year_manuf'];
		} elseif (!empty($product_info)) {
			$data['year_manuf'] = $product_info['year_manuf'];
		} else {
			$data['year_manuf'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($product_info)) {
			$data['jan'] = $product_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($product_info)) {
			$data['isbn'] = $product_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($product_info)) {
			$data['mpn'] = $product_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
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

		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_catalog_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($product_info)) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($product_info)) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
		}

		if (isset($this->request->post['cost'])) {
			$data['cost'] = $this->request->post['cost'];
		} elseif (!empty($product_info)) {
			$data['cost'] = $product_info['cost'];
		} else {
			$data['cost'] = '';
		}

		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (isset($this->request->post['product_recurrings'])) {
			$data['product_recurrings'] = $this->request->post['product_recurrings'];
		} elseif (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_catalog_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($product_info)) {
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		$this->load->model('localisation/condition_status');

		$data['condition_statuses'] = $this->model_localisation_condition_status->getConditionStatuses();

		if (isset($this->request->post['condition_status_id'])) {
			$data['condition_status_id'] = $this->request->post['condition_status_id'];
		} elseif (!empty($product_info)) {
			$data['condition_status_id'] = $product_info['condition_status_id'];
		} else {
			$data['condition_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$data['weight'] = $product_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($product_info)) {
			$data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$data['length'] = $product_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$data['width'] = $product_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$data['height'] = $product_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($product_info)) {
			$data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['product_coordinate'])) {
			$data['product_coordinate'] = $this->request->post['product_coordinate'];
		} elseif (!empty($product_info)) {
			$data['product_coordinate'] = $this->model_catalog_product->getProductCoordinate($product_info['product_id']);;
		} else {
			$data['product_coordinate'] = array();
		}

		
		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

			// Categories
		$this->load->model('catalog/mark');

		if (isset($this->request->post['product_mark'])) {
			$marks = $this->request->post['product_mark'];
		} elseif (isset($this->request->get['product_id'])) {
			$marks = $this->model_catalog_product->getProductMarks($this->request->get['product_id']);
		} else {
			$marks = array();
		}

		$data['product_marks'] = array();

		foreach ($marks as $mark_id) {
			$mark_info = $this->model_catalog_mark->getMark($mark_id);
		
			if ($mark_info) {
				$data['product_marks'][] = array(
					'mark_id' => $mark_info['mark_id'],
					'name'        => ($mark_info['path']) ? $mark_info['path'] . ' &gt; ' . $mark_info['name'] : $mark_info['name']
				);
			}
		}


		
		// Filters
		$data['product_filters'] = array();

		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->post['product_attribute'])) {
			$product_attributes = $this->request->post['product_attribute'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}

		// Options
		$this->load->model('catalog/option');

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
				}
			}
		}

		$data['customer_groups'] = array();

		$data['product_discounts'] = array();

		$data['product_specials'] = array();

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}

		$data['is_image_large'] = false;
		
		if($data['image'] != ''){
			$imagesize = getimagesize(DIR_IMAGE . $data['image']);
			
			if($imagesize[0] > 600){
				$data['is_image_large'] = true;
			}
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
			$data['thumb_coordiname'] = $this->model_tool_image->resize($this->request->post['image'], 1000, 800);
		} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
			$data['thumb_coordiname'] = $this->model_tool_image->resize($product_info['image'], 1000, 800);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			$data['thumb_coordiname'] = $this->model_tool_image->resize('no_image.png', 1000, 800);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_images = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}

		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->post['product_download'])) {
			$product_downloads = $this->request->post['product_download'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_catalog_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['product_reward'])) {
			$data['product_reward'] = $this->request->post['product_reward'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_reward'] = $this->model_catalog_product->getProductRewards($this->request->get['product_id']);
		} else {
			$data['product_reward'] = array();
		}

		if (isset($this->request->post['product_seo_url'])) {
			$data['product_seo_url'] = $this->request->post['product_seo_url'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_seo_url'] = $this->model_catalog_product->getProductSeoUrls($this->request->get['product_id']);
		} else {
			$data['product_seo_url'] = array();
		}

		if (isset($this->request->post['product_layout'])) {
			$data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_catalog_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}

		
		$data['layouts'] = array();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/product_form', $data));
	}

	protected function validateForm() {
	
		foreach ($this->request->post['product_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		
		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			//$this->error['model'] = $this->language->get('error_model');
		}

	
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');
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
				$limit = 15;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);
	
			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				//echo $result['product_id'].'*'.$result['sku'];
				
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['mark_name'], ENT_QUOTES, 'UTF-8')) . ' ' . strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		
	public function product_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/product');
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
				$limit = 15;
			}

			$customer_id = (int)$this->customer->isLogged();
			
			if($customer_id < 1){
				$customer_id = -1;
			}
			
			
			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'customer_id' => $customer_id,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);
	
			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				//echo $result['product_id'].'*'.$result['sku'];
				
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['mark_name'], ENT_QUOTES, 'UTF-8')) . ' ' . strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
		
	public function mark_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/mark');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_mark->getMarks($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'mark_id' => $result['mark_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function category_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 25
			);

			$results = $this->model_catalog_product->getAdminCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function attribute_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/attribute');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 35
			);

			$results = $this->model_catalog_attribute->getAttributes($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'attribute_id'    => $result['attribute_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'attribute_group' => $result['attribute_group']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function manufacturer_autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/manufacturer');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
