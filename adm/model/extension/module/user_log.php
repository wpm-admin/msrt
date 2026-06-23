<?php
class ModelExtensionModuleUserLog extends Model {

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "user_log`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "user_log_ban`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "user_log_post`");
	}

	public function install() {
		$query = $this->db->query("
		CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "user_log` (  
			`user_log_id` int(11) NOT NULL AUTO_INCREMENT,
			`user_id` int(11) NOT NULL,
			`user_name` varchar(20) NOT NULL,
			`action` varchar(50) NOT NULL,
			`result` tinyint(1) NOT NULL,
			`url` varchar(255) NOT NULL,
			`route` varchar(200) NOT NULL,
			`ip` varchar(255) NOT NULL,
			`referer` varchar(255) NOT NULL,
			`user_agent` varchar(255) NOT NULL,
			`post_data` longtext,
			`date` datetime NOT NULL,
			PRIMARY KEY (`user_log_id`)
		) ENGINE=InnoDB ");

		$query = $this->db->query("
		CREATE TABLE IF NOT EXISTS `". DB_PREFIX . "user_log_ban` (
			`log_ban_id` int(11) NOT NULL AUTO_INCREMENT,
			`ip` varchar(255) DEFAULT NULL,
			`date` datetime NOT NULL,
			PRIMARY KEY (`log_ban_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$query = $this->db->query("
		CREATE TABLE IF NOT EXISTS `". DB_PREFIX . "user_log_post` (
			`user_log_id` int(11),
			`post_data` LONGTEXT NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	public function getPost($user_log_id) {
		$sql = "SELECT post_data FROM `" . DB_PREFIX . "user_log` WHERE user_log_id = " . (int)$user_log_id;
		$result = $this->db->query($sql);
		if ($result->num_rows) {
			return print_r(json_decode($result->row['post_data'],true),true);
		} else {
			return '';
		}
	}

	public function getBanIps($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "user_log_ban WHERE 1";
		$re = $this->db->query($sql);
		return ($re->rows);
	}

	public function deleteBanIp($id) {
		$sql = "DELETE FROM " . DB_PREFIX . "user_log_ban WHERE log_ban_id = '" . (int)$id . "'";
		$re = $this->db->query($sql);
	}

	public function clearBanIp() {
		$sql = "TRUNCATE " . DB_PREFIX . "user_log_ban";
		$re = $this->db->query($sql);
	}

	private function getRealIp(){
		$ip = isset($this->request->server['REMOTE_ADDR'])?$this->request->server['REMOTE_ADDR']:'';
		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$ip = $this->request->server['HTTP_X_FORWARDED_FOR'];	
		} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$ip = $this->request->server['HTTP_CLIENT_IP'];	
		}
		return $ip;
	}
	
	public function addBanIp($data) {
		if ($data['ip'] != $this->getRealIp() ) {
			$sql = "INSERT INTO " . DB_PREFIX . "user_log_ban SET
			ip = '" . $this->db->escape($data['ip']) . "'";
			$re = $this->db->query($sql);
			return $this->db->getLastId();
		} else {
			return false;
		}
	}

	public function getRecords($data = array()) {
		$sql = "SELECT 
			`user_log_id`,
			`user_id`,
			`user_name`,
			`action`,
			`result`,
			`url`,
			`route`,
			`ip`,
			`date`		
		FROM " . DB_PREFIX . "user_log WHERE 1";
		if (isset($data['filter_user_id']) && $data['filter_user_id']) {
			$sql .= " AND user_id = '" .(int)$data['filter_user_id'] . "'"; 
		}
		$NOW = date('Y-m-d') . '23:59:59';
		if (isset($data['filter_start']) && $data['filter_start']) {
			$filter_start = min($data['filter_start'], $NOW);
		} else {
			$filter_start = '0';
		}	
		if (isset($data['filter_end']) && $data['filter_end']) {
			list($filter_end) = explode(' ',max($filter_start,$data['filter_end']),1);
			$filter_end  .=  "23:59:59";
		} else {
			$filter_end = $NOW;
		}	
		if (isset($data['filter_start']) || isset($data['filter_end'])) {
			$sql .= " AND `date` BETWEEN  '" . $filter_start . "' AND '" . $filter_end . "'"; 
		}

		if (isset($data['filter_url'])) {
			$sql .= " AND `url` LIKE '%" . $this->db->escape($data['filter_url']) . "%'"; 
		}

		if (isset($data['filter_action'])) {
			$sql .= " AND `result` = '" . (int)$data['filter_action'] ."'"; 
		}
		
		
		$sort_data = array(
			'date',
			'user_id',
			'result',
			'action',
			'url'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}
		
		if (isset($data['sort']) && $data['sort'] != 'date') {
			$sql .= ", date";
		}
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 50;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalRecords($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_log WHERE 1 ";

		$NOW = date('Y-m-d') . ' 00:00:00';

		if (isset($data['filter_user_id']) && $data['filter_user_id']) {
			$sql .= " AND user_id = '" .(int)$data['filter_user_id'] . "'"; 
		}
		$NOW = date('Y-m-d') . ' 23:59:59';
		if (isset($data['filter_start']) && $data['filter_start']) {
			$filter_start = min($data['filter_start'], $NOW);
		} else {
			$filter_start = '0';
		}	
		if (isset($data['filter_end']) && $data['filter_end']) {
			list($filter_end) = explode(' ',max($filter_start,$data['filter_end']),1);
			$filter_end  .=  " 23:59:59";
		} else {
			$filter_end = $NOW;
		}	
		if (isset($data['filter_start']) || isset($data['filter_end'])) {
			$sql .= " AND `date` BETWEEN  '" . $filter_start . "' AND '" . $filter_end . "'"; 
		}

		if (isset($data['filter_action'])) {
			$sql .= " AND `result` = '" . (int)$data['filter_action'] ."'"; 
		}

		if (isset($data['filter_url'])) {
			$sql .= " AND `url` LIKE '%" . $this->db->escape($data['filter_url']) . "%'"; 
		}

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function clear() {
		$this->db->query("TRUNCATE " . DB_PREFIX . "user_log");
		$this->config->set('user_log_id', '');
		$data = array(
			'action' 	=> 'clear log', 
			'result' 	=> 1, 
		);
		$this->user->addLog($data);
		return true;
	}

	public function deleteRecord($user_log_id) {
		if ($user_log_id)
		$this->db->query("DELETE FROM " . DB_PREFIX . "user_log WHERE user_log_id = ".$user_log_id);
		return true;
	}
}
