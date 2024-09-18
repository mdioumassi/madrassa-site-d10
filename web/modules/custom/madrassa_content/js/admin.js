function getAge(val) {
    let today = new Date();
    let birthDate = new Date(val);
    let age = today.getFullYear() - birthDate.getFullYear();
    let m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  }
  
  (function ($, Drupal) {
    Drupal.behaviors.admin = {
      attach: function (contest, settings) {
        $("#edit-field-birthday-0-value-date").change(function () {
    
          if ($(this).val()) {
            $("#edit-field-old-0-value").val(getAge($(this).val()));
          }
        }).trigger("change");
        $("#edit-field-old-0-value").attr('readonly', true);
      }
    }
  })(jQuery, Drupal);
  