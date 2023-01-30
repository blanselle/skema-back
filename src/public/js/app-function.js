/**
 * Function to display message in modal
 * Need to include template partial/_message_modal.html.twig
 *
 * @param message
 */
let displayMessage = function(message, type = 'info') {
    $('#message-modal-content').html(message);
    if (type == 'error') {
        $('#message-modal-content').addClass('alert-danger');
    }
    if (type == 'warning') {
        $('#message-modal-content').addClass('alert-warning');
    }
    if (type == 'success') {
        $('#message-modal-content').addClass('alert-success');
    }
    $('#message-modal').modal('show');
}
