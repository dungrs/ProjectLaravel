(function($) {
    "use strict";
    var HT = {}

    let typingTimer;
    const doneTypingInterval = 100; // Thời gian chờ để nhận diện đã dừng gõ (ms)

    HT.chooseModel = () => {
        $(document).on('change', '.input-radio', function() {
            $('.search-model-result').html(''); 
        })
    }
    
    HT.searchModel = () => {
        $(document).on('keyup', '.search-model', function (e) {
            e.preventDefault();
            let _this = $(this);
            $('.ajax-search-result').show();
    
            if ($("input[type='radio']:checked").length === 0) {
                _this.val('');
                alert('Bạn chưa chọn Module');
                return false;
            }
    
            let keyword = _this.val().trim();
    
            let option = {
                keyword: keyword, // Tạo dữ liệu gửi đi nếu cần
                model: $("input[type='radio']:checked").val()
            };
    
            // Gửi Ajax để lấy kết quả
            HT.sendAjax(option);
        });
    };

    HT.renderSearchResult = (res) => {
        let html = '';
        res.forEach(data => {
            let isChecked = $(`.search-model-result .search-result-item[data-canonical="${data.canonical}"]`).length > 0
            html += `
                <button class="ajax-search-item" 
                        data-canonical="${data.canonical}" 
                        data-id="${data.id}" 
                        data-name="${data.name}" 
                        data-image="${data.image}"
                        data-checked="${ isChecked ? 1 : 0 }"
                        >
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <span>${data.name}</span>
                        <div class="auto-icon">
                            ${ isChecked ? HT.setChecked() : '' }
                        </div>
                    </div>
                </button>
            `;
        });
        return html;
    };

    HT.setChecked = () => {
        let html = `
            <img class="check-icon-widget icon-widget" src="${checkIconPath}" alt="">
        `
        return html;
    }

    HT.unfocusSearchBox = () => {
        $(document).on('click', 'html', function(e) {
            if(!$(e.target).hasClass('search-model-result') && !$(e.target).hasClass('search-model')) {
                $('.ajax-search-result').html('')
            }
        })

        $(document).on('click', '.ajax-search-result', function(e) {
            e.stopPropagation();
        })
    }

    HT.addModel = () => {
        $(document).on('click', '.ajax-search-item', function (e) {
            e.preventDefault();
            let _this = $(this);
    
            // Lấy dữ liệu từ thuộc tính `data-*`
            let data = {
                canonical: _this.data('canonical'),
                id: _this.data('id'),
                name: _this.data('name'),
                image: _this.data('image'),
            };
    
            // Kiểm tra xem item đã tồn tại chưa
            let searchItem = $(`.search-model-result .search-result-item[data-canonical="${data.canonical}"]`);
            let isDuplicate = searchItem.length > 0;
            
            if (isDuplicate) {
                searchItem.remove();
                _this.find('.auto-icon').html('')
            } else {
                // Nếu không trùng, thêm vào danh sách
                $('.search-model-result').append(HT.renderTemplateModel(data)).show();
                
                // Đặt trạng thái checked cho item hiện tại
                $('.ajax-search-item .auto-icon').empty(); // Xóa checked cũ
                _this.attr('data-checked', 1);
                _this.find('.auto-icon').html(HT.setChecked()); // Thêm checked cho item được chọn
    
                $('.ajax-search-result').hide();
            }
    
        });
    };

    HT.renderTemplateModel = (data) => {
        let html = `
            <div class="search-result-item" data-canonical="${data.canonical}">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <div class="uk-flex uk-flex-middle">
                        <span class="image img-cover">
                            <img src="${(data.image) ? data.image : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrfCs7RwnUpivnANUJaLoN6Q-wBvkOkHwmlg&s'}" alt="">
                        </span>
                        <span class="name">${data.name}</span>
                    </div>
                    <div class="deleted">
                        <img class="deleted-icon-widget icon-widget" src="${deleteIconPath}" alt="">
                    </div>
                </div>
            </div>
        `

        return html;
    }

    HT.deleteModel = () => {
        $(document).on('click', '.search-result-item .deleted', function (e){
            e.preventDefault();
    
            // Xóa item khỏi danh sách hiển thị
            let _this = $(this).closest('.search-result-item');
    
            // Xóa item trong danh sách kết quả
            _this.remove();
    
            // Cập nhật trạng thái checked trong danh sách tìm kiếm
            let searchItem = $(`.ajax-search-item[data-canonical="${canonical}"]`);
            if (searchItem.length > 0) {
                searchItem.attr('data-checked', 0); // Đặt lại trạng thái chưa checked
                searchItem.find('.auto-icon').html(''); // Xóa icon check nếu có
            }
        });
    };

    HT.sendAjax = (option) => {
        clearTimeout(typingTimer); // Hủy bỏ timer trước đó nếu có
        typingTimer = setTimeout(function() {
            $.ajax({
                url: 'ajax/dashboard/findModelObject',
                type: 'get',
                data: option,
                dataType: 'JSON',
                beforeSend: function() {
                },
                success: function(res) {
                    if (res.length > 0) {
                        // Gán kết quả render vào div.ajax-search-result
                        $('.ajax-search-result').html(HT.renderSearchResult(res));
                    } else {
                        $('.ajax-search-result').html('<p class="mt10 mb10 no-widget-result">Không tìm thấy kết quả.</p>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching menu:', textStatus, errorThrown);
                }
            });
        }, doneTypingInterval);
    }

    $(document).ready(function() {
        HT.searchModel();
        HT.unfocusSearchBox();
        HT.addModel();
        HT.deleteModel();
        HT.chooseModel();
    });

})(jQuery);
