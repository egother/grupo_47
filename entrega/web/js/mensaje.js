/* Funcion para mostrar un mensaje de notificaci{on */

var updateTimer = 0;
  
  function setupMessageBox(){
    showMessage(); //Mostrar cuando la página se cargue
    jQuery(window).scroll(function() {
       showMessage();
    });    
    clearTimeout(updateTimer);
    activateTimer();
  };
 
  function activateTimer() {
    updateTimer = setTimeout('jQuery("#message_box").animate({ top:"-=15px",opacity:0 }, "slow")', 3000); 
  }
 
  function showMessage(){
      jQuery('#message_box').animate({top:jQuery(window).scrollTop() + "px" }, {queue: false,duration:350});
  }
