<?php
/*
   Plugin Name: Select Category to Post
   Plugin URI: http://ounziw.com/2010/11/08/selectcategory-to-post/
   Description: Select Category to Post
   Author: Fumito MIZUNO
   Version: 2.1
   Author URI: http://ounziw.com/
 */

load_plugin_textdomain( 'select-category-to-post', false, dirname( plugin_basename( __FILE__ ) ) .'/languages/' );
require_once("class.php");
$selectcategory = new selectcategory;
$selectcategoryjs = new selectcategoryjs;
add_action('deactivate_select-category-to-post/select-category-to-post.php',  'selectcategory_delete_options' );
function selectcategory_delete_options() {
    if ( current_user_can( 'delete_plugins' ) ) {
        delete_option('selectcategory_num');
        delete_option('selectcategory_order');
        delete_option('selectcategory_maxnum');
        delete_option('selectcategory_minnum');
    } 
} 
