<?php
class ModelExtensionModuleSmartMarketingManufacturer extends Model {
	public function getManufacturers() {
		$cache_key = 'smart.marketing.manufacturer';

		$manufacturer_data = $this->cache->get($cache_key);

		if (!$manufacturer_data) {
			$manufacturer_data = array();

			$query = $this->db->query("SELECT manufacturer_id, name FROM " . DB_PREFIX . "manufacturer ORDER BY name ASC");

			if ($query->num_rows) {
				foreach ($query->rows as $row) {
					$manufacturer_data[] = array(
						'manufacturer_id' => $row['manufacturer_id'],
						'name'            => $row['name']
					);
				}

				$this->cache->set($cache_key, $manufacturer_data);
			}
		}

		return $manufacturer_data;
	}
}
