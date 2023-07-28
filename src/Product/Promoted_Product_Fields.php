<?php
namespace WOOFP\Product;

class Promoted_Product_Fields {
    public function init_hooks() {
        // Add the "Promoted Product" fields to the single product editor
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_promoted_product_fields']);

        // Save the "Promoted Product" fields when the product is saved
        add_action('woocommerce_process_product_meta', [$this, 'save_promoted_product_fields']);

        // Schedule the expiration check cron job on plugin activation
        register_activation_hook(WOOFP_PLUGIN_FILE, [$this, 'schedule_expiration_check']);

        // Clear the options for expired promotions on cron run
        add_action('woofp_clear_expired_promotions', [$this, 'clear_expired_promotions']);
        
        // Add script to show/hide the expiration date field based on the checkbox status
        add_action('admin_footer', [$this, 'add_script_to_hide_expiration_date']);
    }

    public function add_promoted_product_fields() {
        global $post;

        // Get the current values of the promoted product options
        $promoted_status = get_option('woofp_promoted_product_status_' . $post->ID, 'no');
        $custom_title = get_option('woofp_promoted_product_custom_title_' . $post->ID, '');
        $expiration_date = get_option('woofp_promoted_product_expiration_date_' . $post->ID, '');

        woocommerce_wp_checkbox(array(
            'id' => 'promoted_product',
            'label' => __('Promote this product', 'text-domain'),
            'value' => $promoted_status,
            'desc_tip' => true,
        ));

        woocommerce_wp_text_input(array(
            'id' => 'promoted_product_custom_title',
            'label' => __('Custom Title for Promotion', 'text-domain'),
            'value' => $custom_title,
            'desc_tip' => true,
        ));

        woocommerce_wp_checkbox(array(
            'id' => 'promoted_product_expiration',
            'label' => __('Set Expiration Date', 'text-domain'),
            'value' => $expiration_date ? 'yes' : 'no',
            'desc_tip' => true,
        ));

        woocommerce_wp_text_input(array(
            'id' => 'promoted_product_expiration_date',
            'label' => __('Expiration Date and Time', 'text-domain'),
            'value' => $expiration_date ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($expiration_date)) : '',
            'type' => 'datetime-local', // Use 'date' for date-only input
            'desc_tip' => true,
            'wrapper_class' => 'hide-if-no-expiration', // Add a custom CSS class for hiding the field
        ));
    }

    public function save_promoted_product_fields($post_id) {
        $promoted_status = isset($_POST['promoted_product']) ? 'yes' : 'no';
        $custom_title = isset($_POST['promoted_product_custom_title']) ? sanitize_text_field($_POST['promoted_product_custom_title']) : '';
        $expiration_date = isset($_POST['promoted_product_expiration']) && $_POST['promoted_product_expiration'] === 'yes' ? sanitize_text_field($_POST['promoted_product_expiration_date']) : '';
        $expiration_date = strtotime($expiration_date); // Validate and convert to a timestamp

        update_option('woofp_promoted_product_status_' . $post_id, $promoted_status);
        update_option('woofp_promoted_product_custom_title_' . $post_id, $custom_title);
        update_option('woofp_promoted_product_expiration_date_' . $post_id, $expiration_date);
    }

    public function schedule_expiration_check() {
        if (!wp_next_scheduled('woofp_clear_expired_promotions')) {
            // Schedule the cron job to run every hour
            wp_schedule_event(time(), 'hourly', 'woofp_clear_expired_promotions');
        }
    }

    public function clear_expired_promotions() {
        // Get all products that have the "promoted_product" option set to "yes"
        $promoted_products = get_posts(array(
            'post_type' => 'product',
            'meta_key' => 'promoted_product',
            'meta_value' => 'yes',
            'numberposts' => -1,
        ));

        foreach ($promoted_products as $product) {
            // Get the expiration date and time for the product
            $expiration_date = get_option('woofp_promoted_product_expiration_date_' . $product->ID);
            $expiration_timestamp = strtotime($expiration_date);

            // Check if the expiration date is set and has passed
            if ($expiration_date && $expiration_timestamp < current_time('timestamp')) {
                // Clear the options for the expired product
                update_option('woofp_promoted_product_status_' . $product->ID, 'no');
                update_option('woofp_promoted_product_custom_title_' . $product->ID, '');
                update_option('woofp_promoted_product_expiration_date_' . $product->ID, '');
            }
        }
    }

    public function add_script_to_hide_expiration_date() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Function to show/hide the expiration date field based on checkbox status
                function toggleExpirationDateField() {
                    if ($('#promoted_product_expiration').prop('checked')) {
                        $('.hide-if-no-expiration').show();
                    } else {
                        $('.hide-if-no-expiration').hide();
                    }
                }

                // Trigger the function on page load
                toggleExpirationDateField();

                // Bind the function to the checkbox change event
                $('#promoted_product_expiration').change(function() {
                    toggleExpirationDateField();
                });
            });
        </script>
        <?php
    }
}
