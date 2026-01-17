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

       $event_date = get_post_meta(get_the_ID(), '_wse_event_date', true);

echo '<li>';
echo '<strong>' . esc_html(get_the_title()) . '</strong>';

if (!empty($event_date)) {
    echo ' - <span>' . esc_html($event_date) . '</span>';
}

echo '</li>';

    }

    echo '</ul>';

    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('wse_events', 'wse_events_shortcode');
// Add Event Date meta box
function wse_add_event_date_metabox() {
    add_meta_box(
        'wse_event_date',
        'Event Date',
        'wse_event_date_metabox_callback',
        'event',
        'side'
    );
}

add_action('add_meta_boxes', 'wse_add_event_date_metabox');
function wse_event_date_metabox_callback($post) {

    wp_nonce_field('wse_save_event_date', 'wse_event_date_nonce');

    $event_date = get_post_meta($post->ID, '_wse_event_date', true);
    ?>
    <label for="wse_event_date">Event Date:</label>
    <input
        type="date"
        id="wse_event_date"
        name="wse_event_date"
        value="<?php echo esc_attr($event_date); ?>"
    />
    <?php
}
function wse_save_event_date($post_id) {

    if (!isset($_POST['wse_event_date_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['wse_event_date_nonce'], 'wse_save_event_date')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['wse_event_date'])) {
        update_post_meta(
            $post_id,
            '_wse_event_date',
            sanitize_text_field($_POST['wse_event_date'])
        );
    }
}

add_action('save_post', 'wse_save_event_date');

