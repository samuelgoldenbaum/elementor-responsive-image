<?php
/**
 * Responsive_Image_For_Elementor class.
 *
 * @category   Class
 * @package    ResponsiveImageForElementor
 * @subpackage WordPress
 * @author     Samuel Goldenbaum
 * @copyright  2021 Samuel Goldenbaum
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       https://github.com/samuelgoldenbaum/responsive-image-for-elementor/
 * @since      1.0.0
 * php version 7.3.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

require_once 'constants.php';


abstract class NoticeType
{
    const Error = 'notice-error';
    const Warning = 'notice-warning';
    const Success = 'notice-success';
    const Info = 'notice-info';
}

final class AdminNotice {
    private $_message;
    private $_noticeType;

    function __construct( $message, $noticeType ) {
        $this->_message = $message;
        $this->_noticeType = $noticeType;

        add_action( 'admin_notices', array( $this, 'render' ) );
    }

    function render() {
        printf( '<div class="notice ' . $this->_noticeType . ' is-dismissible">%s</div>', $this->_message );
    }
}

/**
 * Main Responsive Image For Elementor Class
 *
 * The init class that runs the Responsive Image For Elementor plugin.
 * Intended To make sure that the plugin's minimum requirements are met.
 *
 * You should only modify the constants to match your plugin's needs.
 *
 * Any custom code should go inside Plugin Class in the plugin.php file.
 */
final class Responsive_Image_For_Elementor {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.1.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.3';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		// Load the translation.
		add_action( 'init', array( $this, 'i18n' ) );

		// Initialize the plugin.
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( RESPONSIVE_IMAGE_FOR_ELEMENTOR_TD );
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version.
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
		    add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version.
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our widgets.
		require_once 'class-widgets.php';
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		deactivate_plugins( plugin_basename( RESPONSIVE_IMAGE_FOR_ELEMENTOR_FILE ) );

		return sprintf(
			wp_kses(
				'<div class="notice notice-warning is-dismissible"><p><strong>%1$s</strong> requires <strong>%2$s</strong> to be installed and activated.</p></div>',
                array(
                    'div' => array('class'  => array()),
                    'p'      => array(),
                    'strong' => array()
                )
			),
            PLUGIN_NAME,
			'Elementor'
		);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
        deactivate_plugins( plugin_basename( RESPONSIVE_IMAGE_FOR_ELEMENTOR_FILE ) );

		return printf(
			wp_kses(
				'<div class="notice notice-warning is-dismissible"><p><strong>%1$s</strong> requires <strong>%2$s</strong> version %3$s or greater.</p></div>',
				array(
					'div' => array('class'  => array()),
                    'p'      => array(),
                    'strong' => array()
				)
			),
            PLUGIN_NAME,
			'Elementor',
			self::MINIMUM_ELEMENTOR_VERSION
		);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		deactivate_plugins( plugin_basename( RESPONSIVE_IMAGE_FOR_ELEMENTOR_FILE ) );

		return printf(
			wp_kses(
				'<div class="notice notice-warning is-dismissible"><p><strong>%1$s</strong> requires <strong>%2$s</strong> version %3$s or greater.</p></div>',
                array(
                    'div' => array('class'  => array()),
                    'p'      => array(),
                    'strong' => array()
                )
			),
            PLUGIN_NAME,
			'PHP',
			self::MINIMUM_PHP_VERSION
		);
	}
}

// Instantiate
new Responsive_Image_For_Elementor();
