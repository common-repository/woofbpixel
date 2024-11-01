<?php
/**
 * Plugin Name: WooFBPixel
 * Plugin URI: https://woofbpixel.com/
 * Description: This plugin takes care of automatically placing your Facebook Pixel code on all appropriate pages of your WooCommerce store (base code + event code on cart, checkout, and myaccount pages). Best of all, it allows you to track FB custom conversions for specific products that end up in your visitors' carts through our SKU append feature.
 * Version: 1.1
 * Author: WooFBPixel
 * Author URI: https://woofbpixel.com/
 * License: GPL2
 */


global $woo_fb_pixel;
$woo_fb_pixel = new Woo_Fb_Pixel;

class Woo_Fb_Pixel {

	private $textdomain = "woo_fb_pixel";
	private $required_plugins = array();

	function have_required_plugins() {
		if ( empty( $this->required_plugins ) ) {
			return true;
		}
		$active_plugins = (array) get_option( 'active_plugins',array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins,get_site_option( 'active_sitewide_plugins',array() ) );
		}
		foreach ( $this->required_plugins as $key => $required ) {
			$required = ( ! is_numeric( $key ) ) ? "{$key}/{$required}.php" : "{$required}/{$required}.php";
			if ( ! in_array( $required,$active_plugins ) && ! array_key_exists( $required,$active_plugins ) ) {
				return false;
			}
		}

