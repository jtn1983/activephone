jQuery(document).ready(function($){
    // date picker from jquery

    let from = $('input[name="dateFrom"]');
    let to = $('input[name="dateTo"]');

    $('input[name="dateFrom"], input[name="dateTo"]').datepicker({dateFormat : "dd-mm-yy"});
    from.on('change', function(){
        to.datepicker('option', 'minDate', from.val());
    });
    to.on('change', function(){
        from.datepicker('option', 'maxDate', to.val());
    });

})
