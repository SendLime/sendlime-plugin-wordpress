<?php
/**
 * Plugin Name: SendLime
 * Description: SendLime lets you notify your customers about their WooCommerce order updates via SMS.
 * Plugin URI: https://wordpress.org/plugins/sendlime
 * Author: SendLime
 * Author URI: https://www.sendlime.com
 * Version: 1.1.2
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sendlime
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

$wc_order_notification_controller = new \SendLime\SendLime\Admin\WCOrderNotification();

/**
 * The main plugin class
 */
final class SendLime {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0';

    /**
     * Class constructor
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Initializes a singleton instance
     *
     * @return SendLime
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'SENDLIME_VERSION', self::version );
        define( 'SENDLIME_FILE', __FILE__ );
        define( 'SENDLIME_PATH', __DIR__ );
        define( 'SENDLIME_URL', plugins_url( '', SENDLIME_FILE ) );
        define( 'SENDLIME_ASSETS', SENDLIME_URL . '/assets' );
		define( 'SENDLIME_WC_ORDER_NOTIFICATION_SETTINGS_KEY', 'sendlime_wc_order_notification_settings' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        if ( is_admin() ) {
            new SendLime\SendLime\Admin();
        }

    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new SendLime\SendLime\Installer();
		$installer->run();
    }
}

/**
 * Initializes the main plugin
 *
 * @return SendLime
 */
function sendlime() {
    return SendLime::init();
}

add_action( 'woocommerce_order_status_changed', [ $wc_order_notification_controller, 'wc_order_status_change_handler' ] );
add_action( 'woocommerce_new_order', [ $wc_order_notification_controller, 'send_new_order_notification' ] );

function sendlime_settings_link( $links ) {
	$settings_link = array(
		'<a href="admin.php?page=sendlime" title="' . __( 'Settings ', 'sendlime' ) . '">' . __( 'Settings', 'sendlime' ) . '</a>',
	);

	foreach( $settings_link as $link )	{
		array_unshift( $links, $link );
	}

	return $links;
}

add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'sendlime_settings_link' );

// kick-off the plugin
sendlime();
