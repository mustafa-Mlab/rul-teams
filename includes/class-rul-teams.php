<?php 
class RUL_Teams {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'rul_teams';
    }

    public function install() {
        global $wpdb;
    
        $charset_collate = $wpdb->get_charset_collate();
    
        $sql = "CREATE TABLE {$this->table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            string_id varchar(255) NOT NULL,
            member_name varchar(255) NOT NULL,
            designation varchar(255) NOT NULL,
            email varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
