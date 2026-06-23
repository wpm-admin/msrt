<?php
class ModelExtensionModuleSmartMarketingTimer extends Model {
	public function getNow() {
		$query = $this->db->query("SELECT NOW() AS current_datetime");

		return $query->row['current_datetime'];
	}

	public function getRemainingMinutes($schedule_datetime) {
		$query = $this->db->query("SELECT TIMESTAMPDIFF(MINUTE, TIMESTAMPADD(MINUTE, " . (int)$this->config->get('module_smart_marketing_timezone_difference') . ", NOW()), '" . $this->db->escape($schedule_datetime) . "') AS remaining_minutes");

		return $query->row['remaining_minutes'];
	}

	public function getSendgridUnixSendAt($campaign_hour, $utc_offset) {
		// first get current UTC datetime
		$current_utc_unix = date('U', time() - date('Z'));

		// subscriber current local datetime
		$current_local_utc_unix = $current_utc_unix + $utc_offset * 60 * 60;

		$wanted_local_utc_unix = date('U', strtotime(date('Y-m-d', $current_local_utc_unix) . ' ' . $campaign_hour));

		// if at least 10 minutes left until wanted datetime is reached
		if ($current_local_utc_unix - $wanted_local_utc_unix <= -10 * 60) {
			$send_at_unix = $wanted_local_utc_unix;
		} elseif ($current_local_utc_unix - $wanted_local_utc_unix >= 2 * 60 * 60) { // if more than 2 hours already passed over wanted datetime => send tommorow
			// added 1 days => send tomorrow at desired hour
			$send_at_unix = $wanted_local_utc_unix + 24 * 60 * 60;
		} else {
			// already passed less than 2 hours => it's safe to send now
			$send_at_unix = false;
		}

		// force cast to int => OCX strage behaviour
		return (int)$send_at_unix;
	}
}
