<?php
    $notification_controller = new SendLime\SendLime\Admin\WCOrderNotification();
    $settings = get_option( SENDLIME_WC_ORDER_NOTIFICATION_SETTINGS_KEY );

    $check_enable = '';
    $enabled = $settings['enabled'];
    if ( $enabled ) $check_enable = ' checked';
?>

<div class="wrap">
	<h1><?php _e( 'SendLime SMS Notification', 'sendlime' ); ?></h1>

    <form action="" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="enabled"><?php _e( 'Enable', 'sendlime' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="enabled" id="enabled" <?php echo esc_attr( $check_enable ) ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="from"><?php _e( 'From', 'sendlime' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="from" id="from" class="regular-text" value="<?php echo esc_attr( $settings['from'] ) ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="api_key"><?php _e( 'API Key', 'sendlime' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="api_key" id="api_key" class="regular-text" value="<?php echo esc_attr( $settings['api_key'] ) ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="api_secret"><?php _e( 'API Secret', 'sendlime' ); ?></label>
                    </th>
                    <td>
                        <input type="password" name="api_secret" id="api_secret" class="regular-text" value="<?php echo esc_attr( $settings['api_secret'] ) ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="apiSecret"><?php _e( 'Select status', 'sendlime' ); ?></label>
                    </th>
                    <td>
                        <?php $notification_controller->wc_statuses(); ?>
                    </td>
                </tr>
                <?php $notification_controller->wc_status_messages(); ?>
            </tbody>
        </table>

        <?php wp_nonce_field( 'wc-order-notification-settings' ) ?>
        <?php submit_button( __( 'Save Changes', 'sendlime' ), 'primary', 'wc_order_notification_settings' ); ?>
    </form>
</div>