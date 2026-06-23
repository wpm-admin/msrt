<?php
class ModelExtensionModuleSmartMarketingTask extends Model {
	public function getTasksByCampaignId($campaign_id) {
		// set limit based in service used (sendgrid etc)
		$service = $this->config->get('module_smart_marketing_service');
		$limit = $this->config->get('module_smart_marketing_' . $service . '_recipient_limit');

		$query = $this->db->query("SELECT ct.campaign_task_id, ct.campaign_id, s.subscriber_id, s.firstname, s.lastname, s.email, IFNULL(cc.utc, 0) AS utc FROM " . DB_PREFIX . "sm_campaign_task ct LEFT JOIN " . DB_PREFIX . "sm_subscriber s ON (ct.subscriber_id = s.subscriber_id) LEFT JOIN " . DB_PREFIX . "country cc ON (s.country_id = cc.country_id) WHERE ct.campaign_id = '" . (int)$campaign_id . "' AND ct.sent = '0' ORDER BY ct.campaign_task_id LIMIT 0," . (int)$limit);

		return $query->rows;
	}

	public function setSentByCampaignTasks($campaign_tasks) {
		$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign_task SET sent = '1' WHERE campaign_task_id IN (" . implode(',', $campaign_tasks) . ")");
	}
}
