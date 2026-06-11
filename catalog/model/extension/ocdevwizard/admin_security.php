<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
class ModelExtensionOcdevwizardAdminSecurity extends Model {
  private $_name = 'admin_security';
  private $_code = 'ocdw_admin_security';

  public function getRecordByToken($token) {
    $query = $this->db->query("
      SELECT DISTINCT 
        *
      FROM ".DB_PREFIX.$this->_code."_record r 
      WHERE r.token = '".$this->db->escape($token)."'
    ");

    if ($query->num_rows) {
      return [
        'user_id' => $query->row['user_id'],
        'ip'      => $query->row['ip']
      ];
    } else {
      return false;
    }
  }

  public function disableUser($user_id) {
    $this->db->query("UPDATE ".DB_PREFIX."user SET status = '0' WHERE user_id = '".(int)$user_id."'");
  }

  public function addBanned($ip) {
    if (!$this->checkBanned($ip)) {
      $this->db->query("INSERT INTO ".DB_PREFIX.$this->_code."_banned SET status = '1', ip = '".$this->db->escape($ip)."', date_added = NOW()");
    }
  }

  private function checkBanned($ip) {
    $sql = "SELECT DISTINCT * FROM ".DB_PREFIX.$this->_code."_banned WHERE (";

    $sql .= "ip = '".$this->db->escape($ip)."') AND status = '1'";

    $query = $this->db->query($sql)->row;

    if ($query) {
      return true;
    } else {
      return false;
    }
  }
}

?>