<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$templateberg_meta_keys = array(
	'templateberg-connect',
	'templateberg-payments',
	'templateberg-purchase-templates',
	'templateberg-current-theme-payments',
	'templateberg-current-theme-purchase-templates',
	'templateberg-fav-gt',
	'templateberg-gutentor-imd-tmls',
);

foreach ( $templateberg_meta_keys as $templateberg_meta_key ) {
	delete_metadata( 'user', 0, $templateberg_meta_key, '', true );
}

delete_option( '__templateberg_do_redirect' );

$templateberg_transient_prefixes = array(
	'templateberg_gt_',
	'templateberg_edt_',
	'templateberg_tt_',
	'templateberg_ct_',
	'templateberg_edt_free',
	'templateberg_tj_',
	'templateberg_gt_max',
	'templateberg_gt_total',
	'templateberg_tt_max',
	'templateberg_tt_total',
	'gutentor_get_block_json_',
);

foreach ( $templateberg_transient_prefixes as $templateberg_prefix ) {
	$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->prepare(
			"DELETE FROM $wpdb->options WHERE option_name LIKE %s",
			'_transient_' . $templateberg_prefix . '%'
		)
	);
}

$templateberg_upload_dir = wp_upload_dir();
$templateberg_dir = trailingslashit( $templateberg_upload_dir['basedir'] ) . 'templateberg';
if ( is_dir( $templateberg_dir ) ) {
	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	if ( WP_Filesystem() ) {
		global $wp_filesystem;
		/** @var WP_Filesystem_Base $wp_filesystem */
		$wp_filesystem->rmdir( $templateberg_dir, true );
	}
}
