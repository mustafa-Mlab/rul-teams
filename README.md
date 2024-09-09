# RUL Teams

**RUL Teams** is a WordPress plugin for managing team members in a custom database table. It allows you to add, edit, and delete team members and provides a user-friendly interface in the WordPress admin panel.

## Features

- Add new team members
- Edit existing team members
- Delete team members
- Bulk delete team members
- Search functionality
- Customizable fields for team member details

## Installation

1. **Download and Install**:
   - Download the plugin from the GitHub repository.
   - Upload the `rul-teams` folder to the `/wp-content/plugins/` directory on your WordPress site.

2. **Activate the Plugin**:
   - Go to the WordPress admin dashboard.
   - Navigate to `Plugins` > `Installed Plugins`.
   - Find `RUL Teams` in the list and click `Activate`.

## Usage

1. **Accessing the Plugin**:
   - In the WordPress admin dashboard, you will find a new menu item labeled `RUL Teams`.

2. **Managing Team Members**:
   - Click on `RUL Teams` to view the list of team members.
   - Use the `Add New` button to add a new team member.
   - Click on the `Edit` link next to a team member's name to modify their details.
   - Use the `Delete` link to remove a single team member or select multiple team members and use the bulk delete action.

3. **Adding and Editing Members**:
   - When adding or editing a team member, provide the following details:
     - **Name**: The name of the team member.
     - **Designation**: The designation or role of the team member.
     - **ID**: A string id for the team member.
     - **Email**: The team member’s email address.

## Developer Information

### Plugin Structure

- **`rul-teams.php`**: Main plugin file that initializes the plugin and handles admin menus and AJAX actions.
- **`assets/js/rul-teams.js`**: JavaScript file for handling AJAX requests and form interactions.
- **`assets/css/rul-teams.css`**: CSS file for styling the plugin's admin pages.

### Custom Database Table

The plugin uses a custom database table named `rul_teams` with the following columns:
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `member_name` (VARCHAR)
- `designation` (VARCHAR)
- `string_id` (VARCHAR)
- `email` (VARCHAR)

### AJAX Actions

- **`wp_ajax_rul_delete_team_member`**: Handles AJAX requests for deleting individual team members.

## Contributing

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes.
4. Submit a pull request with a clear description of your changes.

## License

This plugin is licensed under the [GPL-3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) license.

## Support

For support, please open an issue on the [GitHub repository](https://github.com/mustafa-Mlab/rul-teams/issues).

---

Thank you for using **RUL Teams**! We hope you find it useful for managing your team members in WordPress.
