<?php
namespace WOOFP;

class Main {
    public function init_hooks() {
        $featured_product_settings = new Product\Featured_Product_Settings();
        $featured_product_settings->init_hooks();

        $promoted_product_fields = new Product\Promoted_Product_Fields();
        $promoted_product_fields->init_hooks();

        $promoted_product_display = new Frontend\Promoted_Product_Display();
        $promoted_product_display->init_hooks();
    }
}
