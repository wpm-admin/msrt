<?php
class ControllerCatalogBlogCategory extends Controller {
	private $error = array();

	public function index() {

		$this->createTables();
				
		$this->load->language('catalog/blog_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_category');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/blog_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_blog_category->addCategory($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/blog_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_blog_category->editCategory($this->request->get['blog_category_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/blog_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_category');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $category_id) {
				$this->model_catalog_blog_category->deleteCategory($category_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	public function repair() {
		$this->load->language('catalog/blog_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/blog_category');

		if ($this->validateRepair()) {
			$this->model_catalog_blog_category->repairCategories();

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/blog_category/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/blog_category/delete', 'token=' . $this->session->data['token'] . $url, true);
		$data['repair'] = $this->url->link('catalog/blog_category/repair', 'token=' . $this->session->data['token'] . $url, true);

		$data['categories'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$category_total = $this->model_catalog_blog_category->getTotalCategories();

		$results = $this->model_catalog_blog_category->getCategories($filter_data);

		foreach ($results as $result) {
			$data['categories'][] = array(
				'blog_category_id' => $result['blog_category_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'edit'        => $this->url->link('catalog/blog_category/edit', 'token=' . $this->session->data['token'] . '&blog_category_id=' . $result['blog_category_id'] . $url, true),
				'delete'      => $this->url->link('catalog/blog_category/delete', 'token=' . $this->session->data['token'] . '&blog_category_id=' . $result['blog_category_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');

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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog_catalog/blog_category_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['blog_category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_parent'] = $this->language->get('entry_parent');
		$data['entry_filter'] = $this->language->get('entry_filter');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_top'] = $this->language->get('entry_top');
		$data['entry_column'] = $this->language->get('entry_column');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_layout'] = $this->language->get('entry_layout');

		$data['help_filter'] = $this->language->get('help_filter');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_top'] = $this->language->get('help_top');
		$data['help_column'] = $this->language->get('help_column');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_design'] = $this->language->get('tab_design');

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

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->error['parent'])) {
			$data['error_parent'] = $this->error['parent'];
		} else {
			$data['error_parent'] = '';
		}
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
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
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['blog_category_id'])) {
			$data['action'] = $this->url->link('catalog/blog_category/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/blog_category/edit', 'token=' . $this->session->data['token'] . '&blog_category_id=' . $this->request->get['blog_category_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/blog_category', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['blog_category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$category_info = $this->model_catalog_blog_category->getCategory($this->request->get['blog_category_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['category_description'])) {
			$data['category_description'] = $this->request->post['category_description'];
		} elseif (isset($this->request->get['blog_category_id'])) {
			$data['category_description'] = $this->model_catalog_blog_category->getCategoryDescriptions($this->request->get['blog_category_id']);
		} else {
			$data['category_description'] = array();
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($category_info)) {
			$data['path'] = $category_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($category_info)) {
			$data['parent_id'] = $category_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		$this->load->model('catalog/filter');

		if (isset($this->request->post['category_filter'])) {
			$filters = $this->request->post['category_filter'];
		} elseif (isset($this->request->get['category_id'])) {
			$filters = $this->model_catalog_blog_category->getCategoryFilters($this->request->get['blog_category_id']);
		} else {
			$filters = array();
		}

		$data['category_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['category_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['category_store'])) {
			$data['category_store'] = $this->request->post['category_store'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_store'] = $this->model_catalog_blog_category->getCategoryStores($this->request->get['category_id']);
		} else {
			$data['category_store'] = array(0);
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($category_info)) {
			$data['keyword'] = $category_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['blog_template'])) {
			$data['blog_template'] = $this->request->post['blog_template'];
		} elseif (!empty($category_info)) {
			$data['blog_template'] = $category_info['blog_template'];
		} else {
			$data['blog_template'] = '';
		}

		if (isset($this->request->post['template'])) {
			$data['template'] = $this->request->post['template'];
		} elseif (!empty($category_info)) {
			$data['template'] = $category_info['template'];
		} else {
			$data['template'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($category_info)) {
			$data['image'] = $category_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($category_info)) {
			$data['top'] = $category_info['top'];
		} else {
			$data['top'] = 0;
		}

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($category_info)) {
			$data['column'] = $category_info['column'];
		} else {
			$data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($category_info)) {
			$data['sort_order'] = $category_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($category_info)) {
			$data['status'] = $category_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['category_layout'])) {
			$data['category_layout'] = $this->request->post['category_layout'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_layout'] = $this->model_catalog_blog_category->getCategoryLayouts($this->request->get['blog_category_id']);
		} else {
			$data['category_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('blog_catalog/blog_category_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/blog_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['category_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (isset($this->request->get['blog_category_id']) && $this->request->post['parent_id']) {
			$results = $this->model_catalog_blog_category->getCategoryPath($this->request->post['parent_id']);
			
			foreach ($results as $result) {
				if ($result['path_id'] == $this->request->get['blog_category_id']) {
					$this->error['parent'] = $this->language->get('error_parent');
					
					break;
				}
			}
		}

		if (utf8_strlen($this->request->post['keyword']) > 0) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['category_id']) && $url_alias_info['query'] != 'blog_category_id=' . $this->request->get['blog_category_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['blog_category_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/blog_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'catalog/blog_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/blog_category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_catalog_blog_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'blog_category_id' => $result['blog_category_id'],
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
	
	public function createTables(){
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category` (
				`blog_category_id` int(11) NOT NULL AUTO_INCREMENT,
				`image` varchar(255) DEFAULT NULL,
				`template` varchar(255) DEFAULT NULL,
				`blog_template` varchar(255) DEFAULT NULL,
				`parent_id` int(11) NOT NULL DEFAULT '0',
				`top` tinyint(1) NOT NULL,
				`column` int(3) NOT NULL,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				`status` tinyint(1) NOT NULL,
				`date_added` datetime NOT NULL,
				`date_modified` datetime NOT NULL,
				PRIMARY KEY (`blog_category_id`),
				KEY `parent_id` (`parent_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "category_description` (
				`blog_category_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`name` varchar(255) NOT NULL,
				`description` text NOT NULL,
				`meta_title` varchar(255) NOT NULL,
				`meta_description` varchar(255) NOT NULL,
				`meta_keyword` varchar(255) NOT NULL,
				PRIMARY KEY (`blog_category_id`,`language_id`),
				KEY `name` (`name`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);

		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category_filter` (
				  `blog_category_id` int(11) NOT NULL,
				  `filter_id` int(11) NOT NULL,
				  PRIMARY KEY (`blog_category_id`,`filter_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);



		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category_path` (
				`blog_category_id` int(11) NOT NULL,
				`path_id` int(11) NOT NULL,
				`level` int(11) NOT NULL,
				PRIMARY KEY (`blog_category_id`,`path_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category_to_layout` (
				`blog_category_id` int(11) NOT NULL,
				`store_id` int(11) NOT NULL,
				`layout_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_category_id`,`store_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_category_to_store` (
				`blog_category_id` int(11) NOT NULL,
				`store_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_category_id`,`store_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product` (
				`blog_product_id` int(11) NOT NULL AUTO_INCREMENT,
				`template` varchar(255) DEFAULT NULL,
				`model` varchar(64) NOT NULL,
				`sku` varchar(64) NOT NULL,
				`upc` varchar(12) NOT NULL,
				`ean` varchar(14) NOT NULL,
				`jan` varchar(13) NOT NULL,
				`isbn` varchar(17) NOT NULL,
				`mpn` varchar(64) NOT NULL,
				`location` varchar(128) NOT NULL,
				`quantity` int(4) NOT NULL DEFAULT '0',
				`stock_status_id` int(11) NOT NULL,
				`image` varchar(255) DEFAULT NULL,
				`manufacturer_id` int(11) NOT NULL,
				`shipping` tinyint(1) NOT NULL DEFAULT '1',
				`price` decimal(15,4) NOT NULL DEFAULT '0.0000',
				`points` int(8) NOT NULL DEFAULT '0',
				`tax_class_id` int(11) NOT NULL,
				`date_available` date NOT NULL DEFAULT '0000-00-00',
				`weight` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				`weight_class_id` int(11) NOT NULL DEFAULT '0',
				`length` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				`width` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				`height` decimal(15,8) NOT NULL DEFAULT '0.00000000',
				`length_class_id` int(11) NOT NULL DEFAULT '0',
				`subtract` tinyint(1) NOT NULL DEFAULT '1',
				`minimum` int(11) NOT NULL DEFAULT '1',
				`sort_order` int(11) NOT NULL DEFAULT '0',
				`status` tinyint(1) NOT NULL DEFAULT '0',
				`viewed` int(5) NOT NULL DEFAULT '0',
				`date_added` datetime NOT NULL,
				`date_modified` datetime NOT NULL,
				PRIMARY KEY (`blog_product_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_attribute` (
				`blog_product_id` int(11) NOT NULL,
				`attribute_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`text` text NOT NULL,
				PRIMARY KEY (`blog_product_id`,`attribute_id`,`language_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_description` (
				`blog_product_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`name` varchar(255) NOT NULL,
				`description` text NOT NULL,
				`tag` text NOT NULL,
				`meta_title` varchar(255) NOT NULL,
				`meta_description` varchar(255) NOT NULL,
				`meta_keyword` varchar(255) NOT NULL,
				PRIMARY KEY (`blog_product_id`,`language_id`),
				KEY `name` (`name`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_discount` (
				`blog_product_discount_id` int(11) NOT NULL AUTO_INCREMENT,
				`blog_product_id` int(11) NOT NULL,
				`customer_group_id` int(11) NOT NULL,
				`quantity` int(4) NOT NULL DEFAULT '0',
				`priority` int(5) NOT NULL DEFAULT '1',
				`price` decimal(15,4) NOT NULL DEFAULT '0.0000',
				`date_start` date NOT NULL DEFAULT '0000-00-00',
				`date_end` date NOT NULL DEFAULT '0000-00-00',
				PRIMARY KEY (`blog_product_discount_id`),
				KEY `blog_product_id` (`blog_product_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_filter` (
				`blog_product_id` int(11) NOT NULL,
				`filter_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_product_id`,`filter_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_image` (
				`blog_product_image_id` int(11) NOT NULL AUTO_INCREMENT,
				`blog_product_id` int(11) NOT NULL,
				`image` varchar(255) DEFAULT NULL,
				`text1` varchar(255) DEFAULT NULL,
				`text2` varchar(255) DEFAULT NULL,
				`text3` varchar(255) DEFAULT NULL,
				`sort_order` int(3) NOT NULL DEFAULT '0',
				PRIMARY KEY (`blog_product_image_id`),
				KEY `blog_product_id` (`blog_product_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_option` (
				`blog_product_option_id` int(11) NOT NULL AUTO_INCREMENT,
				`blog_product_id` int(11) NOT NULL,
				`option_id` int(11) NOT NULL,
				`value` text NOT NULL,
				`required` tinyint(1) NOT NULL,
				PRIMARY KEY (`blog_product_option_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_option_value` (
				`blog_product_option_value_id` int(11) NOT NULL AUTO_INCREMENT,
				`blog_product_option_id` int(11) NOT NULL,
				`blog_product_id` int(11) NOT NULL,
				`option_id` int(11) NOT NULL,
				`option_value_id` int(11) NOT NULL,
				`quantity` int(3) NOT NULL,
				`subtract` tinyint(1) NOT NULL,
				`price` decimal(15,4) NOT NULL,
				`price_prefix` varchar(1) NOT NULL,
				`points` int(8) NOT NULL,
				`points_prefix` varchar(1) NOT NULL,
				`weight` decimal(15,8) NOT NULL,
				`weight_prefix` varchar(1) NOT NULL,
				PRIMARY KEY (`blog_product_option_value_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_recurring` (
				`blog_product_id` int(11) NOT NULL,
				`recurring_id` int(11) NOT NULL,
				`customer_group_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_product_id`,`recurring_id`,`customer_group_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_related` (
				`blog_product_id` int(11) NOT NULL,
				`related_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_product_id`,`related_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_reward` (
				`blog_product_reward_id` int(11) NOT NULL AUTO_INCREMENT,
				`blog_product_id` int(11) NOT NULL DEFAULT '0',
				`customer_group_id` int(11) NOT NULL DEFAULT '0',
				`points` int(8) NOT NULL DEFAULT '0',
				PRIMARY KEY (`blog_product_reward_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_special` (
				`blog_product_special_id` int(11) NOT NULL AUTO_INCREMENT,
				`blog_product_id` int(11) NOT NULL,
				`customer_group_id` int(11) NOT NULL,
				`priority` int(5) NOT NULL DEFAULT '1',
				`price` decimal(15,4) NOT NULL DEFAULT '0.0000',
				`date_start` date NOT NULL DEFAULT '0000-00-00',
				`date_end` date NOT NULL DEFAULT '0000-00-00',
				PRIMARY KEY (`blog_product_special_id`),
				KEY `product_id` (`blog_product_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_to_category` (
				`blog_product_id` int(11) NOT NULL,
				`blog_category_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_product_id`,`blog_category_id`),
				KEY `blog_category_id` (`blog_category_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_to_download` (
				`blog_product_id` int(11) NOT NULL,
				`download_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_product_id`,`download_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_to_layout` (
				`blog_product_id` int(11) NOT NULL,
				`store_id` int(11) NOT NULL,
				`layout_id` int(11) NOT NULL,
				PRIMARY KEY (`blog_product_id`,`store_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		

		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_product_to_store` (
				`blog_product_id` int(11) NOT NULL,
				`store_id` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`blog_product_id`,`store_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog_review` (
				`blog_review_id` int(11) NOT NULL AUTO_INCREMENT,
				`blog_product_id` int(11) NOT NULL,
				`customer_id` int(11) NOT NULL,
				`author` varchar(64) NOT NULL,
				`text` text NOT NULL,
				`rating` int(1) NOT NULL,
				`status` tinyint(1) NOT NULL DEFAULT '0',
				`date_added` datetime NOT NULL,
				`date_modified` datetime NOT NULL,
				PRIMARY KEY (`blog_review_id`),
				KEY `product_id` (`blog_product_id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		
		
	}
	
}

