<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
libxml_use_internal_errors(true);

class ModelExtensionOcdevwizardAdminSecurity extends Model {
  private $_name = 'admin_security';
  private $_code = 'ocdw_admin_security';
  private $_version;

  public function __construct($registry) {
    parent::__construct($registry);

    if (file_exists(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name) && is_dir(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name)) {
      if (file_exists(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/module.ocdw')) {
        $version_array = json_decode(file_get_contents(DIR_SYSTEM.'library/ocdevwizard/'.$this->_name.'/module.ocdw'),true);

        if ($version_array) {
          $this->_version = $version_array['module'];
        }
      }
    }
  }

  public function createDBTables() {
    $sql = [];

    $sql[] = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ocdevwizard_setting` ("
             ."`setting_id` int(11) NOT NULL AUTO_INCREMENT,"
             ."`store_id` int(11) NOT NULL DEFAULT '0',"
             ."`code` text NOT NULL,"
             ."`key` text NOT NULL,"
             ."`value` text NOT NULL,"
             ."`serialized` tinyint(1) NOT NULL,"
             ."PRIMARY KEY (`setting_id`)"
             .") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;";

    $sql[] = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX.$this->_code."_record` ("
             ."`record_id` int(11) NOT NULL AUTO_INCREMENT,"
             ."`username` varchar(32) NOT NULL,"
             ."`user_id` int(11) NOT NULL,"
             ."`ip` varchar(40) NOT NULL,"
             ."`total` int(4) NOT NULL,"
             ."`token` text NOT NULL,"
             ."`date_added` datetime NOT NULL,"
             ."`date_modified` datetime NOT NULL,"
             ."PRIMARY KEY (`record_id`) "
             .") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;";

    $sql[] = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX.$this->_code."_banned` ("
             ."`banned_id` int(11) NOT NULL AUTO_INCREMENT,"
             ."`status` tinyint(1) NOT NULL DEFAULT '0',"
             ."`ip` text NOT NULL,"
             ."`date_added` datetime NOT NULL,"
             ."`date_modified` datetime NOT NULL,"
             ."PRIMARY KEY (`banned_id`)"
             .") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;";

    $sql[] = "CREATE TABLE IF NOT EXISTS ".DB_PREFIX.$this->_code."_email_template ( "
             ."`template_id` int(11) NOT NULL AUTO_INCREMENT,"
             ."`system_name` text NOT NULL,"
             ."`status` tinyint(1) NOT NULL DEFAULT '0',"
             ."`assignment` int(11) NOT NULL,"
             ."`date_added` datetime NOT NULL,"
             ."`date_modified` datetime NOT NULL,"
             ."PRIMARY KEY (`template_id`)"
             .") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;";

    $sql[] = "CREATE TABLE IF NOT EXISTS ".DB_PREFIX.$this->_code."_email_template_description ("
             ."`template_id` int(11) NOT NULL AUTO_INCREMENT,"
             ."`language_id` int(11) NOT NULL,"
             ."`subject` varchar(255) NOT NULL,"
             ."`template` text NOT NULL,"
             ."PRIMARY KEY (`template_id`,`language_id`),"
             ."KEY `subject` (`subject`)"
             .") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;";

    foreach ($sql as $query) {
      $this->db->query($query);
    }
  }

  public function deleteDBTables() {
    $this->db->query("DROP TABLE IF EXISTS ".DB_PREFIX.$this->_code."_record;");
    $this->db->query("DROP TABLE IF EXISTS ".DB_PREFIX.$this->_code."_banned;");
    $this->db->query("DROP TABLE IF EXISTS ".DB_PREFIX.$this->_code."_email_template;");
    $this->db->query("DROP TABLE IF EXISTS ".DB_PREFIX.$this->_code."_email_template_description;");
  }

  public function addRecord($data) {
    $this->db->query("
      INSERT INTO ".DB_PREFIX.$this->_code."_record 
      SET 
        ip = '".$this->db->escape($data['ip'])."',
        token = '".$this->db->escape($data['token'])."',
        date_added = NOW()
    ");

    $record_id = $this->db->getLastId();

    $data['record_id'] = $record_id;

    $this->mailing($data,['to_admin_on_attempt_login']);
  }

  public function getRecord($record_id) {
    $query = $this->db->query("
      SELECT 
        DISTINCT *
      FROM ".DB_PREFIX.$this->_code."_record r
      WHERE 
        r.record_id = '".(int)$record_id."'
    ");

    if ($query->num_rows) {
      return [
        'record_id'     => $query->row['record_id'],
        'username'      => $query->row['username'],
        'user_id'       => $query->row['user_id'],
        'ip'            => $query->row['ip'],
        'token'         => $query->row['token'],
        'banned_status' => $this->checkBanned($query->row['ip']),
        'date_added'    => $query->row['date_added'],
        'date_modified' => $query->row['date_modified']
      ];
    } else {
      return false;
    }
  }

  public function getRecords($data = []) {
    $sql = "SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_record r WHERE r.record_id > '0'";

    if (isset($data['filter_ip']) && !empty($data['filter_ip'])) {
      $sql .= " AND r.ip LIKE '%".$this->db->escape($data['filter_ip'])."%'";
    }

    if (isset($data['filter_date_added']) && !empty($data['filter_date_added'])) {
      $sql .= " AND DATE(r.date_added) = DATE('".$this->db->escape($data['filter_date_added'])."')";
    }

    $sql .= " GROUP BY r.record_id";

    $sort_data = [
      'r.ip',
      'r.date_added',
      'r.status'
    ];

    if (isset($data['sort']) && in_array($data['sort'],$sort_data)) {
      $sql .= " ORDER BY ".$data['sort'];
    } else {
      $sql .= " ORDER BY r.date_added";
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

      $sql .= " LIMIT ".(int)$data['start'].",".(int)$data['limit'];
    }

    $query = $this->db->query($sql)->rows;

    $records = [];

    if ($query) {
      foreach ($query as $row) {
        $records[$row['record_id']] = $this->getRecord($row['record_id']);
      }
    }

    return $records;
  }

  public function getExportRecords() {
    $results = [];

    $query = $this->db->query("SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_record")->rows;

    if ($query) {
      foreach ($query as $row) {
        $data = [];

        $data = $row;

        $results[] = $data;
      }
    }

    return $results;
  }

  public function getTotalRecords($data = []) {
    $sql = "
      SELECT 
        COUNT(DISTINCT r.record_id) AS total
      FROM ".DB_PREFIX.$this->_code."_record r
      WHERE 
        r.record_id > '0'
    ";

    if (isset($data['filter_ip']) && !empty($data['filter_ip'])) {
      $sql .= " AND r.ip LIKE '%".$this->db->escape($data['filter_ip'])."%'";
    }

    if (isset($data['filter_date_added']) && !empty($data['filter_date_added'])) {
      $sql .= " AND DATE(r.date_added) = DATE('".$this->db->escape($data['filter_date_added'])."')";
    }

    return $this->db->query($sql)->row['total'];
  }

  public function getTotalAttempts() {
    $sql = "
      SELECT 
        SUM(r.total) AS total
      FROM ".DB_PREFIX.$this->_code."_record r
      WHERE 
        r.record_id > '0'
    ";

    return $this->db->query($sql)->row['total'];
  }

  public function getBanned($banned_id) {
    return $this->db->query("SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_banned b WHERE b.banned_id = '".(int)$banned_id."'")->row;
  }

  public function getBannedByIp($ip) {
    return $this->db->query("SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_banned b WHERE b.ip = '".$this->db->escape($ip)."'")->rows;
  }

  public function getBanneds($data = []) {
    $sql = "SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_banned b WHERE b.banned_id > '0'";

    if (isset($data['filter_ip']) && !empty($data['filter_ip'])) {
      $sql .= " AND b.ip LIKE '%".$this->db->escape($data['filter_ip'])."%'";
    }

    if (isset($data['filter_date_added']) && !empty($data['filter_date_added'])) {
      $sql .= " AND DATE(b.date_added) = DATE('".$this->db->escape($data['filter_date_added'])."')";
    }

    if (isset($data['filter_date_modified']) && !empty($data['filter_date_modified'])) {
      $sql .= " AND DATE(b.date_modified) = DATE('".$this->db->escape($data['filter_date_modified'])."')";
    }

    if (isset($data['filter_status']) && $data['filter_status'] != '*') {
      $sql .= " AND b.status = '".(int)$data['filter_status']."'";
    }

    $sql .= " GROUP BY b.banned_id";

    $sort_data = [
      'b.ip',
      'b.date_added',
      'b.date_modified',
      'b.status'
    ];

    if (isset($data['sort']) && in_array($data['sort'],$sort_data)) {
      $sql .= " ORDER BY ".$data['sort'];
    } else {
      $sql .= " ORDER BY b.date_added";
    }

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
      $sql .= " DESC, LCASE(b.ip) DESC";
    } else {
      $sql .= " ASC, LCASE(b.ip) ASC";
    }

    if (isset($data['start']) || isset($data['limit'])) {
      if ($data['start'] < 0) {
        $data['start'] = 0;
      }

      if ($data['limit'] < 1) {
        $data['limit'] = 20;
      }

      $sql .= " LIMIT ".(int)$data['start'].",".(int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
  }

  public function getTotalBanneds($data = []) {
    $sql = "SELECT COUNT(DISTINCT b.banned_id) AS total FROM ".DB_PREFIX.$this->_code."_banned b WHERE b.banned_id > '0'";

    if (isset($data['filter_ip']) && !empty($data['filter_ip'])) {
      $sql .= " AND b.ip LIKE '%".$this->db->escape($data['filter_ip'])."%'";
    }

    if (isset($data['filter_date_added']) && !empty($data['filter_date_added'])) {
      $sql .= " AND DATE(b.date_added) = DATE('".$this->db->escape($data['filter_date_added'])."')";
    }

    if (isset($data['filter_date_modified']) && !empty($data['filter_date_modified'])) {
      $sql .= " AND DATE(b.date_modified) = DATE('".$this->db->escape($data['filter_date_modified'])."')";
    }

    if (isset($data['filter_status']) && $data['filter_status'] != '*') {
      $sql .= " AND b.status = '".(int)$data['filter_status']."'";
    }

    return $this->db->query($sql)->row['total'];
  }

  public function getExportBanneds() {
    $results = [];

    $query = $this->db->query("SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_banned")->rows;

    if ($query) {
      foreach ($query as $row) {
        $data = [];

        $data = $row;

        $results[] = $data;
      }
    }

    return $results;
  }

  public function getEmailTemplate($template_id,$language_id = 0) {
    $sql = "SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_email_template et";

    if ($language_id) {
      $sql .= " LEFT JOIN ".DB_PREFIX.$this->_code."_email_template_description etd ON (et.template_id = etd.template_id)";
    }

    $sql .= " WHERE et.template_id = '".(int)$template_id."'";

    if ($language_id) {
      $sql .= " AND etd.language_id = '".(int)$language_id."'";
    }

    return $this->db->query($sql)->row;
  }

  public function getEmailTemplates($data = []) {
    $sql = "SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_email_template et WHERE et.template_id IS NOT NULL";

    if (isset($data['filter_name']) && !empty($data['filter_name'])) {
      $sql .= " AND et.system_name LIKE '%".$this->db->escape($data['filter_name'])."%'";
    }

    if (isset($data['filter_date_added']) && !empty($data['filter_date_added'])) {
      $sql .= " AND DATE(et.date_added) = DATE('".$this->db->escape($data['filter_date_added'])."')";
    }

    if (isset($data['filter_date_modified']) && !empty($data['filter_date_modified'])) {
      $sql .= " AND DATE(et.date_modified) = DATE('".$this->db->escape($data['filter_date_modified'])."')";
    }

    if (isset($data['filter_assignment']) && $data['filter_assignment']) {
      $sql .= " AND et.assignment = '".(int)$data['filter_assignment']."'";
    }

    if (isset($data['filter_status']) && $data['filter_status'] != '*') {
      $sql .= " AND et.status = '".(int)$data['filter_status']."'";
    }

    $sql .= " GROUP BY et.template_id";

    $sort_data = [
      'et.system_name',
      'et.date_added',
      'et.date_modified',
      'et.status'
    ];

    if (isset($data['sort']) && in_array($data['sort'],$sort_data)) {
      if ($data['sort'] == 'et.system_name') {
        $sql .= " ORDER BY LCASE(".$data['sort'].")";
      } else {
        $sql .= " ORDER BY ".$data['sort'];
      }
    } else {
      $sql .= " ORDER BY et.date_added";
    }

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
      $sql .= " DESC, LCASE(et.system_name) DESC";
    } else {
      $sql .= " ASC, LCASE(et.system_name) ASC";
    }

    if (isset($data['start']) || isset($data['limit'])) {
      if ($data['start'] < 0) {
        $data['start'] = 0;
      }

      if ($data['limit'] < 1) {
        $data['limit'] = 20;
      }

      $sql .= " LIMIT ".(int)$data['start'].",".(int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
  }

  public function getEmailTemplateDescription($template_id) {
    $results = [];

    $query = $this->db->query("SELECT * FROM ".DB_PREFIX.$this->_code."_email_template_description WHERE template_id = '".(int)$template_id."'")->rows;

    if ($query) {
      foreach ($query as $row) {
        $results[$row['language_id']] = [
          'subject'  => $row['subject'],
          'template' => $row['template']
        ];
      }
    }

    return $results;
  }

  public function getExportEmailTemplates() {
    $query = $this->db->query("SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_email_template")->rows;

    $results = [];

    if ($query) {
      foreach ($query as $row) {
        $data = [];

        $data = $row;

        $data = array_merge($data,['template_description' => $this->getEmailTemplateDescription($row['template_id'])]);

        $results[] = $data;
      }
    }

    return $results;
  }

  public function getTotalEmailTemplates($data = []) {
    $sql = "SELECT COUNT(DISTINCT et.template_id) AS total FROM ".DB_PREFIX.$this->_code."_email_template et WHERE et.template_id IS NOT NULL";

    if (isset($data['filter_name']) && !empty($data['filter_name'])) {
      $sql .= " AND et.system_name LIKE '%".$this->db->escape($data['filter_name'])."%'";
    }

    if (isset($data['filter_date_added']) && !empty($data['filter_date_added'])) {
      $sql .= " AND DATE(et.date_added) = DATE('".$this->db->escape($data['filter_date_added'])."')";
    }

    if (isset($data['filter_date_modified']) && !empty($data['filter_date_modified'])) {
      $sql .= " AND DATE(et.date_modified) = DATE('".$this->db->escape($data['filter_date_modified'])."')";
    }

    if (isset($data['filter_status']) && $data['filter_status'] != '*') {
      $sql .= " AND et.status = '".(int)$data['filter_status']."'";
    }

    return $this->db->query($sql)->row['total'];
  }

  public function getStoreSetting($store_id) {
    $query = $this->db->query("SELECT `key`, `value` FROM ".DB_PREFIX."setting WHERE store_id = '".(int)$store_id."'")->rows;

    $results = [];

    if ($query) {
      foreach ($query as $row) {
        $results[$row['key']] = $row['value'];
      }
    }

    return $results;
  }

  public function getStore($store_id) {
    return $this->db->query("SELECT DISTINCT * FROM ".DB_PREFIX."store WHERE store_id = '".(int)$store_id."'")->row;
  }

  public function getMultiLanguageValue($filename,$value) {
    $models = [
      'localisation/language'
    ];

    foreach ($models as $model) {
      $this->load->model($model);
    }

    $_      = [];
    $result = [];

    foreach ($this->model_localisation_language->getLanguages() as $language) {
      $file = DIR_LANGUAGE.$language['directory'].'/'.$filename.'.php';

      if (file_exists($file)) {
        require($file);
      }

      if (isset($_[$value]) && $_[$value]) {
        $result[$language['language_id']] = $_[$value];
      }
    }

    return $result;
  }

  public function checkBanned($ip) {
    $sql = "SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_banned WHERE (";

    $sql .= "ip = '".$this->db->escape($ip)."') AND status = '1'";

    $query = $this->db->query($sql)->row;

    if ($query) {
      return true;
    } else {
      return false;
    }
  }

  public function getLanguageIdByCode($code) {
    return $this->db->query("SELECT language_id FROM ".DB_PREFIX."language WHERE code = '".$this->db->escape($code)."'")->row['language_id'];
  }

  private function checkIfColumnExist($table,$table_column) {
    return $this->db->query("SELECT COUNT(*) as total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".DB_DATABASE."' AND TABLE_NAME = '".$table."' AND COLUMN_NAME  = '".$table_column."'")->row['total'];
  }

  public function emptyRecordByIp($ip) {
    $this->db->query("UPDATE ".DB_PREFIX.$this->_code."_record SET total = 0, date_modified = '".$this->db->escape(date('Y-m-d H:i:s'))."' WHERE ip = '".$this->db->escape($ip)."'");
  }

  public function getRecordByIp($ip) {
    $query = $this->db->query("SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_record WHERE ip = '".$this->db->escape($ip)."'");

    if ($query->num_rows) {
      return $query->row;
    }
  }

  public function updateRecord($data) {
    $record_info = $this->getRecordByIp($data['ip']);
    $language_id = $this->getLanguageIdByCode($this->config->get('config_admin_language'));

    if ($data['type'] == 'guest') {
      if ($record_info && ($record_info['total'] >= $data['form_data']['access_attempts']) && strtotime('-1 hour') < strtotime($record_info['date_modified'])) {
        $stop = true;
      }

      if (!isset($stop)) {
        $this->db->query("
          UPDATE ".DB_PREFIX.$this->_code."_record 
          SET 
            total = (total + 1), 
            date_modified = '".$this->db->escape(date('Y-m-d H:i:s'))."' 
          WHERE ip = '".$this->db->escape($data['ip'])."'
        ");

        $filter_data = [
          'record_id'   => $record_info['record_id'],
          'form_data'   => $data['form_data'],
          'language_id' => $language_id
        ];

        if ($data['form_data']['admin_alert_login_attempt_status'] == 1) {
          $this->mailing($filter_data,['to_admin_on_attempt_login']);
        }
      }
    } else if ($data['type'] == 'logged') {
      $this->db->query("
        UPDATE ".DB_PREFIX.$this->_code."_record 
        SET 
          username = '".$this->db->escape($this->user->getUserName())."', 
          user_id = '".$this->db->escape($this->user->getId())."' 
        WHERE ip = '".$this->db->escape($data['ip'])."'
      ");

      $filter_data = [
        'record_id'   => $record_info['record_id'],
        'form_data'   => $data['form_data'],
        'language_id' => $language_id
      ];

      if ($data['form_data']['admin_alert_login_success_status'] == 1) {
        $this->mailing($filter_data,['to_admin_on_success_login']);
      }
    }
  }

  public function checkIp($ip,$ip_allowed) {
    $ip         = explode('.',trim($ip));
    $ip_allowed = explode('.',trim($ip_allowed));

    $count = 0;

    if (isset($ip_allowed[0])) {
      if ($ip_allowed[0] == "*") {
        $count++;
      } else {
        if (isset($ip[0]) && $ip_allowed[0] == $ip[0]) {
          $count++;
        }
      }
    }

    if (isset($ip_allowed[1])) {
      if ($ip_allowed[1] == "*") {
        $count++;
      } else {
        if (isset($ip[1]) && $ip_allowed[1] == $ip[1]) {
          $count++;
        }
      }
    }

    if (isset($ip_allowed[2])) {
      if ($ip_allowed[2] == "*") {
        $count++;
      } else {
        if (isset($ip[2]) && $ip_allowed[2] == $ip[2]) {
          $count++;
        }
      }
    }

    if (isset($ip_allowed[3])) {
      if ($ip_allowed[3] == "*") {
        $count++;
      } else {
        if (isset($ip[3]) && $ip_allowed[3] == $ip[3]) {
          $count++;
        }
      }
    }

    return ($count == 4) ? 1 : 0;
  }

  private function mailing($data,$types) {
    if ($data && $types) {
      $record_info = $this->getRecord($data['record_id']);

      if ($record_info && !$record_info['banned_status']) {
        $form_data        = $data['form_data'];
        $config_store_url = $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG;
        $language_id      = $this->getLanguageIdByCode($this->config->get('config_admin_language'));

        if ($form_data) {
          foreach ($types as $type) {
            if ($type == 'to_admin_on_attempt_login') {
              $filter_data['set_to']      = $form_data['admin_email_for_notification'];
              $filter_data['template_id'] = $form_data['admin_email_login_attempt_template'];
              $filter_data['language_id'] = $language_id;

              $filter_data['tag_codes_subject'] = [
                '{ip}',
                '{record_id}',
                '{date_added}',
                '{store_name}'
              ];

              $filter_data['tag_codes_replace_subject'] = [
                $record_info['ip'],
                $record_info['record_id'],
                date("Y-m-d H:i:s",strtotime($record_info['date_added'])),
                $this->config->get('config_name')
              ];

              $filter_data['tag_codes_template'] = [
                '{ip}',
                '{record_id}',
                '{date_added}',
                '{store_name}',
                '{permanent_user_ban_url}'
              ];

              $filter_data['tag_codes_replace_template'] = [
                $record_info['ip'],
                $record_info['record_id'],
                date("Y-m-d H:i:s",strtotime($record_info['date_added'])),
                $this->config->get('config_name'),
                $config_store_url.'index.php?route=extension/ocdevwizard/'.$this->_name.'/actions&token='.$record_info['token'].'&type=1'
              ];

              $this->mailing_send($filter_data);
            }

            if ($type == 'to_admin_on_success_login') {
              $filter_data['set_to']      = $form_data['admin_email_for_notification'];
              $filter_data['template_id'] = $form_data['admin_email_login_success_template'];
              $filter_data['language_id'] = $data['language_id'];

              $filter_data['tag_codes_subject'] = [
                '{ip}',
                '{username}',
                '{record_id}',
                '{date_added}',
                '{store_name}'
              ];

              $filter_data['tag_codes_replace_subject'] = [
                $record_info['ip'],
                $record_info['username'],
                $record_info['record_id'],
                date("Y-m-d H:i:s",strtotime($record_info['date_added'])),
                $this->config->get('config_name')
              ];

              $filter_data['tag_codes_template'] = [
                '{ip}',
                '{username}',
                '{record_id}',
                '{date_added}',
                '{store_name}',
                '{disable_user_url}'
              ];

              $filter_data['tag_codes_replace_template'] = [
                $record_info['ip'],
                $record_info['username'],
                $record_info['record_id'],
                date("Y-m-d H:i:s",strtotime($record_info['date_added'])),
                $this->config->get('config_name'),
                $config_store_url.'index.php?route=extension/ocdevwizard/'.$this->_name.'/actions&token='.$record_info['token'].'&type=2'
              ];

              $this->mailing_send($filter_data);
            }
          }
        }
      }
    }
  }

  private function mailing_send($data) {
    if ($data) {
      $html_data = [];

      $template_description = $this->getEmailTemplateDescription($data['template_id']);

      if ($template_description) {
        $html_data['title']         = $setSubject = html_entity_decode(str_replace($data['tag_codes_subject'],$data['tag_codes_replace_subject'],$template_description[$data['language_id']]['subject']),ENT_QUOTES,'UTF-8');
        $html_data['html_template'] = html_entity_decode(str_replace($data['tag_codes_template'],$data['tag_codes_replace_template'],$template_description[$data['language_id']]['template']),ENT_QUOTES,'UTF-8');

        if (version_compare(VERSION,'2.0.0.0','>=') && version_compare(VERSION,'2.1.0.2.1','<=')) {
          $setHtml = $this->load->view('default/template/extension/ocdevwizard/'.$this->_name.'/email_template.tpl',$html_data);
        } else if (version_compare(VERSION,'3.0.0.0','>=')) {
          $setHtml = $this->load->view('extension/ocdevwizard/'.$this->_name.'/email_template',$html_data);
        } else if (version_compare(VERSION,'2.0.0.0','<')) {
          $template = new Template();

          $template->data = $html_data;

          $setHtml = $template->fetch('default/template/extension/ocdevwizard/'.$this->_name.'/email_template.tpl');
        } else {
          $setHtml = $this->load->view('extension/ocdevwizard/'.$this->_name.'/email_template.tpl',$html_data);
        }

        // email notification
        if (version_compare(VERSION,'1.5.5.1','>=') && version_compare(VERSION,'2.0.0.0','<')) {
          $mail            = new Mail();
          $mail->protocol  = $this->config->get('config_mail_protocol');
          $mail->parameter = $this->config->get('config_mail_parameter');
          $mail->hostname  = $this->config->get('config_smtp_host');
          $mail->username  = $this->config->get('config_smtp_username');
          $mail->password  = $this->config->get('config_smtp_password');
          $mail->port      = $this->config->get('config_smtp_port');
          $mail->timeout   = $this->config->get('config_smtp_timeout');
        } else if (version_compare(VERSION,'2.0.0.0','>=') && version_compare(VERSION,'2.0.1.1','<=')) {
          $mail = new Mail($this->config->get('config_mail'));
        } else if (version_compare(VERSION,'2.0.2.0','>=') && version_compare(VERSION,'2.0.3.1','<')) {
          $mail                = new Mail();
          $mail->protocol      = $this->config->get('config_mail_protocol');
          $mail->parameter     = $this->config->get('config_mail_parameter');
          $mail->smtp_hostname = $this->config->get('config_mail_smtp_host');
          $mail->smtp_username = $this->config->get('config_mail_smtp_username');
          $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'),ENT_QUOTES,'UTF-8');
          $mail->smtp_port     = $this->config->get('config_mail_smtp_port');
          $mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');
        } else if (version_compare(VERSION,'3.0.0.0','>=')) {
          $mail                = new Mail($this->config->get('config_mail_engine'));
          $mail->parameter     = $this->config->get('config_mail_parameter');
          $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
          $mail->smtp_username = $this->config->get('config_mail_smtp_username');
          $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'),ENT_QUOTES,'UTF-8');
          $mail->smtp_port     = $this->config->get('config_mail_smtp_port');
          $mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');
        } else {
          $mail                = new Mail();
          $mail->protocol      = $this->config->get('config_mail_protocol');
          $mail->parameter     = $this->config->get('config_mail_parameter');
          $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
          $mail->smtp_username = $this->config->get('config_mail_smtp_username');
          $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'),ENT_QUOTES,'UTF-8');
          $mail->smtp_port     = $this->config->get('config_mail_smtp_port');
          $mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');
        }

        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender($this->config->get('config_name'));
        $mail->setSubject($setSubject);
        $mail->setHtml($setHtml);

        if ($data['set_to']) {
          $emails = explode(',',$data['set_to']);

          foreach ($emails as $email) {
            $mail->setTo($email);
            $mail->send();
          }
        }
      }
    }
  }
}