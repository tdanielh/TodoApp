var item_id, item_name,itemType;
jQuery('#lists').on('update',function(e, par){
    var $this = jQuery(this);
    console.log('par ', par);
    selected = [];
    if(typeof par != 'undefined'){

        jQuery.each(par.checked, function(index, value){
            console.log(value.id);
            selected.push(value.id);
        })

        console.log(selected);
    }
    $this.addClass('loading');
    jQuery.ajax({
        type: "GET",
        url: base_url+'/list/lists',
        data: {
            selected: selected
        },
        success: function(data){
            $this
                .html(data)
                .removeClass('loading');
        }
    })
});

jQuery('#sharedWith').on('update',function(){
    var $this = jQuery(this);
    jQuery.ajax({
        url: base_url+'/list/'+item_id+'/sharedwith',
        type: 'GET',
        success: function(data){
            $this.html(data);
        }
    });
});

jQuery(function(){
    jQuery('#lists').trigger('update');

    jQuery('form#createTask').on('submit', function(e){
        e.preventDefault();
        $form = $(this);
        $action = $form.attr('action');
        list_id = $form.data('listid');
        $form.find('.error').html('');

        $form.find('textarea').removeClass('is-invalid');
        data = $form.serialize()+'&list_id='+list_id;
        jQuery.ajax({
            type: "POST",
            url: $action,
            data: data,
            success: function(data){
                jQuery('#list .no_tasks').remove();
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

    jQuery('#confirm-delete')
        .on('show.bs.modal', function(e) {
            $relatedTarget = jQuery(e.relatedTarget);
            item_id = $relatedTarget.closest('.row').data('itemid');
            itemType = $relatedTarget.data('item');
            jQuery('.item', this).html(itemType);
        })
        .on('hidden.bs.modal', function (e) {
            $modal = jQuery(e.target);

            $modal.find('.error').html('');
            $modal.find('.btn-ok').removeClass('disabled');
        });


    $('#confirm-delete').on('click', '.btn-ok', function(e) {
        e.preventDefault();
        $modalDiv = jQuery(e.delegateTarget);
        $target = jQuery(e.target);
        path = $target.data('path')+itemType+'/';
        $target.addClass('disabled');
        jQuery.ajax({
            type: 'DELETE',
            url: path,
            data: {item_id: item_id},
            success: function(){
                jQuery('#list').find('.row[data-itemid='+item_id+']').remove();
                setTimeout(function() {
                    $modalDiv.modal('hide');
                    $target.removeClass('disabled');
                }, 500);
            },
            error: function(data){
                $modalDiv.find('.error').html(data.responseJSON.message)
            }
        });

    });

    jQuery('#shareList')
        .on('show.bs.modal',function(e){
            jQuery('#sharedWith', this).empty();
            jQuery('.listName', this).empty();
            //jQuery("#searchUsers", this).select2();
            $relatedTarget = jQuery(e.relatedTarget);
            item_id = $relatedTarget.closest('.row').data('itemid');
            itemType = $relatedTarget.data('item');
            item_name = $relatedTarget.closest('.row').data('listname');
            jQuery('.listName', this).html(item_name);
            jQuery('#sharedWith').trigger('update');
        })
        .on('hidden.bs.modal', function(e){

        })

    jQuery('form#createList').on('submit', function(e){
        $form = jQuery(this);
        action = $form.attr('action');
        user_id = $form.data('userid');
        data = $form.serialize()+'&user_id='+user_id;
        e.preventDefault();
        jQuery.ajax({
            type: "POST",
            url: action,
            data: data,
            success: function(data){
                jQuery('#list').prepend(data);
                jQuery('#list .no_lists').remove();
                $form.find('#title').val('');
                if(typeof data.gotolist != 'undefined'){
                    console.log(data.listId);
                    window.location.href = base_url+'/list/'+data.listId;
                }
                jQuery('#lists').trigger('update');
            },
            error: function(data){
                $form.find('input').addClass('is-invalid');
                $form.find('.error').html(data.responseJSON.message);
            }
        });
        return false;
    });

    jQuery('form#login').on('submit', function(e){
        e.preventDefault();
        $form = jQuery(this);
        data = $form.serialize();
        action = $form.attr('action');

        jQuery.ajax({
            type: "POST",
            url: action,
            data: data,
            success: function(){
                location.reload();
            },
            error: function(data){
                $form.find('input').addClass('is-invalid');
                $form.find('.error').html(data.responseJSON.message);
            }
        });
    });

    jQuery('#logout').on('click', function(e){
        e.preventDefault();
        path = jQuery(this).data('path');
        jQuery.ajax({
            url: path,
            type: 'POST',
            success: function(){
                location.reload();
            }
        });
    });

    jQuery('form#searchLists').on('change', 'input#private, input#shared',function(e){
        var $checked = jQuery('form#searchLists :checked');
        console.log('this: ', $checked);
        jQuery('#lists').trigger('update', {checked: $checked})
    });

    $('#searchUsers').select2({
        width: '100%',
        dropdownParent: $("#shareList"),
        placeholder: 'Brugere',
        allowClear: true,
        ajax: {
            data: function (params) {
                var query = {
                    name: params.term,
                    listid: item_id,
                }
                return query;
            },
            url: base_url+'/users/list',
            processResults: function (data) {
                return {
                    results: $.map(data, function(value, i) {
                        return { id: i, text: value.name };
                    })
                };
            }
        }
    });

    jQuery('#searchUsers').on('select2:select', function(e){
        var data = e.params.data;
        jQuery.get(base_url+'/list/'+item_id+'/share/'+data.id, function(){
            jQuery('#sharedWith').trigger('update');
        });
    })

    jQuery('#sharedWith').on('click', '.unshare', function(){
        var $this = jQuery(this);
        var userId = $(this).closest('.row').data('userid');
        jQuery.get(base_url+'/list/'+item_id+'/unshare/'+userId, function(){
            $this.trigger('update');
        });
    })
});