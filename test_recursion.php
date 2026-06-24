<?php
require_once 'system/startup.php';


//  simulate _findSubcategoryWithDirectProducts method.
// Define a mock model_catalog_category->getCategories method
function mock_getCategories($parent_id = 0) {
    static $cats = array(
        0 => array(
            array('category_id' => 100, 'name' => 'Root1'),
            array('category_id' => 101, 'name' => 'Root2')
        ),
        100 => array(
            array('category_id' => 200, 'name' => 'Child1'),
            array('category_id' => 201, 'name' => 'Child2')
        ),
        101 => array(
            array('category_id' => 202, 'name' => 'Child3')
        ),
        200 => array(
            array('category_id' => 300, 'name' => 'GrandChild1')
        ),
        300 => array()
    );
    return isset($cats[$parent_id]) ? $cats[$parent_id] : array();
}

// Define a mock model_catalog_product->getTotalProducts
function mock_getTotalProducts($filter_data) {

    $model_id = 13;

    if (isset($filter_data['filter_category_id']) && $filter_data['filter_category_id'] == 300
        && isset($filter_data['filter_model']) && $filter_data['filter_model'] === true
        && isset($filter_data['model_id']) && $filter_data['model_id'] == $model_id
        && isset($filter_data['filter_sub_category']) && $filter_data['filter_sub_category'] === false
    ) {
        return 5; // >0
    }

    if (isset($filter_data['filter_category_id'])) {
        $cat = $filter_data['filter_category_id'];
        if (in_array($cat, array(100, 200, 300)) && isset($filter_data['filter_sub_category']) && $filter_data['filter_sub_category'] === true) {
            return 1;
        }
    }
    return 0;
}

//  implement
function _findSubcategoryWithDirectProducts($category_id, $model_id) {
    $subcats = mock_getCategories($category_id);
    foreach ($subcats as $subcat) {
        $sub_filter = array(
            'filter_category_id' => $subcat['category_id'],
            'filter_sub_category' => false,
            'filter_model'       => true,
            'model_id'           => $model_id,
            'start'              => 0,
            'limit'              => 1
        );
        if (mock_getTotalProducts($sub_filter) > 0) {
            return $subcat['category_id'];
        }
        // check  subcategories
        $deeper_id = _findSubcategoryWithDirectProducts($subcat['category_id'], $model_id);
        if ($deeper_id) {
            return $deeper_id;
        }
    }
    return false;
}

// Test
$model_id = 13;
$result = _findSubcategoryWithDirectProducts(100, $model_id);
echo "For category 100, found subcategory with direct products: " . ($result ? $result : 'none') . "\n";
$result = _findSubcategoryWithDirectProducts(101, $model_id);
echo "For category 101, found subcategory with direct products: " . ($result ? $result : 'none') . "\n";
$result = _findSubcategoryWithDirectProducts(200, $model_id);
echo "For category 200, found subcategory with direct products: " . ($result ? $result : 'none') . "\n";
$result = _findSubcategoryWithDirectProducts(300, $model_id);
echo "For category 300, found subcategory with direct products: " . ($result ? $result : 'none') . "\n";

?>