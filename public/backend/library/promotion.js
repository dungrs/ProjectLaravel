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
            <div class="apply-condition"></div>
        `;
    };

    HT.chooseApplyItem = () => {
        $(document).on('change', '.conditionItem', function () {
            let _this = $(this);
            let conditionValue = _this.val();
            console.log(conditionValue);
    
        });
    }
    
    HT.promotionMultipleSelect2 = () => {
        $('.multipleSelect2').select2({
    //         minimumInputLength: 2,
            placeholder: 'Click vào ô để lựa chọn...',
    //         ajax: {
    //             url: 'ajax/attribute/getAttribute',
    //             type: 'GET',
    //             dataType: 'json',
    //             delay: 250,
    //             // Param là kí tự nhập vào
    //             data: function(params) {
    //                 return {
    //                     search: params.term,
    //                     option: option,
    //                 }
    //             },
                
    //             processResults: function(data){
    //                 // console.log(data)
    //                 return {
    //                     results: $.map(data, function (obj, i){
    //                         console.log(obj);
    //                         return obj
    //                     })
    //                 }
    //             },
    //             cache: true
    //         }
        });
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
