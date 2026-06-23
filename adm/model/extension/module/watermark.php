<?php

class ModelExtensionModuleWatermark extends Model {

    public function getOption($key) {
        $rows = $this->db->query("
            SELECT `id` FROM `" . DB_PREFIX . "watermark_settings`
            WHERE `key` = \"" . $this->db->escape($key) . "\"
            ")->rows;
        if(count($rows) == 1) {
            return array(
                $rows[0]['key'] => $rows[0]['value'],
                );
        }
        return false;
    }

    public function getOptions() {
        $rows = $this->db->query("
            SELECT `key`, `value` FROM `" . DB_PREFIX . "watermark_settings` WHERE 1
            ")->rows;
        $options = array();
        foreach ($rows as $key => $value) {
            $options[$value['key']] = $value['value'];
        }
        return $options;

    }

    public function change($key, $value) {
        $rows = $this->db->query("
            SELECT `id` FROM `" . DB_PREFIX . "watermark_settings`
            WHERE `key` = \"" . $this->db->escape($key) . "\"
            ")->rows;
        if(count($rows) == 1) {
            $this->db->query("
                UPDATE `" . DB_PREFIX . "watermark_settings`
                SET `key` = \"" . $this->db->escape($key) . "\",
                `value` = \"" . $this->db->escape($value) . "\"
                WHERE id = " . $rows[0]['id'] .  "
                ");
        } else {
            $this->db->query("
                INSERT INTO `" . DB_PREFIX . "watermark_settings`(`key`, `value`)
                VALUES(\"" . $this->db->escape($key) . "\",\"" . $this->db->escape($value) . "\")
                ");
        }
    }

    public function install() {
        $this->db->query("
            CREATE TABLE `" . DB_PREFIX . "watermark_settings` (
                `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `key` varchar(255) NOT NULL,
                `value` varchar(1024) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

        $settings = array(
            'active'                => 0,
            'image'                 => 'catalog/logo.png',
            'size_x'                => 280,
            'size_y'                => 20,
            'zoom'                  => 0.5,
            'pos_x'                 => -20,
            'pos_x_center'          => 0,
            'pos_y'                 => -20,
            'pos_y_center'          => 0,
            'opacity'               => 0.8,
            'category_image'        => 0,
            'product_thumb'         => 0,
            'product_popup'         => 1,
            'product_list'          => 0,
            'product_additional'    => 0,
            'product_related'       => 0,
            'product_in_compare'    => 0,
            'product_in_wish_list'  => 0,
            'product_in_cart'       => 0,
            );

        foreach ($settings as $key => $value) {
            $this->change($key, $value);
        }

    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "watermark_settings`");
    }
}