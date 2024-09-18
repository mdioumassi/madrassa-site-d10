(function($, D){

  $(document).ready(function () {

    $("#history").click(function(){
      $("#myModal").modal("show");
    });
    $("#tuteur").click(function(){
      alert('ici');
      $("#tuteurModal").modal("show");
    });
  })

})(jQuery, Drupal);
