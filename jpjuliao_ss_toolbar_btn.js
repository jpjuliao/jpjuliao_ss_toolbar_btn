/**
 * Handles click event for the toolbar button and triggers AJAX request.
 * @param {jQuery} $ - jQuery reference.
 */
jQuery(document).ready(function($) {
  /**
   * Click event handler for the toolbar button.
   * @param {Event} e - Click event object.
   */
  $('#wp-admin-bar-jpjuliao_ss_toolbar_btn a').on('click', function(e) {
      e.preventDefault();

      // Initiate AJAX request
      $.ajax({
          url: jpjuliao_ajax_object.ajaxurl,
          type: 'POST',
          data: {
              action: 'jpjuliao_ss_toolbar_btn'
          },
          /**
           * Success callback function.
           * @param {Object} response - Response object.
           */
          success: function(response) {
              alert(response.data);
          },
          /**
           * Error callback function.
           * @param {jqXHR} xhr - XMLHttpRequest object.
           * @param {string} status - Error status.
           * @param {string} error - Error message.
           */
          error: function(xhr, status, error) {
              alert('Error: ' + xhr.responseText);
          }
      });
  });
});
