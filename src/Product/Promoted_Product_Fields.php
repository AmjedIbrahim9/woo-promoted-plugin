<?php
namespace WOOFP\Product;

class Promoted_Product_Fields {
    public function __construct() {
        // Add the "Promoted Product" fields to the single product editor
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_promoted_product_fields']);

        // Save the "Promoted Product" fields when the product is saved
        add_action('woocommerce_process_product_meta', [$this, 'save_promoted_product_fields']);
        
        // Add script to show/hide the expiration date field based on the checkbox status
        add_action('admin_footer', [$this, 'add_script_to_hide_expiration_date']);

        /**
         * Add check expiration method
         */
        add_action( 'check_promoted_product_event', array( $this, 'check_expiration' ) );

    }

    public function add_promoted_product_fields() {
        global $post;

        // Get the current values of the promoted product options
        $promoted_status = get_option('woofp_promoted_product_status', 'no');
        $custom_title = get_option('woofp_promoted_product_custom_title', '');
        $expiration_date = get_option('woofp_promoted_product_expiration_date', '');

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
            'value' => $expiration_date,
            'type' => 'datetime-local',
            'desc_tip' => true,
            'wrapper_class' => 'hide-if-no-expiration',
        ));
    }

    public function save_promoted_product_fields($post_id) {
        if (isset($_POST['promoted_product']) && $_POST['promoted_product'] === 'yes') {
            $custom_title = isset($_POST['promoted_product_custom_title']) ? sanitize_text_field($_POST['promoted_product_custom_title']) : '';
            $expiration_date = isset($_POST['promoted_product_expiration']) && $_POST['promoted_product_expiration'] === 'yes' ? sanitize_text_field($_POST['promoted_product_expiration_date']) : '';
    
            update_option('woofp_promoted_product_status', 'yes');
            update_option('woofp_promoted_product', $post_id);
    
            if (isset($_POST['promoted_product_custom_title'])) {
                update_option('woofp_promoted_product_custom_title', $custom_title);
            }
    
            if (isset($_POST['promoted_product_expiration'])) {
                update_option('promoted_product_expiration_date_status', 'yes');
                update_option('woofp_promoted_product_expiration_date', $expiration_date);
            } else {
                update_option('promoted_product_expiration_date_status', 'no');
                update_option('woofp_promoted_product_expiration_date', '');
            }
    
            $this->setup_schedule();
    
        } else {
            update_option('woofp_promoted_product_status', 'no');
            update_option('woofp_promoted_product', '');
            update_option('woofp_promoted_product_custom_title', '');
            update_option('promoted_product_expiration_date_status', 'no');
            update_option('woofp_promoted_product_expiration_date', '');
            $this->setup_schedule();
        }
    }
    

    public function setup_schedule() {
		if ( ! wp_next_scheduled( 'check_promoted_product_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'check_promoted_product_event' );
		}

	}

    public function check_expiration() {
		$expiration = get_option( 'promoted_product_expiration' );
		$expiration_date = get_option( 'promoted_product_expiration_date' );

		// If the date is not set, there's nothing to do.
		if ( $expiration=='no' || empty( $expiration_date ) ) {
			if ( wp_next_scheduled( 'check_promoted_product_event' ) ) {
				wp_clear_scheduled_hook( 'check_promoted_product_event' );
			}

			return;
		}

		// If it's due date or less than current date, update the option to be null.
		$current_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$expiration_date = DateTime::createFromFormat( 'Y-m-d\TH:i', $expiration_date, new DateTimeZone( 'UTC' ) );

		if (  $expiration=='yes' && $current_date >= $expiration_date ) {
            update_option( 'woofp_promoted_product_status' , 'no');
            update_option( 'woofp_promoted_product' , '');
            update_option( 'woofp_promoted_product_custom_title', '');
            update_option( 'promoted_product_expiration_date_status', 'no' );
            update_option( 'woofp_promoted_product_expiration_date' , '' );
			wp_clear_scheduled_hook( 'check_promoted_product_event' );
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
