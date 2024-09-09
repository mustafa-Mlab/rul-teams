jQuery(document).ready(function($) {
    $('.delete-member').on('click', function() {
        var memberId = $(this).data('id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'rul_delete_member',
                id: memberId
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    alert('Member deleted successfully.');
                    // location.reload();
                }
            }
        });
    });
});

jQuery(document).ready(function($) {
    // Attach click event handler to the delete button
    $('.ajax-delete').on('click', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this member?')) {
            var memberId = $(this).data('id');
            var nonce = $(this).data('nonce');

            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: {
                    action: 'rul_delete_team_member', // The action name
                    member_id: memberId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Team member deleted successfully!');
                        location.reload(); // Refresh the page after deletion
                    } else {
                        alert('Failed to delete team member.');
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the member.');
                }
            });
        }
    });
});
