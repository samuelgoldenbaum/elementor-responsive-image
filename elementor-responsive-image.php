<?php
/**
 * Elementor Responsive Image WordPress Plugin
 *
 * @package ElementorResponsiveImage
 *
 * Plugin Name: Responsive Image For Elementor
 * Description: Modern Responsive Image
 * Plugin URI:  https://github.com/samuelgoldenbaum/elementor-responsive-image/
 * Version:     1.0.0
 * Author:      Samuel Goldenbaum
 * Author URI:  https://github.com/samuelgoldenbaum/
 * Text Domain: elementor-responsive-image
 */

define( 'ELEMENTOR_RESPONSIVE_IMAGE', __FILE__ );

/**
 * Include the Elementor_Responsive_Image class.
 */
require plugin_dir_path( ELEMENTOR_RESPONSIVE_IMAGE ) . 'class-elementor-responsive-image.php';
