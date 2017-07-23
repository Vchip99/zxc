 // $(function(){
 //    var shrinkHeader = 100;
 //      $(window).scroll(function() {
 //        var scroll = getCurrentScroll();
 //          if ( scroll >= shrinkHeader ) {
 //              $('.navbar-default').addClass('shrink');
 //          }
 //          else {
 //              $('.navbar-default').removeClass('shrink');
 //          }
 //      });
 //    function getCurrentScroll() {
 //        return window.pageYOffset || document.documentElement.scrollTop;
 //      }
 //  });





// $(document).ready(function () {
//   $(".navbar-nav li a").click(function(event) {
//     $(".navbar-collapse").collapse('hide');
//   });
// });

 $(function () {
            $('.navbar-collapse ul li a:not(.dropdown-toggle)').click(function () {
                    $('.navbar-toggle:visible').click();
            });
    });