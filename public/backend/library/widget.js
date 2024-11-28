(function($) {
    "use strict";
    var HT = {}

    let typingTimer;
    const doneTypingInterval = 100; // Thời gian chờ để nhận diện đã dừng gõ (ms)
    
    HT.searchModel = () => {
        $(document).on('keyup', '.search-model', function(e) {
            e.preventDefault();
            let _this = $(this);
            if ($("input[type='radio']:checked").length === 0) {
                _this.val('') 
                alert('Bạn chưa chọn Module');
                return false;
            }
            
            let keyword = _this.val().trim();

            let option = {
                keyword : keyword, // Tạo dữ liệu gửi đi nếu cần
                model : $("input[type='radio']:checked").val()
            };


            HT.sendAjax(option);
        });
    };

    HT.renderSearchResult = (res) => {
        let html = '';
        res.forEach(data => {
            html += `
                <button class="ajax-search-item">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <span>${data.name}</span>
                        <div class="auto-icon">
                            <img class="check-icon-widget icon-widget" src="${checkIconPath}" alt="">
                        </div>
                    </div>
                </button>
            `;
        });
        return html;
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
                        $('.ajax-search-result').html('<p class="mt10 mb10">Không tìm thấy kết quả.</p>');
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
    });

})(jQuery);
