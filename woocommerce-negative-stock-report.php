<?php
/*----------------------------------------------------------------------------------------------------------------------
Plugin Name: WooCommerce Negative Stock Report
Description: A plugin to add basic negative stock reporting.
Version: 1.0.0
Author: New Order Studios
Author URI: http://neworderstudios.com/
----------------------------------------------------------------------------------------------------------------------*/

load_plugin_textdomain( 'woocommerce-negative-stock-report', false, basename( dirname(__FILE__) ) . '/i18n' );

function warn_no_woocommerce() {
    ?>
    <div class="warning">
        <p><?php _e( 'WooCommerce stock report class could not be loaded. Is WooCommerce installed?', 'woocommerce-negative-stock-report' ); ?></p>
    </div>
    <?php
}

if ( ! class_exists( 'WC_Report_Stock' ) ) {

	if ( file_exists(WP_PLUGIN_DIR . '/woocommerce/includes/admin/reports/class-wc-report-stock.php') ) {
		require_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/reports/class-wc-report-stock.php' );
	} else {
		add_action( 'admin_notices', 'warn_no_woocommerce' );
	}

}

if ( class_exists( 'WC_Report_Stock' ) ) {

	class wcNegativeStockRept extends WC_Report_Stock {

		public function __construct() {
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
		 */
		public function get_report( $name ) {
			$this->output_report();
		}

		/**
		 * Let's get our negative stock products.
		 */		
		public function get_items( $current_page, $per_page ) {
			parent::__construct();			
			global $wpdb;

			$this->max_items = 0;
			$this->items     = array();

			// Get products using a query - this is too advanced for get_posts :(
			$stock = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );

			$query_from = "FROM {$wpdb->posts} as posts
				INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
				INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
				WHERE 1=1
				AND posts.post_type IN ( 'product', 'product_variation' )
				AND posts.post_status = 'publish'
				AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
				AND postmeta.meta_key = '_stock' AND postmeta.meta_value < 0
			";

			$this->items     = $wpdb->get_results( $wpdb->prepare( "SELECT posts.ID as id, posts.post_parent as parent {$query_from} GROUP BY posts.ID ORDER BY posts.post_title DESC LIMIT %d, %d;", ( $current_page - 1 ) * $per_page, $per_page ) );
			$this->max_items = $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" );
		}

	}

	if ( is_admin() ) {
	    new wcNegativeStockRept();
	}

}
