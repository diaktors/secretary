/**
 * Note JS
 */
$(document).ready(function() {
    // Toggle Remove Member question
    $('.groups .rightBox ul').find('li a.remove').click(function(e) {
        e.preventDefault();
        $('.groups .rightBox ul').find('li').each(function() {
            $(this).removeClass('active');
        });
        $(this).parent().addClass('active');
        $('#removeModal').find('.modal-body').html($(this).parent().find('span').html());
        $('#removeModal').modal();
    });
    $('#removeModal').find('button.btn-primary').click(function(e) {
        window.location = $('.groups .rightBox ul').find('li.active a').attr('href');
    });
});