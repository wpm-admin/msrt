<?php
require_once 'system/startup.php';

// Mock registry and dependencies for testing
// Since we cannot fully initialize OC, we'll just test the logic with a dummy function
// We'll simulate the _findSubcategoryWithDirectProducts method.

// Let's just test the concept: we have a tree of categories.
// We'll define a simple array representing categories and their subcategories.
// We'll also define a function that returns product count for a given category_id and model_id.

// For simplicity, we'll hardcode a small tree.

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

// Define a mock model_catalog_product->getTotalProducts method that returns >0 for specific category_id and model_id
function mock_getTotalProducts($filter_data) {
    // Assume model_id is fixed for test
    $model_id = 13;
    // Let's say category 300 has direct products for model 13
    if (isset($filter_data['filter_category_id']) && $filter_data['filter_category_id'] == 300
        && isset($filter_data['filter_model']) && $filter_data['filter_model'] === true
        && isset($filter_data['model_id']) && $filter_data['model_id'] == $model_id
        && isset($filter_data['filter_sub_category']) && $filter_data['filter_sub_category'] === false
    ) {
        return 5; // >0
    }
    // For sub_category=true, we might return >0 if any subcategory has products? But we are using this to check if category itself has products via subcategory? Actually in our logic we first check if the category (including subcategories) has products using filter_sub_category=true.
    // We'll simplify: return >0 for categories 100, 200, 300 when filter_sub_category=true.
    if (isset($filter_data['filter_category_id'])) {
        $cat = $filter_data['filter_category_id'];
        if (in_array($cat, array(100, 200, 300)) && isset($filter_data['filter_sub_category']) && $filter_data['filter_sub_category'] === true) {
            return 1;
        }
    }
    return 0;
}

// Now implement the recursive function
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
        // Recursively check deeper subcategories
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