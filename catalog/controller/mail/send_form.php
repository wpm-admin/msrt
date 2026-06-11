<?php
class ControllerMailSendForm extends Controller {
	//public function index(&$route, &$args, &$output) {			            
	public function index() {			            
		$this->load->language('mail/forgotten');

		$data['text_greeting'] = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$data['text_change'] = $this->language->get('text_change');
		$data['text_ip'] = $this->language->get('text_ip');
		
		$data['reset'] = str_replace('&amp;', '&', $this->url->link('account/reset', 'code=' . $args[1], true));
		$data['ip'] = $this->request->server['REMOTE_ADDR'];
		
		$html = '';
		
		foreach($this->request->post as $index => $value){
			
			if(is_array($value)){
				foreach($value as $index2 => $value2){
					$html .= '<b>'.$index2.':</b> ' . $value2 . '<br>';
				}
			}else{
				$html .= '<b>'.$index.':</b> ' . $value . '<br>';
			}
		}
		
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($this->config->get('config_email'));
		//$mail->setTo('folder.list@gmail.com');
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject('Windshield-production');
		//$mail->setText($this->load->view('mail/forgotten', $data));
		$mail->setHtml($html);
		$mail->send();
	}
	
	public function send_production() {
		
		$this->load->model('catalog/mark');
		$this->load->model('catalog/production');		
		
		$production = $this->model_catalog_production->getProduction((int)$this->request->post['windshield']);
		$mark_info = $this->model_catalog_mark->getMark((int)$this->request->post['mark']);
		$model_info = $this->model_catalog_mark->getMark((int)$this->request->post['model']);
		
		//$html = '<h2>Customer selected</h2>';
		
		if($mark_info){
			$html .= '<b>Mark:</b> ' . $mark_info['name'] . '<br>';	
		}
		
		if($model_info){
			$html .= '<b>Model:</b> ' . $model_info['name'] . '<br>';	
		}
		
		//$html .= '<h2>Production selected</h2>';

		if($production){
			$html .= '<b>Product id:</b> ' . $production['production_id'] . '<br>';	
			$html .= '<b>Product name:</b> ' . $production['name'] . '<br>';	
		}
		
		$html .= '<h2>Customer info</h2>';

		if($production){
			$html .= '<b>Name:</b> ' . $this->request->post['production_firstname'] . '<br>';	
			$html .= '<b>Phone:</b> ' . $this->request->post['production_phone'] . '<br>';	
			$html .= '<b>Email:</b> ' . $this->request->post['production_email'] . '<br>';	
		}
		
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($this->config->get('config_email'));
		//$mail->setTo('folder.list@gmail.com');
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject('Request from the SICURVETRO WINDSHIELD form');
		//$mail->setText($this->load->view('mail/forgotten', $data));
		$mail->setHtml($html);
		$mail->send();
	}
	
}