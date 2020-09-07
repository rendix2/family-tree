$(function () {
    $.nette.init();
});

$(document).ready(function()
{
    $('input.datepicker').datepicker(
        {
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.yy',  // mm/dd/yy
            yearRange: '1600:2020'
        });
});