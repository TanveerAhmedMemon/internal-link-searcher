
<?php
/*
Plugin Name: Internal Link Searcher
Description: A simple plugin to search for potential internal linking keywords.
Version: 1.0
Author: Your Name
*/

function search_internal_linking_keywords() {
    global $wpdb;

    // Get all published posts
    $posts = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post'");

    $potential_links = [];

    foreach ($posts as $post) {
        $keyword = $post->post_title;

        // Search the keyword in all posts' content
        $results = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_content LIKE %s AND ID != %d AND post_status = 'publish' AND post_type = 'post'", "%{$keyword}%", $post->ID));

        if ($results) {
            foreach ($results as $result) {
                $potential_links[] = [
                    'keyword' => $keyword,
                    'post_title' => $result->post_title,
                    'post_id' => $result->ID
                ];
            }
        }
    }

    return $potential_links;
}

function display_internal_linking_keywords() {
    $potential_links = search_internal_linking_keywords();
    
    echo '<h2>Potential Internal Linking Keywords</h2>';
    echo '<table>';
    echo '<tr><th>Keyword</th><th>Potential Link</th></tr>';

    foreach ($potential_links as $link) {
        echo '<tr>';
        echo '<td>' . esc_html($link['keyword']) . '</td>';
        echo '<td><a href="' . get_edit_post_link($link['post_id']) . '">' . esc_html($link['post_title']) . '</a></td>';
        echo '</tr>';
    }

    echo '</table>';
}

function add_internal_link_searcher_menu() {
    add_menu_page('Internal Link Searcher', 'Internal Link Searcher', 'manage_options', 'internal_link_searcher', 'display_internal_linking_keywords');
}
add_action('admin_menu', 'add_internal_link_searcher_menu');
