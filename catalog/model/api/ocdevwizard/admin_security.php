<?php
##====================================================##
## @author    : OCdevWizard                           ##
## @contact   : ocdevwizard@gmail.com                 ##
## @support   : http://help.ocdevwizard.com           ##
## @copyright : (c) OCdevWizard. Admin Security, 2018 ##
##====================================================##
class ModelApiOcdevwizardAdminSecurity extends Model {
  private $_name = 'admin_security';
  private $_code = 'ocdw_admin_security';

  public function addBanned($data) {
    $this->db->query("
      INSERT INTO ".DB_PREFIX.$this->_code."_banned
      SET
        status = '".(int)$data['status']."',
        ip = '".$this->db->escape($data['ip'])."',
        date_added = NOW()
    ");

    $banned_id = $this->db->getLastId();

    return $banned_id;
  }

  public function addEmailTemplate($data) {
    $this->db->query("
      INSERT INTO ".DB_PREFIX.$this->_code."_email_template
      SET
        system_name = '".$this->db->escape($data['system_name'])."',
        assignment = '".(int)$data['assignment']."',
        status = '".(int)$data['status']."',
        date_added = NOW()
    ");

    $template_id = $this->db->getLastId();

    foreach ($data['template_description'] as $language_id => $value) {
      $this->db->query("INSERT INTO ".DB_PREFIX.$this->_code."_email_template_description 
        SET 
        template_id = '".(int)$template_id."', 
        language_id = '".(int)$language_id."', 
        subject = '".$this->db->escape($value['subject'])."', 
        template = '".$this->db->escape($value['template'])."'
      ");
    }

    return $template_id;
  }

  public function editBanned($data) {
    $this->db->query("
      UPDATE ".DB_PREFIX.$this->_code."_banned
      SET
        status = '".(int)$data['status']."',
        ip = '".$this->db->escape($data['ip'])."',
        date_modified = NOW()
      WHERE
        banned_id = '".(int)$data['banned_id']."'
    ");
  }

  public function editEmailTemplate($data) {
    $this->db->query("
      UPDATE ".DB_PREFIX.$this->_code."_email_template
      SET
        system_name = '".$this->db->escape($data['system_name'])."',
        assignment = '".(int)$data['assignment']."',
        status = '".(int)$data['status']."',
        date_modified = NOW()
      WHERE
        template_id = '".(int)$data['template_id']."'
    ");

    $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_email_template_description WHERE template_id = '".(int)$data['template_id']."'");

    foreach ($data['template_description'] as $language_id => $value) {
      $this->db->query("INSERT INTO ".DB_PREFIX.$this->_code."_email_template_description 
        SET 
        template_id = '".(int)$data['template_id']."', 
        language_id = '".(int)$language_id."', 
        subject = '".$this->db->escape($value['subject'])."', 
        template = '".$this->db->escape($value['template'])."'
      ");
    }
  }

  public function prepareRecord() {
    $this->db->query("TRUNCATE ".DB_PREFIX.$this->_code."_record");
  }

  public function prepareBanned() {
    $this->db->query("TRUNCATE ".DB_PREFIX.$this->_code."_banned");
  }

  public function prepareEmailTemplate() {
    $this->db->query("TRUNCATE ".DB_PREFIX.$this->_code."_email_template");
    $this->db->query("TRUNCATE ".DB_PREFIX.$this->_code."_email_template_description");
  }

  public function importBanned($data) {
    $this->db->query("
      INSERT INTO ".DB_PREFIX.$this->_code."_banned
      SET
        banned_id = '".(int)$data['banned_id']."',
        status = '".(int)$data['status']."',
        ip = '".$this->db->escape($data['ip'])."',
        date_added = '".$this->db->escape($data['date_added'])."',
        date_modified = '".$this->db->escape($data['date_modified'])."'
    ");
  }

  public function importRecord($data) {
    $this->db->query("
      INSERT INTO ".DB_PREFIX.$this->_code."_record
      SET
        record_id = '".(int)$data['record_id']."',
        username = '".$this->db->escape($data['username'])."',
        user_id = '".(int)$data['record_id']."',
        ip = '".$this->db->escape($data['ip'])."',
        total = '".(int)$data['record_id']."',
        token = '".$this->db->escape($data['token'])."',
        date_added = '".$this->db->escape($data['date_added'])."',
        date_modified = '".$this->db->escape($data['date_modified'])."'
    ");
  }

  public function importEmailTemplate($data) {
    $this->db->query("
      INSERT INTO ".DB_PREFIX.$this->_code."_email_template
      SET
        template_id = '".(int)$data['template_id']."',
        system_name = '".$this->db->escape($data['system_name'])."',
        assignment = '".(int)$data['assignment']."',
        status = '".(int)$data['status']."',
        date_added = '".$this->db->escape($data['date_added'])."',
        date_modified = '".$this->db->escape($data['date_modified'])."'
    ");

    foreach ($data['template_description'] as $language_id => $value) {
      $this->db->query("
        INSERT INTO ".DB_PREFIX.$this->_code."_email_template_description 
        SET 
          template_id = '".(int)$data['template_id']."', 
          language_id = '".(int)$language_id."', 
          subject = '".$this->db->escape($value['subject'])."', 
          template = '".$this->db->escape($value['template'])."'
      ");
    }
  }

  public function deleteRecord($data) {
    $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_record WHERE record_id = '".(int)$data['record_id']."'");

    return true;
  }

  public function deleteRecords() {
    $query = $this->db->query("SELECT record_id FROM ".DB_PREFIX.$this->_code."_record")->rows;

    if ($query) {
      foreach ($query as $row) {
        $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_record WHERE record_id = '".(int)$row['record_id']."'");
      }

      return true;
    } else {
      return false;
    }
  }

  public function deleteBanned($data) {
    $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_banned WHERE banned_id = '".(int)$data['banned_id']."'");

    return true;
  }

  public function deleteBanneds() {
    $query = $this->db->query("SELECT banned_id FROM ".DB_PREFIX.$this->_code."_banned")->rows;

    if ($query) {
      foreach ($query as $row) {
        $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_banned WHERE banned_id = '".(int)$row['banned_id']."'");
      }

      return true;
    } else {
      return false;
    }
  }

  public function deleteEmailTemplate($data) {
    $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_email_template WHERE template_id = '".(int)$data['template_id']."'");
    $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_email_template_description WHERE template_id = '".(int)$data['template_id']."'");

    return true;
  }

  public function deleteEmailTemplates() {
    $query = $this->db->query("SELECT template_id FROM ".DB_PREFIX.$this->_code."_email_template")->rows;

    if ($query) {
      foreach ($query as $row) {
        $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_email_template WHERE template_id = '".(int)$row['template_id']."'");
        $this->db->query("DELETE FROM ".DB_PREFIX.$this->_code."_email_template_description WHERE template_id = '".(int)$row['template_id']."'");
      }

      return true;
    } else {
      return false;
    }
  }

  public function copyBanned($data) {
    $query = $this->db->query("
      SELECT DISTINCT *
      FROM ".DB_PREFIX.$this->_code."_banned b
      WHERE
        b.banned_id = '".(int)$data['banned_id']."'
    ");

    if ($query->num_rows) {
      $data = $query->row;

      $data['status'] = '0';

      $this->addBanned($data);

      return true;
    } else {
      return false;
    }
  }

  public function copyBanneds() {
    $query = $this->db->query("
      SELECT 
        DISTINCT *
      FROM ".DB_PREFIX.$this->_code."_banned
    ")->rows;

    if ($query) {
      foreach ($query as $row) {
        $data = $row;

        $data['status'] = '0';

        $this->addBanned($data);
      }

      return true;
    } else {
      return false;
    }
  }

  public function copyEmailTemplate($data) {
    $query = $this->db->query("
      SELECT
        DISTINCT *
      FROM ".DB_PREFIX.$this->_code."_email_template et
      WHERE
        et.template_id = '".(int)$data['template_id']."'
    ");

    if ($query->num_rows) {
      $data = $query->row;

      $data['status'] = '0';

      $data = array_merge($data,['template_description' => $this->getEmailTemplateDescription($data['template_id'])]);

      $this->addEmailTemplate($data);

      return true;
    } else {
      return false;
    }
  }

  public function copyEmailTemplates() {
    $query = $this->db->query("
      SELECT
        DISTINCT *
      FROM ".DB_PREFIX.$this->_code."_email_template
    ")->rows;

    if ($query) {
      foreach ($query as $row) {
        $data = $row;

        $data['status'] = '0';

        $data = array_merge($data,['template_description' => $this->getEmailTemplateDescription($row['template_id'])]);

        $this->addEmailTemplate($data);
      }

      return true;
    } else {
      return false;
    }
  }

  private function getEmailTemplateDescription($template_id) {
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
}

?>