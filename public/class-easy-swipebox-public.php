<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link            https://github.com/leopuleo/easy-swipebox
 * @since           1.1
 * @package         EasySwipeBox
 *
 * @subpackage 		EasySwipeBox/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package         EasySwipeBox
 * @subpackage 		EasySwipeBox/public
 * @author     		leopuleo
 */
class Easy_SwipeBox_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $options_autodetect;

	private $options_gallery;

	private $options_lightbox;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $options_autodetect, $options_gallery, $options_lightbox ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options_autodetect = $options_autodetect;
		$this->options_gallery = $options_gallery;
		$this->options_lightbox = $options_lightbox;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.1
	 */
	public function enqueue_styles() {

		/**
		 * Dequeue any existing SwipeBox CSS
		 * Register Plugin CSS:
		 * unminifiled for development (set WP_DEBUG true)
		 * minified for production
		 */

		wp_dequeue_style('swipebox');
        wp_dequeue_style('jquery.swipebox');
        wp_dequeue_style('jquery_swipebox');
        wp_dequeue_style('jquery-swipebox');

        if (defined('WP_DEBUG') && true == WP_DEBUG){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/swipebox.css', array(), $this->version, 'all' );
		} else {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/swipebox.min.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.1
	 */
	public function enqueue_scripts() {

		/**
		 * Register SwipeBox Scripts:
		 * 1) Core
		 *    unminifiled for development (set WP_DEBUG true)
		 *    minified for production
		 * 2) Custom init
		 * 3) Autodetect Images
		 * 4) Autodetect Video
		 *
		 */

		if (defined('WP_DEBUG') && true == WP_DEBUG){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jquery.swipebox.js', array( 'jquery' ), $this->version, true);
		} else {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jquery.swipebox.min.js', array( 'jquery' ), $this->version, true);
		}

        wp_enqueue_script($this->plugin_name .'-init', plugin_dir_url( __FILE__ ) . 'js/jquery.init.js', array( 'jquery' ), $this->version, true);
		wp_localize_script($this->plugin_name .'-init', 'easySwipeBox_localize_init_var', $this->localize_init_var());
	}

	/**
	 * Localize vars for SwipeBox init
	 * Print vars stored in db and passed to js files
	 *
	 * @since    1.1
	 */

	public function localize_init_var() {
		$localize_var = array(
            'lightbox' => array(
	            'useCSS' => (bool)$this->options_lightbox['useCSS'],
	            'useSVG' => (bool)$this->options_lightbox['useSVG'],
	            'removeBarsOnMobile' => (bool)$this->options_lightbox['removeBarsOnMobile'],
	            'hideCloseButtonOnMobile' => (bool)$this->options_lightbox['hideCloseButtonOnMobile'],
	            'hideBarsDelay' =>  absint($this->options_lightbox['hideBarsDelay']),
	            'videoMaxWidth' => absint($this->options_lightbox['videoMaxWidth']),
	            'vimeoColor' => $this->sanitize_hex_color($this->options_lightbox['vimeoColor']),
	            'loopAtEnd' => (bool)$this->options_lightbox['loopAtEnd'],
	            'autoplayVideos' => (bool)$this->options_lightbox['autoplayVideos']
            ),
            'autodetect' => array(
            	'autodetectImage' => (bool)$this->options_autodetect['image'],
            	'autodetectVideo' => (bool)$this->options_autodetect['video'],
            	'autodetectExclude' => sanitize_key($this->options_autodetect['class_exclude'])
            ),
            'gallery' => array(
            	'galleryRel' => (bool)$this->options_gallery['gallery_rel'],
            	'galleryHash' =>  sanitize_key('swipebox-' . $this->generate_random(4))
            )
        );

        return $localize_var;
	}


	private function sanitize_hex_color( $color, $hash = false ) {

	    // Remove any spaces and special characters before and after the string
	    $color = trim( $color );

	    // Remove any trailing '#' symbols from the color value
	    $color = str_replace( '#', '', $color );

	    // If the string is 6 characters long then use it in pairs.
	    if ( 3 == strlen( $color ) ) {
	        $color = substr( $color, 0, 1 ) . substr( $color, 0, 1 ) . substr( $color, 1, 1 ) . substr( $color, 1, 1 ) . substr( $color, 2, 1 ) . substr( $color, 2, 1 );
	    }

	    $substr = array();
	    for ( $i = 0; $i <= 5; $i++ ) {
	        $default    = ( 0 == $i ) ? 'F' : ( $substr[$i-1] );
	        $substr[$i] = substr( $color, $i, 1 );
	        $substr[$i] = ( false === $substr[$i] || ! ctype_xdigit( $substr[$i] ) ) ? $default : $substr[$i];
	    }
	    $hex = implode( '', $substr );

	    return ( ! $hash ) ? $hex : '#' . $hex;

	}


	private function generate_random( $length = 64 ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$random_value = '';

		for( $i = 0; $i < $length; $i++ ) {
			$random_value .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		return $random_value;
	}



}
