<?php
/**
 * Plugin Name: Luway WooCommerce Upsale
 * Plugin URI: https://luway.ru
 * Description: Upsale products for WooCommerce
 * Version: 1.1.0
 * Author: Alexey Ponomarev
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: luway-upsale
 */

namespace Luway;

defined( 'ABSPATH' ) || exit;

class WCUpsale
{

    public function __construct()
    {
        add_shortcode('wcupsale', array(&$this, 'upsale'));
        add_action('init', array(&$this, 'block_init'), PHP_INT_MAX);
    }

    public function upsale($atts)
    {
        if (is_admin() || !class_exists('woocommerce'))
            return;

        $atts = shortcode_atts(
            array(
                'columns'   => '5',
                'products'  => '5',
            ),
            $atts
        );

        $maxpoducts = intval($atts['products']);
        if (empty($maxpoducts))
            $maxpoducts = 5;

        global $wpdb;
        $items = WC()->cart->get_cart_contents();

        $ids = $products_ids = array();

        if (is_array($items) && !empty($items))
        {
            $ids = array_column($items, "product_id");
            
            $orders = $wpdb->get_col("
                SELECT order_id
                FROM {$wpdb->prefix}woocommerce_order_itemmeta AS meta
                INNER JOIN {$wpdb->prefix}woocommerce_order_items AS items
                ON meta.order_item_id = items.order_item_id
                WHERE 1 = 1
                AND meta_key = '_product_id'
                AND meta_value IN (" . implode(", ", $ids) . ");
            ");

            if (!empty($orders))
            {
                $products_ids = $wpdb->get_col("
                    SELECT DISTINCT(meta_value)
                    FROM {$wpdb->prefix}woocommerce_order_items AS items
                    INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS meta
                    ON items.order_item_id = meta.order_item_id
                    WHERE 1 = 1
                    AND meta_key = '_product_id'
                    AND order_id IN (" . implode(", ", $orders) . ")
                    AND meta_value NOT IN (" . implode(", ", $ids) . ")
                    LIMIT 0,{$maxpoducts};
                ");
            }
        }

        if (empty($products_ids) || count($products_ids) < 5)
        {
            $merged = array_merge($ids, $products_ids);

            $popular = $wpdb->get_results("
                SELECT meta_value, count(*) AS cnt
                FROM {$wpdb->prefix}woocommerce_order_itemmeta
                WHERE 1 = 1
                AND meta_key = '_product_id'
                " . (!empty($merged) ? "AND meta_value NOT IN (" . implode(", ", $merged) . ")": "") . "
                GROUP BY meta_value
                ORDER BY cnt DESC
                LIMIT 0,{$maxpoducts};
            ");

            $products_ids = array_slice(array_merge($products_ids, array_column($popular, "meta_value")), 0, $maxpoducts);
        }

        if (!empty($products_ids))
            return do_shortcode('[products columns="' . $atts['columns'] . '" ids="' . implode(", ", $products_ids) . '"]');
    }

    public function block_init() {
        register_block_type(__DIR__);
    }
}

new WCUpsale();