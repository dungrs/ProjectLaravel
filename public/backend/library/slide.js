(function($) {
    "use strict";
    var HT = {}
    var counter = 1;

    HT.addSlide = (type) => {
        $(document).on('click', '.addSlide', function(e) {
            e.preventDefault();
            if (typeof(type) == 'undefined') {
                type = "Images";
            }
            
            var finder = new CKFinder();
            finder.resourceType = type;
            finder.selectActionFunction = function(fileUrl, data, allFiles) {
                for (var i = 0; i < allFiles.length; i++) {
                    var image = allFiles[i].url;
                    $('.slide-list').append(HT.renderSlideItemHtml(image))
                    HT.checkSlideNotification();
                }

            }

            finder.popup();
        })
    }

    HT.checkSlideNotification = () => {
        let slideItem = $('.slide-image')
        if (slideItem.length) {
            $('.slide-notification').addClass('hidden')
        } else {
            $('.slide-notification').removeClass('hidden')
        }
    };
    
    // Vẫn đang thiếu phần di chuyển hình ảnh
    HT.renderSlideItemHtml = (image) => {
        let tab_1 = "tab-" + counter;
        let tab_2 = "tab-" + (counter + 1)

        let html = `
                <div class="ui-state-default" style="width: 100% !important; border: none;">
                    <div class="row mb20">
                        <div class="col-lg-3 custom-row">
                            <span class="slide-image img-cover">
                                <img src="${image}" alt="">
                                <input type="hidden" name="slide[image][]" value="${image}">
                                <span class="deleteSlide btn btn-danger"><i class="fa fa-trash"></i></span>
                            </span>
                        </div>
                        <div class="col-lg-9">
                            <div class="tabs-container">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a data-toggle="tab" href="#${tab_1}" aria-expanded="true">Thông tin chung</a>
                                    </li>
                                    <li>
                                        <a data-toggle="tab" href="#${tab_2}" aria-expanded="false">SEO</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="${tab_1}" class="tab-pane active">
                                        <div class="panel-body">
                                            <div class="label-text mb10">Mô tả</div>
                                            <div class="form-row">
                                                <textarea name="slide[description][]" class="form-control"></textarea>
                                            </div>
                                            <div class="form-row form-row-url slide-seo-tab">
                                                <input type="text" name="slide[canonical][]" class="form-control" placeholder="URL">
                                                <div class="overlay">
                                                    <div class="uk-flex uk-flex-middle">
                                                        <label for="input_${tab_1}">Mở trong tab mới</label>
                                                        <input id="input_${tab_1}" type="checkbox" name="slide[window][]" value="_blank">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="${tab_2}" class="tab-pane">
                                        <div class="panel-body">
                                            <div>
                                                <div class="form-row form-row-url slide-neo-tab" style="margin-top: 0 !important">
                                                    <div class="label-text mb10">Tiêu đề ảnh</div>
                                                    <input type="text" name="slide[name][]" class="form-control" placeholder="Tiêu đề ảnh">
                                                </div>
                                            </div>
                                            <div style="margin-top: 10px">
                                                <div class="form-row form-row-url slide-neo-tab">
                                                    <div class="label-text">Mô tả ảnh</div>
                                                    <input type="text" name="slide[alt][]" class="form-control" placeholder="Mô tả ảnh">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
        `

        counter += 2;
        return html;
    }

    HT.deleteSlide = () => {
        $(document).on('click', '.deleteSlide', function() {
            let _this = $(this)
            _this.parents('.ui-state-default').remove();
            HT.checkSlideNotification();
        })
    }

    $(document).ready(function() {
        HT.addSlide();
        HT.deleteSlide();
    });

})(jQuery);
