class OpenBootstrapModalExtension {
    initialize(naja) {
        naja.addEventListener('complete', this.showModal.bind(this));
    }

    showModal({detail}) {
        if (detail.payload.showModal !== undefined) {
            if (detail.payload.showModal === true) {
                $("body.modal-open").removeAttr("style");
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $(".modal").modal('show');
            } else if (detail.payload.showModal === false) {
                $("body.modal-open").removeAttr("style");
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $(".modal").modal('hide');
            }
        }
    }
}

naja.registerExtension(new OpenBootstrapModalExtension());
document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));

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

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});