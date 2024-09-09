<?php
/*
Plugin Name: RUL Teams
Description: Plugin to manage team members using a custom database table.
Version: 1.0
Author: Md Mustafa Kamal Hossain
*/

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-rul-teams.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-rul-teams-admin.php';

function rul_teams_init() {
    $rul_teams = new RUL_Teams();
    $rul_teams->install();
}

register_activation_hook(__FILE__, 'rul_teams_init');


function rul_teams_admin_init() {
    new RUL_Teams_Admin();
}
add_action('plugins_loaded', 'rul_teams_admin_init');


add_action('wp_ajax_rul_delete_member', 'rul_delete_member');

function rul_delete_member() {
    global $wpdb;
    $member_id = intval($_POST['id']);
    $table_name = $wpdb->prefix . 'rul_teams';

    $wpdb->delete($table_name, ['id' => $member_id]);

    wp_send_json_success('Member deleted');
}
