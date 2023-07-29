<?php
namespace WOOFP\Frontend;

class Promoted_Product_Display {
    public function __construct() {
        // Hook the display function to show the promoted product under the header
        add_action('wp_body_open', [$this, 'display_promoted_product']);
    }

    public function display_promoted_product() {
        // Check if the promoted product option is set to "yes" for any product
        if (get_option('woofp_promoted_product_status') === 'yes') {
            $product_id = get_option('woofp_promoted_product');
            $custom_title = get_option('woofp_promoted_product_custom_title');

            $product = wc_get_product($product_id);
            $original_title = $product->get_name(); // Update this line
            $product_url = get_permalink($product->get_id()); // Use get_id() method

            $custom_product_deal = get_option('woo_featured_product_text', '');
            $background_color = get_option('woo_featured_background_color', '#ffffff');
            $text_color = get_option('woo_featured_text_color', '#000000');

            if (empty($custom_title)) {
                $custom_title = $original_title;
            }

            // Display the promoted product HTML
            echo '<div class="promoted-product my-custom-promoted-product" style="background-color: ' . esc_attr($background_color) . '; color: ' . esc_attr($text_color) . '!important; padding: 10px; text-align: center;">';
            echo '<a class="promoted-product-link" href="' . esc_url($product_url) . '"><strong>' . esc_html($custom_title) . '</strong></a>';
            echo '<small>' . esc_html($custom_product_deal) . '</small>';
            echo '</div>';
        }
    }
}