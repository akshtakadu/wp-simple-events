<?php
/**
 * Plugin Name: WP Simple Events
 * Plugin URI: https://github.com/akshtakadu/wp-simple-events
 * Description: A simple WordPress plugin to manage and display events.
 * Version: 1.0.0
 * Author: Akshata Sampat Kadu
 * Author URI: https://github.com/akshtakadu
 * License: GPLv2 or later
 * Text Domain: wp-simple-events
 */
defined('ABSPATH') || exit;
// Register Custom Post Type: Event
function wse_register_event_cpt() {

    $labels = array(
        'name'                  => 'Events',
        'singular_name'         => 'Event',
        'menu_name'             => 'Events',
        'name_admin_bar'        => 'Event',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Event',
        'new_item'              => 'New Event',
        'edit_item'             => 'Edit Event',
        'view_item'             => 'View Event',
        'all_items'             => 'All Events',
        'search_items'          => 'Search Events',
        'not_found'             => 'No events found',
        'not_found_in_trash'    => 'No events found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-calendar',
        'supports'           => array('title', 'editor'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'events'),
        'show_in_rest'       => true,
    );

    register_post_type('event', $args);
}

add_action('init', 'wse_register_event_cpt');
// Shortcode to display events
function wse_events_shortcode() {

    $query = new WP_Query(array(
        'post_type'      => 'event',
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    ));

    if (!$query->have_posts()) {
        return '<p>No events found.</p>';
    }

    ob_start();

    echo '<ul class="wse-events">';

    while ($query->have_posts()) {
        $query->the_post();

        echo '<li>';
        echo esc_html(get_the_title());
        echo '</li>';
    }

    echo '</ul>';

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('wse_events', 'wse_events_shortcode');

