<?php
class selectcategory {

    protected $number = 20;
    protected $hideempty = 0;
    protected $order = 'DESC';
    protected $unit = 'pt';
    protected $orderby = 'cloud';
    protected $maxnum = 22;
    protected $minnum = 8;

    public function __construct() {
        add_action( 'admin_head-index.php', array(&$this, 'dashboard_css') );
        add_action( 'wp_dashboard_setup', array(&$this, 'category_post_dashboard_widget') );
        add_action( 'admin_menu', array(&$this, 'myoptionpages_add') );
        add_action('contextual_help', array(&$this, 'my_plugin_help'), 10, 3);
    }



    public function my_plugin_help($contextual_help, $screen_id, $screen) {
        if ($screen_id == 'settings_page_selectcategoryoptions') {
            $contextual_help = '<p>';
            $contextual_help .= __('CSS can be used for editing select-category-to-post dashborad style.', 'select-category-to-post');
            $contextual_help .= '</p>';
            $contextual_help .= '<p>';
            $contextual_help .= __('Widget id = "select_category_post"', 'select-category-to-post');
            $contextual_help .= '</p>';
            $contextual_help .= '<p>';
            $contextual_help .= __('ul class= "selectcategory" or "selectcategory-cloud"', 'select-category-to-post');
            $contextual_help .= '</p>';
            $contextual_help .= '<p>';
            $contextual_help .= __('a href id= "category[num]" where [num] is a category ID', 'select-category-to-post');
            $contextual_help .= '</p>';
            $contextual_help .= '<p>';
            $contextual_help .= __('a href class= (cloud only) "size[num]" where [num] is a size', 'select-category-to-post');
            $contextual_help .= '</p>';
            $contextual_help .= '<p>';
            $contextual_help .= __('This plugin does not check the validity of CSS you have entered.', 'select-category-to-post');
            $contextual_help .= '</p>';
        }
        return $contextual_help;
    }

    public function dashboard_css() {
        if( get_option('selectcategory_css') ) {
            echo '<style type="text/css">';
            esc_html_e(get_option('selectcategory_css'));
                echo '</style>';
        }
    }

    public function myoptionpages_add() {
        add_options_page(__('Select Category to Post', 'select-category-to-post'), __('Select Category to Post', 'select-category-to-post'), 'edit_plugins', 'selectcategoryoptions',  array(&$this, 'selectcategory_options_page') );
    }

    public function selectcategory_options_page() {
?>
<div class="wrap">
    <h2><?php _e('Options', 'select-category-to-post');?></h2>
<form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <?php $selectcategory_order = esc_attr(get_option('selectcategory_order', $this->orderby));?>
    <table class="form-table">

        <tr valign="top">
            <th scope="row"><label for="selectcategory_num"><?php _e('Number of Categories', 'select-category-to-post');?></label></th>
            <td><input type="text" id="selectcategory_num" name="selectcategory_num" value="<?php esc_html_e(get_option('selectcategory_num', $this->number)); ?>" size="5"></input></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="selectcategory_order"><?php _e('Order of Categories', 'select-category-to-post');?></label></th>
            <td><select id="selectcategory_order" name="selectcategory_order">
            <option value="name"<?php selected( $selectcategory_order, 'name' ); ?>><?php _e('Name','select-category-to-post'); ?></option>
            <option value="count"<?php selected( $selectcategory_order, 'count' ); ?>><?php _e('Count','select-category-to-post'); ?></option>
            <option value="none"<?php selected( $selectcategory_order, 'none' ); ?>><?php _e('term_id','select-category-to-post'); ?></option>			
            <option value="cloud"<?php selected( $selectcategory_order, 'cloud' ); ?>><?php _e('cloud','select-category-to-post'); ?></option>			
            </select></td>
        </tr>
         <tr valign="top">
            <th scope="row"><label for="selectcategory_order"><?php _e('Size of Clouds (if you choose cloud)', 'select-category-to-post');?></label></th>
             <td>Max: <input type="text" id="selectcategory_maxnum" name="selectcategory_maxnum" value="<?php esc_html_e(get_option('selectcategory_maxnum', $this->maxnum)); ?>" size="5"></input> &nbsp;&nbsp; min: <input type="text" id="selectcategory_minnum" name="selectcategory_minnum" value="<?php esc_html_e(get_option('selectcategory_minnum', $this->minnum)); ?>" size="5"></input></td>
        </tr>
         <tr valign="top">
            <th scope="row"><label for="selectcategory_css"><?php _e('CSS', 'select-category-to-post');?></label></th>
             <td><textarea id="selectcategory_css" name="selectcategory_css" cols="50" rows="5"><?php esc_html_e(get_option('selectcategory_css')); ?></textarea></td>
        </tr>
        </table>

    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="selectcategory_num,selectcategory_order,selectcategory_maxnum,selectcategory_minnum,selectcategory_css" />

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'select-category-to-post') ?>" />
    </p>

</form>
</div>
<?php }

public function category_post_dashboard_widget() {
    wp_add_dashboard_widget('select_category_post', __('Select Category to Post', 'select-category-to-post'), array(&$this, 'select_category_post'));	
} 


public function select_category_post() {
    $option_orderby = get_option('selectcategory_order', $this->orderby);
    if ( $option_orderby == 'name' ) {
        $this->orderby = 'name';
    } elseif ( $option_orderby == 'cloud' ) {            
        $this->orderby = 'name';
    } elseif ( $option_orderby == 'count' ) {            
        $this->orderby = 'count';
    } elseif ( $option_orderby == 'none' ) {
        $this->orderby = 'none';
    } else {
        wp_die( 'invalid selectcategory_order' );
    }

    if ( is_numeric( get_option('selectcategory_num', $this->number) ) ) {
        $this->number = intval( get_option('selectcategory_num', $this->number) );
    } else {
        wp_die( 'invalid selectcategory_num' );	    
    }

    if ( $option_orderby == 'cloud' ) {
        print $this->output_cloudlist();
    } else {
        print $this->output_catlist();
    }
}

