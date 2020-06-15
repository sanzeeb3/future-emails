<?php
/**
 * Plugin Name: Future Emails
 * Description: Send emails in future from within your WordPress dashboard.
 * Version: 1.0.0
 * Author: Sanjeev Aryal
 * Author URI: http://www.sanjeebaryal.com.np
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once( 'vendor/woocommerce/action-scheduler/action-scheduler.php' );

add_action( 'admin_menu', 'add_settings' );
add_action( 'admin_init', 'get_form_data' );
  
function add_settings() {

	// This tools sub-menu is available for all user roles. If you'd like to restrict, change the capability. 
	add_management_page( 'Future Emails', 'Future Emails', 'read', 'future-emails', 'future_email_form' );
}
 
function future_email_form() {
	?>
		<h2 class="wp-heading-inline"><?php esc_html_e( 'Future Emails Settings', 'future-emails' ); ?></h2>
		<form method="post">
			<table class="form-table">

				<tr valign="top">
			    	<th scope="row"><?php echo esc_html__( 'Send Test Email To:', 'future-emails' );?></th>
			    		<td><input style="width:300px" type="email" name="to" />
			    		</td>
			    </tr>
				<tr valign="top">
			    	<th scope="row"><?php echo esc_html__( 'Email Subject:', 'future-emails' );?></th>
			    		<td><input style="width:300px" type="text" name="subject" />
			    		</td>
			    </tr>
				<tr valign="top">
			    	<th scope="row"><?php echo esc_html__( 'Email Message:', 'future-emails' );?></th>
			    		<td><textarea style="width:300px" name="message"></textarea>
			    		</td>
			    </tr>
   				<tr valign="top">
			    	<th scope="row"><?php echo esc_html__( 'Send email after (days):', 'future-emails' );?></th>
			    		<td><input style="width:300px" type="number" name="day" />
			    		</td>
			    </tr>

			</table>
		        <?php wp_nonce_field( 'future_email_settings', 'future_email_settings_nonce' );?>
				<?php submit_button( __( 'Send Email', 'future-emails' ) ); ?>
		</form>
	<?php
}

/**
 * Get form data.
 */
function get_form_data() {

	if( isset( $_POST['future_email_settings_nonce'] ) ) {

		if( ! wp_verify_nonce( $_POST['future_email_settings_nonce'], 'future_email_settings' )
			) {
			   print 'Nonce Failed!';
			   exit;
		}

		$to 	 = isset( $_POST['to'] ) ? sanitize_email( $_POST['to'] ) : '' ;
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '' ;
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '' ;
		$day 	 = isset( $_POST['day'] ) ? absint( $_POST['day'] ) : '' ;

		if ( empty( $day ) ) {
			send_email( $to, $subject, $message );
			return;
		}

		as_schedule_recurring_action( strtotime( '+'. $day .'day' ), DAY_IN_SECONDS, 'future_email', array( $to, $subject, $message ), 'future_email' );
	}
}

add_action( 'future_email', 'send_email', 10, 3 );

/**
 * Send emails.
 */
function send_email( $to, $subject, $message ) {

	$sent = wp_mail( $to, $subject, $message );

	// The end of the things. Hope you enjoy.
}