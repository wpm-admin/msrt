<?php
class ControllerReportAbandonedCarts extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('report/abandoned_carts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/abandoned_carts');

		$this->getList();
	}

	public function create_order(){
	
			if(!isset($this->request->get['customer_id'])){
				return false;
			}
			
			$this->load->model('extension/module/abandoned_carts');
			
			$customer_id = (int)$this->request->get['customer_id'];
			
			$cart_info = $this->model_extension_module_abandoned_carts->getOrderOnCustomerId($customer_id);
			
			$order_data = array();


			$this->load->language('checkout/checkout');

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}
			}
			
			$this->load->model('customer/customer');

			$customer_info = $this->model_customer_customer->getCustomer($customer_id);
			$customer_address = $this->model_customer_customer->getAddress($customer_info['address_id']);

			$order_data['customer_id'] = $customer_id;
			$order_data['customer_group_id'] = $customer_info['customer_group_id'];
			$order_data['firstname'] = $customer_info['firstname'];
			$order_data['lastname'] = $customer_info['lastname'];
			$order_data['email'] = $customer_info['email'];
			$order_data['telephone'] = $customer_info['telephone'];
			$order_data['custom_field'] = '';
	

	
			$order_data['payment_firstname'] = $customer_info['firstname'];
			$order_data['payment_lastname'] = $customer_info['lastname'];
			$order_data['payment_company'] = $customer_info['company'];
			$order_data['payment_address_1'] = $customer_address['address_1'];
			$order_data['payment_address_2'] = $customer_address['address_2'];
			$order_data['payment_city'] = $customer_address['city'];
			$order_data['payment_postcode'] = $customer_address['postcode'];
			$order_data['payment_zone'] = $customer_address['zone'];
			$order_data['payment_zone_id'] = $customer_address['zone_id'];
			$order_data['payment_country'] = $customer_address['country'];
			$order_data['payment_country_id'] = $customer_address['country_id'];
			$order_data['payment_address_format'] =$customer_address['address_format'];
			$order_data['payment_custom_field'] = array();

			$order_data['payment_method'] = '';
			$order_data['payment_code'] = '';
	
			$order_data['shipping_firstname'] = $customer_info['firstname'];
			$order_data['shipping_lastname'] = $customer_info['lastname'];
			$order_data['shipping_company'] = $customer_info['company'];
			$order_data['shipping_address_1'] = $customer_address['address_1'];
			$order_data['shipping_address_2'] = $customer_address['address_2'];
			$order_data['shipping_city'] = $customer_address['city'];
			$order_data['shipping_postcode'] = $customer_address['postcode'];
			$order_data['shipping_zone'] = $customer_address['zone'];
			$order_data['shipping_zone_id'] = $customer_address['zone_id'];
			$order_data['shipping_country'] = $customer_address['country'];
			$order_data['shipping_country_id'] = $customer_address['country_id'];
			$order_data['shipping_address_format'] = $customer_address['address_format'];
			$order_data['shipping_custom_field'] = array();

			$order_data['shipping_method'] = '';
			$order_data['shipping_code'] = '';


			$order_data['products'] = array();
			$total_data['total'] = 0;
			$products = $this->model_extension_module_abandoned_carts->getOrderProducts($cart_info['session_id']);
			
			foreach ($products as $product) {
				$option_data = array();

				$order_data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'price'      => $product['price'],
					'total'      => $product['price'] * $product['quantity'],
					'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
				
				$total_data['total'] += $product['price'] * $product['quantity'];
			}

			// Gift Voucher

			$order_data['comment'] = 'Abandoned cart';
			$order_data['total'] = $total_data['total'];

			$order_data['affiliate_id'] = 0;
			$order_data['commission'] = 0;
			$order_data['marketing_id'] = 0;
			$order_data['tracking'] = '';
		
		
			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->config->get('config_currency'));
			$order_data['currency_code'] = $this->config->get('config_currency');
			$order_data['currency_value'] = $this->currency->getValue($this->config->get('config_currency'));
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			$order_data['forwarded_ip'] = '';

			$order_data['user_agent'] = '';

			$order_data['accept_language'] = '';

			$totals[] = array(
							  'code' => 'sub_total',
							  'title' => 'Sub-Total',
							  'value' => $total_data['total'],
							  'sort_order' => '1',
							  );
			
			$totals[] = array(
							  'code' => 'total',
							  'title' => 'Total',
							  'value' => $total_data['total'],
							  'sort_order' => '9',
							  );
			
			$order_data['totals'] = $totals;
			
			$order_id = $this->model_extension_module_abandoned_carts->addOrder($order_data);
			
			//$this->model_extension_module_abandoned_carts->deleteCart($cart_info['session_id']);
			
			$this->response->redirect($this->url->link('sale/orderpro/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true));

	}
	
	public function recover() {
		$this->load->language('report/abandoned_carts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/abandoned_carts');

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $order_id) {
				$this->model_extension_module_abandoned_carts->recoverEmail($order_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			$this->response->redirect($this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['recover'] = $this->url->link('report/abandoned_carts/recover', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete']  = $this->url->link('report/abandoned_carts/delete', 'user_token=' . $this->session->data['user_token'], true);
		$data['orders']  = array();

		$filter_data = array(
			'days'  => $this->config->get('abandoned_carts_limit'),
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_extension_module_abandoned_carts->getTotalOrders($filter_data);

		$results     = $this->model_extension_module_abandoned_carts->getOrders($filter_data);

		foreach ($results as $result) {
			//$existing_carts = $this->model_extension_module_abandoned_carts->checkDuplicates($result['ip']);

			$products = $this->model_extension_module_abandoned_carts->getOrderProducts($result['session_id']);
			
			$total = 0;
			foreach($products as $index => $row){
				$products[$index]['price_format'] = $this->currency->format($row['price'], $this->config->get('config_currency'), 1);
				$products[$index]['total_format'] = $this->currency->format($row['price'] * $row['quantity'], $this->config->get('config_currency'), 1);
				$total += $row['price'] * $row['quantity'];
			}
			
			
			$data['orders'][] = array(
				'session_id'        => $result['session_id'],
				'customer'        => $result['customer'],
				'customer_id'        => $result['customer_id'],
				'telephone'        => $result['telephone'],
				'email'        => $result['email'],
				'products'        => $products,
				'customer_href'        => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'] . $url, true),
				'total'        => $this->currency->format($total, $this->config->get('config_currency'), 1),
				'date_added'      => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			);
			
		}

		$data['heading_title']          = $this->language->get('heading_title');

		$data['text_list']              = $this->language->get('text_list');
		$data['text_no_results']        = $this->language->get('text_no_results');
		$data['text_confirm']           = $this->language->get('text_confirm');
		$data['text_success']           = $this->language->get('text_success');

		$data['column_order_id']        = $this->language->get('column_order_id');
		$data['column_customer']        = $this->language->get('column_customer');
		$data['column_status']          = $this->language->get('column_status');
		$data['column_total']           = $this->language->get('column_total');
		$data['column_date_added']      = $this->language->get('column_date_added');
		$data['column_date_modified']   = $this->language->get('column_date_modified');
		$data['column_abandoned']       = $this->language->get('column_abandoned');
		$data['column_action']          = $this->language->get('column_action');

		$data['button_recover']         = $this->language->get('button_recover');
		$data['button_delete']          = $this->language->get('button_delete');
		$data['button_view']            = $this->language->get('button_view');

		$data['user_token']                  = $this->session->data['user_token'];

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

		$data['sort_order']         = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer']      = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_status']        = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total']         = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added']    = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);
		$data['sort_abandoned']     = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . '&sort=o.abandoned' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination         = new Pagination();
		$pagination->total  = $order_total;
		$pagination->page   = $page;
		$pagination->limit  = $this->config->get('config_limit_admin');
		$pagination->url    = $this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		$data['results']    = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['sort']       = $sort;
		$data['order']      = $order;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['header']         = $this->load->controller('common/header');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['footer']         = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/abandoned_carts', $data));
	}


	public function delete() {
		$this->load->language('report/abandoned_carts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/abandoned_carts');

		
		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $order_session_id) {
				$this->model_extension_module_abandoned_carts->deleteCart($order_session_id);
			}

			$this->session->data['success'] = $this->language->get('text_deleted');

			$this->response->redirect($this->url->link('report/abandoned_carts', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'report/abandoned_carts')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