		return true;
	}


	function __construct() {

		if ( ! $this->have_required_plugins() ) {
			return;
		}
		load_plugin_textdomain( $this->textdomain,false,dirname( plugin_basename( __FILE__ ) ) . '/lang' );

        // Enqueue Scripts and Styles in the Frontend and Backend
		add_action( 'init', array( $this,'wcpxl_add_css_js_libraries' ), 20 );
		add_action( 'init', array( $this,'wcpxl_add_css_js_to_admin'), 20 );

		add_action( 'wp_head' ,   array($this, 'wcpxl_add_js_code_to_head'), 21 );
		add_action( 'wp_footer' , array($this, 'wcpxl_add_js_code_to_cart_page'), 22 );
		add_action( 'wp_footer' , array($this, 'wcpxl_add_js_code_to_checkout_page'), 23 );
		add_action( 'wp_footer' , array($this, 'wcpxl_add_js_code_to_account_page'), 24 );

		add_action( 'woocommerce_before_shop_loop_item_title', array($this, 'wcpxl_add_sku_to_product'), 30 );
		add_action( 'woocommerce_before_add_to_cart_button',   array($this, 'wcpxl_add_sku_to_product'), 30 );

		// add menu under Woocommerce
		add_action( 'admin_menu', array($this, 'wcpxl_menu_addition'), 101 );
		add_action( 'admin_init', array( $this, 'wcpxl_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'wcpxl_setup_fields' ) );

	}

	/**
	 * Function to enqueue required CSS and JS files to the frontend
	 */
	public function wcpxl_add_css_js_libraries() {

		wp_enqueue_script(
			'jquery-cookie',
			plugins_url( 'assets/js/jquery.cookie.min.js', __FILE__ ),
			array('jquery'),
			'1.4.1',
			false
		);

		if ( ! is_admin() ) {

			wp_enqueue_script(
				'wcpxl-frontend',
				plugins_url( 'assets/js/wcpxl-frontend.js', __FILE__ ),
				array( 'jquery', 'jquery-ui-core', 'jquery-cookie' ),
				time(),
				false
			);

			wp_enqueue_style(
				'wcpxl-frontend',
				plugins_url( 'assets/css/wcpxl-frontend.css', __FILE__ ),
				false
			);
		}
	}

	/**
	 * Function to enqueue required CSS and JS files to the backend
	 */
	public function wcpxl_add_css_js_to_admin() {
		if ( is_admin() ) {

            wp_enqueue_style(
                'wcpxl-backend',
                plugins_url( 'assets/css/wcpxl-backend.css', __FILE__ ),
                false
            );

		}
    }

	/**
	 * Adds a submenu page under a woocommerce parent menu.
	 */
	function wcpxl_menu_addition() {
	    $parent_page = 'woocommerce';
	    $page_title = __('WooFBPixel Settings Page', 'woo-fbpixel');
	    $menu_title = __('WooFBPixel', 'woo-fbpixel');
	    $capability = 'manage_options';
	    $slug = 'woofbpixel';
	    $callback = array($this,'wcpxl_options');
	    $position = 101;
		add_submenu_page(
			$parent_page,
			$page_title,
			$menu_title,
			$capability,
			$slug,
			$callback,
            $position
		);
	}

	/**
	 * Display main option page.
	 */
	function wcpxl_options() {
		?>

        <!--  WRAP  -->
        <div class="wrap">
            <h2><?php _e( 'WooFBPixel', 'woo-fbpixel' ); ?></h2>
            <p><?php _e( 'This is the settings menu for WooFBPixel', 'woo-fbpixel' ); ?></p>
            <div class="notice inline notice-success notice-alt">
                <p>If you want to take advantage of the PRO features you can upgrade your version from the official site <a href="https://woofbpixel.com/" target="_blank">here</a></p>
            </div>
            <!--  Start of Left Panel  -->
            <div class="leftpanel">

                <form method="post" action="options.php">

					<?php settings_fields( 'woofbpixel' ); ?>
					<?php do_settings_sections( 'woofbpixel' ); ?>

                    </div> <!-- end of license-tab content -->

                    <div class="fieldwrap">
						<?php submit_button(__('Save Changes', 'woo-fbpixel')); ?>
                    </div>

                </form>

            </div>
            <!--  End of Left Panel  -->
            <!--  Start of Right Panel   -->
            <div class="rightpanel">

                <a href="https://danhenry.clickfunnels.com/copy-of-facebook-ads-for-entrepreneurs-webinare3nqj16p?affiliate_id=664201" target="_blank">
                    <img class="alignnone wp-image-111 size-full" src="<?php echo plugins_url( 'assets/images/1.png', __FILE__ ); ?>" alt="" width="300" height="250" />
                </a>

                <hr />

                <h2><a href="https://clickfunnels.com/?cf_affiliate_id=554228&affiliate_id=554228">LEARN CLICKFUNNELS</a></h2>

                <a href="https://clickfunnels.com/?cf_affiliate_id=554228&affiliate_id=554228" target="_blank">
                    <img src="<?php echo plugins_url( 'assets/images/2.jpg', __FILE__ ); ?>" width="300" height="250" class="affIMGURL">
                </a>

                <hr />

                <h2><a href="https://webinar.funnelscripts.com/register?cf_affiliate_id=554228&affiliate_id=554228">LEARN FUNNEL SCRIPTS</h2>

                <a href="https://webinar.funnelscripts.com/register?cf_affiliate_id=554228&affiliate_id=554228" target="_blank">
                    <img src="<?php echo plugins_url( 'assets/images/3.png', __FILE__ ); ?>" width="300" height="250" class="affIMGURL">
                </a>

            </div>
            <!--  End of Right Panel  -->


        </div>
        <!--  End of WRAP  -->
		<?php
	}
	// End of Woo Pixel Options

	/**
	 * Instantiate Main Sections for Options Page
	 */
	public function wcpxl_setup_sections() {
		add_settings_section( 'wcpxl_settings_section', false , array($this, 'wcpxl_sections_callback'), 'woofbpixel' );
	}

	/**
	 * Setup Main Sections/Tabs for Options Page
	 */
	public function wcpxl_sections_callback($arguments) {
            // Settings Tab
            echo '<div class="tab-content is-active" id="settings-tab">';
    }

	/**
	 * Setup fields for Options Page
	 */
	public function wcpxl_setup_fields() {
	    $pages = get_pages();
	    $pages_array = array();
        foreach ( $pages as $page ) {
            $pages_array[get_page_link( $page->ID ) ] = $page->post_title;
        }

		$fields = array(
			array(
				'uid' => 'js_code_to_head',
				'label' => __('Base Pixel Code', 'woo-fbpixel'),
				'section' => 'wcpxl_settings_section',
				'type' => 'textarea',
				'options' => false,
				'placeholder' => 'Please paste your base pixel code here',
				'helper' => 'By placing your base code above, your base pixel code will automatically be applied to every page of your website.',
				'supplemental' => '',
				'default' => ''
			),
			array(
				'uid' => 'js_code_to_cart_page',
				'label' => __('Cart Page Event Code', 'woo-fbpixel'),
				'section' => 'wcpxl_settings_section',
				'type' => 'textarea',
				'options' => false,
				'placeholder' => 'Please paste event code here',
				'helper' => 'The event code placed above will automatically be applied to your Cart page(s).',
				'supplemental' => '',
				'default' => '<script>fbq(\'track\', \'AddToCart\');</script>'
			),
			array(
				'uid' => 'js_code_to_checkout_page',
				'label' => __('Checkout Page Event Code', 'woo-fbpixel'),
				'section' => 'wcpxl_settings_section',
				'type' => 'textarea',
				'options' => false,
				'placeholder' => 'Please paste event code here',
				'helper' => 'The event code placed above will automatically be applied to your Checkout page(s).',
				'supplemental' => '',
				'default' => '<script>fbq(\'track\', \'InitiateCheckout\');</script>'
			),
			array(
				'uid' => 'js_code_to_myaccount_page',
				'label' => __('Account Page Event Code', 'woo-fbpixel'),
				'section' => 'wcpxl_settings_section',
				'type' => 'textarea',
				'options' => false,
				'placeholder' => 'Please paste event code here',
				'helper' => 'The event code placed above will automatically be applied to your My Account page(s).',
				'supplemental' => '',
				'default' => '<script>fbq(\'track\', \'CompleteRegistration\');</script>'
			)
		);
		foreach( $fields as $field ) {
            add_settings_field( $field['uid'], $field['label'], array( $this, 'wcpxl_field_callback' ), 'woofbpixel', $field['section'], $field );
            register_setting( 'woofbpixel', $field['uid'] );
		}
	}

	/**
	 * Field callbacks for Options Page
	 */
	public function wcpxl_field_callback( $arguments ) {

		$value = get_option( $arguments['uid'] ); // Get the current value, if there is one
		if( ! $value ) { // If no value exists
			$value = $arguments['default']; // Set to our default
		}

		// Check which type of field we want
		switch( $arguments['type'] ){
			case 'text':
			case 'password':
			case 'number':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
				break;
			case 'textarea': // If it is a textarea
				printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
				break;
			case 'radio':
			case 'checkbox':
				if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
					$options_markup = '';
					$iterator = 0;
					foreach( $arguments['options'] as $key => $label ){
						$iterator++;
						$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
						// var_dump( $value[ array_search( $key, $value, true ) ] );
					}
					printf( '<fieldset>%s</fieldset>', $options_markup );
				}

				break;
			case 'select':
			case 'multiselect':
				if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
					$attributes = '';
					$options_markup = '';

					foreach( $arguments['options'] as $key => $label ){
						$options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
					}
					if( $arguments['type'] === 'multiselect' ){
						$attributes = ' multiple="multiple" ';
					}
					printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
				}
			    break;
		}

		// If there is help text
		if( $helper = $arguments['helper'] ){
			printf( '<span class="helper"> %s</span>', $helper ); // Show it
		}

		// If there is supplemental text
		if( $supplimental = $arguments['supplemental'] ){
			printf( '<p class="description">%s</p>', $supplimental ); // Show it
		}

	}
    // End of fields callback

	/**
	 * Function to add JS code into the Head site wide
	 */
	public function wcpxl_add_js_code_to_head() {
	    echo get_option('js_code_to_head');
	}

	/**
	 * Function to add JS code into the cart page
	 */
	public function wcpxl_add_js_code_to_cart_page() {
		if( is_cart() ) {
			echo get_option('js_code_to_cart_page');
		}
	}

	/**
	 * Function to add JS code into the checkout page
	 */
	public function wcpxl_add_js_code_to_checkout_page() {
		if( is_checkout() ) {
			echo get_option('js_code_to_checkout_page');
		}
	}

	/**
	 * Function to add JS code into the my account page
	 */
	public function wcpxl_add_js_code_to_account_page() {
		if( is_account_page() ) {
			echo get_option('js_code_to_myaccount_page');
		}
	}

	/**
	 * Function to Add Product SKU into the product section
	 */
	public function wcpxl_add_sku_to_product() {
		if ( class_exists( 'WooCommerce' ) ) {
			global $product;

			if ( $product->get_sku() ) {
				echo '<span class="fpix-sku">' . $product->get_sku() . '</span>';
			}
		}
	}

}
// End of Class
