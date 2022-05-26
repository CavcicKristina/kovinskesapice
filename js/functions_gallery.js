$(document).ready(function() {
    $('.filter-button').click(function(e) {
        e.preventDefault();
        $('.all-filters').stop(true, true).slideToggle();
        //loadSCrollBar();
    });
});
