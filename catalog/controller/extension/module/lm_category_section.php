<?php
class ControllerExtensionModuleLmCategorySection extends Controller {
    public function models() {
        $this->load->model('catalog/mark');
        $json = array();

        if (isset($this->request->get['mark_id'])) {
            $mark_id = (int)$this->request->get['mark_id'];
            $results = $this->model_catalog_mark->getMarks($mark_id);

            $this->load->model('tool/image');

            foreach ($results as $result) {
                $json[] = array(
                    'model_id' => $result['mark_id'],
                    'name'     => $result['name'],
                    'image'    => $result['image'] ? $this->model_tool_image->resize($result['image'], 50, 50) : '',
                    'href'     => $this->url->link('product/mark', 'mark_id=' . $result['mark_id'])
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function categories() {
        $this->load->model('catalog/product');
        $json = array();

        if (isset($this->request->get['model_id'])) {
            $model_id = (int)$this->request->get['model_id'];
            $this->session->data['model_id'] = $model_id;

            $filter_data = array(
                'filter_diagram'        => true,
                'filter_model'          => true,
                'filter_second_photos'  => true,
                'sort'                  => 'pd.name',
                'order'                 => 'ASC',
                'start'                 => 0,
                'limit'                 => 10000
            );
            $results = $this->model_catalog_product->getProducts($filter_data);
            foreach ($results as $product) {
                $json[] = array(
                    'product_id' => (int)$product['product_id'],
                    'name'       => $product['name'],
                    'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function categoryBottom() {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $json = array();

        if (isset($this->request->get['model_id'])) {
            $model_id = (int)$this->request->get['model_id'];
            $this->session->data['model_id'] = $model_id;

            $filter_data = array(
                'filter_diagram'        => true,
                'filter_model'          => true,
                'filter_second_photos'  => true,
                'sort'                  => 'pd.name',
                'order'                 => 'ASC',
                'start'                 => 0,
                'limit'                 => 6
            );
            $results = $this->model_catalog_product->getProducts($filter_data);
            foreach ($results as $product) {
                $thumb = $product['image'] ? $this->model_tool_image->resize($product['image'], 265, 184) : '';
                $json[] = array(
                    'product_id' => (int)$product['product_id'],
                    'name'       => $product['name'],
                    'thumb'      => $thumb,
                    'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}