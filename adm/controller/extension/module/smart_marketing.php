<?php
class ControllerExtensionModuleSmartMarketing extends Controller {
	private $version = '1.4.6';
	private $error = array();

	public function install() {
		$this->load->model('extension/module/smart_marketing');

		$this->model_extension_module_smart_marketing->createTables();

		$this->load->model('setting/event');

		$this->model_setting_event->addEvent('smart_marketing', 'admin/view/common/column_left/before', 'extension/module/smart_marketing/eventMenu');
		$this->model_setting_event->addEvent('smart_marketing', 'admin/controller/common/header/before', 'extension/module/smart_marketing/eventHeaderSetup');

		// Add permissions to new controllers
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/block');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/campaign');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/customer_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/product');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/sender');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/subscriber');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/template');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/search');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/smart_marketing/language');

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/block');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/campaign');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/customer_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/product');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/sender');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/subscriber');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/template');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/search');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/smart_marketing/language');

		// Build subscribers list on install
		$this->load->model('extension/module/smart_marketing/api');
		$this->model_extension_module_smart_marketing_api->syncSubscribers();
	}

	public function uninstall() {
      $this->load->model('extension/module/smart_marketing');

		$this->model_extension_module_smart_marketing->removeTables();

		$this->load->model('setting/event');

      $this->model_setting_event->deleteEventByCode('smart_marketing');
	}

	public function index() {
		$this->load->language('extension/module/smart_marketing');
		$this->load->language('extension/module/smart_marketing/api');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/smart-marketing.css');

		// check update | if new version is available
		$this->document->addScript('https://www.oc-extensions.com/catalog/view/javascript/api/js/update.min.js?extension_version=' . $this->version . '&oc_version=' . VERSION . '&email=' . $this->config->get('config_email'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_smart_marketing', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title') . ' ' . $this->version;

		// Tabs
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_social'] = $this->language->get('tab_social');
		$data['tab_help'] = $this->language->get('tab_help');

		// Legend
		$data['legend_service'] = $this->language->get('legend_service');
		$data['legend_sendgrid'] = $this->language->get('legend_sendgrid');
		$data['legend_image'] = $this->language->get('legend_image');
		$data['legend_sender_info'] = $this->language->get('legend_sender_info');
		$data['legend_timezone'] = $this->language->get('legend_timezone');
		$data['legend_maintenance'] = $this->language->get('legend_maintenance');

		// Text
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select'] = $this->language->get('text_select');

		$data['text_send'] = $this->language->get('text_send');
		$data['text_request'] = $this->language->get('text_request');
		$data['text_at_every'] = $this->language->get('text_at_every');
		$data['text_second'] = $this->language->get('text_second');
		$data['text_minute'] = $this->language->get('text_minute');
		$data['text_hour'] = $this->language->get('text_hour');
		$data['text_day'] = $this->language->get('text_day');

		$data['text_service_sendgrid'] = $this->language->get('text_service_sendgrid');

		$data['text_each_open'] = $this->language->get('text_each_open');
		$data['text_each_click'] = $this->language->get('text_each_click');

		// Entry
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_api_key'] = $this->language->get('entry_api_key');

		$data['entry_service'] = $this->language->get('entry_service');

		$data['entry_sendgrid_api_key'] = $this->language->get('entry_sendgrid_api_key');
		$data['entry_sendgrid_recipient_limit'] = $this->language->get('entry_sendgrid_recipient_limit');
		$data['entry_sendgrid_send_interval'] = $this->language->get('entry_sendgrid_send_interval');

		$data['entry_sendgrid_event'] = $this->language->get('entry_sendgrid_event');
		$data['entry_sendgrid_task_event'] = $this->language->get('entry_sendgrid_task_event');
		$data['entry_sendgrid_rating_event'] = $this->language->get('entry_sendgrid_rating_event');
		$data['entry_sendgrid_rating_open'] = $this->language->get('entry_sendgrid_rating_open');
		$data['entry_sendgrid_rating_click'] = $this->language->get('entry_sendgrid_rating_click');
		$data['entry_sendgrid_dropped_event'] = $this->language->get('entry_sendgrid_dropped_event');
		$data['entry_sendgrid_dropped_reason'] = $this->language->get('entry_sendgrid_dropped_reason');
		$data['entry_sendgrid_unsubscribe_event'] = $this->language->get('entry_sendgrid_unsubscribe_event');

		$data['entry_sendgrid_dropped_reason'] = $this->language->get('entry_sendgrid_dropped_reason');

		$data['entry_sender_name'] = $this->language->get('entry_sender_name');
		$data['entry_sender_email'] = $this->language->get('entry_sender_email');

		$data['entry_timezone_difference'] = $this->language->get('entry_timezone_difference');
		$data['entry_maintenance_status'] = $this->language->get('entry_maintenance_status');
		$data['entry_maintenance_interval'] = $this->language->get('entry_maintenance_interval');

		// Help
		$data['help_status'] = $this->language->get('help_status');
		$data['help_api_key'] = $this->language->get('help_api_key');

		$data['help_service'] = $this->language->get('help_service');
		$data['help_sendgrid_api_key'] = $this->language->get('help_sendgrid_api_key');
		$data['help_sendgrid_recipient_limit'] = $this->language->get('help_sendgrid_recipient_limit');
		$data['help_sendgrid_send_interval'] = $this->language->get('help_sendgrid_send_interval');

		$data['help_sendgrid_event'] = $this->language->get('help_sendgrid_event');
		$data['help_sendgrid_task_event'] = $this->language->get('help_sendgrid_task_event');
		$data['help_sendgrid_rating_event'] = $this->language->get('help_sendgrid_rating_event');
		$data['help_sendgrid_rating_open'] = $this->language->get('help_sendgrid_rating_open');
		$data['help_sendgrid_rating_click'] = $this->language->get('help_sendgrid_rating_click');
		$data['help_sendgrid_dropped_event'] = $this->language->get('help_sendgrid_dropped_event');
		$data['help_sendgrid_dropped_reason'] = $this->language->get('help_sendgrid_dropped_reason');
		$data['help_sendgrid_unsubscribe_event'] = $this->language->get('help_sendgrid_unsubscribe_event');

		$data['help_sender_name'] = $this->language->get('help_sender_name');
		$data['help_sender_email'] = $this->language->get('help_sender_email');

		$data['entry_product_image'] = $this->language->get('entry_product_image');
		$data['entry_category_image'] = $this->language->get('entry_category_image');
		$data['entry_image_width'] = $this->language->get('entry_image_width');
		$data['entry_image_height'] = $this->language->get('entry_image_height');

		$data['help_product_image'] = $this->language->get('help_product_image');
		$data['help_category_image'] = $this->language->get('help_category_image');

		$data['help_timezone_difference'] = $this->language->get('help_timezone_difference');
		$data['help_maintenance_status'] = $this->language->get('help_maintenance_status');
		$data['help_maintenance_interval'] = $this->language->get('help_maintenance_interval');

		$this->load->model('extension/module/smart_marketing/timer');

		$data['help_mysql_time'] = sprintf($this->language->get('help_mysql_time'), $this->model_extension_module_smart_marketing_timer->getNow());

		// Buttons
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		// Error Messages
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['api_key'])) {
			$data['error_api_key'] = $this->error['api_key'];
		} else {
			$data['error_api_key'] = '';
		}

		if (isset($this->error['sendgrid_api_key'])) {
			$data['error_sendgrid_api_key'] = $this->error['sendgrid_api_key'];
		} else {
			$data['error_sendgrid_api_key'] = '';
		}

		if (isset($this->error['sendgrid_recipient_limit'])) {
			$data['error_sendgrid_recipient_limit'] = $this->error['sendgrid_recipient_limit'];
		} else {
			$data['error_sendgrid_recipient_limit'] = '';
		}

		if (isset($this->error['sendgrid_send_interval'])) {
			$data['error_sendgrid_send_interval'] = $this->error['sendgrid_send_interval'];
		} else {
			$data['error_sendgrid_send_interval'] = '';
		}

		if (isset($this->error['sendgrid_event'])) {
			$data['error_sendgrid_event'] = $this->error['sendgrid_event'];
		} else {
			$data['error_sendgrid_event'] = '';
		}

		if (isset($this->error['sendgrid_task_event'])) {
			$data['error_sendgrid_task_event'] = $this->error['sendgrid_task_event'];
		} else {
			$data['error_sendgrid_task_event'] = '';
		}

		if (isset($this->error['sendgrid_rating_event'])) {
			$data['error_sendgrid_rating_event'] = $this->error['sendgrid_rating_event'];
		} else {
			$data['error_sendgrid_rating_event'] = '';
		}

		if (isset($this->error['sendgrid_rating_open'])) {
			$data['error_sendgrid_rating_open'] = $this->error['sendgrid_rating_open'];
		} else {
			$data['error_sendgrid_rating_open'] = '';
		}

		if (isset($this->error['sendgrid_rating_click'])) {
			$data['error_sendgrid_rating_click'] = $this->error['sendgrid_rating_click'];
		} else {
			$data['error_sendgrid_rating_click'] = '';
		}

		if (isset($this->error['sendgrid_dropped_event'])) {
			$data['error_sendgrid_dropped_event'] = $this->error['sendgrid_dropped_event'];
		} else {
			$data['error_sendgrid_dropped_event'] = '';
		}

		if (isset($this->error['sendgrid_dropped_reason'])) {
			$data['error_sendgrid_dropped_reason'] = $this->error['sendgrid_dropped_reason'];
		} else {
			$data['error_sendgrid_dropped_reason'] = '';
		}

		if (isset($this->error['sendgrid_unsubscribe_event'])) {
			$data['error_sendgrid_unsubscribe_event'] = $this->error['sendgrid_unsubscribe_event'];
		} else {
			$data['error_sendgrid_unsubscribe_event'] = '';
		}

		if (isset($this->error['sender_name'])) {
			$data['error_sender_name'] = $this->error['sender_name'];
		} else {
			$data['error_sender_name'] = '';
		}

		if (isset($this->error['sender_email'])) {
			$data['error_sender_email'] = $this->error['sender_email'];
		} else {
			$data['error_sender_email'] = '';
		}

		if (isset($this->error['product_image_width'])) {
			$data['error_product_image_width'] = $this->error['product_image_width'];
		} else {
			$data['error_product_image_width'] = '';
		}

		if (isset($this->error['product_image_height'])) {
			$data['error_product_image_height'] = $this->error['product_image_height'];
		} else {
			$data['error_product_image_height'] = '';
		}

		if (isset($this->error['category_image_width'])) {
			$data['error_category_image_width'] = $this->error['category_image_width'];
		} else {
			$data['error_category_image_width'] = '';
		}

		if (isset($this->error['category_image_height'])) {
			$data['error_category_image_height'] = $this->error['category_image_height'];
		} else {
			$data['error_category_image_height'] = '';
		}

		if (isset($this->error['timezone_difference'])) {
			$data['error_timezone_difference'] = $this->error['timezone_difference'];
		} else {
			$data['error_timezone_difference'] = '';
		}

		if (isset($this->error['maintenance_interval'])) {
			$data['error_maintenance_interval'] = $this->error['maintenance_interval'];
		} else {
			$data['error_maintenance_interval'] = '';
		}

		if (isset($this->error['social_channel'])) {
			$data['error_social_channel'] = $this->error['social_channel'];
		} else {
			$data['error_social_channel'] = array();
		}

		// Breadcrumbs
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_extension'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/smart_marketing', 'user_token=' . $this->session->data['user_token'], true)
		);

		// Actions
		$data['action'] = $this->url->link('extension/module/smart_marketing', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		// Extension settings
		if (isset($this->request->post['module_smart_marketing_status'])) {
			$data['module_smart_marketing_status'] = $this->request->post['module_smart_marketing_status'];
		} elseif ($this->config->get('module_smart_marketing_status')) {
			$data['module_smart_marketing_status'] = $this->config->get('module_smart_marketing_status');
		} else {
			$data['module_smart_marketing_status'] = '';
		}

		if (isset($this->request->post['module_smart_marketing_api_key'])) {
			$data['module_smart_marketing_api_key'] = $this->request->post['module_smart_marketing_api_key'];
		} elseif ($this->config->get('module_smart_marketing_api_key')) {
			$data['module_smart_marketing_api_key'] = $this->config->get('module_smart_marketing_api_key');
		} else {
			$data['module_smart_marketing_api_key'] = '';
		}

		if (isset($this->request->post['module_smart_marketing_service'])) {
			$data['module_smart_marketing_service'] = $this->request->post['module_smart_marketing_service'];
		} elseif ($this->config->get('module_smart_marketing_service')) {
			$data['module_smart_marketing_service'] = $this->config->get('module_smart_marketing_service');
		} else {
			$data['module_smart_marketing_service'] = 'sendgrid';
		}

		if (isset($this->request->post['module_smart_marketing_sendgrid_api_key'])) {
			$data['module_smart_marketing_sendgrid_api_key'] = $this->request->post['module_smart_marketing_sendgrid_api_key'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_api_key')) {
			$data['module_smart_marketing_sendgrid_api_key'] = $this->config->get('module_smart_marketing_sendgrid_api_key');
		} else {
			$data['module_smart_marketing_sendgrid_api_key'] = '';
		}

		if (isset($this->request->post['module_smart_marketing_sendgrid_recipient_limit'])) {
			$data['module_smart_marketing_sendgrid_recipient_limit'] = $this->request->post['module_smart_marketing_sendgrid_recipient_limit'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_recipient_limit')) {
			$data['module_smart_marketing_sendgrid_recipient_limit'] = $this->config->get('module_smart_marketing_sendgrid_recipient_limit');
		} else {
			$data['module_smart_marketing_sendgrid_recipient_limit'] = 999;
		}

		if (isset($this->request->post['module_smart_marketing_sendgrid_send_interval'])) {
			$data['module_smart_marketing_sendgrid_send_interval'] = $this->request->post['module_smart_marketing_sendgrid_send_interval'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_send_interval')) {
			$data['module_smart_marketing_sendgrid_send_interval'] = $this->config->get('module_smart_marketing_sendgrid_send_interval');
		} else {
			$data['module_smart_marketing_sendgrid_send_interval'] = 10;
		}

		$catch_events = array('delivered', 'open', 'click', 'bounce', 'dropped', 'spamreport', 'unsubscribe');

		if (isset($this->request->post['module_smart_marketing_sendgrid_event'])) {
			$data['module_smart_marketing_sendgrid_event'] = $this->request->post['module_smart_marketing_sendgrid_event'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_event')) {
			$data['module_smart_marketing_sendgrid_event'] = $this->config->get('module_smart_marketing_sendgrid_event');
		} else {
			$data['module_smart_marketing_sendgrid_event'] = $catch_events;
		}

		$task_events = array('delivered', 'open', 'click', 'bounce', 'unsubscribe');

		if (isset($this->request->post['module_smart_marketing_sendgrid_task_event'])) {
			$data['module_smart_marketing_sendgrid_task_event'] = $this->request->post['module_smart_marketing_sendgrid_task_event'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_task_event')) {
			$data['module_smart_marketing_sendgrid_task_event'] = $this->config->get('module_smart_marketing_sendgrid_task_event');
		} else {
			$data['module_smart_marketing_sendgrid_task_event'] = $task_events;
		}

		$rating_events = array('open', 'click');

		if (isset($this->request->post['module_smart_marketing_sendgrid_rating_event'])) {
			$data['module_smart_marketing_sendgrid_rating_event'] = $this->request->post['module_smart_marketing_sendgrid_rating_event'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_rating_event')) {
			$data['module_smart_marketing_sendgrid_rating_event'] = $this->config->get('module_smart_marketing_sendgrid_rating_event');
		} else {
			$data['module_smart_marketing_sendgrid_rating_event'] = $rating_events;
		}

		if (isset($this->request->post['module_smart_marketing_sendgrid_rating_open'])) {
			$data['module_smart_marketing_sendgrid_rating_open'] = $this->request->post['module_smart_marketing_sendgrid_rating_open'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_rating_open')) {
			$data['module_smart_marketing_sendgrid_rating_open'] = $this->config->get('module_smart_marketing_sendgrid_rating_open');
		} else {
			$data['module_smart_marketing_sendgrid_rating_open'] = 10;
		}

		if (isset($this->request->post['module_smart_marketing_sendgrid_rating_click'])) {
			$data['module_smart_marketing_sendgrid_rating_click'] = $this->request->post['module_smart_marketing_sendgrid_rating_click'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_rating_click')) {
			$data['module_smart_marketing_sendgrid_rating_click'] = $this->config->get('module_smart_marketing_sendgrid_rating_click');
		} else {
			$data['module_smart_marketing_sendgrid_rating_click'] = 100;
		}

		if (isset($this->request->post['module_smart_marketing_sendgrid_dropped_event'])) {
			$data['module_smart_marketing_sendgrid_dropped_event'] = $this->request->post['module_smart_marketing_sendgrid_dropped_event'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_dropped_event')) {
			$data['module_smart_marketing_sendgrid_dropped_event'] = $this->config->get('module_smart_marketing_sendgrid_dropped_event');
		} else {
			$data['module_smart_marketing_sendgrid_dropped_event'] = 'dropped';
		}

		$unsubscribe_events = array('dropped', 'spamreport', 'unsubscribe');

		if (isset($this->request->post['module_smart_marketing_sendgrid_unsubscribe_event'])) {
			$data['module_smart_marketing_sendgrid_unsubscribe_event'] = $this->request->post['module_smart_marketing_sendgrid_unsubscribe_event'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_unsubscribe_event')) {
			$data['module_smart_marketing_sendgrid_unsubscribe_event'] = $this->config->get('module_smart_marketing_sendgrid_unsubscribe_event');
		} else {
			$data['module_smart_marketing_sendgrid_unsubscribe_event'] = $unsubscribe_events;
		}

		// also unsubscribe on dropped event if reason is in list
		$dropped_reasons = array('Service Unavailable', 'Unsubscribed Address', 'Unable to resolve MX', 'No Such User', 'Unknown User', 'Is in a Black List', 'Invalid MX host', 'Bounced Address', 'Invalid', 'Recipient address rejected', 'Not found by SMTP address lookup', 'Mailbox Unavailable', 'Email account that you tried to reach does not exist', 'Exceeded max time without delivery');

		if (isset($this->request->post['module_smart_marketing_sendgrid_dropped_reason'])) {
			$data['module_smart_marketing_sendgrid_dropped_reason'] = $this->request->post['module_smart_marketing_sendgrid_dropped_reason'];
		} elseif ($this->config->get('module_smart_marketing_sendgrid_dropped_reason')) {
			$data['module_smart_marketing_sendgrid_dropped_reason'] = $this->config->get('module_smart_marketing_sendgrid_dropped_reason');
		} else {
			$data['module_smart_marketing_sendgrid_dropped_reason'] = $dropped_reasons;
		}

		if (isset($this->request->post['module_smart_marketing_sender_name'])) {
			$data['module_smart_marketing_sender_name'] = $this->request->post['module_smart_marketing_sender_name'];
		} elseif (!is_null($this->config->get('module_smart_marketing_sender_name'))) {
			$data['module_smart_marketing_sender_name'] = $this->config->get('module_smart_marketing_sender_name');
		} else {
			$data['module_smart_marketing_sender_name'] = $this->config->get('config_name');
		}

		if (isset($this->request->post['module_smart_marketing_sender_email'])) {
			$data['module_smart_marketing_sender_email'] = $this->request->post['module_smart_marketing_sender_email'];
		} elseif (!is_null($this->config->get('module_smart_marketing_sender_email'))) {
			$data['module_smart_marketing_sender_email'] = $this->config->get('module_smart_marketing_sender_email');
		} else {
			$data['module_smart_marketing_sender_email'] = $this->config->get('config_email');
		}

		if (isset($this->request->post['module_smart_marketing_product_image_width'])) {
			$data['module_smart_marketing_product_image_width'] = $this->request->post['module_smart_marketing_product_image_width'];
		} elseif (!is_null($this->config->get('module_smart_marketing_product_image_width'))) {
			$data['module_smart_marketing_product_image_width'] = $this->config->get('module_smart_marketing_product_image_width');
		} else {
			$data['module_smart_marketing_product_image_width'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
		}

		if (isset($this->request->post['module_smart_marketing_product_image_height'])) {
			$data['module_smart_marketing_product_image_height'] = $this->request->post['module_smart_marketing_product_image_height'];
		} elseif (!is_null($this->config->get('module_smart_marketing_product_image_height'))) {
			$data['module_smart_marketing_product_image_height'] = $this->config->get('module_smart_marketing_product_image_height');
		} else {
			$data['module_smart_marketing_product_image_height'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
		}

		if (isset($this->request->post['module_smart_marketing_category_image_width'])) {
			$data['module_smart_marketing_category_image_width'] = $this->request->post['module_smart_marketing_category_image_width'];
		} elseif (!is_null($this->config->get('module_smart_marketing_category_image_width'))) {
			$data['module_smart_marketing_category_image_width'] = $this->config->get('module_smart_marketing_category_image_width');
		} else {
			$data['module_smart_marketing_category_image_width'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width');
		}

		if (isset($this->request->post['module_smart_marketing_category_image_height'])) {
			$data['module_smart_marketing_category_image_height'] = $this->request->post['module_smart_marketing_category_image_height'];
		} elseif (!is_null($this->config->get('module_smart_marketing_category_image_height'))) {
			$data['module_smart_marketing_category_image_height'] = $this->config->get('module_smart_marketing_category_image_height');
		} else {
			$data['module_smart_marketing_category_image_height'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height');
		}

		if (isset($this->request->post['module_smart_marketing_timezone_difference'])) {
			$data['module_smart_marketing_timezone_difference'] = $this->request->post['module_smart_marketing_timezone_difference'];
		} elseif (!is_null($this->config->get('module_smart_marketing_timezone_difference'))) {
			$data['module_smart_marketing_timezone_difference'] = $this->config->get('module_smart_marketing_timezone_difference');
		} else {
			$data['module_smart_marketing_timezone_difference'] = '0';
		}

		if (isset($this->request->post['module_smart_marketing_maintenance_status'])) {
			$data['module_smart_marketing_maintenance_status'] = $this->request->post['module_smart_marketing_maintenance_status'];
		} elseif (!is_null($this->config->get('module_smart_marketing_maintenance_status'))) {
			$data['module_smart_marketing_maintenance_status'] = $this->config->get('module_smart_marketing_maintenance_status');
		} else {
			$data['module_smart_marketing_maintenance_status'] = '';
		}

		if (isset($this->request->post['module_smart_marketing_maintenance_interval'])) {
			$data['module_smart_marketing_maintenance_interval'] = $this->request->post['module_smart_marketing_maintenance_interval'];
		} elseif (!is_null($this->config->get('module_smart_marketing_maintenance_interval'))) {
			$data['module_smart_marketing_maintenance_interval'] = $this->config->get('module_smart_marketing_maintenance_interval');
		} else {
			$data['module_smart_marketing_maintenance_interval'] = 10;
		}

		if (isset($this->request->post['module_smart_marketing_social_channel'])) {
			$data['module_smart_marketing_social_channel'] = $this->request->post['module_smart_marketing_social_channel'];
		} elseif (!is_null($this->config->get('module_smart_marketing_social_channel'))) {
			$data['module_smart_marketing_social_channel'] = $this->config->get('module_smart_marketing_social_channel');
		} else {
			$data['module_smart_marketing_social_channel'] = array();
		}

		// build events code + name (based on catch_events)
		$data['sendgrid_events'] = array();

		foreach ($catch_events as $event_code) {
			$data['sendgrid_events'][] = array(
				'code' => $event_code,
				'name' => $this->language->get('text_event_' . $event_code)
			);
		}

		$data['social_networks'] = array('Facebook', 'Twitter', 'Instagram', 'Pinterest', 'Youtube', 'WhatsApp', 'Snapchat', 'Skype', 'Messenger');

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_api_key']) != 35) {
			$this->error['api_key'] = $this->language->get('error_api_key');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_sendgrid_api_key']) < 10) {
			$this->error['sendgrid_api_key'] = $this->language->get('error_sendgrid_api_key');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_sendgrid_recipient_limit']) < 1 || !is_numeric($this->request->post['module_smart_marketing_sendgrid_recipient_limit'])) {
			$this->error['sendgrid_recipient_limit'] = $this->language->get('error_sendgrid_recipient_limit');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_sendgrid_send_interval']) < 1 || !is_numeric($this->request->post['module_smart_marketing_sendgrid_send_interval'])) {
			$this->error['sendgrid_send_interval'] = $this->language->get('error_sendgrid_send_interval');
		}

		if (!isset($this->request->post['module_smart_marketing_sendgrid_event'])) {
			$this->error['sendgrid_event'] = $this->language->get('error_sendgrid_event');
		}

		if (!isset($this->request->post['module_smart_marketing_sendgrid_task_event'])) {
			$this->error['sendgrid_task_event'] = $this->language->get('error_sendgrid_task_event');
		}

		if (!isset($this->request->post['module_smart_marketing_sendgrid_rating_event'])) {
			$this->error['sendgrid_rating_event'] = $this->language->get('error_sendgrid_rating_event');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_sendgrid_rating_open']) < 1 || !is_numeric($this->request->post['module_smart_marketing_sendgrid_rating_open'])) {
			$this->error['sendgrid_rating_open'] = $this->language->get('error_sendgrid_rating_open');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_sendgrid_rating_click']) < 1 || !is_numeric($this->request->post['module_smart_marketing_sendgrid_rating_click'])) {
			$this->error['sendgrid_rating_click'] = $this->language->get('error_sendgrid_rating_click');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_sendgrid_dropped_event']) < 1 ) {
			$this->error['sendgrid_dropped_event'] = $this->language->get('error_sendgrid_dropped_event');
		}

		if (!isset($this->request->post['module_smart_marketing_sendgrid_dropped_reason'])) {
			$this->error['dropped_reason'] = $this->language->get('error_dropped_reason');
		}

		if (utf8_strlen($this->request->post['module_smart_marketing_sender_name']) < 1 ) {
			$this->error['sender_name'] = $this->language->get('error_sender_name');
		}

		if (!filter_var($this->request->post['module_smart_marketing_sender_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['sender_email'] = $this->language->get('error_sender_email');
		}

		if (!is_numeric($this->request->post['module_smart_marketing_product_image_width'])) {
			$this->error['product_image_width'] = $this->language->get('error_image_width');
		}

		if (!is_numeric($this->request->post['module_smart_marketing_product_image_width'])) {
			$this->error['product_image_width'] = $this->language->get('error_image_width');
		}

		if (!is_numeric($this->request->post['module_smart_marketing_product_image_height'])) {
			$this->error['product_image_height'] = $this->language->get('error_image_height');
		}

		if (!is_numeric($this->request->post['module_smart_marketing_category_image_width'])) {
			$this->error['category_image_width'] = $this->language->get('error_image_width');
		}

		if (!is_numeric($this->request->post['module_smart_marketing_category_image_height'])) {
			$this->error['category_image_height'] = $this->language->get('error_image_height');
		}

		if (!is_numeric($this->request->post['module_smart_marketing_timezone_difference'])) {
			$this->error['timezone_difference'] = $this->language->get('error_timezone_difference');
		}

		if (isset($this->request->post['module_smart_marketing_social_channel'])) {
			foreach ($this->request->post['module_smart_marketing_social_channel'] as $key => $value) {
				if (utf8_strlen($value['code']) < 1) {
					$this->error['social_channel'][$key]['code'] = $this->language->get('error_social_channel_code');
					$this->error['warning'] = $this->language->get('error_social_channel');
				}

				if (utf8_strlen($value['link']) < 1) {
					$this->error['social_channel'][$key]['link'] = $this->language->get('error_social_channel_link');
					$this->error['warning'] = $this->language->get('error_social_channel');
				}
			}
		}

		// check only if there's no other error => Test Sendgrid API
		if (!$this->error) {
			$this->load->model('extension/module/smart_marketing/api');

			$api_response = $this->model_extension_module_smart_marketing_api->getAPIStatus($this->request->post['module_smart_marketing_api_key']);

			if (isset($api_response['error'])) {
				$this->error['api_key'] = vsprintf($this->language->get($api_response['error']['code']), $api_response['error']['args']);
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_general');
		}

		return !$this->error;
	}

	public function deletecache() {
		$this->load->language('extension/module/smart_marketing');

		$template_cache = isset($this->request->get['template']) ? true : false;
		$block_cache = isset($this->request->get['block']) ? true : false;

		if ($template_cache) {
			$this->load->model('extension/module/smart_marketing/template');

			$this->model_extension_module_smart_marketing_template->clearTemplatesCache();
		}

		if ($block_cache) {
			$this->load->model('extension/module/smart_marketing/block');

			$this->model_extension_module_smart_marketing_block->clearBlocksCache();
		}

		$this->session->data['success'] = $this->language->get('text_success_clear_cache');

		$this->response->redirect($this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'], true));
	}

	// EVENTS

	public function eventMenu($route, &$data) {
		$menu = array();

		$this->language->load('extension/module/smart_marketing');

		if ($this->user->hasPermission('access', 'extension/module/smart_marketing')) {
			$menu[] = array(
				'name'	   => $this->language->get('menu_setting'),
				'href'     => $this->url->link('extension/module/smart_marketing', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_subscriber'),
				'href'     => $this->url->link('extension/module/smart_marketing/subscriber', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_template'),
				'href'     => $this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_campaign'),
				'href'     => $this->url->link('extension/module/smart_marketing/campaign', 'user_token=' . $this->session->data['user_token'], true),
				'children' => array()
			);

			$cache[] = array(
				'name'	  => $this->language->get('menu_cache_all'),
				'href'     => $this->url->link('extension/module/smart_marketing/deletecache', 'user_token=' . $this->session->data['user_token'] .'&template=1&block=1', true),
				'children' => array()
			);

			$cache[] = array(
				'name'	  => $this->language->get('menu_cache_template'),
				'href'     => $this->url->link('extension/module/smart_marketing/deletecache', 'user_token=' . $this->session->data['user_token'] .'&template=1', true),
				'children' => array()
			);

			$cache[] = array(
				'name'	  => $this->language->get('menu_cache_block'),
				'href'     => $this->url->link('extension/module/smart_marketing/deletecache', 'user_token=' . $this->session->data['user_token'] .'&block=1', true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_cache'),
				'href'     => '',
				'children' => $cache
			);

         $menu[] = array(
				'name'	  => $this->language->get('menu_help'),
				'href'     => 'http://www.oc-extensions.com/OpenCart-Smart-Marketing-Opencart-2.x-Help',
				'children' => array()
			);
		}

		if ($menu) {
			$data['menus'][] = array(
				'id'       => 'menu-smart-marketing',
				'icon'	   => 'fa-send',
				'name'	   => $this->language->get('menu_smart_marketing'),
				'href'     => '',
				'children' => $menu
			);
		}
	}

	public function eventHeaderSetup() {
		if (isset($this->request->get['route'])) {
			if (strpos($this->request->get['route'], 'smart_marketing') !== false) {
				$this->document->addStyle('view/stylesheet/smart-marketing.css');
				$this->document->addScript('view/javascript/jquery/recliner.js');
				$this->document->addScript('view/javascript/jquery/smart-marketing.js');
			}
		}
	}
}
?>
