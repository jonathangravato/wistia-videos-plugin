jQuery(document).ready(function($){
    $('.post-action').on('click',function(){
        var postData = $(this).val();
        var data = {
            action: 'handle_ajax',
            data: postData
        } 

        $.post(ajax_object.ajax_url, data, function(response){
            console.log(data);
        });

        return false;
    });
});