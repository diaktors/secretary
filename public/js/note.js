/**
 * Note JS
 */
$(document).ready(function() {
    $('#noteForm').find('input[type="checkbox"]').change(function(e) {
        $('#shareNote').toggle();
    });
    $('#groupSelectForm').submit(function(e) {
        e.preventDefault();
        var group = $('#groupSelectForm').find('select').val();
        var post = {'group': group};
        $('#groupHidden').val(group);
        $('#membersHidden').val('');
        $.post($(this).attr('action'), post, function(msg) {
            if (msg.success) {
                var members = msg.groupMembers;
                var ul = $('<ul/>');
                $.each(members, function(key, value) {
                    var html  = '<i class="icon-user"></i> ';
                    html     += value.displayName;
                    html     += ' (' + value.email + ')';
                    html     += ' <i class="icon-thumbs-up"></i> ';
                    var li    = $('<li/>', {class: value.id}).html(html).appendTo(ul);
                });
                $('#groupMembers').html(ul);
                $('#selectedGroupMembers').find('ul').html('');
            }
        });
    });
    $('#groupMembers').find('i.icon-thumbs-up').live('click', function() {
        var li = $(this).parent();
        var id = li.attr('class');
        $('#membersHidden').val($('#membersHidden').val() + id + ',');
        li.find('i.icon-thumbs-up').attr('class', 'icon-thumbs-down');
        $('#selectedGroupMembers').find('ul').append(li);
    });
    $('#selectedGroupMembers').find('i.icon-thumbs-down').live('click', function() {
        var li      = $(this).parent();
        var id      = li.attr('class');
        var members = $('#membersHidden').val().replace(id + ',', '');
        $('#membersHidden').val(members);
        li.find('i.icon-thumbs-down').attr('class', 'icon-thumbs-up');
        $('#groupMembers').find('ul').append(li);
    });
});