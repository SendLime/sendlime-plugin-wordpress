<?php

namespace SendLime\SendLime\Admin;

/**
 * The Menu handler class
 */
class Menu {

    /**
     * Initialize the class
     */
    function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Register admin menu
     *
     * @return void
     */
    public function admin_menu() {
        add_submenu_page( 'woocommerce', __( 'SendLime', 'sendlime' ), __( 'SendLime Notifications', 'sendlime' ), 'manage_woocommerce', 'sendlime', [ $this, 'order_notification' ] );
    }

    /**
     * Render the plugin page
     *
     * @return void
     */
    public function order_notification() {
        $wc_order_notification = new WCOrderNotification();
		$wc_order_notification->plugin_page();
    }
}
