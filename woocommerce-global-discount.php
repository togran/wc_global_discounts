<?php
/**
 * Plugin Name:       WC Global Discount
 * Plugin URI:        https://example.com/
 * Description:       Set discount to all products
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Max Sobolev
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_menu', 'my_admin_menu');


function print_cat_list($parent=0, $level=0)
{
    $categories = get_categories(array(
        'taxonomy' => 'product_cat',
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => 1,
        'hierarchical' => 1,
        'exclude' => '',
        'pad_counts' => false,
        'parent' => $parent,
    ));
    if ($categories) {
        $pad=str_pad('', $level*6*4, '&nbsp;');
        foreach ($categories as $cat) {
            ?>
            categories["<?php echo($cat->cat_ID); ?>"]=["<?php echo($pad.$cat->name); ?>", <?php echo $level ?>];
            <?php
            print_cat_list($cat->cat_ID, $level+1);
        }
    }
}
function my_admin_menu() {
    add_menu_page('Global Discount', 'Global Discount Settings', 1, 'GD', 'print_page_function');

    function print_page_function()
    {
        wp_enqueue_script( 'gd_script', plugin_dir_url( __FILE__ ).'script.js');
        if (isset($_POST['discount']) && isset($_POST['gdCat'])) {
            update_option('gd_discount', ["gdCat"=>$_POST['gdCat'], "discount"=>$_POST['discount']]);
            $current_discounts = get_option('gd_discount');
            foreach ($current_discounts['gdCat'] as $n=>$discount) {
                $cat=$current_discounts['gdCat'][$n];
                $discount=$current_discounts['discount'][$n];
                $args=array(
                    'limit' => -1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'return' => 'ids',
                );
                if($cat!=0) {
                    $args['category'] = [get_term($cat, 'product_cat')->slug];
                }
                $query = new WC_Product_Query($args);
                $products = $query->get_products();
                foreach ($products as $productID) {
                    $product = wc_get_product($productID);
                    $product->set_sale_price($product->get_regular_price() * ((100 - $discount) / 100));
                    $product->save();
                }
            }
        }

        ?>
        <form method="post" id="gdForm">
            <div id="discounts"></div>
            <button type="submit">Submit</button>
        </form>
        <button type="button" id="bAdd">Add</button>
                        <script>
                            var categories={'0':['All', 0]};
                            var discounts=[];
        <?php


        print_cat_list();
        $current_discount = get_option('gd_discount');
//        var_dump($current_discount);
        if($current_discount){
            foreach ($current_discount['gdCat'] as $i=>$discount){
                ?>
                    discounts.push([<?php echo($current_discount['gdCat'][$i]);?>, <?php echo($current_discount['discount'][$i]);?>]);
                <?php
            }
        }
            ?>

                        </script>
                            <?php
    }
}
