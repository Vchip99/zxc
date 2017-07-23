
 $(document).ready(function() {              
  $('i.fa-thumbs-o-up, i.fa-thumbs-o-down').click(function(){    
    var $this = $(this),
    c = $this.data('count');    
    if (!c) c = 0;
    c++;
    $this.data('count',c);
    $('#'+this.id+'-bs3').html(c);
  });      
  $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
  });                                        
}); 