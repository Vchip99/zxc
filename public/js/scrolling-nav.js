 $(function () {
            $('.navbar-collapse ul li a:not(.dropdown-toggle)').click(function () {
                    $('.navbar-toggle:visible').click();
            });
    });

 $('.dropdown').hover(function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
}, function() {
  $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
});