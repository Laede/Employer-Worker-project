$(document).ready(function(){
    var date = new Date();
    date.setDate(date.getDate()-1);
    $('.js-datepicker').datepicker({
        startDate: date,
        format: 'yyyy-mm-dd'
    });
});