<?php

GFForms::include_feed_addon_framework();

/**
 * Gravity Forms Advision_4Dem Add-On.
 *
 * @since     1.0
 * @package   GravityForms
 * @author    4marketing.it
 * @copyright Copyright (c) 2017, 4marketing.it
 */
class GFAdvision_4Dem extends GFFeedAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the 4Dem Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_version Contains the version, defined from advision-4dem.php
	 */
	protected $_version = GF_ADVISION_4DEM_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '1.9.12';

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravityformsadvision-4dem';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravityformsadvision-4dem/advision-4dem.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'http://www.gravityforms.com';

	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
	protected $_title = 'Gravity Forms 4Dem Add-On';

	/**
	 * Defines the short title of the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = '4Dem Newsletter';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;

	/**
	 * Defines the capabilities needed for the Advision_4Dem Add-On
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_advision_4dem', 'gravityforms_advision_4dem_uninstall' );

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_advision_4dem';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_advision_4dem';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_advision_4dem_uninstall';

	/**
	 * Defines the Advision_4Dem list field tag name.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $merge_var_name The Advision_4Dem list field tag name; used by gform_advision_4dem_field_value.
	 */
	protected $merge_var_name = '';

	/**
	 * Contains an instance of the 4Dem API library, if available.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    object $api If available, contains an instance of the 4Dem API library.
	 */
	private $api = null;

	/**
	 * Get an instance of this class.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return GFAdvision_4Dem
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;

	}

	/**
	 * Autoload the required libraries.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFAddOn::is_gravityforms_supported()
	 */
	public function pre_init() {

		parent::pre_init();

		if ( $this->is_gravityforms_supported() ) {

			// Load the Mailgun API library.
			if ( ! class_exists( 'GF_Advision_4Dem_API' ) ) {
				require_once( 'includes/class-gf-advision-4dem-api.php' );
			}

		}

	}

	/**
	 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFFeedAddOn::add_delayed_payment_support()
	 */
	public function init() {

		parent::init();

		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Subscribe user to 4Dem only when payment is received.', 'gravityformsadvision-4dem' ),
			)
		);

	}

	/**
	 * Remove unneeded settings.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function uninstall() {

		parent::uninstall();

		GFCache::delete( 'advision_4dem_plugin_settings' );
		delete_option( 'gf_advision_4dem_settings' );
		delete_option( 'gf_advision_4dem_version' );

	}

	/**
	 * Register needed styles.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array(
			array(
				'handle'  => $this->_slug . '_form_settings',
				'src'     => $this->get_base_url() . '/css/form_settings.css',
				'version' => $this->_version,
				'enqueue' => array( 'admin_page' => array( 'form_settings' ) ),
			),
		);

		return array_merge( parent::styles(), $styles );

	}

	// # PLUGIN SETTINGS -----------------------------------------------------------------------------------------------

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {

		return array(
			array(
				'description' => '<p>' .
					sprintf(
						esc_html__( '4Dem makes it easy to send email newsletters to your customers, manage your subscriber lists, and track campaign performance. Use Gravity Forms to collect customer information and automatically add it to your 4Dem subscriber list. If you don\'t have a 4Dem account, you can %1$ssign up for one here.%2$s', 'gravityformsadvision-4dem' ),
						'<a href="http://www.4dem.it/" target="_blank">', '</a>'
					)
					. '</p>',
				'fields'      => array(
					array(
						'name'              => 'apiKey',
						'label'             => esc_html__( '4Dem APIkey', 'gravityformsadvision-4dem' ),
						'type'              => 'text',
						'class'             => 'medium',
						'feedback_callback' => array( $this, 'initialize_api' ),
					),
				),
			),
		);

	}





	// # FEED SETTINGS -------------------------------------------------------------------------------------------------


	public function field_map_title() {
		return esc_html__( '4Dem Custom Field', 'gravityformsadvision-4dem' );
	}

	/**
	 * Configures the settings which should be rendered on the feed edit page.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function feed_settings_fields() {

		return array(
			array(
				'title'  => esc_html__( '4Dem Feed Settings', 'gravityformsadvision-4dem' ),
				'fields' => array(
					array(
						'name'     => 'feedName',
						'label'    => esc_html__( 'Name', 'gravityformsadvision-4dem' ),
						'type'     => 'text',
						'required' => true,
						'class'    => 'medium',
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Name', 'gravityformsadvision-4dem' ),
							esc_html__( 'Enter a feed name to uniquely identify this setup.', 'gravityformsadvision-4dem' )
						),
					),
					array(
						'name'     => 'advision4demList',
						'label'    => esc_html__( '4Dem List', 'gravityformsadvision-4dem' ),
						'type'     => 'advision_4dem_list',
						'required' => true,
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( '4Dem List', 'gravityformsadvision-4dem' ),
							esc_html__( 'Select the 4Dem list you would like to add your contacts to.', 'gravityformsadvision-4dem' )
						),
					),
				),
			),
			array(
				'dependency' => 'advision4demList',
				'fields'     => array(
					array(
						'name'      => 'mappedFields',
						'label'     => esc_html__( 'Custom Fields Mapping', 'gravityformsadvision-4dem' ),
						'type'      => 'field_map',
						'field_map' => $this->merge_vars_field_map(),
						'tooltip'   => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Custom Fields Mapping', 'gravityformsadvision-4dem' ),
							esc_html__( 'Associate your 4Dem custom fields to the appropriate Gravity Form fields by selecting the appropriate form field from the list.', 'gravityformsadvision-4dem' )
						),
					),
					array(
						'name'    => 'optinCondition',
						'label'   => esc_html__( 'Conditional Logic', 'gravityformsadvision-4dem' ),
						'type'    => 'feed_condition',
						'tooltip' => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Conditional Logic', 'gravityformsadvision-4dem' ),
							esc_html__( 'When conditional logic is enabled, form submissions will only be exported to 4Dem when the conditions are met. When disabled all form submissions will be exported.', 'gravityformsadvision-4dem' )
						),
					),
					array( 'type' => 'save' ),
				),
			),
		);

	}

	/**
	 * Define the markup for the advision_4dem_list type field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $field The field properties.
	 * @param bool  $echo  Should the setting markup be echoed. Defaults to true.
	 *
	 * @return string
	 */
	public function settings_advision_4dem_list( $field, $echo = true ) {

		// Initialize HTML string.
		$html = '';

		// If API is not initialized, return.
		if ( ! $this->initialize_api() ) {
			return $html;
		}

		// Prepare list request parameters.
		$params = array( 'start' => 0, 'limit' => 100 );

		// Filter parameters.
		$params = apply_filters( 'gform_advision_4dem_lists_params', $params );

		// Convert start parameter to 3.0.
		if ( isset( $params['start'] ) ) {
			$params['offset'] = $params['start'];
			unset( $params['start'] );
		}

		// Convert limit parameter to 3.0.
		if ( isset( $params['limit'] ) ) {
			$params['count'] = $params['limit'];
			unset( $params['limit'] );
		}

		try {

			// Log contact lists request parameters.
			$this->log_debug( __METHOD__ . '(): Retrieving contact lists; params: ' . print_r( $params, true ) );

			// Get lists.
			$lists = $this->api->getRecipients();

		} catch ( Exception $e ) {

			// Log that contact lists could not be obtained.
			$this->log_error( __METHOD__ . '(): Could not retrieve 4Dem contact lists; ' . $e->getMessage() );

			// Display error message.
			printf( esc_html__( 'Could not load 4Dem contact lists. %sError: %s', 'gravityformsadvision-4dem' ), '<br/>', $e->getMessage() );

			return;

		}

		// If no lists were found, display error message.
		if ( count($lists['data']) == 0 ) {

			// Log that no lists were found.
			$this->log_error( __METHOD__ . '(): Could not load 4Dem contact lists; no lists found.' );

			// Display error message.
			printf( esc_html__( 'Could not load 4Dem contact lists. %sError: %s', 'gravityformsadvision-4dem' ), '<br/>', esc_html__( 'No lists found.', 'gravityformsadvision-4dem' ) );

			return;

		}

		// Log number of lists retrieved.
		$this->log_debug( __METHOD__ . '(): Number of lists: ' . count( $lists['data'] ) );

		// Initialize select options.
		$options = array(
			array(
				'label' => esc_html__( 'Select a 4Dem List', 'gravityformsadvision-4dem' ),
				'value' => '',
			),
		);

		// Loop through Advision_4Dem lists.
		foreach ( $lists['data'] as $list ) {

			// Add list to select options.
			$options[] = array(
				'label' => esc_html( $list['name'] ),
				'value' => esc_attr( $list['id'] ),
			);

		}

		// Add select field properties.
		$field['type']     = 'select';
		$field['choices']  = $options;
		$field['onchange'] = 'jQuery(this).parents("form").submit();';

		// Generate select field.
		$html = $this->settings_select( $field, false );

		if ( $echo ) {
			echo $html;
		}

		return $html;

	}

	/**
	 * Return an array of Advision_4Dem list fields which can be mapped to the Form fields/entry meta.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function merge_vars_field_map() {

		// Initialize field map array.
		$field_map = array(
			'EMAIL' => array(
				'name'       => 'EMAIL',
				'label'      => esc_html__( 'Email Address', 'gravityformsadvision-4dem' ),
				'required'   => true,
				'field_type' => array( 'email', 'hidden' ),
			),
		);

		// If unable to initialize API, return field map.
		if ( ! $this->initialize_api() ) {
			return $field_map;
		}

		// Get current list ID.
		$list_id = $this->get_setting( 'advision4demList' );

		try {

			// Get merge fields.
			$custom_fields = $this->api->getRecipientCustomFields( $list_id );

		} catch ( Exception $e ) {

			// Log error.
			$this->log_error( __METHOD__ . '(): Unable to get custom fields from 4Dem list; ' . $e->getMessage() );

			return $field_map;

		}

		// If merge fields exist, add to field map.
		if ( ! empty( $custom_fields['data'] ) ) {

			// Loop through merge fields.
			foreach ( $custom_fields['data'] as $single_custom_field ) {

				// Define required field type.
				$field_type = null;

				// If this is an email merge field, set field types to "email" or "hidden".
				if ( 'EMAIL' === strtoupper( $single_custom_field['name'] ) ) {
					$field_type = array( 'email', 'hidden' );
				}

				// If this is an address merge field, set field type to "address".
				if ( 'address' === $single_custom_field['type'] ) {
					$field_type = array( 'address' );
				}

				// Add to field map.
				// var_dump($single_custom_field);die();
				$field_map[ $single_custom_field['id'] ] = array(
					'name'       		=> $single_custom_field['id'],
					'label'      		=> $single_custom_field['name'],
					'required'   		=> $single_custom_field['validation']['required'],
					'field_type' 		=> $field_type,
				);
				
			}

		}

		return $field_map;
	}

	/**
	 * Prevent feeds being listed or created if the API key isn't valid.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		return $this->initialize_api();

	}

	/**
	 * Configures which columns should be displayed on the feed list page.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function feed_list_columns() {

		return array(
			'feedName'            => esc_html__( 'Name', 'gravityformsadvision-4dem' ),
			'advision_4dem_list_name' => esc_html__( 'Advision_4Dem List', 'gravityformsadvision-4dem' ),
		);

	}

	/**
	 * Returns the value to be displayed in the Advision_4Dem List column.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $feed The feed being included in the feed list.
	 *
	 * @return string
	 */
	public function get_column_value_advision_4dem_list_name( $feed ) {

		// If unable to initialize API, return the list ID.
		if ( ! $this->initialize_api() ) {
			return rgars( $feed, 'meta/advision4demList' );
		}

		try {

			// Get list.
			$list = $this->api->getRecipientInformation( rgars( $feed, 'meta/advision4demList' ) );

			// Return list name.
			return rgar( $list, 'name' );

		} catch ( Exception $e ) {

			// Log error.
			$this->log_error( __METHOD__ . '(): Unable to get 4Dem list for feed list; ' . $e->getMessage() );

			// Return list ID.
			return rgars( $feed, 'meta/advision4demList' );

		}

	}

	/**
	 * Define which field types can be used for the group conditional logic.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFAddOn::get_current_form()
	 * @uses GFCommon::get_label()
	 * @uses GF_Field::get_entry_inputs()
	 * @uses GF_Field::get_input_type()
	 * @uses GF_Field::is_conditional_logic_supported()
	 *
	 * @return array
	 */
	public function get_conditional_logic_fields() {

		// Initialize conditional logic fields array.
		$fields = array();

		// Get the current form.
		$form = $this->get_current_form();

		// Loop through the form fields.
		foreach ( $form['fields'] as $field ) {

			// If this field does not support conditional logic, skip it.
			if ( ! $field->is_conditional_logic_supported() ) {
				continue;
			}

			// Get field inputs.
			$inputs = $field->get_entry_inputs();

			// If field has multiple inputs, add them as individual field options.
			if ( $inputs && 'checkbox' !== $field->get_input_type() ) {

				// Loop through the inputs.
				foreach ( $inputs as $input ) {

					// If this is a hidden input, skip it.
					if ( rgar( $input, 'isHidden' ) ) {
						continue;
					}

					// Add input to conditional logic fields array.
					$fields[] = array(
						'value' => $input['id'],
						'label' => GFCommon::get_label( $field, $input['id'] ),
					);

				}

			} else {

				// Add field to conditional logic fields array.
				$fields[] = array(
					'value' => $field->id,
					'label' => GFCommon::get_label( $field ),
				);

			}

		}

		return $fields;

	}

	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Process the feed, subscribe the user to the list.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $feed  The feed object to be processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form  The form object currently being processed.
	 *
	 * @return array
	 */
	public function process_feed( $feed, $entry, $form ) {

		// Log that we are processing feed.
		$this->log_debug( __METHOD__ . '(): Processing feed.' );

		// If unable to initialize API, log error and return.
		if ( ! $this->initialize_api() ) {
			$this->add_feed_error( esc_html__( 'Unable to process feed because API could not be initialized.', 'gravityformsadvision-4dem' ), $feed, $entry, $form );
			return $entry;
		}

		// Set current merge variable name.
		$this->merge_var_name = 'EMAIL';

		// Get field map values.
		$field_map = $this->get_field_map_fields( $feed, 'mappedFields' );


		// Get mapped email address.
		$email = $this->get_field_value( $form, $entry, $field_map['EMAIL'] );
		// If email address is invalid, log error and return.
		if ( GFCommon::is_invalid_or_empty_email( $email ) ) {
			$this->add_feed_error( esc_html__( 'A valid Email address must be provided.', 'gravityformsadvision-4dem' ), $feed, $entry, $form );
			return $entry;
		}

		/**
		 * Prevent empty form fields erasing values already stored in the mapped Advision_4Dem MMERGE fields
		 * when updating an existing subscriber.
		 *
		 * @param bool  $override If the merge field should be overridden.
		 * @param array $form     The form object.
		 * @param array $entry    The entry object.
		 * @param array $feed     The feed object.
		 */
		$override_empty_fields = gf_apply_filters( 'gform_advision_4dem_override_empty_fields', array( $form['id'] ), true, $form, $entry, $feed );

		// Log that empty fields will not be overridden.
		if ( ! $override_empty_fields ) {
			$this->log_debug( __METHOD__ . '(): Empty fields will not be overridden.' );
		}

		// Initialize array to store merge vars.
		$custom_fields = array();
		// Loop through field map.
		// var_dump($field_map);die();
		foreach ( $field_map as $name => $field_id ) {

			// If no field is mapped, skip it.
			if ( rgblank( $field_id ) ) {
				continue;
			}

			// If this is the email field, skip it.
			if ( strtoupper( $name ) === 'EMAIL' ) {
				continue;
			}

			// Set merge var name to current field map name.
			$this->merge_var_name = $name;

			// Get field object.
			$field = GFFormsModel::get_field( $form, $field_id );

			// Get field value.
			$field_value = $this->get_field_value( $form, $entry, $field_id );

			// If field value is empty and we are not overriding empty fields, skip it.
			if ( empty( $field_value ) && ( ! $override_empty_fields || ( is_object( $field ) && 'address' === $field->get_input_type() ) ) ) {
				continue;
			}
			// var_dump($name);
 			$custom_fields[] = array('id'=> $name, 'value' => $field_value);
		}

		// // Get member info.
		// $member = $this->api->getContactByEmail( $feed['meta']['advision4demList'], array('email_address' => $email) );
		// if($this->api->getRequestSuccessful()){
		// 	$member_found = true;
		// }else{
		// 	if($member['status'] === 404) $member_found = false;
		// }

		// Prepare subscription arguments.
		$list_id = $feed['meta']['advision4demList'];
		$subscription = array(
			'email_address'   => $email,
			'custom_fields'   => $custom_fields,
			'subscription' => array('ip' => rgar( $entry, 'ip' )),
			"update_if_duplicate" => true
		);
		// var_dump($subscription);

		// Prepare transaction type for filter.
		// $transaction = $member_found ? 'Update' : 'Subscribe';

		/**
		 * Modify the subscription object before it is executed.
		 *
		 * @deprecated 4.0 @use gform_advision_4dem_subscription
		 *
		 * @param array  $subscription Subscription arguments.
		 * @param array  $form         The form object.
		 * @param array  $entry        The entry object.
		 * @param array  $feed         The feed object.
		 * @param string $transaction  Transaction type. Defaults to Subscribe.
		 */
		// $subscription = gf_apply_filters( array( 'gform_advision_4dem_args_pre_subscribe', $form['id'] ), $subscription, $form, $entry, $feed, $transaction );
		// $subscription = gf_apply_filters( array( 'gform_advision_4dem_args_pre_subscribe', $form['id'] ), $subscription, $form, $entry, $feed );

		/**
		 * Modify the subscription object before it is executed.
		 *
		 * @since 1.0 Added existing member object as $member parameter.
		 *
		 * @param array       $subscription Subscription arguments.
		 * @param string      $list_id      Advision_4Dem list ID.
		 * @param array       $form         The form object.
		 * @param array       $entry        The entry object.
		 * @param array       $feed         The feed object.
		 * @param array|false $member       The existing member object. (False if member does not currently exist in Advision_4Dem.)
		 */
		$subscription = gf_apply_filters( array( 'gform_advision_4dem_subscription', $form['id'] ), $subscription, $list_id, $form, $entry, $feed );

		// Remove merge_fields if none are defined.
		// if ( empty( $subscription['merge_fields'] ) ) {
		// 	unset( $subscription['merge_fields'] );
		// }

		// $action = $member_found ? 'updated' : 'added';

		try {

			// Log the subscriber to be added or updated.
			// $this->log_debug( __METHOD__ . "(): Subscriber to be {$action}: " . print_r( $subscription, true ) );
			// Add or update subscriber.
			$debug_test = $this->api->subscribeContact( $list_id, $subscription );
			// Log that the subscription was added or updated.
			// $this->log_debug( __METHOD__ . "(): Subscriber successfully {$action}." );

		} catch ( Exception $e ) {

			// Log that subscription could not be added or updated.
			$this->add_feed_error( sprintf( esc_html__( 'Unable to add/update subscriber: %s', 'gravityformsadvision-4dem' ), $e->getMessage() ), $feed, $entry, $form );

			// Log field errors.
			if ( $e->hasErrors() ) {
				$this->log_error( __METHOD__ . '(): Field errors when attempting subscription: ' . print_r( $e->getErrors(), true ) );
			}

			return;

		}

	}

	/**
	 * Returns the value of the selected field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array  $form     The form object currently being processed.
	 * @param array  $entry    The entry object currently being processed.
	 * @param string $field_id The ID of the field being processed.
	 *
	 * @uses GFAddOn::get_full_name()
	 * @uses GF_Field::get_value_export()
	 * @uses GFFormsModel::get_field()
	 * @uses GFFormsModel::get_input_type()
	 * @uses GFAdvision_4Dem::get_full_address()
	 * @uses GFAdvision_4Dem::maybe_override_field_value()
	 *
	 * @return array
	 */
	public function get_field_value( $form, $entry, $field_id ) {

		// Set initial field value.
		$field_value = '';

		// Set field value based on field ID.
		switch ( strtolower( $field_id ) ) {

			// Form title.
			case 'form_title':
				$field_value = rgar( $form, 'title' );
				break;

			// Entry creation date.
			case 'date_created':

				// Get entry creation date from entry.
				$date_created = rgar( $entry, strtolower( $field_id ) );

				// If date is not populated, get current date.
				$field_value = empty( $date_created ) ? gmdate( 'Y-m-d H:i:s' ) : $date_created;
				break;

			// Entry IP and source URL.
			case 'ip':
			case 'source_url':
				$field_value = rgar( $entry, strtolower( $field_id ) );
				break;

			default:

				// Get field object.
				$field = GFFormsModel::get_field( $form, $field_id );

				if ( is_object( $field ) ) {

					// Check if field ID is integer to ensure field does not have child inputs.
					$is_integer = $field_id == intval( $field_id );

					// Get field input type.
					$input_type = GFFormsModel::get_input_type( $field );

					if ( $is_integer && 'address' === $input_type ) {

						// Get full address for field value.
						$field_value = $this->get_full_address( $entry, $field_id );

					} else if ( $is_integer && 'name' === $input_type ) {

						// Get full name for field value.
						$field_value = $this->get_full_name( $entry, $field_id );

					} else if ( $is_integer && 'checkbox' === $input_type ) {

						// Initialize selected options array.
						$selected = array();

						// Loop through checkbox inputs.
						foreach ( $field->inputs as $input ) {
							$index = (string) $input['id'];
							if ( ! rgempty( $index, $entry ) ) {
								$selected[] = $this->maybe_override_field_value( rgar( $entry, $index ), $form, $entry, $index );
							}
						}

						// Convert selected options array to comma separated string.
						$field_value = implode( ', ', $selected );

					} else if ( 'phone' === $input_type && $field->phoneFormat == 'standard' ) {

						// Get field value.
						$field_value = rgar( $entry, $field_id );

						// Reformat standard format phone to match Advision_4Dem format.
						// Format: NPA-NXX-LINE (404-555-1212) when US/CAN.
						if ( ! empty( $field_value ) && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $field_value, $matches ) ) {
							$field_value = sprintf( '%s-%s-%s', $matches[1], $matches[2], $matches[3] );
						}

					} else {

						// Use export value if method exists for field.
						if ( is_callable( array( 'GF_Field', 'get_value_export' ) ) ) {
							$field_value = $field->get_value_export( $entry, $field_id );
						} else {
							$field_value = rgar( $entry, $field_id );
						}

					}

				} else {

					// Get field value from entry.
					$field_value = rgar( $entry, $field_id );

				}

		}

		return $this->maybe_override_field_value( $field_value, $form, $entry, $field_id );

	}

	/**
	 * Use the legacy gform_advision_4dem_field_value filter instead of the framework gform_SLUG_field_value filter.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $field_value The field value.
	 * @param array  $form        The form object currently being processed.
	 * @param array  $entry       The entry object currently being processed.
	 * @param string $field_id    The ID of the field being processed.
	 *
	 * @return string
	 */
	public function maybe_override_field_value( $field_value, $form, $entry, $field_id ) {

		return gf_apply_filters( 'gform_advision_4dem_field_value', array( $form['id'], $field_id ), $field_value, $form['id'], $field_id, $entry, $this->merge_var_name );

	}

	// # HELPERS -------------------------------------------------------------------------------------------------------

	/**
	 * Initializes Advision_4Dem API if credentials are valid.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $api_key Advision_4Dem API key.
	 *
	 * @uses GFAddOn::get_plugin_setting()
	 * @uses GFAddOn::log_debug()
	 * @uses GFAddOn::log_error()
	 * @uses GF_Advision_4Dem_API::account_details()
	 *
	 * @return bool|null
	 */
	public function initialize_api( $api_key = null ) {

		// If API is alredy initialized, return true.
		if ( ! is_null( $this->api ) ) {
			return true;
		}

		// Get the API key.
		if ( rgblank( $api_key ) ) {
			$api_key = $this->get_plugin_setting( 'apiKey' );
		}

		// If the API key is blank, do not run a validation check.
		if ( rgblank( $api_key ) ) {
			return null;
		}

		// Log validation step.
		$this->log_debug( __METHOD__ . '(): Validating API Info.' );

		// Setup a new Advision_4Dem object with the API credentials.
		$adv_4dem = new GF_Advision_4Dem_API( $api_key );

		try {

			// Retrieve account information.
			$adv_4dem->userInfo();

			// Assign API library to class.
			$this->api = $adv_4dem;

			// Log that authentication test passed.
			$this->log_debug( __METHOD__ . '(): 4Dem successfully authenticated.' );

			return true;

		} catch ( Exception $e ) {

			// Log that authentication test failed.
			$this->log_error( __METHOD__ . '(): Unable to authenticate with 4Dem; '. $e->getMessage() );

			return false;

		}

	}

	/**
	 * Returns the combined value of the specified Address field.
	 * Street 2 and Country are the only inputs not required by Advision_4Dem.
	 * If other inputs are missing Advision_4Dem will not store the field value, we will pass a hyphen when an input is empty.
	 * Advision_4Dem requires the inputs be delimited by 2 spaces.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array  $entry    The entry currently being processed.
	 * @param string $field_id The ID of the field to retrieve the value for.
	 *
	 * @return array|null
	 */
	public function get_full_address( $entry, $field_id ) {

		// Initialize address array.
		$address = array(
			'addr1'   => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.1' ) ) ),
			'addr2'   => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.2' ) ) ),
			'city'    => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.3' ) ) ),
			'state'   => str_replace( '  ', ' ', trim( rgar( $entry, $field_id . '.4' ) ) ),
			'zip'     => trim( rgar( $entry, $field_id . '.5' ) ),
			'country' => trim( rgar( $entry, $field_id . '.6' ) ),
		);

		// Get address parts.
		$address_parts = array_values( $address );

		// Remove empty address parts.
		$address_parts = array_filter( $address_parts );

		// If no address parts exist, return null.
		if ( empty( $address_parts ) ) {
			return null;
		}

		// Replace country with country code.
		if ( ! empty( $address['country'] ) ) {
			$address['country'] = GF_Fields::get( 'address' )->get_country_code( $address['country'] );
		}

		return $address;

	}





	// # UPGRADES ------------------------------------------------------------------------------------------------------

	/**
	 * Checks if a previous version was installed and if the feeds need migrating to the framework structure.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $previous_version The version number of the previously installed version.
	 */
	public function upgrade( $previous_version ) {

		// If previous version is not defined, set it to the version stored in the options table.
		if ( empty( $previous_version ) ) {
			$previous_version = get_option( 'gf_advision_4dem_version' );
		}
	}

	/**
	 * Upgrade versions of Advision_4Dem Add-On before 3.0 to the Add-On Framework.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function upgrade_to_addon_framework() {

		//get old plugin settings
		$old_settings = get_option( 'gf_advision_4dem_settings' );
		//remove username and password from the old settings; these were very old legacy api settings that we do not support anymore

		if ( is_array( $old_settings ) ) {

			foreach ( $old_settings as $id => $setting ) {
				if ( $id != 'username' && $id != 'password' ) {
					if ( $id == 'apikey' ) {
						$id = 'apiKey';
					}
					$new_settings[ $id ] = $setting;
				}
			}
			$this->update_plugin_settings( $new_settings );

		}

		//get old feeds
		$old_feeds = $this->get_old_feeds();

		if ( $old_feeds ) {

			$counter = 1;
			foreach ( $old_feeds as $old_feed ) {
				$feed_name  = 'Feed ' . $counter;
				$form_id    = $old_feed['form_id'];
				$is_active  = rgar( $old_feed, 'is_active' ) ? '1' : '0';
				$field_maps = rgar( $old_feed['meta'], 'field_map' );
				$list_id    = rgar( $old_feed['meta'], 'contact_list_id' );

				$new_meta = array(
					'feedName'         => $feed_name,
					'advision4demList'    => $list_id,
					'sendWelcomeEmail' => rgar( $old_feed['meta'], 'welcome_email' ) ? '1' : '0',
				);

				//add mappings
				foreach ( $field_maps as $key => $mapping ) {
					$new_meta[ 'mappedFields_' . $key ] = $mapping;
				}
				//add conditional logic, legacy only allowed one condition
				$conditional_enabled = rgar( $old_feed['meta'], 'optin_enabled' );
				if ( $conditional_enabled ) {
					$new_meta['feed_condition_conditional_logic']        = 1;
					$new_meta['feed_condition_conditional_logic_object'] = array(
						'conditionalLogic' =>
							array(
								'actionType' => 'show',
								'logicType'  => 'all',
								'rules'      => array(
									array(
										'fieldId'  => rgar( $old_feed['meta'], 'optin_field_id' ),
										'operator' => rgar( $old_feed['meta'], 'optin_operator' ),
										'value'    => rgar( $old_feed['meta'], 'optin_value' )
									),
								)
							)
					);
				} else {
					$new_meta['feed_condition_conditional_logic'] = 0;
				}

				$this->insert_feed( $form_id, $is_active, $new_meta );
				$counter ++;

			}

			//set paypal delay setting
			$this->update_paypal_delay_settings( 'delay_advision_4dem_subscription' );
		}

		// Delete old options.
		delete_option( 'gf_advision_4dem_settings' );
		delete_option( 'gf_advision_4dem_version' );

	}

	/**
	 * Migrate the delayed payment setting for the PayPal add-on integration.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $old_delay_setting_name Old PayPal delay settings name.
	 *
	 * @uses GFAddon::log_debug()
	 * @uses GFFeedAddOn::get_feeds_by_slug()
	 * @uses GFFeedAddOn::update_feed_meta()
	 * @uses GFAdvision_4Dem::get_old_paypal_feeds()
	 * @uses wpdb::update()
	 */
	public function update_paypal_delay_settings( $old_delay_setting_name ) {

		global $wpdb;

		// Log that we are checking for delay settings for migration.
		$this->log_debug( __METHOD__ . '(): Checking to see if there are any delay settings that need to be migrated for PayPal Standard.' );

		$new_delay_setting_name = 'delay_' . $this->_slug;

		// Get paypal feeds from old table.
		$paypal_feeds_old = $this->get_old_paypal_feeds();

		// Loop through feeds and look for delay setting and create duplicate with new delay setting for the framework version of PayPal Standard
		if ( ! empty( $paypal_feeds_old ) ) {
			$this->log_debug( __METHOD__ . '(): Old feeds found for ' . $this->_slug . ' - copying over delay settings.' );
			foreach ( $paypal_feeds_old as $old_feed ) {
				$meta = $old_feed['meta'];
				if ( ! rgempty( $old_delay_setting_name, $meta ) ) {
					$meta[ $new_delay_setting_name ] = $meta[ $old_delay_setting_name ];
					//Update paypal meta to have new setting
					$meta = maybe_serialize( $meta );
					$wpdb->update( "{$wpdb->prefix}rg_paypal", array( 'meta' => $meta ), array( 'id' => $old_feed['id'] ), array( '%s' ), array( '%d' ) );
				}
			}
		}

		// Get paypal feeds from new framework table.
		$paypal_feeds = $this->get_feeds_by_slug( 'gravityformspaypal' );
		if ( ! empty( $paypal_feeds ) ) {
			$this->log_debug( __METHOD__ . '(): New feeds found for ' . $this->_slug . ' - copying over delay settings.' );
			foreach ( $paypal_feeds as $feed ) {
				$meta = $feed['meta'];
				if ( ! rgempty( $old_delay_setting_name, $meta ) ) {
					$meta[ $new_delay_setting_name ] = $meta[ $old_delay_setting_name ];
					$this->update_feed_meta( $feed['id'], $meta );
				}
			}
		}

	}

	/**
	 * Retrieve any old PayPal feeds.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFAddOn::log_debug()
	 * @uses GFAddOn::table_exists()
	 * @uses GFFormsModel::get_form_table_name()
	 * @uses wpdb::get_results()
	 *
	 * @return bool|array
	 */
	public function get_old_paypal_feeds() {

		global $wpdb;

		// Get old PayPal Add-On table name.
		$table_name = $wpdb->prefix . 'rg_paypal';

		// If the table does not exist, exit.
		if ( ! $this->table_exists( $table_name ) ) {
			return false;
		}

		$form_table_name = GFFormsModel::get_form_table_name();
		$sql             = "SELECT s.id, s.is_active, s.form_id, s.meta, f.title as form_title
				FROM {$table_name} s
				INNER JOIN {$form_table_name} f ON s.form_id = f.id";

		$this->log_debug( __METHOD__ . "(): getting old paypal feeds: {$sql}" );

		$results = $wpdb->get_results( $sql, ARRAY_A );

		$this->log_debug( __METHOD__ . "(): error?: {$wpdb->last_error}" );

		$count = count( $results );

		$this->log_debug( __METHOD__ . "(): count: {$count}" );

		for ( $i = 0; $i < $count; $i ++ ) {
			$results[ $i ]['meta'] = maybe_unserialize( $results[ $i ]['meta'] );
		}

		return $results;

	}

	/**
	 * Retrieve any old feeds which need migrating to the Feed Add-On Framework.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GFAddOn::table_exists()
	 * @uses GFFormsModel::get_form_table_name()
	 * @uses wpdb::get_results()
	 *
	 * @return bool|array
	 */
	public function get_old_feeds() {

		global $wpdb;

		// Get pre-3.0 table name.
		$table_name = $wpdb->prefix . 'rg_advision_4dem';

		// If the table does not exist, exit.
		if ( ! $this->table_exists( $table_name ) ) {
			return false;
		}

		$form_table_name = GFFormsModel::get_form_table_name();
		$sql             = "SELECT s.id, s.is_active, s.form_id, s.meta, f.title as form_title
					FROM $table_name s
					INNER JOIN $form_table_name f ON s.form_id = f.id";

		$results = $wpdb->get_results( $sql, ARRAY_A );

		$count = count( $results );
		for ( $i = 0; $i < $count; $i ++ ) {
			$results[ $i ]['meta'] = maybe_unserialize( $results[ $i ]['meta'] );
		}

		return $results;

	}

	/**
	 * Retrieve the group setting key.
	 *
	 * @param string $grouping_id The group ID.
	 * @param string $group_name The group name.
	 *
	 * @return string
	 */
	public function get_group_setting_key( $grouping_id, $group_name ) {

		$plugin_settings = GFCache::get( 'advision_4dem_plugin_settings' );
		if ( empty( $plugin_settings ) ) {
			$plugin_settings = $this->get_plugin_settings();
			GFCache::set( 'advision_4dem_plugin_settings', $plugin_settings );
		}

		$key = 'group_key_' . $grouping_id . '_' . str_replace( '%', '', sanitize_title_with_dashes( $group_name ) );

		if ( ! isset( $plugin_settings[ $key ] ) ) {
			$group_key               = sanitize_key( uniqid( 'adv_4dem_group_', true ) );
			$plugin_settings[ $key ] = $group_key;
			$this->update_plugin_settings( $plugin_settings );
			GFCache::set( 'advision_4dem_plugin_settings', $plugin_settings );
		}

		return $plugin_settings[ $key ];
	}

}
