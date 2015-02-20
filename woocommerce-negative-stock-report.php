<?php
/*----------------------------------------------------------------------------------------------------------------------
Plugin Name: WooCommerce Negative Stock Report
Description: A plugin to add basic negative stock reporting.
Version: 1.0.0
Author: New Order Studios
Author URI: http://neworderstudios.com/
----------------------------------------------------------------------------------------------------------------------*/

if ( is_admin() ) {
    new wcNegativeStockRept();
}

class wcNegativeStockRept {

	public function __construct() {
		load_plugin_textdomain( 'woocommerce-negative-stock-report', false, basename( dirname(__FILE__) ) . '/i18n' );
		add_filter( 'woocommerce_admin_reports', array( $this, 'add_report_link' ) );
	}

	/**
	 * Let's add a link to our report.
	 */
	public function add_report_link( $reports ) {
		$reports['stock']['reports']['negative_stock'] = array(
				'title'       => __( 'Negative Stock', 'woocommerce-negative-stock-report' ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $this, 'get_report' )
			);
		return $reports;
	}

	/**
	 * Let's render our report.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function get_report( $post ) {
		

		die();
	}
}
