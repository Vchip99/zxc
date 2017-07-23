 $(function(){
    $('.show').on('click',function(){
      id = $(this).data('show')        ;
        $('[id ^=corse-detail-'+id).slideToggle('slow');
    });
    $('.close').on('click',function(){
    	id = $(this).data('close')
        $('[id ^=corse-detail-'+id).slideToggle('slow');
    });
});