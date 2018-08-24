<?php

/*

@description   The main entry point for the 'collections single' page. Used internally by the custom post type single template

@version       1.0.2
@since         1.0.49
@path          templates/collections-single.php
@partials      templates/partials/collections/single

@docs          https://wpshop.io/docs/templates/collections-single


*/

if ( !defined('ABSPATH') ) {
	exit;
}

global $post;

use WPS\Factories\Templates_Factory;
use WPS\Factories\DB_Collections_Factory;

$Templates = Templates_Factory::build();
$DB_Collections = DB_Collections_Factory::build();

$wps_products = $Templates->get_collection_products_data($post->ID);
$wps_collection = $DB_Collections->get_collection($post->ID);

get_header('wps');

do_action('wps_breadcrumbs');
do_action('wps_collection_single_before');
do_action('wps_collection_single_start', $wps_collection);
do_action('wps_collection_single_header', $wps_collection);
do_action('wps_collection_single_content', $wps_collection);
do_action('wps_collection_single_products', $wps_collection, $wps_products);
do_action('wps_collection_single_end', $wps_collection);
do_action('wps_collection_single_after');
do_action('wps_collection_single_sidebar');

get_footer('wps');
