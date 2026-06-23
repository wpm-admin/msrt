<?php
class ModelExtensionModuleSmartMarketingLanguage extends Model {
	public function getActiveLanguages() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE status = '1' ORDER BY name ASC");

		return $query->rows;
	}

	public function getLanguageIdByCode($code) {
		$query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->db->escape($code) . "'");

		if ($query->num_rows) {
			return $query->row['language_id'];
		} else {
			return 0;
		}
	}

	public function getLanguageNameById($language_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "language WHERE language_id = '" . (int)$language_id . "'");

		if ($query->num_rows) {
			return $query->row['name'];
		} else {
			return '';
		}
	}
}
