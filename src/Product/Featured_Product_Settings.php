<?php
namespace WOOFP\Product;

class Featured_Product_Settings {
    public function __construct() {
        // Register a new section in the WooCommerce settings
        add_filter('woocommerce_get_sections_products', array($this, 'featured_product_add_section'));

        // Add the settings fields to the new section
        add_filter('woocommerce_get_settings_products', array($this, 'add_featured_product_settings'), 10, 2);

        // Save the promoted product settings
        add_action('woocommerce_settings_save_products', array($this, 'save_featured_product_settings'));
    }

    // Callback to add a new section
    public function featured_product_add_section($sections) {
        $sections = array_merge($sections, array(
            'featured_product' => __('Featured Product', 'text-domain'),
        ));
        return $sections;
    }

    // Callback to add settings fields to the new section
    public function add_featured_product_settings($settings, $current_section) {
        if ($current_section === 'featured_product') {
            $new_settings = array(
                array(
                    'title'     => __('Featured Product Settings', 'text-domain'),
                    'type'      => 'title',
                    'id'        => 'woo_featured_product_settings_title',
                ),
                array(
                    'title'     => __('Title of the Promoted Sale', 'text-domain'),
                    'desc'      => __('Enter some text for the featured product.', 'text-domain'),
                    'id'        => 'woo_featured_product_text',
                    'type'      => 'text',
                    'css'       => 'min-width:300px;',
                    'desc_tip'  => true,
                ),
                array(
                    'title'     => __('Background Color', 'text-domain'),
                    'desc'      => __('Select a color for the featured product background.', 'text-domain'),
                    'id'        => 'woo_featured_background_color',
                    'type'      => 'color',
                    'default'   => '#ffffff', // Set a default color if needed
                    'desc_tip'  => true,
                ),
                array(
                    'title'     => __('Text Color', 'text-domain'),
                    'desc'      => __('Select a color for the featured product text.', 'text-domain'),
                    'id'        => 'woo_featured_text_color',
                    'type'      => 'color',
                    'default'   => '#ffffff', // Set a default color if needed
                    'desc_tip'  => true,
                ),
                array(
                    'type'      => 'sectionend',
                    'id'        => 'woo_featured_product_settings_end',
                ),
            );

            $settings = array_merge($settings, $new_settings);
        }

        return $settings;
    }

    // Save the promoted product settings
    public function save_featured_product_settings() {
        $woo_featured_product_text = isset($_POST['woo_featured_product_text']) ? wc_clean($_POST['woo_featured_product_text']) : '';
        $woo_featured_background_color = isset($_POST['woo_featured_background_color']) ? wc_clean($_POST['woo_featured_background_color']) : '';
        $woo_featured_text_color = isset($_POST['woo_featured_text_color']) ? wc_clean($_POST['woo_featured_text_color']) : '';

        update_option('woo_featured_product_text', $woo_featured_product_text);
        update_option('woo_featured_background_color', $woo_featured_background_color);
        update_option('woo_featured_text_color', $woo_featured_text_color);
    }
}
