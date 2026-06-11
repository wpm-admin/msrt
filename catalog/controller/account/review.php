<?php
class ControllerAccountReview extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/review');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/review', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['a_reviews'] = array();
		$data['r_reviews'] = array();

		$this->load->model('catalog/review');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$data['answers'] = $this->model_catalog_review->getAnswersOnCustomerId($this->customer->isLogged(), 1);
		$data['reviews'] = $this->model_catalog_review->getReviewsOnCustomerId($this->customer->isLogged(), 1);
		
			
		foreach ($data['answers'] as $result) {
			
			if((int)$result['type_id'] == 1){

				$key = 'consignment_id';
				$product_info = $this->model_catalog_product->getConsignment($result['product_id']);
				
				$images = explode(';', $product_info['images_normal']);
				
				$image = false;
				
				if(count($images) > 0){
					$image = $images[0];
				}
				
				if ($image) {
					$image = $this->model_tool_image->resize($image, 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
			}else{
				$key = 'product_id';
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
			}
			
			if(($result['answer'] <> '' AND (int)$result['read_quest'] == 0)){
				$data['a_reviews'][] = array(
					'review_id'   => $result['review_id'],
					'info'   => $result,
					'active'   => ($result['answer'] <> '' AND (int)$result['read_quest'] == 0) ? true : false,
					'image'       => $image,
					'product'     => $product_info,
					'href'       => $this->url->link('product/product', $key . '=' . $result['product_id'], true),
				);
			}
		}
		
		foreach ($data['reviews'] as $result) {
			
			if((int)$result['type_id'] == 1){

				$key = 'consignment_id';
				$product_info = $this->model_catalog_product->getConsignment($result['product_id']);
				
				$images = explode(';', $product_info['images_normal']);
				
				$image = false;
				
				if(count($images) > 0){
					$image = $images[0];
				}
				
				if ($image) {
					$image = $this->model_tool_image->resize($image, 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
			}else{
				$key = 'product_id';
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
			}
	
			if(((int)$result['read_customer'] == 0)){
				$data['r_reviews'][] = array(
					'review_id'   => $result['review_id'],
					'info'   => $result,
					'active'   => ((int)$result['read_customer'] == 0) ? true : false,
					'image'       => $image,
					'product'     => $product_info,
					'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id'], true),
				);
			}
		}
		
		$data['all_answer_href'] = $this->url->link('account/review/all_answer', '', true);
		$data['all_review_href'] = $this->url->link('account/review/all_review', '', true);
		$data['new_href'] = $this->url->link('account/review', '', true);
		
		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/review_list', $data));
	}
	
	public function all_review() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/review');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/review', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['a_reviews'] = array();
		$data['r_reviews'] = array();

		$this->load->model('catalog/review');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$data['reviews'] = $this->model_catalog_review->getReviewsOnCustomerId($this->customer->isLogged());
		
		foreach ($data['reviews'] as $result) {
			
			if((int)$result['type_id'] == 1){

				$product_info = $this->model_catalog_product->getConsignment($result['product_id']);
				
				$images = explode(';', $product_info['images_normal']);
				
				$image = false;
				
				if(count($images) > 0){
					$image = $images[0];
				}
				
				if ($image) {
					$image = $this->model_tool_image->resize($image, 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
		
					$data['r_reviews'][] = array(
						'review_id'   => $result['review_id'],
						'info'   => $result,
						'active'   => ((int)$result['read_customer'] == 0) ? true : false,
						'image'       => $image,
						'product'     => $product_info,
						'href'       => $this->url->link('product/product', 'consignment_id=' . $result['product_id'], true),
					);
				
				
			}else{
			
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
		
					$data['r_reviews'][] = array(
						'review_id'   => $result['review_id'],
						'info'   => $result,
						'active'   => ((int)$result['read_customer'] == 0) ? true : false,
						'image'       => $image,
						'product'     => $product_info,
						'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id'], true),
					);
			}
		}
		
		$data['all_answer_href'] = $this->url->link('account/review/all_answer', '', true);
		$data['all_review_href'] = $this->url->link('account/review/all_review', '', true);
		$data['new_href'] = $this->url->link('account/review', '', true);
		
		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/review_list', $data));
	}
	
	public function all_answer() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/review');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/review', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['a_reviews'] = array();
		$data['r_reviews'] = array();

		$this->load->model('catalog/review');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$data['answers'] = $this->model_catalog_review->getAnswersOnCustomerId($this->customer->isLogged());
		$data['answers'] = array_merge($data['answers'], $this->model_catalog_review->getAnswersOnCustomerId($this->customer->isLogged(), 1));
			
		foreach ($data['answers'] as $result) {
			
			if((int)$result['type_id'] == 1){

				$product_info = $this->model_catalog_product->getConsignment($result['product_id']);
				
				$images = explode(';', $product_info['images_normal']);
				
				$image = false;
				
				if(count($images) > 0){
					$image = $images[0];
				}
				
				if ($image) {
					$image = $this->model_tool_image->resize($image, 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
		
					$data['r_reviews'][] = array(
						'review_id'   => $result['review_id'],
						'info'   => $result,
						'active'   => ((int)$result['read_customer'] == 0) ? true : false,
						'image'       => $image,
						'product'     => $product_info,
						'href'       => $this->url->link('product/product', 'consignment_id=' . $result['product_id'], true),
					);
				
				
			}else{
			
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], 96, 66, 'product_list');
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 96, 66);
				}
		
					$data['r_reviews'][] = array(
						'review_id'   => $result['review_id'],
						'info'   => $result,
						'active'   => ((int)$result['read_customer'] == 0) ? true : false,
						'image'       => $image,
						'product'     => $product_info,
						'href'       => $this->url->link('product/product', 'product_id=' . $result['product_id'], true),
					);
			}
		}
		
		$data['all_answer_href'] = $this->url->link('account/review/all_answer', '', true);
		$data['all_review_href'] = $this->url->link('account/review/all_review', '', true);
		$data['new_href'] = $this->url->link('account/review', '', true);
		
		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/review_list', $data));
	}
	
	
	public function make_as_read(){
		
		$review_id = (int)$this->request->get['review_id'];
		$field = $this->db->escape($this->request->get['field']);
		
		$this->db->query("UPDATE " . DB_PREFIX . "review SET
							$field = '1'
							WHERE review_id = '" . (int)$review_id. "'");
			
	}
	
	public function write_answer(){
		
		$review_id = (int)$this->request->get['review_id'];
		$text = $this->db->escape($this->request->get['text']);
		
		$this->db->query("UPDATE " . DB_PREFIX . "review SET
							answer = '".$this->db->escape($text)."'
							WHERE review_id = '" . (int)$review_id. "'");

		
	}
	
	
	
}
