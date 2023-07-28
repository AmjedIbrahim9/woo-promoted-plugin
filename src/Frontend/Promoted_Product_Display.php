<?php
namespace WOOFP\Frontend;

class Promoted_Product_Display {
    public function init_hooks() {
        // Hook the display function to show the promoted product under the header
        add_action('wp_body_open', [$this, 'display_promoted_product']);
    }

    public function display_promoted_product() {
        // Check if the promoted product option is set to "yes" for any product
        $promoted_product = get_posts(array(
            'post_type' => 'product',
            'meta_key' => 'promoted_product',
            'meta_value' => 'yes',
            'numberposts' => 1,
        ));

        if (!empty($promoted_product)) {
            $promoted_product = $promoted_product[0];
            $promote_this_product = get_option('woofp_promoted_product_status_' . $promoted_product->ID);
            if ($promote_this_product === 'yes'){
            

            $custom_product_deal = get_option('woo_featured_product_text', $promoted_product->ID, '');

            // Get the custom title for the promoted product
            $custom_title = get_option('woofp_promoted_product_custom_title_' . $promoted_product->ID, '');

            // If a custom title is not set, use the product title
            $title = $custom_title ? $custom_title : get_the_title($promoted_product->ID);

            // Get the URL of the promoted product
            $product_url = get_permalink($promoted_product->ID);

            // Get the background and text colors for the promoted product
            $background_color = get_option('woo_featured_background_color', '#ffffff');
            $text_color = get_option('woo_featured_text_color', '#000000');

            // Display the promoted product HTML
            echo '<div id="my-custom-promoted-product" class="promoted-product my-custom-promoted-product" style="background-color: ' . esc_attr($background_color) . '; color: ' . esc_attr($text_color) . '!important; padding: 10px; text-align: center;">';
            echo '<a class="promoted-product my-custom-promoted-product" href="' . esc_url($product_url) . '"><strong>' . esc_html($title) . '</strong></a>';
            echo '<small>' . esc_html($custom_product_deal) . '</small>';
            echo '</div>';
            }
        }
    }
}