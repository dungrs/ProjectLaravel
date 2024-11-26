(function($) {
    "use strict"
    var HT = {};

    HT.setupCkeditor = () => {
        if ($('.ck-editor').length) {
            $('.ck-editor').each(function() {
                let editor = $(this);
                let elementId = editor.attr('id');
                let elementHeight = editor.attr('data-height')
                HT.ckeditor4(elementId, elementHeight);
            });
        }
    }

    HT.multipleUploadImageCkeditor = () => {
        $(document).on('click', '.multipleUploadImageCkeditor', function(e) {
            e.preventDefault();
            let object = $(this);
            let target = object.attr('data-target')
            HT.browseServerCkeditor(object, 'Images', target);
        })
    }

    HT.uploadAlbum = () => {
        $(document).on('click', '.upload-picture', function(e) {
            e.preventDefault();
            HT.browseServerAlbum();
        })
    }
    
    HT.ckeditor4 = (elementId, elementHeight) => {
        if (typeof(elementHeight) == 'undefined') {
            elementHeight = 500;
        }
        CKEDITOR.replace(elementId, {
            height: elementHeight,
            removeButtons: '', // Đây là thuộc tính đúng, removeButton không hợp lệ
            entities: true,
            allowedContent: true,
    
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'styles' },
                { name: 'colors' },
                { name: 'about' }
            ],
            
        });
    }
    

    HT.uploadImageToInput = () => {
        $('.upload-image').click(function() {
            let input = $(this);
            let type = input.attr('data-type')
            HT.setupCKFinder2(input, type);
        });
    };

    HT.uploadImageAvatar = () => {
        $('.image-target').click(function() {
            let input = $(this);
            let type = 'Images';
            HT.browseServerAvatar(input, type)
        })
    }

    HT.setupCKFinder2 = (object, type) => {
        if (typeof(type) == 'undefined') {
            type = 'Images';
        }

        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function(fileUrl, data) {
            object.val(fileUrl)
        }

        finder.popup();
    };

    HT.browseServerAvatar = (object, type) => {
        if (typeof(type) == 'undefined') {
            type = 'Images';
        }

        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function(fileUrl, data) {
            object.find('img').attr('src', fileUrl);
            object.siblings('input').val(fileUrl)
        }

        finder.popup();
    }

    HT.browseServerCkeditor = (object, type, target) => {
        if (typeof(type) == 'undefined') {
            type = 'Images';
        }

        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function(fileUrl, data, allFiles) {
            for (var i = 0; i < allFiles.length; i++) {
                let html = '';
                var image = allFiles[i].url;
                html += '<div class="image-content"><figure>'
                    html += '<img src="'+ image +'" alt="'+ image +'" />'
                    html += '<figcaption>Nhập vào mô tả của bạn</figcaption>'
                html += '</figure></div>'
                CKEDITOR.instances[target].insertHtml(html)
            }
        }

        finder.popup();
    }

    HT.browseServerAlbum = () => {
        let type = 'Images';
        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function(fileUrl, data, allFiles) {
            let html = '';
            for (var i = 0; i < allFiles.length; i++) {
                var image = allFiles[i].url;
                html += '<li class="ui-state-default">'
                    html += '<div class="thumb">'
                        html += '<span class="span image img-scaledown">'
                            html += '<img src="'+ image +'" alt="'+image+'">'
                            html += '<input type="hidden" name="album[]" value="'+ image +'">'
                        html += '</span>'
                        html += '<button class="delete-image"><i class="fa fa-trash"></i></button>'
                    html += '</div>'
                html += '</li>'
            }
            $('#sortable').append(html);
            $('.click-to-upload').addClass('hidden');
            $('.upload-list').removeClass('hidden');
        }

        finder.popup();
    }

    HT.deletePicture = () => {
        $(document).on('click', '.delete-image', function(e) {
            e.preventDefault();
            let _this = $(this);
            _this.parents('.ui-state-default').remove();
            if ($('.ui-state-default').length == 0) {
                $('.click-to-upload').removeClass('hidden');
                $('.upload-list').addClass('hidden');
            }
        })
    }

    $(document).ready(function() {
        HT.uploadImageToInput();
        HT.setupCkeditor();
        HT.uploadImageAvatar();
        HT.multipleUploadImageCkeditor();
        HT.uploadAlbum();
        HT.deletePicture();
    });

})(jQuery);
