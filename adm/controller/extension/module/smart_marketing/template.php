<?php
class ControllerExtensionModuleSmartMarketingTemplate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/smart_marketing/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/template');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/module/smart_marketing/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/template');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_smart_marketing_template->addTemplate($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/smart_marketing/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/template');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_module_smart_marketing_template->editTemplate($this->request->get['template_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/module/smart_marketing/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module/smart_marketing/template');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $template_id) {
				$this->model_extension_module_smart_marketing_template->deleteTemplate($template_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function clearCache() {
		$this->load->language('extension/module/smart_marketing/template');

		$this->load->model('extension/module/smart_marketing/template');

		$this->model_extension_module_smart_marketing_template->clearTemplatesCache();

		$this->session->data['success'] = $this->language->get('text_success_clear_cache');

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->response->redirect($this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	protected function getList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/module/smart_marketing/template/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['clear_cache'] = $this->url->link('extension/module/smart_marketing/template/clearCache', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('extension/module/smart_marketing/template/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['templates'] = array();

		$templates = $this->model_extension_module_smart_marketing_template->getTemplates();

		if ($templates) {
			foreach ($templates as $template) {
				foreach($template['versions'] as $template_version) {
					if ($template_version['active']) {
						$data['templates'][] = array(
							'template_id'   => $template['id'],
							'name'          => $template['name'],
							'subject'       => $template_version['subject'],
							'date_modified' => date($this->language->get('datetime_format'), strtotime($template_version['updated_at'])),
							'edit'          => $this->url->link('extension/module/smart_marketing/template/edit', 'user_token=' . $this->session->data['user_token'] . '&template_id=' . $template_version['template_id'], true)
						);
					}
				}
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_subject'] = $this->language->get('column_subject');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_clear_cache'] = $this->language->get('button_clear_cache');

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

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/template_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['template_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_html_content'] = $this->language->get('entry_html_content');
		$data['entry_plain_content'] = $this->language->get('entry_plain_content');

		$data['help_name'] = $this->language->get('help_name');
		$data['help_html_content'] = $this->language->get('help_html_content');
		$data['help_plain_content'] = $this->language->get('help_plain_content');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_preview'] = $this->language->get('button_preview');
		$data['button_code_full'] = $this->language->get('button_code_full');
		$data['button_edit_design'] = $this->language->get('button_edit_design');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['html_content'])) {
			$data['error_html_content'] = $this->error['html_content'];
		} else {
			$data['error_html_content'] = '';
		}

		if (isset($this->error['plain_content'])) {
			$data['error_plain_content'] = $this->error['plain_content'];
		} else {
			$data['error_plain_content'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['template_id'])) {
			$data['action'] = $this->url->link('extension/module/smart_marketing/template/add', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/smart_marketing/template/edit', 'user_token=' . $this->session->data['user_token'] . '&template_id=' . $this->request->get['template_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/module/smart_marketing/template', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['template_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$template_info = $this->model_extension_module_smart_marketing_template->getTemplate($this->request->get['template_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($template_info['name'])) {
			$data['name'] = $template_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['html_content'])) {
			$data['html_content'] = $this->request->post['html_content'];
		} elseif (!empty($template_info) && isset($template_info['html_content'])) {
			$data['html_content'] = $template_info['html_content'];
		} else {
			$data['html_content'] = '';
		}

		if (isset($this->request->post['plain_content'])) {
			$data['plain_content'] = $this->request->post['plain_content'];
		} elseif (!empty($template_info) && isset($template_info['plain_content'])) {
			$data['plain_content'] = $template_info['plain_content'];
		} else {
			$data['plain_content'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/template_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['name']) < 1) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (utf8_strlen($this->request->post['html_content']) < 20) {
			$this->error['html_content'] = $this->language->get('error_html_content');
		}

		if (utf8_strlen($this->request->post['plain_content']) < 1) {
			$this->error['plain_content'] = $this->language->get('error_plain_content');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/smart_marketing/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post['selected']) {
			// get Templates assigned to campaigns still in progress
			$this->load->model('extension/module/smart_marketing/template');

			$used_templates = $this->model_extension_module_smart_marketing_template->getInUseTemplates();

			// check templates assigned to in progress campaign
			if ($used_templates) {
				foreach ($used_templates as $used_template) {
					if (in_array($used_template['template_id'], $this->request->post['selected'])) {
						$this->error['warning'] = $this->language->get('error_in_use');
					}
				}
			}
		}

		return !$this->error;
	}

	public function manager() {
		$this->load->language('extension/module/smart_marketing/template');

		$this->load->model('tool/image');

		$data['lazy'] = $this->model_tool_image->resize('placeholder.png', 400, 470);

		$data['templates'] = array();

		// GET EXISTING TEMPLATES IN SENDGRID ACCOUNT
		$this->load->model('extension/module/smart_marketing/template');

		$templates = $this->model_extension_module_smart_marketing_template->getTemplates();

		if ($templates) {
			foreach ($templates as $template) {
				foreach($template['versions'] as $template_version) {
					if ($template_version['active']) {
						$data['templates'][] = array(
							'template_id'   => $template['id'],
							'name'          => $template['name'],
							'image'         => 'https://via.placeholder.com/400x470/f7f7f7/555555?text=NO+IMAGE+AVAILABLE',
							'provider'      => 'sendgrid'
						);
					}
				}
			}
		}

		// GET TEMPLATES FROM OC-EXTENSIONS SERVER
		$templates = $this->model_extension_module_smart_marketing_template->getOCXTemplates();

		if ($templates) {
			foreach ($templates as $template) {
				$data['templates'][] = array(
					'template_id'   => $template['template_id'],
					'name'          => $template['name'],
					'image'         => $template['image'],
					'provider'      => 'ocextensions'
				);
			}
		}

		$data['heading_title'] = $this->language->get('heading_title_manager');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_provider_all'] = $this->language->get('text_provider_all');
		$data['text_provider_ocx'] = $this->language->get('text_provider_ocx');
		$data['text_provider_sendgrid'] = $this->language->get('text_provider_sendgrid');

		$data['entry_search'] = $this->language->get('entry_search');

		$data['button_search'] = $this->language->get('button_search');

		$data['user_token'] = $this->session->data['user_token'];

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/template_manager', $data));
	}

	public function preview() {
		$json = array();

		if (isset($this->request->get['provider']) && isset($this->request->get['template_id'])) {
			$this->load->model('extension/module/smart_marketing/template');

			if ($this->request->get['provider'] == 'sendgrid') {
				$template_info = $this->model_extension_module_smart_marketing_template->getTemplate($this->request->get['template_id']);
			} else {
				$template_info = $this->model_extension_module_smart_marketing_template->getOCXTemplate($this->request->get['template_id']);
			}

			if ($template_info) {
				// convert special keywords like {store.url}
				$html_content = $this->model_extension_module_smart_marketing_template->convertSpecialKeywords(html_entity_decode($template_info['html_content'], ENT_QUOTES, 'UTF-8'));

				// covert <ocx-template-tag> to html tags
				$html_content = $this->model_extension_module_smart_marketing_template->convertHtmlTags($html_content, 'ocx2regular');

				$json['html_content'] = $html_content;
				$json['name'] = html_entity_decode($template_info['name'], ENT_QUOTES, 'UTF-8');

				$json['success'] = true;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function import() {
		$json = array();

		if (isset($this->request->get['provider']) && isset($this->request->get['template_id'])) {
			$this->load->model('extension/module/smart_marketing/template');

			if ($this->request->get['provider'] == 'sendgrid') {
				$template_info = $this->model_extension_module_smart_marketing_template->getTemplate($this->request->get['template_id']);
			} else {
				$template_info = $this->model_extension_module_smart_marketing_template->getOCXTemplate($this->request->get['template_id']);
			}

			if ($template_info) {
				// decode first html_content
				$html_content = html_entity_decode($template_info['html_content'], ENT_QUOTES, 'UTF-8');

				// replace special keywords
				$html_content = $this->model_extension_module_smart_marketing_template->convertSpecialKeywords($html_content);

				// convert regular tags to ocx tags
				$html_content = $this->model_extension_module_smart_marketing_template->convertHtmlTags($html_content);

				$json['html_content'] = $html_content;

				$json['plain_content'] = $this->model_extension_module_smart_marketing_template->convertSpecialKeywords(html_entity_decode($template_info['plain_content'], ENT_QUOTES, 'UTF-8'));

				if ($template_info['blocks']) {
					$json['blocks'] = $this->model_extension_module_smart_marketing_template->processTemplateBlocks($template_info['blocks']);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function sendTest() {
		$this->load->language('extension/module/smart_marketing/template');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$recipients = explode(",", str_replace(" ", "", $this->request->post['test_recipient']));

			if (!$recipients) {
				$json['error'] = $this->language->get('error_test_recipient');
			} else {
				foreach ($recipients as $recipient) {
					if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
						$json['error'] = $this->language->get('error_test_recipient_invalid');
					}
				}
			}

			if (!$json) {
				if ($this->request->post['test_email_type'] == 'html') {
					if (utf8_strlen($this->request->post['html_content']) < 20) {
						$json['error'] = $this->language->get('error_html_content');
					}
				}

				if ($this->request->post['test_email_type'] == 'plain') {
					if (utf8_strlen($this->request->post['plain_content']) < 1) {
						$json['error'] = $this->language->get('error_html_content');
					}
				}
			}

			if (!$json) {
				if (isset($this->request->post['subject'])) {
					if (utf8_strlen($this->request->post['subject']) < 1) {
						$json['error'] = $this->language->get('error_test_subject');
					}
				}

				if (isset($this->request->post['sender_name'])) {
					if (utf8_strlen($this->request->post['sender_name']) < 1) {
						$json['error'] = $this->language->get('error_test_sender_name');
					}
				}

				if (isset($this->request->post['sender_email'])) {
					if (!filter_var($this->request->post['sender_email'], FILTER_VALIDATE_EMAIL)) {
						$json['error'] = $this->language->get('error_test_sender_email');
					}
				}
			}

			// OK - it's safe to continue
			if (!$json) {
				$this->load->model('extension/module/smart_marketing/template');

				if (isset($this->request->post['subject'])) {
					$subject = sprintf($this->language->get('mail_subject_test'), $this->request->post['subject']);
				} else {
					$subject = $this->language->get('mail_subject_test_default');
				}

				$sender_name = isset($this->request->post['sender_name']) ? html_entity_decode($this->request->post['sender_name'], ENT_QUOTES, 'UTF-8') : $this->config->get('module_smart_marketing_sender_name');
				$sender_email = isset($this->request->post['sender_email']) ? html_entity_decode($this->request->post['sender_email'], ENT_QUOTES, 'UTF-8') : $this->config->get('module_smart_marketing_sender_email');

				$message = html_entity_decode($this->request->post['plain_content'], ENT_QUOTES, 'UTF-8');

				$html = $this->model_extension_module_smart_marketing_template->convertHtmlTags(html_entity_decode($this->request->post['html_content'], ENT_QUOTES, 'UTF-8'), 'ocx2regular');

				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setFrom($sender_email);
				$mail->setSender($sender_name);
				$mail->setSubject($subject);
				$mail->setText($message);

				if ($this->request->post['test_email_type'] == 'html') {
					$mail->setHtml($html);
				}

				foreach ($recipients as $recipient) {
					$mail->setTo($recipient);
					$mail->send();
				}

				$json['success'] = $this->language->get('text_success_test');
			}

		} else {

			$data['heading_title'] = $this->language->get('heading_title_test');

			$data['entry_test_recipient'] = $this->language->get('entry_test_recipient');

			$data['help_test_recipient'] = $this->language->get('help_test_recipient');

			$data['button_send_test'] = $this->language->get('button_send_test');

			$json['output'] = $this->load->view('extension/module/smart_marketing/template_test', $data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
