(function($, D){

  $(document).ready(function () {
    $("#formEditParent").submit(function(e) {
      e.preventDefault();
      var url = $(this).attr('action');
      
      var formData = new FormData(this);
      $.ajax({
          url: url,
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
              location.reload();
          },
          error: function(response) {
              $.each(response.responseJSON.errors, function(key, value) {
                  alert(value);
              });
          }
      });

      // $.ajax({
      //     url: url,
      //     type: 'POST',
      //     data: formData,
      //     contentType: false,
      //     processData: false,
      //     success: function(response) {
      //         $('#modal-create-users').modal('hide');
      //         $('#form-create-user').trigger('reset');
      //         location.reload();
      //     },
      //     error: function(response) {
      //         $('#form-create-user').find(".print-error-msg").find("ul").html('');
      //         $('#form-create-user').find(".print-error-msg").css('display', 'block');
      //         $.each(response.responseJSON.errors, function(key, value) {
      //             $('#form-create-user').find(".print-error-msg").find("ul").append(
      //                 '<li>' + value + '</li>');
      //         });
      //     }
      // });
  });
  })

})(jQuery, Drupal);
