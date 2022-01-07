jQuery(document).ready(function() {
    jQuery('.scm_datepicker').datepicker({ 
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        }
    );  
    
    jQuery('.entry-myself').submit( function(e){
        e.peventDefault();
        console.log(e);
    });
});