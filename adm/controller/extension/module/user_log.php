<?php
class ControllerExtensionModuleUserLog extends Controller {
	private $error = array();
	private $data = array();

	private $path_module = 'extension/module/user_log';
	private $module_name ='user_log';
	private $my_model ='model_extension_module_user_log';
	private $extension_prefix ='module_';
	private $path_extension ='marketplace/extension&type=module';
	private $token = 'user_token';
	
	public function index() {
		$this->load->language($this->path_module);
		$this->load->model($this->path_module);
		$this->document->addStyle('view/stylesheet/user_log.css?ver=1');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('user_log', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
        	if (isset($this->request->get['apply'])) {
				$this->response->redirect($this->makeUrl($this->path_module));
			} else {
				$this->response->redirect($this->makeUrl($this->path_extension, 'type=module'));
			}
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] =  $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] =  false;
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_form'] = $this->language->get('text_form');

		$this->data['tab_settings'] = $this->language->get('tab_settings');
		$this->data['tab_help'] = $this->language->get('tab_help');

		$this->data['text_description'] = $this->language->get('text_description');
		$this->data['text_help'] = $this->language->get('text_help');

		$this->data['button_save_quit'] = $this->language->get('button_save_quit');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_clear'] = $this->language->get('button_clear');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_view_log'] = $this->language->get('button_view_log');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['entry_user_log_enable']  = $this->language->get('entry_user_log_enable');
		$this->data['entry_user_log_enable_help']  = $this->language->get('entry_user_log_enable_help');
		$this->data['entry_user_log_login']   = $this->language->get('entry_user_log_login');
		$this->data['entry_user_log_login_help']   = $this->language->get('entry_user_log_login_help');
		$this->data['entry_user_log_logout']  = $this->language->get('entry_user_log_logout');
		$this->data['entry_user_log_logout_help']  = $this->language->get('entry_user_log_logout_help');
		$this->data['entry_user_log_failedlog']  = $this->language->get('entry_user_log_failedlog');
		$this->data['entry_user_log_failedlog_help']  = $this->language->get('entry_user_log_failedlog_help');
		$this->data['entry_user_log_access']  = $this->language->get('entry_user_log_access');
		$this->data['entry_user_log_access_help']  = $this->language->get('entry_user_log_access_help');
		$this->data['entry_user_log_modify']  = $this->language->get('entry_user_log_modify');
		$this->data['entry_user_log_modify_help']  = $this->language->get('entry_user_log_modify_help');
		$this->data['entry_user_log_allowed'] = $this->language->get('entry_user_log_allowed');
		$this->data['entry_user_log_display'] = $this->language->get('entry_user_log_display');
		$this->data['entry_log_day'] = $this->language->get('entry_log_day');

		$this->data['text_enabled']  = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['text_denied']  = $this->language->get('text_denied');
		$this->data['text_allowed'] = $this->language->get('text_allowed');
		$this->data['text_all'] = $this->language->get('text_all');
		$this->data['text_select_all'] = $this->language->get('text_select_all');
		$this->data['text_unselect_all'] = $this->language->get('text_unselect_all');

		$this->data['entry_user_log_failed_mail'] = $this->language->get('entry_user_log_failed_mail');
		$this->data['text_user_log_failed_mailaddress'] = $this->language->get('entry_user_log_failed_mail');
		$this->data['entry_user_log_ban'] = $this->language->get('entry_user_log_ban');
		$this->data['entry_user_log_ban_count'] = $this->language->get('entry_user_log_ban_count');
		$this->data['entry_user_log_ban_day'] = $this->language->get('entry_user_log_ban_day');


		if (isset($this->request->post['user_log_failed_mail'])) {
			$this->data['user_log_failed_mail'] = $this->request->post['user_log_failed_mail'];
		} else {
			$this->data['user_log_failed_mail'] = $this->config->get('user_log_failed_mail');
		}
		if (isset($this->request->post['user_log_ban'])) {
			$this->data['user_log_ban'] = $this->request->post['user_log_ban'];
		} else {
			$this->data['user_log_ban'] = $this->config->get('user_log_ban');
		}
		if (isset($this->request->post['user_log_ban_period'])) {
			$this->data['user_log_ban_period'] = $this->request->post['user_log_ban_period'];
		} else {
			$this->data['user_log_ban_period'] = ($this->config->get('user_log_ban_period'))?$this->config->get('user_log_ban_period'):5;
		}

		$this->data['interval_types'] = array(
			'HOUR' => $this->language->get('text_hours'),
			'DAY' => $this->language->get('text_days'),
			'MINUTE' => $this->language->get('text_minute'),
		);

		if (isset($this->request->post['user_log_interval_type'])) {
			$this->data['user_log_interval_type'] = $this->request->post['user_log_interval_type'];
		} else {
			$this->data['user_log_interval_type'] = $this->config->get('user_log_interval_type')?$this->config->get('user_log_interval_type'):3;
		}

		if (isset($this->request->post['user_log_ban_count'])) {
			$this->data['user_log_ban_count'] = $this->request->post['user_log_ban_count'];
		} else {
			$this->data['user_log_ban_count'] = $this->config->get('user_log_ban_count')?$this->config->get('user_log_ban_count'):3;
		}

		if (isset($this->request->post['user_log_failed_mailaddress'])) {
			$this->data['user_log_failed_mailaddress'] = $this->request->post['user_log_failed_mailaddress'];
		} else {
			$this->data['user_log_failed_mailaddress'] = $this->config->get('user_log_failed_mailaddress');
		}

		if (isset($this->request->post['user_log_enable'])) {
			$this->data['user_log_enable'] = $this->request->post['user_log_enable'];
		} else {
			$this->data['user_log_enable'] = $this->config->get('user_log_enable');
		}

		if (isset($this->request->post['user_log_day'])) {
			$this->data['user_log_day'] = $this->request->post['user_log_day'];
		} else {
			$this->data['user_log_day'] = $this->config->get('user_log_day');
		}
		
		if (isset($this->request->post['user_log_login'])) {
			$this->data['user_log_login'] = $this->request->post['user_log_login'];
		} else {
			$this->data['user_log_login'] = $this->config->get('user_log_login');
		}

		if (isset($this->request->post['user_log_logout'])) {
			$this->data['user_log_logout'] = $this->request->post['user_log_logout'];
		} else {
			$this->data['user_log_logout'] = $this->config->get('user_log_logout');
		}

		if (isset($this->request->post['user_log_failed'])) {
			$this->data['user_log_failed'] = $this->request->post['user_log_failed'];
		} else {
			$this->data['user_log_failed'] = $this->config->get('user_log_failed');
		}

		if (isset($this->request->post['user_log_access'])) {
			$this->data['user_log_access'] = $this->request->post['user_log_access'];
		} else {
			$this->data['user_log_access'] = $this->config->get('user_log_access');
		}

		if (isset($this->request->post['user_log_access_list'])) {
			$this->data['user_log_access_list'] = $this->request->post['user_log_access_list'];
		} elseif ($this->config->has('user_log_access_list')) {
			$this->data['user_log_access_list'] = $this->config->get('user_log_access_list');
		} else {
			$this->data['user_log_access_list'] = array();
		}
		
		if (isset($this->request->post['user_log_modify'])) {
			$this->data['user_log_modify'] = $this->request->post['user_log_modify'];
		} else {
			$this->data['user_log_modify'] = $this->config->get('user_log_modify');
		}

		if (isset($this->request->post['user_log_modify_list'])) {
			$this->data['user_log_modify_list'] = $this->request->post['user_log_modify_list'];
		} elseif ($this->config->has('user_log_modify_list')) {
			$this->data['user_log_modify_list'] = $this->config->get('user_log_modify_list');
		} else {
			$this->data['user_log_modify_list'] = array();
		}

		if (isset($this->request->post['user_log_allowed'])) {
			$this->data['user_log_allowed'] = $this->request->post['user_log_allowed'];
		} else {
			$this->data['user_log_allowed'] = $this->config->get('user_log_allowed');
		}

		if (isset($this->request->post['user_log_display'])) {
			$this->data['user_log_display'] = $this->request->post['user_log_display'];
		} elseif ($this->config->get('user_log_display')) {
			$this->data['user_log_display'] = $this->config->get('user_log_display');
		} else {
			$this->data['user_log_display'] = 50;
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		$this->data['enabled_disabled'] = array(
			0 => $this->language->get('text_disabled'),
			1 => $this->language->get('text_enabled'),
		);
		$this->load->model('user/user');
		$this->data['users'] = $this->model_user_user->getUsers(); 
		$this->breadcrumbs($this->data);

		$this->data['action_apply'] = $this->makeUrl($this->path_module, 'apply=1');
		$this->data['action'] = $this->makeUrl($this->path_module);
		$this->data['view_log'] = $this->makeUrl($this->path_module . '/view_log');
		
		$this->data['cancel'] = $this->makeUrl($this->path_extension);
		$this->nav($this->path_module,$this->data);
		$this->footer('_main', $this->data);
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', $this->path_module)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function install(){
		$this->load->model($this->path_module);
		$this->{$this->my_model}->install();
	}

	public function uninstall(){
		$this->load->model($this->path_module);
		$this->{$this->my_model}->uninstall();
	}

	private function makeUrl($route, $arg=''){
		if ($arg) {
			$arg = '&' . ltrim($arg,'&');
		}
		return $this->url->link($route, $this->token . '=' . $this->session->data[$this->token] . $arg, true);
	}

	private function makeUrlScript($route, $arg=''){
		return str_replace('&amp;','&',$this->makeUrl($route, $arg));
	}

	private function footer($template, $data) {
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view($this->path_module . '/' . $this->module_name . $template, $data));
	}
	
	public function addToBanIp() {
		if ($this->validate()) {
			if (isset($this->request->get['ip'])) {
				$this->load->model($this->path_module);
				$this->model_extension_module_user_log->addBanIp($this->request->post['ip']);
			}
		}
	}
	public function ban_clear() {
		if ($this->validate()) {
			$this->load->model($this->path_module);
			$this->load->language($this->path_module);
			$this->session->data['success'] = $this->language->get('text_clear');
			$this->model_extension_module_user_log->clearBanIp();
		}
		$this->response->redirect($this->makeUrl($this->path_module . '/ban'));
	}

	public function ban_delete() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model($this->path_module);
			$this->load->language($this->path_module);
			if (isset($this->request->post['selected'])) {
				
				foreach ($this->request->post['selected'] as $record) {
					$this->model_extension_module_user_log->deleteBanIp($record);
	  			}
				$this->session->data['success'] = $this->language->get('text_ban_delete');
			}
		}
		
		$this->response->redirect($this->makeUrl($this->path_module . '/ban'));
	}

	public function ban() {
		$this->load->language($this->path_module);
		$this->load->model($this->path_module);
		$this->document->addStyle('view/stylesheet/user_log.css?ver=1');

		$this->document->setTitle(strip_tags($this->language->get('heading_ban')));

		$this->load->model('setting/setting');

		if (isset($this->session->data['success'])) {
			$this->data['success'] =  $this->session->data['success'];
			unset($this->session->data['success']);
		}

		if (isset($this->session->data['warnig'])) {
			$this->data['warnig'] =  $this->session->data['warnig'];
			unset($this->session->data['warnig']);
		}
		
		$limit = $this->config->get('user_log_display')?$this->config->get('user_log_display'):$this->config->get('config_limit_admin');

  		$records = $this->model_extension_module_user_log->getBanIps();
		
		$this->data['records'] = array();
		foreach ($records as $record) {
			$ips = explode(',',$record['ip']);
			$ip_records = array();
			foreach ($ips as $ip) {
				$ip_records[trim($ip)] = $ip;
			}
			$record['ip'] = implode(', ', $ip_records);
			
      		$this->data['records'][] = array(
      			'log_ban_id' => $record['log_ban_id'],
      			'ip'	    => $record['ip'],
			);
    	}
		
  		$this->breadcrumbs($this->data);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_ban'),
			'href'      => $this->makeUrl($this->path_module . '/ban')
   		);

		$this->data['button_settings'] = $this->language->get('heading_title');
		$this->data['action_settings'] = $this->makeUrl($this->path_module);
		$this->data['clear_action'] =    $this->makeUrl($this->path_module . '/ban_clear');
		$this->data['delete_action'] =   $this->makeUrl($this->path_module . '/ban_delete');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		$this->nav($this->path_module . '/ban', $this->data);

		$this->footer('_ban', $this->data);
	}

	private function nav($route,&$data) {
		$data['navs'] = array();

		if ($route == $this->path_module) $active = 'active'; else $active = '';

		$data['navs'][] = array(
			'href' => $this->makeUrl($this->path_module),
			'text' => $this->language->get('nav_settings'),
			'active' => $active
		);

		if ($route == $this->path_module . '/view_log') $active = 'active'; else $active = '';
		$data['navs'][] = array(
			'href' => $this->makeUrl($this->path_module . '/view_log'),
			'text' => $this->language->get('nav_view'),
			'active' => $active
		);
		if ($route == $this->path_module . '/ban') $active = 'active'; else $active = '';
		$data['navs'][] = array(
			'href' => $this->makeUrl($this->path_module . '/ban'),
			'text' => $this->language->get('nav_ban'),
			'active' => $active
		);
	}

	private function breadcrumbs(&$data) {
		
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->makeUrl('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->makeUrl($this->path_extension)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->makeUrl($this->path_module)
        );
	}
	public function clear() {
		if ($this->validate()) {
			$this->load->model($this->path_module);
			$this->load->language($this->path_module);
			$this->session->data['success'] = $this->language->get('text_clear');
			$this->model_extension_module_user_log->clear();
		}
		$this->response->redirect($this->makeUrl($this->path_module . '/view_log'));
	}
	
	public function delete() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model($this->path_module);
			$this->load->language($this->path_module);
			if (isset($this->request->post['selected'])) {
				
				foreach ($this->request->post['selected'] as $record) {
					$this->model_extension_module_user_log->deleteRecord($record);
	  			}
				$count = count($this->request->post['selected']);

	  			$this->user->addLog(array(
					'action' 	=> 'delete record (' . $count . ') user:' . $this->user->getId(), 
					'result' 	=> 1)
				);

				$this->session->data['success'] = $this->language->get('text_delete') . $count;
			}
		}

		$url = '';
		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
		}
		if (isset($this->request->get['filter_start'])) {
			$url .= '&filter_start=' . $this->request->get['filter_start'];
		}
		if (isset($this->request->get['filter_end'])) {
			$url .= '&filter_end=' . $this->request->get['filter_end'];
		}
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}
		if (isset($this->request->get['filter_url'])) {
			$url .= '&filter_url=' . $this->request->get['filter_url'];
		}
	
		$this->response->redirect($this->makeUrl($this->path_module . '/view_log', $url));
	}
	
	public function view_log() {
		$this->load->language($this->path_module);
		$this->load->model($this->path_module);
		$this->document->addStyle('view/stylesheet/user_log.css?ver=1');

		$this->document->setTitle(strip_tags($this->language->get('heading_log')));

		$this->load->model('setting/setting');

		if (isset($this->session->data['success'])) {
			$this->data['success'] =  $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] =  false;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$this->data['records'] = array();
		
		$limit = $this->config->get('user_log_display')?$this->config->get('user_log_display'):$this->config->get('config_limit_admin');
		
		$data_filters['start'] = ($page - 1) * $limit;
		$data_filters['limit'] = $limit;
		if (isset($this->request->get['filter_user_id'])) {
			$data_filters['filter_user_id'] = $this->request->get['filter_user_id'];
			$this->data['filter_user_id'] = $this->request->get['filter_user_id'];
		} else {
			$this->data['filter_user_id'] = '';
		}
		if (isset($this->request->get['filter_action'])) {
			$data_filters['filter_action'] = $this->request->get['filter_action'];
			$this->data['filter_action'] = $this->request->get['filter_action'];
		} else {
			$this->data['filter_action'] = '';
		}
		
		if (isset($this->request->get['filter_start'])) {
			$data_filters['filter_start'] = $this->request->get['filter_start'];
			$this->data['filter_start'] = $this->request->get['filter_start'];
		} else {
			$this->data['filter_start'] = '';
		}
		
		if (isset($this->request->get['filter_end'])) {
			$data_filters['filter_end'] = $this->request->get['filter_end'];
			$this->data['filter_end'] = $this->request->get['filter_end'];
		} else {
			$this->data['filter_end'] = '';
		}

		if (isset($this->request->get['filter_url'])) {
			$data_filters['filter_url'] = $this->request->get['filter_url'];
			$this->data['filter_url'] = $this->request->get['filter_url'];
		} else {
			$this->data['filter_url'] = '';
		}
		
		$records_total = $this->model_extension_module_user_log->getTotalRecords($data_filters);
  		$records = $this->model_extension_module_user_log->getRecords($data_filters);

		foreach ($records as $record) {
			$action_descr = '';
			if (preg_match('#(edit|delete|insert|add)#',$record['url'],$matches)) {
				$action_descr = $matches[0];
				if ($record['action'] == 'modify - save') {$action_descr .= '&amp;save';}
			}
      		$this->data['records'][] = array(
      			'user_log_id'	=> $record['user_log_id'],
      			'user'          => $this->makeUrl('user/user/edit', '&user_id=' . $record['user_id'], true),
      			'user_name'	    => $record['user_name'],
				'action'	    => $record['action'],
				'result'	    => $record['result'],
				'url'		    => $record['url'],
				'action_descr'  => $action_descr,
				'ip'		    => $record['ip'],
				'date'		    => $record['date'],
			);
    	}
		
		$url = '';
		if (isset($this->request->get['filter_action'])) {
			$url .= '&filter_action=' . $this->request->get['filter_action'];
		}
		if (isset($this->request->get['filter_start'])) {
			$url .= '&filter_start=' . $this->request->get['filter_start'];
		}
		if (isset($this->request->get['filter_end'])) {
			$url .= '&filter_end=' . $this->request->get['filter_end'];
		}
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}
		if (isset($this->request->get['filter_url'])) {
			$url .= '&filter_url=' . $this->request->get['filter_url'];
		}
		
		$this->load->model('user/user');
		$this->data['post_view'] = $this->makeUrlScript($this->path_module . '/postView');
		$this->data['users'] = $this->model_user_user->getUsers();
		$this->data[$this->token] = $this->session->data[$this->token];

		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->makeUrl($this->path_module . '/view_log','page={page}' . $url, true);

		$this->data['pagination'] = $pagination->render();

		$this->data['result'] = sprintf(
		$this->language->get('text_pagination'), ($records_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($records_total - $limit)) ? $records_total : ((($page - 1) * $limit) + $limit), $records_total, ceil($records_total / $limit));

  		$this->breadcrumbs($this->data);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title_log'),
			'href'      => $this->makeUrl($this->path_module . '/view_log'),
   		);

		$this->data['button_setting'] = $this->language->get('button_setting');
		
		$this->data['action_setting'] = $this->makeUrl($this->path_module);
		$this->data['clear_action']   = $this->makeUrl($this->path_module . '/clear');
		$this->data['delete_action']  = $this->makeUrl($this->path_module . '/delete', $url);
		$this->data['cancel']         = $this->makeUrl($this->path_module);

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		$this->nav($this->path_module . '/view_log',$this->data);
		$this->footer('_log', $this->data);
	}

	public function postView() {
		$this->load->model($this->path_module);
		if (isset($this->request->get['user_log_id'])) {
			if ($this->user->hasPermission('modify', $this->path_module)) {
				$record = $this->model_extension_module_user_log->getPost($this->request->get['user_log_id']);
				$this->response->setOutput($record);
			}
		}
	}

}