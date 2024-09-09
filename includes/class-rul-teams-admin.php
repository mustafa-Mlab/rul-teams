<?php

class RUL_Teams_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu'], 10);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_rul_delete_team_member', [$this, 'ajax_delete_team_member']); 
    }

    public function add_admin_menu() {
        add_menu_page(
            'Team Members',
            'RUL Teams',
            'manage_options',
            'rul-teams',
            array($this,'display_team_page'),
            'dashicons-groups'
        );
        add_submenu_page(
            'rul-teams',
            'Add Team Member', 
            'Add Team Member', 
            'manage_options', 
            'rul-teams-add', 
            [$this, 'display_add_member_page']
        );
    }

    public function display_team_page() {
        global $wpdb;
        $table = new RUL_Teams_List_Table();
        $table->prepare_items();
    
        // Check if there's an edit action
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['team_member'])) {
            $this->display_edit_member_page();
            return; // Exit to prevent the default team page from rendering
        }
    
        // Output the heading and the "Add New" button
        echo '<div class="wrap">';
        echo '<h2 style="display: inline-block;">Team Members</h2>';
        echo '<a href="?page=rul-teams-add" class="page-title-action" style="margin-left: 10px;">Add New</a>';
        
        // Add a form for the table search box and display table
        echo '<form method="post">';
        $table->search_box('Search Members', 'search_id');
        $table->display();
        echo '</form>';
        echo '</div>';
    }
    

    public function display_add_member_page() {
        $this->display_member_page('Add Team Member', []);
    }
    
    public function display_edit_member_page() {
        global $wpdb;
        $member_id = isset($_GET['team_member']) ? intval($_GET['team_member']) : 0;
    
        if ($member_id) {
            $table_name = $wpdb->prefix . 'rul_teams';
            $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $member_id), ARRAY_A);
            
            if ($member) {
                $this->display_member_page('Edit Team Member', $member);
            } else {
                echo '<div id="message" class="error notice is-dismissible"><p>Team member not found.</p></div>';
            }
        } else {
            echo '<div id="message" class="error notice is-dismissible"><p>Invalid team member ID.</p></div>';
        }
    }
    

    private function display_member_page($title, $member) {
        ob_start();
    
        // Check if user has permission to manage options
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $wpdb;
            $table_name = $wpdb->prefix . 'rul_teams';
    
            $data = array(
                'member_name' => sanitize_text_field($_POST['name']),
                'designation' => sanitize_text_field($_POST['designation']),
                'string_id' => sanitize_text_field($_POST['string_id']),
                'email' => sanitize_email($_POST['email']),
            );
            $member['member_name'] = $data['member_name'];
            $member['designation'] = $data['designation'];
            $member['string_id'] = $data['string_id'];
            $member['email'] = $data['email'];
            $where = array('id' => isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0);
    
            if (!empty($_POST['edit_id'])) {
                $wpdb->update($table_name, $data, $where, array('%s', '%s', '%s', '%s'), array('%d'));
                echo '<div id="message" class="updated notice is-dismissible"><p>Team member updated successfully.</p></div>';
            } else {
                $wpdb->insert($table_name, $data, array('%s', '%s', '%s', '%s'));
                echo '<div id="message" class="updated notice is-dismissible"><p>Team member added successfully.</p></div>';
                unset($member);
            }
        }
    
        ?>
        <div class="wrap">
            <h2><?php echo esc_html($title); ?></h2>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="name">Name</label></th>
                        <td><input name="name" type="text" id="name" class="regular-text" value="<?php echo esc_attr(isset($member['member_name']) ? $member['member_name'] : ''); ?>" required></td>
                    </tr>
                    <tr>
                        <th><label for="designation">Designation</label></th>
                        <td><input name="designation" type="text" id="designation" class="regular-text" value="<?php echo esc_attr(isset($member['designation']) ? $member['designation'] : ''); ?>" required></td>
                    </tr>
                    <tr>
                        <th><label for="string_id">ID</label></th>
                        <td><input name="string_id" type="text" id="string_id" class="regular-text" value="<?php echo esc_attr(isset($member['string_id']) ? $member['string_id'] : ''); ?>" required></td>
                    </tr>
                    <tr>
                        <th><label for="email">Email</label></th>
                        <td><input name="email" type="email" id="email" class="regular-text" value="<?php echo esc_attr(isset($member['email']) ? $member['email'] : ''); ?>" required></td>
                    </tr>
                </table>
                <?php 
                if (isset($member['id'])) {
                    echo '<input type="hidden" name="edit_id" value="' . esc_attr($member['id']) . '">';
                }
                submit_button($title);
                ?>
            </form>
        </div>
        <?php
        ob_end_flush();
    }
    
    

    public function enqueue_assets() {
        $screen = get_current_screen();
        
        if ($screen->id === 'toplevel_page_rul-teams' || $screen->id === 'rul-teams_page_rul-teams-add' || $screen->id === 'rul-teams_page_rul-teams-edit') {
            wp_enqueue_script(
                'rul-teams-js',
                plugin_dir_url(__FILE__) . 'assets/js/rul-teams.js',
                array('jquery'),
                null,
                true
            );
    
            // Enqueue CSS
            wp_enqueue_style(
                'rul-teams-css',
                plugin_dir_url(__FILE__) . 'assets/css/rul-teams.css'
            );
        }
    }

    public function ajax_delete_team_member() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rul_delete_team_member')) {
            wp_send_json_error('Invalid nonce');
        }
    
        // Check if user has the right capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'rul_teams'; 

        $member_id = intval($_POST['member_id']);
        $delete = $wpdb->delete($table_name, ['id' => $member_id], ['%d']);
    
        if ($delete) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to delete team member');
        }
    }
    
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class RUL_Teams_List_Table extends WP_List_Table {

    private $example_data;

    public function __construct() {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'team_member',
            'plural'   => 'team_members',
            'ajax'     => false
        ));
    }

    // Prepare the items for the table
    public function prepare_items() {
        global $wpdb;
    
        $table_name = $wpdb->prefix . 'rul_teams';
        $search = (isset($_REQUEST['s'])) ? trim($_REQUEST['s']) : '';
    
        // Get sorting parameters
        $orderby = !empty($_GET['orderby']) ? esc_sql($_GET['orderby']) : 'id'; // Default to 'id'
        $order = !empty($_GET['order']) ? esc_sql($_GET['order']) : 'asc';
    
        // Query the data from the database
        if ($search) {
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE member_name LIKE %s OR designation LIKE %s ORDER BY $orderby $order",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        } else {
            $query = "SELECT * FROM $table_name ORDER BY $orderby $order";
        }
    
        $data = $wpdb->get_results($query, ARRAY_A);
    
        $per_page     = 10;
        $current_page = $this->get_pagenum();
        $total_items  = count($data);
    
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));
    
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
    
        $this->items = $data;
    
        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());
    }
    
    
    

    // Define the columns that are going to be used in the table
    public function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'member_name'  => __('Name', 'rul-teams'),
            'designation'  => __('Designation', 'rul-teams'),
            'string_id'    => __('ID', 'rul-teams'),
            'email'        => __('Email', 'rul-teams')
        );
        return $columns;
    }

    // Prepare bulk actions
    public function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    // Define the sortable columns
    protected function get_sortable_columns() {
        $sortable_columns = array(
            'member_name'  => array('member_name', false), 
            // 'designation'  => array('designation', false)
        );
        return $sortable_columns;
    }
    

    // Handle checkbox column
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    public function column_member_name($item) {
        $delete_nonce = wp_create_nonce('rul_delete_team_member');
    
        $title = '<strong>' . $item['member_name'] . '</strong>';
    
        $actions = array(
            'edit'   => sprintf('<a href="?page=rul-teams&action=edit&team_member=%s">Edit</a>', absint($item['id'])),
            'delete' => sprintf('<a href="javascript:void(0);" class="ajax-delete" data-id="%s" data-nonce="%s">Delete</a>', absint($item['id']), $delete_nonce)
        );
    
        return sprintf('%1$s %2$s', $title, $this->row_actions($actions));
    }
    

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'string_id':
            case 'member_name':
            case 'designation':
            case 'email':
                return $item[$column_name];
            default:
                return print_r($item, true); // Show the whole array for troubleshooting purposes
        }
    }


    // Handle bulk actions
    public function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            if (isset($_POST['bulk-delete'])) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'rul_teams';

                foreach ($_POST['bulk-delete'] as $id) {
                    $wpdb->delete($table_name, ['id' => $id], ['%d']);
                }
            }
        }
    }

    public function search_box($text, $input_id) {
        if (empty($_REQUEST['s']) && !$this->has_items()) {
            return;
        }

        $input_id = $input_id . '-search-input';

        echo '<p class="search-box">';
        echo '<label class="screen-reader-text" for="' . $input_id . '">' . $text . ':</label>';
        echo '<input type="search" id="' . $input_id . '" name="s" value="' . _admin_search_query() . '" />';
        submit_button($text, '', '', false, array('id' => 'search-submit'));
        echo '</p>';
    }
}
