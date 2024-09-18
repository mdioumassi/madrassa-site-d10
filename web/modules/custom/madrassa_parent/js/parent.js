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
        $("#birthdate").change(function () {
    
          if ($(this).val()) {
            $("#old").val(getAge($(this).val()));
          }
        }).trigger("change");
        $("#old").attr('readonly', true);
      }
    }
  })(jQuery, Drupal);
  