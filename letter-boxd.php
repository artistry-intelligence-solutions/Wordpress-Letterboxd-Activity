<?php
/**
 * Plugin Name: Activity Letterboxd 
 * Description: Optimized by Artistry Intelligence Solutions (Pty) Ltd. A lightweight widget which uses the Letterboxd RSS feed to display your Letterboxd activity.
 * Version: 1.3
 * Author: Johannes Schröter
 * Author URI: https://github.com/artistry-intelligence-solutions/Wordpress-Letterboxd-Activity
 */

class Activity_Letterboxd_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'activity_letterboxd_widget',
            __('Letterboxd Activity', 'text_domain'),
            array('description' => __('A widget to display your latest Letterboxd activity', 'text_domain'),)
        );
    }

    public function widget($args, $instance) {
        extract($args);

        $title = apply_filters('widget_title', $instance['title']);
        $letterboxd_name = $instance['letterboxd_name'];
        $max_items = $instance['max_items'];

        echo $before_widget;

        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }

        $feed_url = "https://letterboxd.com/{$letterboxd_name}/rss/";

        $rss = fetch_feed($feed_url);

        if (!is_wp_error($rss)) {
            $maxitems = $rss->get_item_quantity($max_items);
            $rss_items = $rss->get_items(0, $maxitems);

            foreach ($rss_items as $item) {
                echo '<a href="' . esc_url($item->get_permalink()) . '">';
                echo esc_html($item->get_title());
                echo '</a><br>';
            }
        } else {
            echo 'Unable to fetch Letterboxd activity.';
        }

        echo $after_widget;
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $letterboxd_name = isset($instance['letterboxd_name']) ? $instance['letterboxd_name'] : '';
        $max_items = isset($instance['max_items']) ? $instance['max_items'] : '';

        echo '<p>';
        echo '<label for="' . $this->get_field_id('title') . '">Title:</label>';
        echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '">';
        echo '</p>';

        echo '<p>';
        echo '<label for="' . $this->get_field_id('letterboxd_name') . '">Letterboxd Username:</label>';
        echo '<input class="widefat" id="' . $this->get_field_id('letterboxd_name') . '" name="' . $this->get_field_name('letterboxd_name') . '" type="text" value="' . esc_attr($letterboxd_name) . '">';
        echo '</p>';

        echo '<p>';
        echo '<label for="' . $this->get_field_id('max_items') . '">Max Items:</label>';
        echo '<input class="widefat" id="' . $this->get_field_id('max_items') . '" name="' . $this->get_field_name('max_items') . '" type="number" value="' . esc_attr($max_items) . '">';
        echo '</p>';
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['letterboxd_name'] = (!empty($new_instance['letterboxd_name'])) ? strip_tags($new_instance['letterboxd_name']) : '';
        $instance['max_items'] = (!empty($new_instance['max_items'])) ? absint($new_instance['max_items']) : 0;

        return $instance;
    }
}

function activity_letterboxd_init() {
    register_widget('Activity_Letterboxd_Widget');
}

add_action('widgets_init', 'activity_letterboxd_init');
