<?php
class ControllerExtensionModuleSmartMarketingSender extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/smart_marketing/sender');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_sending'] = sprintf($this->language->get('text_sending'), $this->config->get('module_smart_marketing_sendgrid_recipient_limit'), $this->config->get('module_smart_marketing_sendgrid_send_interval'));
		$data['text_do_not_close'] = $this->language->get('text_do_not_close');

		$data['campaign_id'] = isset($this->request->get['campaign_id']) ? $this->request->get['campaign_id'] : 0;
		$data['send_interval'] = $this->config->get('module_smart_marketing_sendgrid_send_interval');

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/smart_marketing/sender', $data));
	}

	public function send() {
		$this->load->language('extension/module/smart_marketing/sender');

		$json = array();

		$logs = array();

		// sent percent from all campaign emails
		$recipient_total = 0;
		$sent_total = 0;
		$sent_percent = 0;

		if (isset($this->request->post['campaign_id'])) {
			$campaign_id = $this->request->post['campaign_id'];
		} else {
			$campaign_id = 0;
		}

		$this->load->model('extension/module/smart_marketing/campaign');

		$campaign_info = $this->model_extension_module_smart_marketing_campaign->getCampaign($campaign_id);

		if ($campaign_info) {
			$this->load->model('extension/module/smart_marketing/task');
			$this->load->model('extension/module/smart_marketing/timer');

			$tasks = $this->model_extension_module_smart_marketing_task->getTasksByCampaignId($campaign_id);

			if ($tasks) {
				$sendgrid_mail = new SendGrid\Mail();

				$sendgrid_mail->setFrom(new SendGrid\Email($campaign_info['sender_name'], $campaign_info['sender_email']));
				$sendgrid_mail->setTemplateId($campaign_info['template_id']);
				$sendgrid_mail->setSubject($campaign_info['subject']);

				// SendGrid Tracking
				$tracking_settings = new SendGrid\TrackingSettings();

				$click_tracking = new SendGrid\ClickTracking();
				$click_tracking->setEnable(true);
				$click_tracking->setEnableText(true);

				$tracking_settings->setClickTracking($click_tracking);

				$open_tracking = new SendGrid\OpenTracking();
				$open_tracking->setEnable(true);

				$tracking_settings->setOpenTracking($open_tracking);

				$subscription_tracking = new SendGrid\SubscriptionTracking();
				$subscription_tracking->setEnable(true);

				$tracking_settings->setSubscriptionTracking($subscription_tracking);

				$sendgrid_mail->setTrackingSettings($tracking_settings);

				foreach($tasks as $task) {
					$sendgrid_personalization = new SendGrid\Personalization();

					$sendgrid_personalization->addTo(new SendGrid\Email($task['firstname'] . ' ' . $task['lastname'], $task['email']));

					$sendgrid_personalization->addSubstitution("{subscriber.firstname}", ucfirst($task['firstname']));
					$sendgrid_personalization->addSubstitution("{subscriber.lastname}", ucfirst($task['lastname']));
					$sendgrid_personalization->addSubstitution("{subscriber.email}", ucfirst($task['email']));

					// custom unsubscribe link
					$sendgrid_personalization->addSubstitution("{unsubscribe}", HTTPS_CATALOG . 'index.php?route=extension/module/smart_marketing/event/unsubscribe&subscriber_id=' . $task['subscriber_id'] . '&campaign_task_id=' . $task['campaign_task_id']);

					$sendgrid_personalization->addCustomArg("campaign_task_id", $task['campaign_task_id']);
					$sendgrid_personalization->addCustomArg("campaign_id", $task['campaign_id']);
					$sendgrid_personalization->addCustomArg("subscriber_id", $task['subscriber_id']);

					// if campaign has prefered localhour | E.g want each subscriber to get at 9.00AM on his local hour
					if ($campaign_info['timezone_hour'] != '00:00:00') {
						$send_at_unix = $this->model_extension_module_smart_marketing_timer->getSendgridUnixSendAt($campaign_info['timezone_hour'], $task['utc']);

						if ($send_at_unix) {
							$sendgrid_personalization->setSendAt($send_at_unix);
						}
					}

					$sendgrid_mail->addPersonalization($sendgrid_personalization);

					$logs[] = array(
						'campaign_task_id' => $task['campaign_task_id'],
						'email' 				 => $task['email']
					);
				}

				$sendgrid_response = $this->sendgrid->client->mail()->send()->post($sendgrid_mail);

				if ($sendgrid_response->isAccepted()) {
					$this->markAsSent($logs);
				}
			}

			$campaign_stats = $this->model_extension_module_smart_marketing_campaign->getCampaignStats($campaign_id);

			if ($campaign_stats) {
				$recipient_total = $campaign_stats['recipient'];
				$sent_total = $campaign_stats['sent'];
				$sent_percent = $campaign_stats['sent_percent'];
			}
		}

		if ($sent_percent < 100) {
			$json['continue'] = true;
			$json['status_text'] = sprintf($this->language->get('text_pause'), $sent_total, $this->config->get('module_smart_marketing_sendgrid_send_interval'), $this->config->get('module_smart_marketing_sendgrid_recipient_limit'));
			$json['guide_text'] = $this->language->get('text_do_not_close');
		} else {
			$json['status_text'] = $this->language->get('text_success');
			$json['guide_text'] = $this->language->get('text_safe_close');
		}

		$json['recipient_total'] = $recipient_total;
		$json['sent_total'] = $sent_total;
		$json['sent_percent'] = $sent_percent;

		//$json['logs'] = $logs;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function markAsSent($logs) {
		$campaign_tasks = array();

		if ($logs) {
			foreach ($logs as $log) {
				$campaign_tasks[] = $log['campaign_task_id'];
			}
		}

		if ($campaign_tasks) {
			$this->load->model('extension/module/smart_marketing/task');

			$this->model_extension_module_smart_marketing_task->setSentByCampaignTasks($campaign_tasks);
		}
	}
}
