jQuery('form#createTask').on('submit', function(e){
    e.preventDefault();
    $form = $(this);
    $action = $form.attr('action');
    $list_id = $form.data('listid');
    $user_id = $form.data('userid');
    $form.find('.error').html('');

    $form.find('textarea').removeClass('is-invalid');
    data = $form.serialize()+'&user_id='+$user_id+'&list_id='+$list_id;
    jQuery.ajax({
        type: "POST",
        url: $action,
        data: data,
        success: function(data){
                jQuery('#tasks .no_tasks').remove();
                jQuery('#tasks').prepend(data);
                $form.find('#description').val('');
        },
        error: function(data){
            $form.find('textarea').addClass('is-invalid');
            $form.find('.error').html(data.responseJSON.message);
        }
    });
    return false;
});

jQuery(document).on('click', '.task_check', function(e){
    e.preventDefault();
    $this = jQuery(this);
    $container = $this.closest('.row');
    task_id = $container.data('itemid');
    path = $this.data('path');
    checked = $this.data('done');
    unchecked = $this.data('todo');
    waiting = $container.data('waiting');
    action = $this.data('action');

    $this.removeClass(unchecked)
         .removeClass(checked);
    if(action == 'status')
        $this.addClass(waiting);
    jQuery.ajax({
        type: "POST",
        url: path,
        data: {task_id: task_id},
        success:function(data){
            data = JSON.parse(data);
            if(action == 'status')
                $this.removeClass(waiting).addClass(data.status == 'done' ? checked : unchecked);
        }
    })
});

$('#confirm-delete').on('click', '.btn-ok', function(e) {
    $modalDiv = $(e.delegateTarget);
    item_id = $(this).data('itemid');
    item = $(this).data('item');
    path = $(this).data('path')+item+'/';
    console.log(path);
    $.ajax({url: path, data: {item_id: item_id}, type: 'DELETE'})
    $modalDiv.addClass('loading');
    setTimeout(function() {
        $modalDiv.modal('hide').removeClass('loading');
        jQuery('#list').find('.row[data-itemid='+item_id+']').remove();
    }, 1000);
});

jQuery('#confirm-delete').on('show.bs.modal', function(e) {
    $container = jQuery(e.relatedTarget).closest('.row');
    item_id = $container.data('itemid');
    console.log(item_id);
    item = jQuery(e.relatedTarget).data('item');
    jQuery('.btn-ok', this)
        .attr('data-itemid', item_id)
        .attr('data-item', item);
    jQuery('.item', this).html(item);

});


jQuery('form#createList').on('submit', function(e){
    $form = $(this);
    $action = $form.attr('action');
    $user_id = $form.data('userid');
    data = $form.serialize()+'&user_id='+$user_id;
    e.preventDefault();
    jQuery.ajax({
        type: "POST",
        url: $action,
        data: data,
        success: function(data){
            jQuery('#lists').prepend(data);
        },
        error: function(data){
            $form.find('input').addClass('is-invalid');
            $form.find('.error').html(data.responseJSON.message);
        }
    });
    return false;
});