jQuery(document).ready(function($) {
    // Attach click event handler to the delete button
    $('.ajax-delete').on('click', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to delete this member?')) {
            var memberId = $(this).data('id');
            var nonce = $(this).data('nonce');

            $.ajax({
                url: ajaxurl, 
                type: 'POST',
                data: {
                    action: 'rul_delete_team_member', // The action name
                    member_id: memberId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Team member deleted successfully!');
                        location.reload(); 
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

    // For Bulk action
    $('#doaction').click(function() {
        var action = $('#bulk-action-selector-top').val();
        if (action === 'delete') {
            var ids = [];
            $('input[name="bulk-delete[]"]:checked').each(function() {
                ids.push($(this).val());
            });

            if (ids.length > 0) {
                var data = {
                    action: 'rul_delete_team_member',
                    nonce: ajax_params.nonce,
                    member_ids: ids
                };

                $.post(ajaxurl, data, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data);
                    }
                });
            } else {
                alert('No items selected.');
            }
        }
    });
});
