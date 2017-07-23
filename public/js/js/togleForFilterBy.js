 $(".toggle").slideUp();
$(".trigger").click(function(){
    $(this).next(".toggle").slideToggle("slow");
  });


    
    var acc = document.getElementsByClassName("v_plus_minus_symbol");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].onclick = function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  }
}