<?php
/**
 * Plugin Name: Woo Hotspot Product Slider
 * Description: This plugin is for woocommerce product slide when clink on hotspot on the image product will show and if you click on arrow product will slide one y one(Hotspot image + random products + desktop 50/50 with arrow slider)
 * Version: 4.0
 * Author: Reetarani Samal
 */

if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('hotspot-css', plugin_dir_url(__FILE__) . 'hotspot.css');
    wp_enqueue_script('hotspot-js', plugin_dir_url(__FILE__) . 'hotspot.js', ['jquery'], false, true);
    wp_localize_script('hotspot-js','hotspot_ajax',[ 'ajax_url'=>admin_url('admin-ajax.php') ]);
});

function hotspot_load_product_ajax(){
    $pid=intval($_POST['product_id']);
    $product=wc_get_product($pid);
    if(!$product) wp_die();

    echo '<div class="hotspot-product-item">';
    echo $product->get_image('large');
    echo '<h2>'.$product->get_name().'</h2>';
    echo '<p class="price">'.$product->get_price_html().'</p>';
    echo '<div class="hotspot-nav">';
    echo '<button class="hotspot-prev"><span>‹</span></button>';
    echo '<button class="hotspot-next"><span>›</span></button>';
    echo '</div>';
    echo '</div>';
    wp_die();
}
add_action('wp_ajax_hotspot_load_product','hotspot_load_product_ajax');
add_action('wp_ajax_nopriv_hotspot_load_product','hotspot_load_product_ajax');

function woo_hotspot_shortcode(){

    $q=new WP_Query([
        'post_type'=>'product',
        'posts_per_page'=>3,
        'orderby'=>'rand',
        'post_status'=>'publish'
    ]);

    $ids=[];
    while($q->have_posts()){ $q->the_post(); $ids[]=get_the_ID(); }
    wp_reset_postdata();

    if(empty($ids)) return "<p>No products found.</p>";

    $img_url = plugins_url('frame.jpg', __FILE__);

    ob_start(); ?>

<div class="hotspot-wrapper">

    <div class="hotspot-media">
        <img src="<?php echo $img_url; ?>" class="hotspot-img">

        <div class="hotspot-dot dot1" data-product="<?php echo $ids[0]; ?>"></div>
        <?php if(isset($ids[1])): ?><div class="hotspot-dot dot2" data-product="<?php echo $ids[1]; ?>"></div><?php endif; ?>
        <?php if(isset($ids[2])): ?><div class="hotspot-dot dot3" data-product="<?php echo $ids[2]; ?>"></div><?php endif; ?>
    </div>

    <div class="hotspot-product-panel" id="product-output"></div>

</div>

<script>window.hotspotProducts = <?php echo json_encode($ids); ?>;</script>

<?php return ob_get_clean();
}
add_shortcode('woo_hotspot','woo_hotspot_shortcode');
?>