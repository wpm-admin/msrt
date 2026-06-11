<?php
class ModelAccountNotify extends Model {
	public function getNotifyTotal() {
		
		
		$order_query = $this->db->query("SELECT count(oh.order_history_id) AS total
										FROM `" . DB_PREFIX . "order_history` oh
										LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = oh.order_id)
										WHERE oh.readed= '0' AND o.customer_id = '" . (int)$this->customer->getId() . "' AND o.customer_id != '0' AND o.order_status_id > '0' AND oh.notify = '1'");

		return (int)$order_query->row['total'];
										
	}
	
	public function getNotifyOrderTotal($order_id) {
		
		
		$order_query = $this->db->query("SELECT count(oh.order_history_id) AS total
										FROM `" . DB_PREFIX . "order_history` oh
										LEFT JOIN `" . DB_PREFIX . "order` o ON (o.order_id = oh.order_id)
										WHERE oh.order_id = '" .  (int)$order_id . "' AND oh.readed= '0' AND o.customer_id = '" . (int)$this->customer->getId() . "' AND o.customer_id != '0' AND o.order_status_id > '0' AND oh.notify = '1'");

		return (int)$order_query->row['total'];
										
	}
	
	public function setNotifyOrderReaded($order_id) {
		
		
		$this->db->query("UPDATE `" . DB_PREFIX . "order_history` SET
										readed= '1'
										WHERE order_id = '" .  (int)$order_id . "'");

										
	}
	
	
}