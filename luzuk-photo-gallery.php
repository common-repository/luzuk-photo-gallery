<?php
/*
Plugin Name: Luzuk Photo Gallery
Plugin URI:
Description: Luzuk Photo Gallery is a plugin to create your own Image Gallery section. You can add this shortcode [luzuk_gallery].
Version: 0.0.1
Author: Luzuk
Author URI: https://www.luzuk.com
License: GPLv2
*/



//defining path
define( 'LPGP_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'LPGP_DIR_URL', plugin_dir_url( __FILE__ ) );


// Active plugin
function lpgp_activation() {
}
register_activation_hook(__FILE__, 'lpgp_activation');

// Deactive plugin
function lpgp_deactivation() {
}
register_deactivation_hook(__FILE__, 'lpgp_deactivation');



// Added styes css
add_action('wp_enqueue_scripts', 'flpgp_styles');
function flpgp_styles() {

  
    wp_register_style('bootstrap', plugins_url('assets/css/bootstrap.min.css', __FILE__));
    wp_enqueue_style('bootstrap');

    
    wp_register_style('ekko_lightbox_style', plugins_url('assets/css/ekko-lightbox.css', __FILE__));
    wp_enqueue_style('ekko_lightbox_style');
    

    wp_register_style('style', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_style('style');

   
}


// Added script
add_action('wp_enqueue_scripts', 'flpgp_scripts');
function flpgp_scripts() {

  
    wp_register_script('luzuk_photo', plugins_url('assets/js/luzukphoto.min.js', __FILE__),array("jquery"));
    wp_enqueue_script('luzuk_photo');

    
    wp_register_script('popper_min', plugins_url('assets/js/popper.min.js', __FILE__),array("jquery"));
    wp_enqueue_script('popper_min');
  
    
    wp_register_script('ekko_lightbox', plugins_url('assets/js/ekko-lightbox.js', __FILE__),array("jquery"));
    wp_enqueue_script('ekko_lightbox');


    wp_register_script('bootstrap_js', plugins_url('assets/js/bootstrap.min.js', __FILE__),array("jquery"));
    wp_enqueue_script('bootstrap_js');


}




// Dynamic colors patterns 
function flpg_css_strip_whitespace($css){
      $replace = array(
        "#/\*.*?\*/#s" => "",  // Strip C style comments.
        "#\s\s+#"      => " ", // Strip excess whitespace.
      );
      $search = array_keys($replace);
      $css = preg_replace($search, $replace, $css);

      $replace = array(
        ": "  => ":",
        "; "  => ";",
        " {"  => "{",
        " }"  => "}",
        ", "  => ",",
        "{ "  => "{",
        ";}"  => "}", // Strip optional semicolons.
        ",\n" => ",", // Don't wrap multiple selectors.
        "\n}" => "}", // Don't wrap closing braces.
        "} "  => "}\n", // Put each rule on it's own line.
      );
      $search = array_keys($replace);
      $css = str_replace($search, $replace, $css);
      return trim($css);
}

// Adding Custome Post Type
function createCustomeTypesLuzukPhotoGallery() {
// team
    register_post_type( 'our-lgallery',
        array(
            'labels' => array(
                'name' => __( 'Luzuk Photo Gallery' , 'Luzuk'),
                'singular_name' => __( 'Photo Gallery', 'Luzuk' )
            ),
            'public' => true,
            'featured_image'=>true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-admin-media', //  The url to the icon to be used for this menu or the name of the icon from the iconfont
            'supports' => array('title', 'thumbnail', 'author', 'page-attributes'),
        )
    );

}

// post type initialize
add_action( 'init', 'createCustomeTypesLuzukPhotoGallery' );



/**
 * Liting the team/trainer details 
 * @param : int $pageId default is null
 * @param : boolean $isCustomizer default is false, if set to true will get the data stored with customizer
 * @param : int $i default is null, it will used as a iteration for data with customizer, this will be used only if the $isCustomizer is set to true.
 * @return: Text $text
 */
function lpg_imageShortCode($pageId = null, $isCustomizer = false, $i = null) {
  global $lpgp_options;

  $args = array('post_type' => 'our-lgallery');
  if (!empty($pageId)) {
      $args['page_id'] = absint($pageId);
  }
  $args['posts_per_page'] = -1;
  $colCls = '';
  $cols = get_theme_mod('galleryluzuk_npp_count', 2);
  $cols++;
  switch($cols){
      case 1:
          $colCls = 'col-md-12 col-sm-12 col-xs-12';
          break;
      case 2:
          $colCls = 'col-md-6 col-sm-6 col-xs-12';
          break;
      case 3:
      case 5:
      case 6:
          $colCls = 'col-md-4 col-sm-6 col-xs-12';
          break;
      default:
          $colCls = 'col-md-3 col-sm-6 col-xs-12';
          break;
  }
              
  $text = '';
  $query = new WP_Query($args);
  if ($query->have_posts()):
      $postN = 0; ?>



  <div id = "gallery">
    <?php
      while ($query->have_posts()) : $query->the_post();
      $luzuk_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'total-galleryluzuk-thumb');
      $post = get_post();
      ?>

    <div class = "<?php echo esc_attr($colCls) ?> glly">
      <?php
        if (has_post_thumbnail()) {
          $image_url = $luzuk_image[0];
        } else {
          $image_url = get_template_directory_uri() . '/images/about.jpg';
        }
        ?>
      <div>
        <a href="<?php echo esc_url($image_url) ?>" data-toggle="lightbox" data-gallery="gallery">
          <img src="<?php echo esc_url($image_url) ?>" class="imggallery">
        </a>
      </div>
    </div>
    <?php
    endwhile; ?>
    <div class="clearfix"></div>
  </div>

  <!-- ** Lightbox Script ** -->
  <script>
    $(document).on("click", '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });
  </script>


  <?php
  $text = ob_get_contents();
  ob_clean();
  endif;
  wp_reset_postdata();
  return $text;

}


//adding a shortcode for the team / trainer list 
add_shortcode('luzuk_gallery', 'lpg_imageShortCode');

?>