<?php

/*
Plugin Name: Restrict User Registration
Plugin URI: 
Description: Allows you to restrict registration for custom usernames, email addresses and custom email service providers
Author: Samuel Elh
Version: 1.0.1
Author URI: http://samelh.com
*/

// Ignore direct access
defined( 'ABSPATH' ) || exit;

class RUR
{

	protected static $instance = null;

	public static function instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	function __construct() {


		$this->init();

	}

	public function init() {


		add_action( 'admin_menu', function() {
			add_users_page('Restrict User Registration', 'Restrict Registration', 'manage_options', 'rur', array( $this, 'rur_settings' ));
		});

		add_action( 'admin_enqueue_scripts', function() {

			wp_enqueue_script( 'rur-js',  plugin_dir_url(__FILE__) . 'assets/admin.js', array('jquery'), '1.0', true );
			wp_enqueue_style( 'rur-css',  plugin_dir_url(__FILE__) . 'assets/admin.css' );
			
		});

		add_filter( "plugin_action_links_".plugin_basename(__FILE__), function($links) {
		    array_push( $links, '<a href="users.php?page=rur">' . __( 'Settings' ) . '</a>' );
		  	return $links;
		});

		$this->bail();

	}

	public function rur_settings() {

		$this->update();

		?>

			<div class="wrap rur">
				
				<div class="rur_left">

					<h2>Restrict User Registration &raquo; Settings</h2>

					<form method="post">
							
						<table>
							
							<tr>
								
								<td valign="top">
									<h3>Usernames</h3>
									<i>Add usernames to restrict for user registration</i>
								</td>
								<td>
									<strong>Usernames:</strong>
									<textarea name="usernames" placeholder="enter usernames separated by commas" rows="4" cols="40" id="add-username" style="display:block"><?php echo implode(',', $this->get()['usernames']); ?></textarea><br/>
									<div id="add-username-cont" class="add-data-cont" data-for="username"></div>
									<span class="add-data" onclick="rur_add('add-username')">+ Add</span>
									<p></p>
									<label><strong>Error message:</strong><br/>
									<input type="text" name="usernames_error" placeholder="Error text" size="40" value="<?php echo $this->get()['err_username']; ?>" /></label>
								</td>

							</tr>

							<tr>
								
								<td valign="top">
									<h3>Emails</h3>
									<i>Add email address(s) you wish to ignore while registering new users</i>
								</td>
								<td>
									<strong>Emails:</strong>
									<textarea name="emails" placeholder="enter emails separated by commas" rows="4" cols="40" id="add-email" style="display:block"><?php echo implode(',', $this->get()['emails']); ?></textarea><br/>
									<div id="add-email-cont" class="add-data-cont" data-for="email"></div>
									<span class="add-data" onclick="rur_add('add-email')">+ Add</span>
									<p></p>
									<label><strong>Error message:</strong><br/>
									<input type="text" name="emails_error" placeholder="Error text" size="40" value="<?php echo $this->get()['err_email']; ?>" /></label>
								</td>

							</tr>

							<tr>
								
								<td valign="top">
									<h3>Email service providers</h3>
									<i>Add email service providers you wish to ignore when registering, example <code>outlook.com</code> to ignore all registrations using an email address ending in <code>@outlook.com</code></i>
								</td>
								<td>
									<strong>Email service providers domains:</strong>
									<textarea name="services" placeholder="enter services separated by commas" rows="4" cols="40" id="add-service" style="display:block"><?php echo implode(',', $this->get()['services']); ?></textarea><br/>
									<div id="add-service-cont" class="add-data-cont" data-for="service"></div>
									<span class="add-data" onclick="rur_add('add-service')">+ Add</span>
									<p></p>
									<label><strong>Error message:</strong><br/>
									<input type="text" name="services_error" placeholder="Error text" size="40" value="<?php echo $this->get()['err_service']; ?>" /></label>
								</td>

							</tr>

							<tr>
								<td><?php submit_button(); ?></td>
							</tr>

						</table>

					</form>

				</div>

				<?php require 'sidebar.php'; ?>

			</div>

		<?php

	}

	public function update() {

		if( isset( $_POST['submit'] ) ) {

			$usernames = isset( $_POST['usernames'] ) ? explode( ',', $_POST['usernames'] ) : array();
			$usernames = array_filter( array_unique( $usernames ) );

			$emails = isset( $_POST['emails'] ) ? explode( ',', $_POST['emails'] ) : array();
			$emails = array_filter( array_unique( $emails ) );

			$services = isset( $_POST['services'] ) ? explode( ',', $_POST['services'] ) : array();
			$services = array_filter( array_unique( $services ) );

			$err_username = isset( $_POST['usernames_error'] ) ? str_replace( '"', '\'', sanitize_text_field( $_POST['usernames_error'] ) ) : '';
			$err_email = isset( $_POST['emails_error'] ) ? str_replace( '"', '\'', sanitize_text_field( $_POST['emails_error'] ) ) : '';
			$err_service = isset( $_POST['services_error'] ) ? str_replace( '"', '\'', sanitize_text_field( $_POST['services_error'] ) ) : '';

			$object = '{ "usernames": "' . implode(',', $usernames) . '", "emails": "' . implode(',', $emails) . '", "services": "' . implode(',', $services) . '", "err_username": "' . $err_username . '", "err_email": "' . $err_email . '", "err_service": "' . $err_service . '" }';

			update_option( 'rur_settings', $object );

			echo '<div id="updated" class="updated notice is-dismissible"><p>Changes saved successfully.</p></div>';

		}

	}

	public function get() {

		$object = strlen( get_option('rur_settings') ) > 10 ? get_option('rur_settings') : '{ "usernames": "", "emails": "", "services": "", "err_username": "", "err_email": "", "err_service": "" }';
		$object = json_decode( stripslashes( $object ), false );

		$return = array();

		$return['usernames'] = explode( ',', $object->usernames );
		$return['emails'] = explode( ',', $object->emails );
		$return['services'] = explode( ',', $object->services );

		$return['err_username'] = $object->err_username !== '' ? $object->err_username : 'Sorry, you can\'t use this username.';
		$return['err_email'] = $object->err_email !== '' ? $object->err_email : 'Sorry, you can\'t use this email.';
		$return['err_service'] = $object->err_service !== '' ? $object->err_service : 'Sorry, you can\'t use this emails.';

		return $return;

	}

	public function bail() {

		add_filter( 'registration_errors', function( $errors, $user_login, $user_email ) {

		    if ( in_array( $user_login, $this->get()['usernames'] ) ) {
		        
		        $errors->add( 'myexception_code', $this->get()['err_username'] );
		    
		    }

		    if ( in_array( $user_email, $this->get()['emails'] ) ) {
		        
		        $errors->add( 'myexception_code', $this->get()['err_email'] );
		    
		    }

		    if ( in_array( substr( $user_email, strpos( $user_email, '@' ) + 1 ), $this->get()['services'] ) ) {
		        
		        $errors->add( 'myexception_code', $this->get()['err_service'] );
		    
		    }

		    return $errors;

		}, 10, 3 );

	}

	

}

RUR::instance();