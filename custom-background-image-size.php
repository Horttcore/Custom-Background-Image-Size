<?php
/*
Plugin Name: Custom Background Image Size
Plugin URI: http://horttcore.de
Description: Add custom background image size options in theme options
Version: 1.0
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
Textdomain: custom-background-image-size
*/



/**
 * Security, checks if WordPress is running
 **/
if ( !function_exists( 'add_action' ) ) :
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
endif;



/**
 * Custom Background Image Size Options
 */
class Custom_Background_Image_Size
{



	/**
	 * Default option
	 *
	 * @var string
	 */
	protected $default = 'auto auto';



	/**
	 * Default background selector
	 *
	 * @var string
	 */
	protected $default_selector = 'body.custom-background';



	/**
	 * Constructor
	 *
	 * @access public
	 * @uses current_theme_supports
	 * @uses is_admin
	 * @uses add_action
	 * @since v1.0
	 * @author Ralf Hortt
	 */
	public function __construct()
	{

		if ( is_admin() ) :
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_register_scripts' ) );
			add_action( 'admin_print_scripts-appearance_page_custom-background', array( $this, 'wp_enqueue_script' ) );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'wp_ajax_set-background-image-size', array( $this, 'set_background_image_size' ) ); # Workaround as WordPress does not supply any hooks on that screen
			#add_action( 'admin_enqueue_scripts', array( $this, 'wp_register_styles' ) ); # Prolly needed in a future version
			#add_action( 'admin_print_scripts-appearance_page_custom-background', array( $this, 'wp_enqueue_style' ) ); # Prolly needed in a future version
		else :
			add_action( 'wp_head', array( $this, 'wp_head' ) );
		endif;

	} // end __construct



	/**
	 * Load plugin textdomain
	 *
	 * @access public
	 * @since v1.0
	 * @author Ralf Hortt
	 */
	public function load_plugin_textdomain()
	{

		load_plugin_textdomain( 'custom-background-image-size', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	} // end load_plugin_textdomain



	/**
	 * Save background image size
	 *
	 * @access public
	 * @uses current_user_can
	 * @uses set_theme_mod
	 * @uses sanitize_text_field
	 * @since v1.0
	 * @author Ralf Hortt
	 */
	public function set_background_image_size()
	{

		if ( !current_user_can( 'edit_theme_options' ) || !isset( $_POST['backgroundImageSize'] ) )
			return;

		set_theme_mod( 'background-image-size', sanitize_text_field( $_REQUEST['backgroundImageSize'] ) );

		if ( 'custom' == $_REQUEST['backgroundImageSize'] ) :
			set_theme_mod( 'background-image-height', sanitize_text_field( $_REQUEST['backgroundImageHeight'] ) );
			set_theme_mod( 'background-image-width', sanitize_text_field( $_REQUEST['backgroundImageWidth'] ) );
		else :
			set_theme_mod( 'background-image-height', '' );
			set_theme_mod( 'background-image-width', '' );
		endif;

	}



	/**
	 * Enqueue contentbox localized javascript
	 *
	 * @access public
	 * @uses wp_enqueue_script
	 * @uses wp_localize_script
	 * @uses get_theme_mod
	 * @since v1.0
	 * @author Ralf Hortt
	 **/
	public function wp_enqueue_script()
	{
		wp_enqueue_script( 'custom-background-image-size' );
		wp_localize_script( 'custom-background-image-size', 'customBackgroundImageSizeOptions', array(
			'auto' => __( 'Auto', 'custom-background-image-size' ),
			'backgroundSize' => __( 'Dimensions of the background image', 'custom-background-image-size' ),
			'backgroundImageSize' => get_theme_mod( 'background-image-size' ),
			'backgroundImageHeight' => get_theme_mod( 'background-image-height' ),
			'backgroundImageWidth' => get_theme_mod( 'background-image-width' ),
			'contain' => __( 'Contain', 'custom-background-image-size' ),
			'cover' => __( 'Cover', 'custom-background-image-size' ),
			'custom' => __( 'Custom', 'custom-background-image-size' ),
			'imageSize' => __( 'Image size', 'custom-background-image-size' ),
			'height' => __( 'Height', 'custom-background-image-size' ),
			'value' => get_theme_mod( 'background-image-size', $this->default ),
			'width' => __( 'Width', 'custom-background-image-size' ),
		) );
	}



	/**
	 * Enqueue styles
	 *
	 * @access public
	 * @uses wp_enqueue_style
	 * @since v1.0
	 * @author Ralf Hortt
	 **/
	public function wp_enqueue_style()
	{

		wp_enqueue_style( 'custom-background-image-size' );

	} // end wp_enqueue_style



	/**
	 * Inject css
	 *
	 * @access public
	 * @uses get_theme_mod
	 * @uses apply_filters `default-selector` `background-image-size` `background-image-size-important`
	 * @since v1.0
	 * @author Ralf Hortt
	 */
	public function wp_head()
	{
		$background_image_size = get_theme_mod( 'background-image-size' );

		if ( 'custom' == $background_image_size ) :
			$background_image_size = get_theme_mod( 'background-image-width' ) . ' ' . get_theme_mod( 'background-image-height' );
		endif;

		if ( $this->default != $background_image_size && '' != $background_image_size && FALSE !== $background_image_size ) :
			?>
			<style type="text/css">
				<?php echo apply_filters( 'default-selector', $this->default_selector ) ?> {
					background-size: <?php echo apply_filters( 'background-image-size', $background_image_size ) ?><?php if ( TRUE === apply_filters( 'background-image-size-important', FALSE ) ) echo ' !important' ?>;
				}
			</style>
			<?php
		endif;
	}



	/**
	 * Register javascripts
	 *
	 * @access public
	 * @uses wp_register_scripts
	 * @since v1.0
	 * @author Ralf Hortt
	 **/
	public function wp_register_scripts()
	{

		wp_register_script( 'custom-background-image-size', plugins_url( 'javascript/custom-background-image-size.js', __FILE__ ), FALSE, NULL, TRUE );

	} // end wp_register_scripts



	/**
	 * Register css styles
	 *
	 * @access public
	 * @uses wp_register_style
	 * @since v1.0
	 * @author Ralf Hortt
	 **/
	public function wp_register_styles()
	{

		wp_register_style( 'custom-background-image-size', plugins_url( 'css/custom-background-image-size.css', __FILE__ ) );

	} // end wp_register_styles



}

new Custom_Background_Image_Size;