protected function output_cloudlist() {
    if ( is_numeric( get_option('selectcategory_maxnum', $this->maxnum) ) ) {
        $largest = intval( get_option('selectcategory_maxnum', $this->maxnum) );
    } else {
        wp_die( 'invalid selectcategory_maxnum' );	    
    }
    if ( is_numeric( get_option('selectcategory_minnum', $this->minnum) ) ) {
        $smallest = intval( get_option('selectcategory_minnum', $this->minnum) );
    } else {
        wp_die( 'invalid selectcategory_minnum' );	    
    }
    $categories = get_terms('category', array(
        'number' => $this->number,
        'hide_empty' => $this->hideempty,
        'orderby' => 'count',
        'order' => $this->order ));

    foreach ( $categories as $catdata ) {
        $counts[] = $catdata->count;
    }
    $min_count = min( $counts );
    $spread = max( $counts ) - $min_count;
    if ( $spread <= 0 ) {
        $spread = 1;
    }
    $font_spread = $largest - $smallest;
    if ( $font_spread < 0 ) {
        $font_spread = 1;
    }
    $font_step = $font_spread / $spread;

    uasort( $categories, create_function('$a, $b', 'return strnatcasecmp($a->name, $b->name);') );

    $output = "<ul class=\"selectcategory-cloud\">\n<li>\n";
    foreach ( $categories as $catdata ) {
        $cat_id = $catdata->term_id;
        $catname = esc_html( apply_filters('the_category', $catdata->name) );
        $output .= "<a href=\"" . admin_url('/') . "post-new.php?defaultcatid=". $cat_id ."\" title=\"" . $catname . "\" id=\"selectcategory" . $cat_id . "\" class=\"size" . ( $smallest + ( ( $catdata->count - $min_count ) * $font_step ) ) . "\" style='font-size: " . ( $smallest + ( ( $catdata->count - $min_count ) * $font_step ) ) . $this->unit . ";'>";
        $output .= $catname;
        $output .= "</a>\n";
    }
    $output .= "</li>\n</ul>\n";

    return $output;
}

protected function output_catlist() {
    $categories = get_terms('category', array(
        'number' => $this->number,
        'hide_empty' => $this->hideempty,
        'orderby' => $this->orderby,
        'order' => $this->order ));

    $output = "<ul class=\"selectcategory\">\n<li>\n";
    foreach ( $categories as $catdata ) {
        $cat_id = $catdata->term_id;
        $catname = wp_specialchars( apply_filters('the_category', $catdata->name) );
        $output .= "<a href=\"" . admin_url('/') . "post-new.php?defaultcatid=". $cat_id ."\" title=\"" . $catname . "\" id=\"selectcategory" . $cat_id . "\">";
        $output .= $catname;
        $output .= "</a>\n";
    }
    $output .= "</li>\n</ul>\n";

    return $output;
}

}
class selectcategoryjs {
    protected $catid = 0;
    protected $js = "" ;
    public function __construct() {
        add_action( 'admin_head-post-new.php', array(&$this, 'catid_via_get') );
        if ( WP_DEBUG == TRUE ) {
            //add_action('admin_footer', array(&$this,'mylimetest'));
            add_action( 'wp_dashboard_setup', array(&$this, 'dashboard_limetest') );
        }
    }
    public function catid_via_get() {
        if ( current_user_can('edit_posts') && isset( $_GET['defaultcatid'] ) ){
            $this->catidcheck($_GET['defaultcatid']) ;
        }
    }

    protected function catidcheck($catid) {
        $this->catid = intval( $catid );
        if ( $this->catid ) {
            add_action( 'admin_print_footer_scripts', array(&$this, 'output_js') );
            return $this->catid;
        } else {
            return false;
        }
    }

    public function output_js() {
        echo $this->_output_js();  
    }
    private function _output_js() {
        if ( get_cat_name( $this->catid ) ) {
            $this->js = <<<EOF
<script>
window.onload= getcatid;
function getcatid(){
document.getElementById('in-category-
EOF;
            $this->js .= $this->catid;
            $this->js .= <<<EOF
').checked = true;
}  </script>
EOF;
            return $this->js;
        } else {
            $this->js = "";
            wp_die( 'invalid catid' );
        }
    }

    public function dashboard_limetest() {
        wp_add_dashboard_widget('select_category_post_limetest', __('Test for Select Category to Post JavaScript', 'select-category-to-post'), array(&$this, 'mylimetest'));
    }
    public function mylimetest() {
        if (file_exists(ABSPATH.PLUGINDIR."/select-category-to-post/lime.php")){
            require_once("lime.php");
            $t = new lime_test(null,new lime_output_color);
            echo "<pre>";
            $t->diag('Select Category JS Test');
            $t->is($this->catidcheck(0),false,"If category doesn't exist, return 'false'");
            // I assume category 1 (=uncategorized) exists.
            $t->is($this->catidcheck(1),1,"catidcheck returns catid. I assume category 1 (=uncategorized) exists");
            $t->is($this->_output_js(),"<script>
window.onload= getcatid;
function getcatid(){
document.getElementById('in-category-1').checked = true;
}  </script>","output_js generates javascript");
            echo "</pre>";
        } else {
            _e('Test needs lime.php.', 'select-category-to-post');
            _e('Please put lime.php in the plugin folder.', 'select-category-to-post');
        }
    }
}
