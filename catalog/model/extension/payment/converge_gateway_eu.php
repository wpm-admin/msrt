<?php

use ConvergeGatewayEu\ConvergeGatewayEu;

class ModelExtensionPaymentConvergeGatewayEu extends Model
{
    public function getMethod($address, $total)
    {
        $query = $this->db->query(
            "SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" .
            (int)$this->config->get('payment_converge_gateway_eu_geo_zone_id') . "' AND `country_id` = '" .
            (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')"
        );

        if ($this->config->get('payment_converge_gateway_eu') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_converge_gateway_eu_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $this->load->language('extension/payment/converge');
        $title = $this->config->get('payment_converge_gateway_eu_title') ?: $this->language->get('text_title');
        return $status ? [
            'code'       => ConvergeGatewayEu::EXTENSION_NAME,
            'title'      => $title,
            'terms'      => '',
            'sort_order' => $this->config->get('payment_converge_gateway_eu_sort_order')
        ] : [];
    }
}