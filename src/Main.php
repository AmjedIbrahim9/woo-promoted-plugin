<?php
namespace WOOFP;

class Main {
    public function init_hooks() {

    new Product\Featured_Product_Settings();
    new Product\Promoted_Product_Fields();
    new Frontend\Promoted_Product_Display();
    
    }
}
