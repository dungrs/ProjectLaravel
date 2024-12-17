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

    var ranges = [];

    // Hàm kiểm tra xung đột phạm vi
    HT.checkbtnJs100ConflickRange = (newFrom, newTo) => {
        for (let i = 0; i < ranges.length; i++) {
            let existRange = ranges[i];
    
            if (
                (newFrom >= existRange.from && newFrom <= existRange.to) || // newFrom nằm trong phạm vi existRange
                (newTo >= existRange.from && newTo <= existRange.to) ||     // newTo nằm trong phạm vi existRange
                (newFrom <= existRange.from && newTo >= existRange.to)      // Phạm vi mới bao trùm existRange
            ) {
                return true; // Có xung đột
            }
        }
        return false; // Không có xung đột
    };
    
    // Hàm kiểm tra điều kiện và xử lý input
    HT.checkBtnJs100Condition = () => {
        let $lastRow = $('.order_amount_range').find('tbody tr:last-child');
        let inputFrom = $lastRow.find('.order_amount_range_from input').val();
        let inputTo = $lastRow.find('.order_amount_range_to input').val();
    
        // Loại bỏ dấu phẩy nếu có
        let cleanInputFrom = inputFrom.replace(/,/g, '');
        let cleanInputTo = inputTo.replace(/,/g, '');
    
        // Xóa class lỗi trước khi kiểm tra
        $lastRow.removeClass('error-range');
    
        if (cleanInputTo == 0 || cleanInputTo === '') {
            alert('Giá trị đến không hợp lệ!');
            $lastRow.addClass('error-range'); // Thêm class lỗi
            $lastRow.find('.order_amount_range_to input').val('');
            return false; // Dừng lại nếu giá trị không hợp lệ
        }
    
        if (parseInt(cleanInputTo) < parseInt(cleanInputFrom)) {
            alert('Giá trị "đến" phải lớn hơn hoặc bằng "từ"!');
            $lastRow.addClass('error-range'); // Thêm class lỗi
            $lastRow.find('.order_amount_range_to input').val('');
            return false; // Dừng lại nếu giá trị không hợp lệ
        }
    
        // Kiểm tra xung đột phạm vi
        if (HT.checkbtnJs100ConflickRange(parseInt(cleanInputFrom), parseInt(cleanInputTo))) {
            alert('Phạm vi này đã tồn tại hoặc bị chồng lấn!');
            $lastRow.addClass('error-range'); // Thêm class lỗi
            return false; // Dừng lại nếu có xung đột
        }
    
        // Trả về giá trị "to" đã được định dạng
        return HT.formatNumberWithCommas(cleanInputTo);
    };
    
    // Hàm định dạng số với dấu phẩy
    HT.formatNumberWithCommas = (number) => {
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };
    
    // Hàm xử lý thêm dòng mới
    HT.btnJs100 = () => {
        $(document).on('click', '.btn-js-100', function () {
            let inputToFormatted = HT.checkBtnJs100Condition();
    
            // Nếu giá trị không hợp lệ thì không thêm item
            if (!inputToFormatted) {
                return; // Dừng lại nếu có lỗi
            }
    
            // Lấy giá trị "từ" của dòng mới
            let lastInputFrom = $('.order_amount_range')
                .find('tbody tr:last-child')
                .find('.order_amount_range_from input')
                .val()
                .replace(/,/g, '');
            let newFrom = parseInt(lastInputFrom) + 1;
            let newTo = parseInt(inputToFormatted.replace(/,/g, ''));
    
            // Thêm phạm vi vào mảng ranges
            ranges.push({ from: newFrom, to: newTo });
    
            let tdList = [
                { class: 'order_amount_range_from', name: 'field1', value: HT.formatNumberWithCommas(newTo.toString()), attribute: { readonly: false } },
                { class: 'order_amount_range_to', name: 'field2', value: 0, attribute: { readonly: false } }
            ];
    
            let inputFields = tdList.map(item => {
                let readonlyAttr = item.attribute.readonly ? 'readonly' : '';
                return ` 
                    <td class="${item.class}">
                        <input type="text" name="${item.name}" class="form-control int" placeholder="0" value="${item.value}" ${readonlyAttr}>
                    </td>
                `;
            }).join('');
    
            let html = `
                <tr>
                    ${inputFields}
                    <td class="discountType">
                        <div class="uk-flex uk-flex-middle">
                            <input type="text" name="" class="form-control int" placeholder="0" value="0">
                            <select name="" class="setupSelect2">
                                <option value="cash">đ</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="delete-some-item delete-order-amount-range-condition">
                            <i class="fa fa-trash"></i>
                        </div>
                    </td>
                </tr>
            `;
    
            // Thêm dòng mới vào bảng
            $('.order_amount_range table tbody').append(html);
    
            // Kích hoạt Select2 cho các select mới
            $('.setupSelect2').select2();
        });
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
        HT.btnJs100();
    });

})(jQuery);
