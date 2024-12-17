(function($) {
    "use strict"
    var HT = {};

    HT.promotionNeverEnd = () => {
        $(document).on('click', '#neverEnd', function() {
            let _this = $(this);
            let isChecked = _this.prop('checked')
            if (isChecked) {
                $('input[name=end_date]').val('').attr('disabled', true);
            } else {
                let endDate = ('input[name=start_date]').val()
                $('input[name=end_date]').val(endDate).attr('disabled', false);
            }
        })
    }

    HT.promotionSource = () => {
        $(document).on('click', '.chooseSource', function () {
            let _this = $(this);
            let parentContent = _this.closest('.ibox-content');
    
            if (_this.attr('id') === 'allSource') {
                parentContent.find('.source-wrapper').remove();
            } else if (parentContent.find('.source-wrapper').length === 0) {
                let sourceData = {
                    0: { id: 1, name: "Tiktok" },
                    1: { id: 2, name: "Facebook" },
                };
                parentContent.append(HT.renderSourcePromotion(sourceData));
                HT.promotionMultipleSelect2();
            }
        });
    };
    
    HT.renderSourcePromotion = (sourceData) => {
        let dataHtml = Object.values(sourceData)
            .map(value => `<option value="${value.id}">${value.name}</option>`)
            .join('');
    
        return `
            <div class="source-wrapper">
                <select name="source" id="sourceSelect" class="multipleSelect2" multiple>
                    ${dataHtml}
                </select>
            </div>
        `;
    };
    
    HT.chooseCustomerCondition = () => {
        $(document).on('click', '.chooseApply', function () {
            let _this = $(this);
            let parentContent = _this.closest('.ibox-content');
    
            if (_this.attr('id') === 'allApply') {
                parentContent.find('.apply-wrapper').remove();
            } else if (parentContent.find('.apply-wrapper').length === 0) {
                let applyData = {
                    0: { id: 'staff_take_care_customer', name: "Nhân Viên Phụ Trách" },
                    1: { id: 'customer_group', name: "Nhóm Khách Hàng" },
                    2: { id: 'customer_gender', name: "Giới tính" },
                    3: { id: 'customer_birthday', name: "Ngày Sinh" },
                };
                parentContent.append(HT.renderApplyCondition(applyData));
                HT.promotionMultipleSelect2();
            }
        });
    };
    
    HT.renderApplyCondition = (applyData) => {
        let dataHtml = Object.values(applyData)
            .map(value => `<option value="${value.id}">${value.name}</option>`)
            .join('');
    
        return `
            <div class="apply-wrapper">
                <select name="apply" id="applySelect" class="multipleSelect2 conditionItem" multiple>
                    ${dataHtml}
                </select>
            </div>
            <div class="wrapper-condition"></div>
        `;
    };

    HT.chooseApplyItem = () => {
        $(document).on('change', '.conditionItem', function () {
            HT.handleSelectChange($(this));
        });
    
        $(document).on('select2:unselect', '.conditionItem', function (e) {
            HT.handleSelectUnselect(e);
        });
    
        $(document).on('click', '.wrapper-condition-item .delete', function () {
            HT.handleDeleteCondition($(this));
        });
    };
    
    // Xử lý khi thay đổi select2
    HT.handleSelectChange = (_this) => {
        let selectedValues = _this.val(); // Lấy tất cả giá trị được chọn
    
        if (!selectedValues) return; // Kiểm tra nếu không có giá trị nào được chọn
    
        selectedValues.forEach(value => {
            if (!$(`.wrapper-condition .${value}`).length) { // Kiểm tra nếu chưa tồn tại
                let condition = {
                    value: value,
                    label: _this.find(`option[value="${value}"]`).text() // Lấy label từ option
                };
                let html = HT.renderConditionHTML(condition);
                $('.wrapper-condition').append(html); // Thêm vào DOM
            }
        });
    
        HT.promotionMultipleSelect2();
    };
    
    // Xử lý khi xóa item trong select2
    HT.handleSelectUnselect = (e) => {
        let removedValue = e.params.data.id; // Lấy giá trị item bị xóa
        $(`.wrapper-condition .${removedValue}`).remove(); // Xóa item tương ứng trong wrapper-condition
    };
    
    // Xử lý khi nhấn nút delete trong wrapper-condition-item
    HT.handleDeleteCondition = (deleteButton) => {
        let parentItem = deleteButton.closest('.wrapper-condition-item'); // Lấy item cha
        let itemClass = parentItem.attr('class').split(' ')[0]; // Lấy class chính (tương ứng với value)
    
        // Xóa item trong wrapper-condition
        parentItem.remove();
    
        // Loại bỏ item tương ứng trong Select2 và cập nhật
        HT.updateSelect2Values(itemClass);
    };
    
    // Cập nhật lại giá trị cho select2 sau khi xóa item
    HT.updateSelect2Values = (itemClass) => {
        let select2 = $('.conditionItem');
    
        if (!select2.length) return; // Tránh lỗi khi select2 không tồn tại
    
        let values = select2.val() || [];
        values = values.filter(value => value !== itemClass); // Loại bỏ item đã xóa
        select2.val(values).trigger('change'); // Cập nhật Select2
    };
    
    // Tạo HTML điều kiện
    HT.renderConditionHTML = (condition) => {
        const optionData = {
            'staff_take_care_customer': [
                { id: 1, name: 'Khách Vip' },
                { id: 2, name: 'Khách Buôn Bán' }
            ],
            'customer_group': [
                { id: 3, name: 'Nhóm A' },
                { id: 4, name: 'Nhóm B' }
            ],
            'customer_gender': [
                { id: 'male', name: 'Nam' },
                { id: 'female', name: 'Nữ' }
            ],
            'customer_birthday': [
                { id: 'today', name: 'Hôm nay' },
                { id: 'tomorrow', name: 'Ngày mai' }
            ]
        };
    
        let content = optionData[condition.value]
            ? optionData[condition.value]
                  .map(option => `<option value="${option.id}">${option.name}</option>`)
                  .join('')
            : '';
    
        return `
            <div class="${condition.value} wrapper-condition-item mt10">
                <div class="mb5">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                        <div class="conditionLabel">${condition.label}</div>
                        <div class="delete">
                            <i class="fa fa-trash"></i>
                        </div>
                    </div>
                </div>
                <select name="apply_${condition.value}" class="multipleSelect2 mt10" multiple>
                    ${content}
                </select>
            </div>
        `;
    };
    
    
    HT.promotionMultipleSelect2 = () => {
        $('.multipleSelect2').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                placeholder: 'Click vào ô để lựa chọn...',
            });
        });

    //     $('.multipleSelect2').select2({
    // //         minimumInputLength: 2,
    //         placeholder: 'Click vào ô để lựa chọn...',
    // //         ajax: {
    // //             url: 'ajax/attribute/getAttribute',
    // //             type: 'GET',
    // //             dataType: 'json',
    // //             delay: 250,
    // //             // Param là kí tự nhập vào
    // //             data: function(params) {
    // //                 return {
    // //                     search: params.term,
    // //                     option: option,
    // //                 }
    // //             },
                
    // //             processResults: function(data){
    // //                 // console.log(data)
    // //                 return {
    // //                     results: $.map(data, function (obj, i){
    // //                         console.log(obj);
    // //                         return obj
    // //                     })
    // //                 }
    // //             },
    // //             cache: true
    // //         }
    //     });
    }
    

    // Gọi HT.seoPreview khi tài liệu đã sẵn sàng
    $(document).ready(function() {
        HT.promotionNeverEnd();
        HT.promotionSource();
        HT.promotionMultipleSelect2();
        HT.chooseCustomerCondition();
        HT.chooseApplyItem();
    });

})(jQuery);
