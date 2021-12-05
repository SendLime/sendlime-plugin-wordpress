<?php

namespace SendLime\SendLime;

/**
 * The admin class
 */
class Admin {

    /**
     * Initialize the class
     */
    function __construct() {
		$this->dispatch_actions();
        new Admin\Menu();
    }

	public function dispatch_actions() {
		$wc_order_notification = new Admin\WCOrderNotification();
		add_action( 'admin_init', [ $wc_order_notification, 'form_handler' ] );
	}
}
