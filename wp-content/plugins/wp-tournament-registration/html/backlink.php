<?php

/* Provides a backlink to plugin homepage. Must only be conditional due to WordPress rules. */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function wptournreg_get_backlink( $medium = 'html' ) {
	
	return '<div class="wptournreg-backlink"><a href="https://ingram-braun.net/erga/wp-tournament-registration-wordpress-plugin/#ib_campaign=wp-tournament-registration-'.WP_TOURNREG_PLUGIN_VER.'&ib_medium=' . $medium . '&ib_source=' . $_SERVER[ 'SERVER_NAME' ] . '">' . sprintf( __( '%s by %s', 'wp-tournament-registration' ), 'WP Tournament Registration', 'Ingram Braun' ) . '</a></div>';
}

