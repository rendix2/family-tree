
// nete forms/ajax
$(function () {
    $.nette.init();
});

// date picked
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

// tiny mce
tinyMCE.init({
    mode : "specific_textareas",
    editor_selector : "tinyMCE"
});