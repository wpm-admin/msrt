<?php
class ModelExtensionModuleSmartMarketingCampaign extends Model {
	public function addCampaign($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "sm_campaign SET name = '" . $this->db->escape($data['name']) . "', sender_name = '" . $this->db->escape($data['sender_name']) . "', sender_email = '" . $this->db->escape($data['sender_email']) . "', subject = '" . $this->db->escape($data['subject']) . "', html_content = '" . $this->db->escape($data['html_content']) . "', plain_content = '" . $this->db->escape($data['plain_content']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$campaign_id = $this->db->getLastId();

		// save segmentation
		$this->db->query("INSERT INTO " . DB_PREFIX . "sm_campaign_segmentation SET campaign_id = '" . (int)$campaign_id . "', type = '" . $this->db->escape($data['segmentation_type']) . "', `match` = '" . $this->db->escape($data['segmentation_match']) . "', `condition` = '" . $this->db->escape(isset($data['segmentation_condition']) ? serialize($data['segmentation_condition']) : '') . "'");

		// Check if is required to update or to add new template
		$this->load->model('extension/module/smart_marketing/template');

		if ($data['template_id'] && $data['template_update']) {
			$template_id = $data['template_id'];

			$this->model_extension_module_smart_marketing_template->editTemplate($template_id, $data);
		} else {
			$template_id = $this->model_extension_module_smart_marketing_template->addTemplate($data);
		}

		// Set template_id for campaign
		$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign SET template_id = '" . $this->db->escape($template_id) . "' WHERE campaign_id = '" . (int)$campaign_id . "'");

		// Set date_schedule
		if ($data['scheduled']) {
			$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign SET date_scheduled = '" . $this->db->escape($data['date_schedule']) . "' WHERE campaign_id = '" . (int)$campaign_id . "'");
		}

		// Set timezone_hour
		if ($data['timezone_based']) {
			$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign SET timezone_hour = '" . $this->db->escape($data['timezone_hour']) . "' WHERE campaign_id = '" . (int)$campaign_id . "'");
		}

		// Generate campaign tasks / log
		$task_data = array(
			'campaign_id'  => $campaign_id,
			'segmentation' => array(
				'type' 		=> $data['segmentation_type'],
				'match' 		=> $data['segmentation_match'],
				'condition' => isset($data['segmentation_condition']) ? $data['segmentation_condition'] : array()
			)
		);

		$this->generateCampaignTasks($task_data);
	}

	public function editCampaign($campaign_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign SET name = '" . $this->db->escape($data['name']) . "', sender_name = '" . $this->db->escape($data['sender_name']) . "', sender_email = '" . $this->db->escape($data['sender_email']) . "',  subject = '" . $this->db->escape($data['subject']) . "', html_content = '" . $this->db->escape($data['html_content']) . "', plain_content = '" . $this->db->escape($data['plain_content']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE campaign_id = '" . (int)$campaign_id . "'");

		// update segmentation
		$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign_segmentation SET campaign_id = '" . (int)$campaign_id . "', type = '" . $this->db->escape($data['segmentation_type']) . "', `match` = '" . $this->db->escape($data['segmentation_match']) . "', `condition` = '" . $this->db->escape(isset($data['segmentation_condition']) ? serialize($data['segmentation_condition']) : '') . "' WHERE campaign_id = '" . (int)$campaign_id . "'");

		// Check if is required to update or to add new template
		$this->load->model('extension/module/smart_marketing/template');

		if ($data['template_id'] && $data['template_update']) {
			$template_id = $data['template_id'];

			$this->model_extension_module_smart_marketing_template->editTemplate($template_id, $data);
		} else {
			$template_id = $this->model_extension_module_smart_marketing_template->addTemplate($data);
		}

		// Set template_id for campaign
		$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign SET template_id = '" . $this->db->escape($template_id) . "' WHERE campaign_id = '" . (int)$campaign_id . "'");

		// Set date_schedule
		if ($data['scheduled']) {
			$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign SET date_scheduled = '" . $this->db->escape($data['date_schedule']) . "' WHERE campaign_id = '" . (int)$campaign_id . "'");
		}

		// Set timezone_hour
		if ($data['timezone_based']) {
			$this->db->query("UPDATE " . DB_PREFIX . "sm_campaign SET timezone_hour = '" . $this->db->escape($data['timezone_hour']) . "' WHERE campaign_id = '" . (int)$campaign_id . "'");
		}

		// Generate campaign tasks / log (on edit only for subscribers where previous version of campaign wasn't sent yet)
		$this->db->query("DELETE FROM " . DB_PREFIX . "sm_campaign_task WHERE campaign_id = '" . (int)$campaign_id . "' AND sent = '0'");

		// Generate campaign tasks / log
		$task_data = array(
			'campaign_id'  => $campaign_id,
			'segmentation' => array(
				'type' 		=> $data['segmentation_type'],
				'match' 		=> $data['segmentation_match'],
				'condition' => isset($data['segmentation_condition']) ? $data['segmentation_condition'] : array()
			)
		);

		$this->generateCampaignTasks($task_data);
	}

	public function deleteCampaign($campaign_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sm_campaign WHERE campaign_id = '" . (int)$campaign_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "sm_campaign_segmentation WHERE campaign_id = '" . (int)$campaign_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "sm_campaign_task WHERE campaign_id = '" . (int)$campaign_id . "'");
	}

	public function getCampaign($campaign_id) {
		$query = $this->db->query("SELECT c.*, cs.type as segmentation_type, cs.match as segmentation_match, cs.condition as segmentation_condition FROM " . DB_PREFIX . "sm_campaign c LEFT JOIN " . DB_PREFIX . "sm_campaign_segmentation cs ON (c.campaign_id = cs.campaign_id) WHERE c.campaign_id = '" . (int)$campaign_id . "'");

		return $query->row;
	}

	public function getCampaigns($data = array()) {
		$sql = "SELECT c.*,  (SELECT COUNT(*) FROM " . DB_PREFIX . "sm_campaign_task ct WHERE ct.campaign_id = c.campaign_id) AS recipient_total, (SELECT COUNT(*) FROM " . DB_PREFIX . "sm_campaign_task ct WHERE ct.campaign_id = c.campaign_id AND sent = '1') AS sent_total FROM " . DB_PREFIX . "sm_campaign c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "c.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_subject'])) {
			$implode[] = "c.subject LIKE '%" . $this->db->escape($data['filter_subject']) . "%'";
		}

		if (!empty($data['filter_sender_name'])) {
			$implode[] = "c.sender_name LIKE '" . $this->db->escape($data['filter_sender_name']) . "%'";
		}

		if (!empty($data['filter_sender_email'])) {
			$implode[] = "c.sender_email = '" . (int)$data['filter_sender_email'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_scheduled'])) {
			$implode[] = "DATE(c.date_scheduled) = DATE('" . $this->db->escape($data['filter_date_scheduled']) . "')";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'c.campaign_id',
			'c.name',
			'c.status',
			'c.date_scheduled',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.campaign_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCampaignStats($campaign_id) {
		$stats = array();

		// faster with SUM instead of COUNT | work with sum because is 0 or 1
		$query = $this->db->query("SELECT COUNT(*) AS recipient_total, SUM(sent) AS sent_total, SUM(delivered) AS delivered_total, SUM(open) AS open_total, SUM(click) AS click_total, SUM(bounce) AS bounce_total, SUM(unsubscribe) AS unsubscribe_total, SUM(sent = '1' AND open = '0' AND click = '0' AND bounce = '0' AND unsubscribe = '0') AS no_action_total FROM " . DB_PREFIX . "sm_campaign_task WHERE campaign_id = '" . (int)$campaign_id . "'");

		if ($query->num_rows) {
			$stats = array(
				'recipient'                => $query->row['recipient_total'],
				// Sent = sent to SendGrid server
				'sent'                     => $query->row['sent_total'],
				'sent_percent'             => number_format($query->row['sent_total'] / $query->row['recipient_total'] * 100, 2),
				// Delivered = sent to subscriber inbox
				'delivered'                => $query->row['delivered_total'],
				'delivered_percent'        => number_format($query->row['delivered_total'] / $query->row['recipient_total'] * 100, 2),
				// Waiting to be send to Sendgrid server
				'waiting'                  => $query->row['recipient_total'] - $query->row['sent_total'],
				'waiting_percent'          => number_format(($query->row['recipient_total'] - $query->row['sent_total']) / $query->row['recipient_total'] * 100, 2),
				// Are already on Sendgrid server and waiting to be sent to subscriber inbox
				'waiting_delivery'         => $query->row['recipient_total'] - $query->row['delivered_total'],
				'waiting_delivery_percent' => number_format(($query->row['recipient_total'] - $query->row['delivered_total']) / $query->row['recipient_total'] * 100, 2),
				'open'                		=> $query->row['open_total'],
				'open_percent'        		=> number_format($query->row['open_total'] / $query->row['recipient_total'] * 100, 2),
				'click'               		=> $query->row['click_total'],
				'click_percent'       		=> number_format($query->row['click_total'] / $query->row['recipient_total'] * 100, 2),
				'bounce'              		=> $query->row['bounce_total'],
				'bounce_percent'      		=> number_format($query->row['bounce_total'] / $query->row['recipient_total'] * 100, 2),
				'unsubscribe'         		=> $query->row['unsubscribe_total'],
				'unsubscribe_percent' 		=> number_format($query->row['unsubscribe_total'] / $query->row['recipient_total'] * 100, 2),
				'no_action'           		=> $query->row['no_action_total'],
				'no_action_percent'   		=> number_format($query->row['no_action_total'] / $query->row['recipient_total'] * 100, 2),
			);
		}

		return $stats;
	}

	public function getCampaignFirstHoursPerformance($campaign_id, $interval) {
		$performance_data = array();

		$sql = "SELECT DATE(date_modified) AS event_date, HOUR(date_modified) AS event_hour, SUM(open) AS open_total, SUM(click) AS click_total, SUM(bounce) AS bounce_total, SUM(unsubscribe) AS unsubscribe_total FROM " . DB_PREFIX . "sm_campaign_task WHERE campaign_id = '" . (int)$campaign_id . "' AND sent = '1' AND TIMESTAMPDIFF(HOUR, date_added, date_modified) <= '" . (int)$interval . "' AND date_modified != '0000-00-00 00:00:00' GROUP BY CONCAT(DATE(date_modified), ' ', HOUR(date_modified)) ORDER BY date_modified ASC";

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			foreach($query->rows as $event) {
				$key = $event['event_date'] . '-' . (($event['event_hour'] < 10) ? '0' : '') . $event['event_hour'];

				if (!isset($performance_data[$key])) {
					$performance_data[$key] = array(
						'key'         => $key,
						'date'        => $event['event_date'],
						'hour'        => $event['event_hour'],
						'open'        => $event['open_total'],
						'click'       => $event['click_total'],
						'bounce'      => $event['bounce_total'],
						'unsubscribe' => $event['unsubscribe_total']
					);
				}
			}

			// now fill missing intervals
			$start_date = $query->rows[0]['event_date'];
			$start_hour = $query->rows[0]['event_hour'];

			for ($step = 1; $step <= $interval; $step++) {
				$key = date('Y-m-d-H', strtotime($start_date . ' ' . (($start_hour < 10) ? '0' : '') . $start_hour . ':00:00 + ' . $step . ' Hours'));

				if (!isset($performance_data[$key])) {
					$performance_data[$key] = array(
						'key'         => $key,
						'date'        => substr($key, 0, 10),
						'hour'        => (int)substr($key, 11, 2),
						'open'        => 0,
						'click'       => 0,
						'bounce'      => 0,
						'unsubscribe' => 0
					);
				}
			}
		}

		// sort after key (event date - hour)
		ksort($performance_data);

		return $performance_data;
	}

	public function getTotalCampaigns($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sm_campaign c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "c.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_subject'])) {
			$implode[] = "c.subject LIKE '%" . $this->db->escape($data['filter_subject']) . "%'";
		}

		if (!empty($data['filter_sender_name'])) {
			$implode[] = "c.sender_name LIKE '" . $this->db->escape($data['filter_sender_name']) . "%'";
		}

		if (!empty($data['filter_sender_email'])) {
			$implode[] = "c.sender_email = '" . (int)$data['filter_sender_email'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_scheduled'])) {
			$implode[] = "DATE(c.date_scheduled) = DATE('" . $this->db->escape($data['filter_date_scheduled']) . "')";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function generateCampaignTasks($data) {
		$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "sm_campaign_task (campaign_id, subscriber_id, date_added) " . $this->getSQLConditionsBased($data) . " ORDER BY rating DESC, date_added DESC");
	}

	public function getTotalRecipientsConditionsBased($data) {
		$task_data = array(
			'campaign_id'  => PHP_INT_MAX,
			'segmentation' => array(
				'type' 		=> $data['segmentation_type'],
				'match' 		=> $data['segmentation_match'],
				'condition' => isset($data['segmentation_condition']) ? $data['segmentation_condition'] : array()
			)
		);

		$query = $this->db->query($this->getSQLConditionsBased($task_data));

		return $query->num_rows;
	}

	public function getSQLConditionsBased($data) {
		// SQL which will generate data for campaign_task based on some conditions
		$sql = "SELECT " . $data['campaign_id'] . " AS campaign_id, subscriber_id, NOW() AS date_added FROM " . DB_PREFIX . "sm_subscriber WHERE status = '1'";

		// all active subscribers
		if ($data['segmentation']['type'] == 'subscriber') {
			// no extra condition - already covered by default sql
		}

		// only customers
		if ($data['segmentation']['type'] == 'customer') {
			$sql .= " AND customer_id > 0";
		}

		// only customers from certain group
		if ($data['segmentation']['type'] == 'customer_group') {
			$customer_groups = $this->getSegmentationConditionValues($data['segmentation']['condition'][$data['segmentation']['type']]);

			$sql .= " AND customer_id > 0 AND customer_group_id IN (" . implode(",", $customer_groups) . ")";
		}

		// customers who bought certain products
		if ($data['segmentation']['type'] == 'customer_who_bought_product') {
			$sql .= " AND LOWER(email) IN (" . $this->getSQLCustomersWhoBoughtProduct($data['segmentation']['condition'][$data['segmentation']['type']]) . ")";
		}

		// segmenation case which require to check more than a field
		$custom_segmentation_types = $this->getCustomConditionKeys('customer');

		foreach ($custom_segmentation_types as $custom_segmentation_type) {
			if ($data['segmentation']['type'] == $custom_segmentation_type) {
				$sql .= " AND LOWER(email) IN (" . $this->{'getSQLCustomers' . ucwords(str_replace(array('customer','_'), "", $custom_segmentation_type))}($data['segmentation']['condition'][$custom_segmentation_type][0]) . ")";
			}
		}

		if ($data['segmentation']['type'] == 'advanced_segment') {
			// special cases where is NOT possible to apply fitler on single field from table subscriber
			$custom_condition_keys = $this->getCustomConditionKeys();

			$implode = array();

			$sql_parts = $this->getSQLPartsOperatorsBased();

			foreach ($data['segmentation']['condition'][$data['segmentation']['type']] as $condition) {
				$sql_part = $sql_parts[$condition['operator']];

				$find = array(
					'{key}',
					'{value}'
				);

				$replace = array(
					'key'   => $this->db->escape($condition['key']),
					'value' => $this->db->escape($condition['value'])
				);

				if (in_array($condition['key'], $custom_condition_keys)) {
					$implode[] = "LOWER(email) IN (" . $this->{'getSQLCustomers' . ucwords(str_replace("_", "", $condition['key']))}($condition) . ")";
				} else {
					if (strpos($sql_part, '{key}') !== false && strpos($sql_part, '{value}') !== false) {
						$implode[] = str_replace($find, $replace, $sql_part);
					} elseif (strpos($sql_part, '{value}') !== false) {
						$implode[] = $condition['key'] . ' ' . str_replace($find, $replace, $sql_part);
					} else {
						$operator = $sql_part;

						$implode[] = $condition['key'] . ' ' . $operator . ' \'' . $condition['value'] . '\'';
					}
				}
			}

			if ($implode) {
				$sql .= " AND ( " . implode(utf8_strtoupper(' ' . $data['segmentation']['match']) . ' ', $implode) . " )";
			}
		}

		return $sql;
	}

	public function getSegmentationConditionValues($conditions, $filter_key = '') {
		$values = array();

		if ($conditions) {
			foreach ($conditions as $condition) {
				if (!$filter_key || ($filter_key && $condition['key'] == $filter_key)) {
					$values[] = $condition['value'];
				}
			}
		}

		return $values;
	}

	public function getSegmentationConditionValueByConditionKey($conditions, $condition_key) {
		$condition_value = false;

		if ($conditions) {
			foreach ($conditions as $condition) {
				if ($condition['key'] == $condition_key) {
					$condition_value = $condition['value'];

					break;
				}
			}
		}

		return $condition_value;
	}

	public function getSQLPartsOperatorsBased() {
		return array(
			'equal'              => '=',
			'not-equal'          => '!=',
			'greater'            => '>',
			'less'               => '<',
			'contain'            => 'LOWER({key}) LIKE LOWER(\'%{value}%\')',
			'not-contain'        => 'LOWER({key}) NOT LIKE LOWER(\'%{value}%\')',
			'start-with'         => 'LOWER({key}) LIKE LOWER(\'{value}%\')',
			'end-with'           => 'LOWER({key}) NOT LIKE LOWER(\'%{value}\')',
			'is-before'          => 'DATE({key}) < DATE(\'{value}\')',
			'is-after'           => 'DATE({key}) > DATE(\'{value}\')',
			'is-before-or-equal' => 'DATE({key}) <= DATE(\'{value}\')',
			'is-after-or-equal'  => 'DATE({key}) => DATE(\'{value}\')'
		);
	}

	public function convertOCX2SQLOperator($operator) {
		$sql_parts = $this->getSQLPartsOperatorsBased();

		return $sql_parts[$operator];
	}

	public function getPendingCompleteOrderStatuses() {
		$order_statuses =  array_unique(array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')));

		return $order_statuses;
	}

	public function getSQLCustomersWhoBoughtProduct($condition) {
		$products = $this->getSegmentationConditionValues($condition, 'product_id');
		$date_start = $this->getSegmentationConditionValueByConditionKey($condition, 'date_start');
		$date_end = $this->getSegmentationConditionValueByConditionKey($condition, 'date_end');

		$order_statuses = $this->getPendingCompleteOrderStatuses();

		$sql = "SELECT DISTINCT LOWER(o.email) AS email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE o.order_status_id IN (" . implode(",", $order_statuses) . ") AND op.product_id IN (" . implode(",", $products) . ")";

		if ($date_start) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($date_start) . "')";
		}

		if ($date_end) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($date_end) . "')";
		}

		return $sql;
	}

	public function getSQLCustomersTotalSpent($condition) {
		$order_statuses = $this->getPendingCompleteOrderStatuses();

		$operator = $this->convertOCX2SQLOperator($condition['operator']);
		$total_spent = $condition['value'];

		$sql = "SELECT LOWER(o.email) FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id IN (" . implode(",", $order_statuses) . ") GROUP BY LOWER(o.email) HAVING SUM(o.total) " . $operator . " '" . (float)$total_spent . "'";

		return $sql;
	}

	public function getSQLCustomersOrderCount($condition) {
		$order_statuses =  $this->getPendingCompleteOrderStatuses();

		$operator = $this->convertOCX2SQLOperator($condition['operator']);
		$total_orders = $condition['value'];

		if ($operator == '=' && $total_orders == 0) {
			$sql = "SELECT LOWER(s.email) FROM " . DB_PREFIX . "sm_subscriber s WHERE LOWER(s.email) NOT IN (SELECT DISTINCT LOWER(o.email) FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id IN (" . implode(",", $order_statuses) . "))";
		} else {
			$sql = "SELECT LOWER(o.email) FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id IN (" . implode(",", $order_statuses) . ") GROUP BY LOWER(o.email) HAVING COUNT(o.order_id) " . $operator . " '" . (float)$total_orders . "'";
		}

		return $sql;
	}

	public function getSQLCustomersLastOrder($condition) {
		$order_statuses = $this->getPendingCompleteOrderStatuses();

		$operator = $this->convertOCX2SQLOperator($condition['operator']);
		$days_ago = $condition['value'];

		$sql = "SELECT LOWER(o.email) FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id IN (" . implode(",", $order_statuses) . ") GROUP BY LOWER(o.email) HAVING DATEDIFF(NOW(), MAX(o.date_added)) " . $operator . " '" . (int)$days_ago . "'";

		return $sql;
	}

	public function getSQLCustomersLastLogin($condition) {
		$order_statuses = $this->getPendingCompleteOrderStatuses();

		$operator = $this->convertOCX2SQLOperator($condition['operator']);
		$days_ago = $condition['value'];

		$sql = "SELECT LOWER(c.email) FROM " . DB_PREFIX . "customer_activity ca LEFT JOIN " . DB_PREFIX . "customer c ON (ca.customer_id = c.customer_id) WHERE ca.key = 'login' GROUP BY ca.customer_id HAVING DATEDIFF(NOW(), MAX(ca.date_added)) " . $operator . " '" . (int)$days_ago . "'";

		return $sql;
	}

	public function getCustomConditionKeys($prefix = '') {
		$key_prefix = ($prefix) ? $prefix . '_' : '';

		$custom_condition_keys = array(
			$key_prefix . 'total_spent',
			$key_prefix . 'order_count',
			$key_prefix . 'last_order',
			$key_prefix . 'last_login'
		);

		return $custom_condition_keys;
	}
}
