/** 
  * Template Name: Varsity
  * Version: 1.1  
  * Template Scripts
  * Author: MarkUps
  * Author URI: http://www.markups.io/

  Custom JS
  

  
  1. SEARCH FORM
  2. ABOUT US VIDEO
  3. TOP SLIDER
  4. ABOUT US (SLICK SLIDER) 
  5. LATEST COURSE SLIDER (SLICK SLIDER) 
  6. TESTIMONIAL SLIDER (SLICK SLIDER)
  7. COUNTER
  8. RELATED ITEM SLIDER (SLICK SLIDER)
  9. LIGHTBOX ( FOR PORTFOLIO POPUP VIEW )   
  10. HOVER DROPDOWN MENU
  11. SCROLL TOP BUTTON  



  
**/

jQuery(function($){


  
  /* ----------------------------------------------------------- */
  /*  3. TOP SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */    

    jQuery('#mu-slider').slick({
      dots: false,
      infinite: true,
      arrows: true,
      speed: 500,     
      autoplay: true,      
      cssEase: 'linear'
    });

  /* ----------------------------------------------------------- */
  /*  4. ABOUT US (SLICK SLIDER)
  /* ----------------------------------------------------------- */      

    jQuery('#customer-slide').slick({
      dots: true,
      infinite: true,
      arrows: false,
      speed: 500,
      autoplay: true,          
      cssEase: 'linear'
    });
  

  /* ----------------------------------------------------------- */
  /*  5. LATEST COURSE SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */    

    jQuery('#mu-latest-course-slide').slick({
      dots: true,
      arrows: false,
      infinite: true,
      speed: 300,
      slidesToShow: 3,
      slidesToScroll: 2,
      autoplay: true,
      autoplaySpeed: 2500,
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 3,
            infinite: true,
            dots: true
          }
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ]
    });

  /* ----------------------------------------------------------- */
  /*  6. TESTIMONIAL SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */    

    jQuery('.customer-slider').slick({
      dots: true,      
      infinite: true,
      arrows: false,
      autoplay: true,
      speed: 500,      
      cssEase: 'linear'
    });       

  

  /* ----------------------------------------------------------- */
  /*  8. RELATED ITEM SLIDER (SLICK SLIDER)
  /* ----------------------------------------------------------- */    

    jQuery('#mu-related-item-slide').slick({
      dots: false,
      arrows: true,
      infinite: true,
      speed: 300,
      slidesToShow: 2,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2500,
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            infinite: true,
            dots: false
          }
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ]
    });

});

