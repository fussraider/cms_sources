$(document).ready(function(){

    function modalError(json){
        UIkit.modal.alert('<div class="uk-modal-header"><h2><i class="uk-icon uk-icon-exclamation-circle uk-text-danger"></i> ОШИБКА</h2></div>'+json.message)
        .on({
				'hide.uk.modal': function(){
                    if(json.url)
                        window.location.href = json.url;
                }
			});
        console.log(json);
    }

    function modalSuccess(json){
        UIkit.modal.alert('<div class="uk-modal-header"><h2><i class="uk-icon uk-icon-check-circle-o uk-text-success"></i> Выполнено</h2></div>'+json.message)
        .on({
				'hide.uk.modal': function(){
                    if(json.url)
                        window.location.href = json.url;
                }
			});
        console.log(json);
    }

    function processIcon(selector, process){
        if(process === true){
            $(selector).find('i.uk-icon').addClass('uk-icon-spinner uk-icon-spin')
        }
        else
            $(selector).find('i.uk-icon').removeClass('uk-icon-spinner uk-icon-spin')
    }

    tinymce.init({
        selector: 'textarea.tinymce',
        height: 300,
        language: 'ru',
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste responsivefilemanager code'
        ],
        toolbar: 'undo redo | cut copy paste | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | responsivefilemanager',
        image_advtab: true ,
        
        external_filemanager_path:"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
    });

    $(document).on('click', '#status-btn #status-values a[id]', function(e){
        e.preventDefault();
        var status_btn = $('#status-btn button#page-status');

        status_btn.val($(this).attr('id')).html($(this).text());
        $(this).closest('div[data-uk-dropdown]').removeClass('uk-open');
    });

    $(document).on('click', '#visibility-btn #visibility-values a[id]', function(e){
        e.preventDefault();
        var status_btn = $('#visibility-btn button#page-visibility');

        status_btn.val($(this).attr('id')).html($(this).text());
        $(this).closest('div[data-uk-dropdown]').removeClass('uk-open');
    });

    $(document).on('click', 'button#page-cover', function(e){
        var lightbox = UIkit.lightbox.create([
            {'source': '/admin/modules/stock/filemanager/dialog.php', 'type':'iframe'}
        ]);
        
        lightbox.show();
    });

    $(document).on('click', 'a#delete-page-btn, .actions-bar>a.page-remove', function(e){
        e.preventDefault();
        var btn = $(this);
        processIcon(btn, true);
        UIkit.modal.confirm("Удалить эту страницу?", function(){
            var data = {
                confirmed: 'true'
            }

            $.ajax({
                url: btn.attr('href'),
                method: 'POST',
                dataType: 'JSON',
                data: data,
                success: function(r){
                    if(r.length == 0){
                        modalError({"result": "error", "message": "AJAX-запрос вернул пустой результат. Изменения сохранить не удалось."});
                    }
                    else{
                        if(r.result == 'success')
                            modalSuccess(r)
                        else
                            modalError(r);
                    }

                    processIcon(btn, false);
                }
            });

        },
        function(){
            processIcon(btn, false);
        });

    });

    $(document).on('click', 'a#save-page-btn', function(e){
        e.preventDefault();
        var btn = $(this);
        processIcon(btn, true);
        var form_data = {
            page_id: $('#page_id').text(),
            title: $('input#title').val(),
            announce: $('textarea#announce').val(),
            body: tinymce.get('body').getContent(),
            status: $('button#page-status').val(),
            visibility: $('button#page-visibility').val(),
            page_url: $('input#page-url').val()
        }
        if(form_data.title.length == 0){
            $('input#title').addClass('uk-form-danger');
            processIcon(btn, false);
        }
        else{
            $('input#title').removeClass('uk-form-danger');

            $.ajax({
                url: btn.attr('href'),
                method: 'POST',
                dataType: 'JSON',
                data: form_data,
                success: function(r){
                    if(r.length == 0){
                        modalError({"result": "error", "message": "AJAX-запрос вернул пустой результат. Изменения сохранить не удалось."});
                    }
                    else{
                        if(r.result == 'success')
                            modalSuccess(r)
                        else
                            modalError(r);
                    }

                    processIcon(btn, false);
                }
            });
            
        }
    })
});