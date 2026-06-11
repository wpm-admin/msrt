<?php
class ModelCheckoutAuction extends Model {
	private $error = array();


	public function checkEndAuction() {
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "auction WHERE date_end <= NOW() AND status = '1'");
		
		return $query->rows;
	}
	
	public function setAuctionDone($auction_id){
		$this->db->query("UPDATE " . DB_PREFIX . "auction SET status = '0' WHERE auction_id = '".(int)$auction_id."'");
	}
	
}