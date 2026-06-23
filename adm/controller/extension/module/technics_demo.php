<?php
class ControllerExtensionModuleTechnicsDemo extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/technics_demo');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->installdemo();

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/technics_demo', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/technics_demo', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['button_install_demo'] = $this->language->get('button_install_demo');

		if (isset($this->request->post['module_technics_status'])) {
			$data['module_technics_status'] = $this->request->post['module_technics_status'];
		} else {
			$data['module_technics_status'] = $this->config->get('module_technics_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/technics_demo', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/technics_demo')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function installdemo() {
		
		$querys = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "manufacturer_description'");
		$pathls = DIR_APPLICATION . 'controller/extension/module/technics/demo/';

		if($querys->num_rows){ //ocStore
			if(phpversion() > '5.0' && phpversion() < '5.6'){ 
				require_once($pathls . 'ocstore_php54_55.php');
			}elseif(phpversion() >= '5.6' && phpversion() < '7.1'){ 
				require_once($pathls . 'ocstore_php56_70.php');
//			}elseif(phpversion() >= '7.1' && phpversion() < '7.3'){ 
//				require_once($pathls . 'ocstore_php71_72.php');
			}else{
//				require_once($pathls . 'ocstore_php73.php');
				require_once($pathls . 'ocstore_php71_74.php');
			}
		}else{ //OpenCart
			if(phpversion() > '5.0' && phpversion() < '5.6'){ 
				require_once($pathls . 'opencart_php54_55.php');
			}elseif(phpversion() >= '5.6' && phpversion() < '7.1'){ 
				require_once($pathls . 'opencart_php56_70.php');
//			}elseif(phpversion() >= '7.1' && phpversion() < '7.3'){ 
//				require_once($pathls . 'opencart_php71_72.php');
			}else{
//				require_once($pathls . 'opencart_php73.php');
				require_once($pathls . 'opencart_php71_74.php');
			}
		} 

	}	
}