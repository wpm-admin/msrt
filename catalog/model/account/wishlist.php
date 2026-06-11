<?php
class ModelAccountWishlist extends Model {
	public function addWishlist($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_wishlist SET customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', date_added = NOW()");
	}

	public function deleteWishlist($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");
	}

	public function getWishlist() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->rows;
	}

	public function getAllWishlist() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_wishlist cw
								  LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = cw.customer_id)
								  ORDER BY cw.customer_id");

		return $query->rows;
	}

	public function getTotalWishlist() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
	
	public function isWishlist($product_id){
		
		$in_wishlist = false;
		
		$wishlist = $this->getWishlist();
		
		
		if($wishlist){
			foreach($wishlist as $wish){
				if((int)$product_id == (int)$wish['product_id']){
					$in_wishlist = true;
				}
			}
		}
		if(isset($_COOKIE['productsWish'])){
			foreach($_COOKIE['productsWish'] as $prod_id => $wish){
				if((int)$product_id == (int)$prod_id){
					$in_wishlist = true;
				}
			}
		}
			
		return $in_wishlist;
	}
}
