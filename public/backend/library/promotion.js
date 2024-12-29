(function($) {
    "use strict"
    var HT = {};
    let typingTimer;
    const doneTypingInterval = 1000; // Thời gian chờ để nhận diện đã dừng gõ (ms)

    HT.promotionNeverEnd = () => {
        let isChecked = $('#never_end_date').prop('checked');
        if (isChecked) {
            $('input[name=end_date]').val('').attr('disabled', true);
        } else {
            $('input[name=end_date]').attr('disabled', false);
        }
    
        // Khi người dùng click vào checkbox
        $(document).on('click', '#never_end_date', function() {
            let _this = $(this);
            let isChecked = _this.prop('checked');
            if (isChecked) {
                $('input[name=end_date]').val('').attr('disabled', true);
            } else {
                $('input[name=end_date]').attr('disabled', false);
            }
        });
    };
    

    HT.promotionSource = () => {
        $(document).on('click', '.chooseSource', function () {
            let _this = $(this);
            let parentContent = _this.closest('.ibox-content');
    
            if (_this.attr('id') === 'allSource') {
                parentContent.find('.source-wrapper').remove();
            } else if (parentContent.find('.source-wrapper').length === 0) {
                let sourceData = {};
                $.ajax({
                    url: 'ajax/source/getAllSource',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250, // Giảm tải server bằng cách delay trước khi gửi request
                    success: function (res) {
                        if (res.sources && Array.isArray(res.sources)) {
                            res.sources.forEach(source => {
                                sourceData[source.id] = {
                                    id: source.id,
                                    name: source.name
                                };
                            });
    
                            parentContent.append(HT.renderSourcePromotion(sourceData));
                            HT.promotionMultipleSelect2(); // Khởi tạo lại Select2
                        } else {
                            console.error("Dữ liệu trả về không hợp lệ.");
                        }
                    },
                    error: function (err) {
                        console.error('Lỗi khi gọi AJAX:', err);
                    }
                });
            }
        });
    };
    
    HT.renderSourcePromotion = (sourceData) => {
        let dataHtml = Object.values(sourceData)
            .map(data => `<option value="${data.id}">${data.name}</option>`)
            .join('');
    
        return `
            <div class="source-wrapper">
                <select name="sourceValue[]" id="sourceSelect" class="multipleSelect2" multiple>
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
                <select name="applyValue[]" id="applySelect" class="multipleSelect2 conditionItem" multiple>
                    ${dataHtml}
                </select>
            </div>
            <div class="wrapper-condition"></div>
        `;
    };

    HT.chooseApplyItem = function() {
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
        let selectedValues = _this.val() || []; // Lấy tất cả giá trị được chọn
        selectedValues.forEach(value => {
            if (!$(`.wrapper-condition .${value}`).length) { // Kiểm tra nếu chưa tồn tại
                let condition = {
                    value: value,
                    label: _this.find(`option[value="${value}"]`).text() // Lấy label từ option
                };
                let optionData = {};
    
                // Gọi AJAX để lấy dữ liệu
                $.ajax({
                    url: 'ajax/dashboard/getPromotionConditionValue',
                    type: 'GET',
                    dataType: 'json',
                    data: { value },
                    success: function (res) {
                        if (res.data && Array.isArray(res.data)) {
                            optionData[condition.value] = res.data.map(item => ({
                                id: item.id,
                                name: item.name
                            }));
    
                            let html = HT.renderConditionHTML(condition, optionData);
                            $('.wrapper-condition').append(html); // Thêm vào DOM
    
                            // Khởi tạo lại multipleSelect2 sau khi thêm mới
                            HT.promotionMultipleSelect2();
                        } else {
                            console.error("Dữ liệu trả về không hợp lệ:", res);
                        }
                    },
                    error: function (err) {
                        console.error('Lỗi khi gọi AJAX:', err);
                    }
                });
            }
        });
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
    HT.renderConditionHTML = (condition, optionData) => {
        let content = '';
        if (optionData[condition.value]) {
            content = optionData[condition.value]
                .map(item => `<option value="${item.id}">${item.name}</option>`)
                .join('\n');
        }
    
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
                <select name="${condition.value}[]" class="multipleSelect2 mt10" multiple>
                    ${content}
                </select>
            </div>
        `;
    };
    
    // Khởi tạo select2
    HT.promotionMultipleSelect2 = () => {
        $('.multipleSelect2').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                placeholder: 'Click vào ô để lựa chọn...',
                allowClear: true
            });
        });
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
                { class: 'order_amount_range_from', name: 'promotion_order_amount_range[amountFrom][]', value: HT.formatNumberWithCommas(newTo.toString()), attribute: { readonly: false } },
                { class: 'order_amount_range_to', name: 'promotion_order_amount_range[amountTo][]', value: 0, attribute: { readonly: false } }
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
                            <input type="text" name="promotion_order_amount_range[amountValue][]" class="form-control int" placeholder="0" value="0">
                            <select name="promotion_order_amount_range[amountType][]" class="setupSelect2">
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

    HT.deleteAmountRangeCondition = () => {
        $(document).on('click', '.delete-order-amount-range-condition', function() {
            $(this).closest('tr').remove()
        })
    }

    HT.renderOrderRangeConditionContainer = () => {
        // Lấy giá trị hiện tại của .promotionMethod
        let option = $('.promotionMethod').val();
    
        // Nếu có giá trị trước đó, thực hiện hành động ngay lập tức
        if (option) {
            performAction(option);
        } 

        $(document).on('change', '.promotionMethod', function () {
            let _this = $(this);
            option = _this.val();
            performAction(option);
        });
    
    
        function performAction(option) {
            const actions = {
                order_amount_range: HT.renderOrderAmountRange,
                product_and_quantity: HT.renderProductAndQuantity,
                // product_quantity_range: () => console.log('product_quantity_range'),
                // goods_discount_by_quantity: () => console.log('goods_discount_by_quantity'),
            };
    
            // Gọi hành động tương ứng hoặc xóa container nếu không có hành động
            (actions[option] || HT.removePromotionContainer)();
        }
    };
    
    
    HT.renderOrderAmountRange = () => {
        let html = `
            <div class="order_amount_range">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-right">Giá trị từ</th>
                            <th class="text-right">Giá trị đến</th>
                            <th class="text-right" style="width: 40px;">Chiết khấu(%)</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                        <tbody>
                        `
        let datas = JSON.parse($('.input_order_amount_range').val());
        if (datas && datas.amountFrom && datas.amountFrom.length > 0) {
            datas.amountFrom.forEach((amountFrom, index) => {
                html += `
                    <tr>
                        <td class="order_amount_range_from">
                            <input type="text" name="promotion_order_amount_range[amountFrom][]" class="form-control int" placeholder="0" value="${amountFrom}">
                        </td>
                        <td class="order_amount_range_to">
                            <input type="text" name="promotion_order_amount_range[amountTo][]" class="form-control int" placeholder="0" value="${datas.amountTo[index]}">
                        </td>
                        <td class="discountType">
                            <div class="uk-flex uk-flex-middle">
                                <input type="text" name="promotion_order_amount_range[amountValue][]" class="form-control int" placeholder="0" value="${datas.amountValue[index]}">
                                <select name="promotion_order_amount_range[amountType][]" class="setupSelect2">
                                    <option value="cash" ${datas.amountType[index] === 'cash' ? 'selected' : ''}>đ</option>
                                    <option value="percent" ${datas.amountType[index] === 'percent' ? 'selected' : ''}>%</option>
                                </select>
                            </div>
                        </td>
                        ${index === 0 ? '' : `
                            <td>
                                <div class="delete-some-item delete-order-amount-range-condition">
                                    <i class="fa fa-trash"></i>
                                </div>
                            </td>
                        `}
                    </tr>
                `;
            });

            html += `
                </tbody>
            </table>
            `
        } else {
            html = `
                <div class="order_amount_range">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-right">Giá trị từ</th>
                                <th class="text-right">Giá trị đến</th>
                                <th class="text-right">Chiết khấu(%)</th>
                                <th class="text-right"></th>
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="order_amount_range_from">
                                    <input type="text" name="promotion_order_amount_range[amountFrom][]" class="form-control int" placeholder="0" value="0">
                                </td>
                                <td class="order_amount_range_to">
                                    <input type="text" name="promotion_order_amount_range[amountTo][]" class="form-control int" placeholder="0" value="0">
                                </td>
                                <td class="discountType">
                                    <div class="uk-flex uk-flex-middle">
                                        <input type="text" name="promotion_order_amount_range[amountValue][]" class="form-control int" placeholder="0" value="0">
                                        <select name="promotion_order_amount_range[amountType][]" id="" class="setupSelect2">
                                            <option value="cash">đ</option>
                                            <option value="percent">%</option>
                                        </select>
                                    </div>
                                </td>
                                <td>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
            `
        }

        html += `
            <button class="btn btn-success btn-custom btn-js-100" value="" type="button">Thêm điều kiện</button>
                </div>
        `

        HT.renderPromotionContainer(html)
    };

    HT.renderProductAndQuantity = () => {
        // Lấy dữ liệu từ thẻ HTML
        const productData = JSON.parse(document.getElementById('productData').dataset.products);
        var module_type_selected = $('.preload_select_module_type').val();

        // Tạo các option từ productData
        const options = Object.entries(productData).map(([key, val]) => {
            let selected = module_type_selected === key ? 'selected' : '';
            return `<option value="${key}" ${selected}>${val}</option>`;
        }).join('');

        let preloadData = JSON.parse($('.input_product_and_quantity').val()) || {
            quantity: ['1'],
            maxDiscountValue: ['0'],
            discountValue: ['0'],
            discountType: ['cash']
        };

        let html = `
            <div class="product-and-quantity">
                <div class="choose-module mt20">
                    <div class="fix-label" style="color: blue;">Sản phẩm áp dụng</div>
                    <select name="module_type" id="" class="setupSelect2 select-product-and-quantity">
                        <option value="none">Chọn hình thức</option>
                        ${options}
                    </select>
                </div>
                <div class="product-and-quantity">
                    <table class="table table-striped mt20">
                        <thead>
                            <tr>
                                <th class="text-right" style="width: 400px;">Sản phẩm mua</th>
                                <th class="text-right" style="width: 70px;">SL tối thiểu</th>
                                <th class="text-right">Giới hạn KM</th>
                                <th class="text-right" style="max-width: 40px;">Chiết khấu</th>
                                <th></th>
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="" style="max-width: 400px">
                                    <div 
                                    class="product-quantity" 
                                    data-toggle="modal" 
                                    data-target="#findProduct" 
                                    type="button" 
                                    name="">
                                        <div class="uk-flex uk-flex-middle">
                                            <div class="boxWrapper choose-product-list">
                                                <div class="boxSearchIcon">
                                                    <i class="fa fa-search"></i>
                                                </div>
                                                <div class="boxSearchInput fix-grid-6">
                                                    <p>Tìm theo tên, mã sản phẩm..</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="">
                                    <input type="text" name="product_and_quantity[quantity]" class="form-control int" value="${typeof preloadData.quantity !== 'undefined' ? preloadData.quantity : 1}">
                                </td>
                                <td>
                                    <input type="text" name="product_and_quantity[maxDiscountValue]" class="form-control int"  value="${typeof preloadData.maxDiscountValue !== 'undefined' ? preloadData.maxDiscountValue : 0}">
                                </td>
                                <td class="discountType">
                                    <div class="uk-flex uk-flex-middle">
                                        <input type="text" name="product_and_quantity[discountValue]" class="form-control int" placeholder="0" value="${typeof preloadData.discountValue !== 'undefined' ? preloadData.discountValue : 0}">
                                        <select name="product_and_quantity[discountType]" id="" class="setupSelect2">
                                            <option value="cash" ${preloadData.discountType === 'cash' ? 'selected' : ''}>đ</option>
                                            <option value="percent" ${preloadData.discountType === 'percent' ? 'selected' : ''}>%</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        `

        HT.renderPromotionContainer(html)
        HT.searchAjax();
    }

    HT.renderPromotionContainer = (html) => {
        $('.promotion-container').html(html)
        $('.setupSelect2').select2();
    }
    
    HT.removePromotionContainer = () => {
        $('.promotion-container').empty();
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
    }

    HT.searchProducts = () => {
        $(document).on('keyup', '.search-model', function(e) {
            let _this = $(this);
            // Sử dụng hàm trim() để loại bỏ khoảng trắng ở đâu và cuối
            let keyword = _this.val().trim();
            let modelFromSelect = $('.select-product-and-quantity').val();
            let model = modelFromSelect ? modelFromSelect : 'Product';

            clearTimeout(typingTimer); // Hủy bỏ timer trước đó nếu có
            typingTimer = setTimeout(function() {
            let option = {
                keyword : keyword, // Tạo dữ liệu gửi đi nếu cần
                model : model,
            };

            HT.loadProduct(option);
            }, doneTypingInterval);
        });
    };

    HT.productQuanityListProduct = () => {
        $(document).on('click', '.product-quantity', function(e) {
            e.preventDefault();
            let modelFromSelect = $('.select-product-and-quantity').val();
            let model = modelFromSelect ? modelFromSelect : 'Product';
            
            let option = {
                keyword : $('.search-model').val(),
                model : model
            }
            HT.loadProduct(option);
        })
    }

    HT.fillToObjectList = (data) => {
        const mapping = {
            "Product": HT.fillProductToList,
            "ProductCatalogue": HT.fillProductCatalogueToList
        };
        console.log(data);
        if (mapping[data.model]) {
            mapping[data.model](data.objects); // Chuyển sang data.data thay vì data.objects
        }
    };

    HT.fillProductCatalogueToList = (objects) => {
        const product_catalogues = objects.data;
        const container = $('.search-list'); // Lưu trữ phần tử container để tránh gọi DOM nhiều lần
        container.html('');
        console.log(objectChooses);
    
        // Kiểm tra nếu có sản phẩm
        if (product_catalogues.length) {
            let htmlContent = ''; // Khởi tạo một biến để lưu nội dung HTML
    
            // Tạo HTML cho từng sản phẩm
            product_catalogues.forEach(product_catalogue => {
                // Kiểm tra nếu sản phẩm đã được chọn trong objectChooses
                const isChecked = objectChooses.some(obj => obj.product_catalogue_id == product_catalogue.id);
    
                htmlContent += `
                    <div class="search-object-item" data-product_catalogue_id="${product_catalogue.id}" data-name="${product_catalogue.name}">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="object-info">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="uk-flex uk-flex-middle">
                                        <input type="checkbox" name="product_catalogue-${product_catalogue.id}" value="${product_catalogue.id}" class="input-checkbox" ${isChecked ? 'checked' : ''}>
                                    </div>
                                    <div class="object-name">
                                        <div class="name ml5" style="margin-bottom: 0px !important;">${product_catalogue.name}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
    
            // Thêm phân trang
            htmlContent += HT.paginationLinks(objects.links);
            container.append(htmlContent);
        }
    };
    
    HT.fillProductToList = (objects) => {
        const products = objects.data;
        const container = $('.search-list'); // Lưu trữ phần tử container để tránh gọi DOM nhiều lần
        container.html('');
        
        // Kiểm tra nếu có sản phẩm
        if (products.length) {
            let htmlContent = ''; // Khởi tạo một biến để lưu nội dung HTML
    
            // Tạo HTML cho từng sản phẩm
            products.forEach(product => {
                const formattedPrice = HT.formatPrice(product.price); // Định dạng giá
                let inventory = (typeof product.inventory != 'undefined') ? product.inventory : 0;
                let couldSell = (typeof product.couldSell != 'undefined') ? product.couldSell : 0;
    
                // Kiểm tra nếu sản phẩm đã tồn tại trong objectChooses
                const isChecked = objectChooses.some(obj => 
                    obj.product_id == product.id && obj.variant_uuid == product.variant_uuid
                );
    
                htmlContent += `
                    <div class="search-object-item" data-product_id="${product.id}" data-variant_uuid="${(product.variant_uuid) ?? ''}" data-name="${product.variant_name}">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="object-info">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="uk-flex uk-flex-middle">
                                        <input type="checkbox" name="product-${product.id}" value="${product.id + '_' + (product.variant_uuid ?? '')}" class="input-checkbox" ${isChecked ? 'checked' : ''}>
                                    </div>
                                    <span class="img img-scaledown">
                                        <img src="${product.image}" alt="${product.name}">
                                    </span>
                                    <div class="object-name">
                                        <div class="name">${product.variant_name}</div>
                                        <div class="jscode">Mã SP: ${product.sku}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="object-extra-info">
                                <div class="price">${formattedPrice}</div> <!-- Hiển thị giá đã định dạng -->
                                <div class="object-inventory">
                                    <div class="uk-flex uk-flex-middle">
                                        <span class="text-1">Tồn kho: </span>
                                        <span class="text-value ml5"> ${inventory}</span>
                                        <span class="text-1 slash">|</span>
                                        <span class="text-value">Có thể bán: ${couldSell}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
    
            // Thêm phân trang
            htmlContent += HT.paginationLinks(objects.links);
            container.append(htmlContent);
        }
    };
    
    var objectChooses = [];

    HT.selectProductAndQuality = () => {
        $(document).on('change', '.select-product-and-quantity', function() {
            const modal = $(this).val();
            objectChooses = []; // Reset objectChooses
            HT.checkEmptyGoodList();
            HT.bindModalOffClick();

            if (modal) {
                HT.productQuanityListProduct();
                bindModalEvents(modal);
            } else {
                $(document).off('click', '.product-quantity');
            }
        });

        const initializeObjectChooses = (modal, objectInput) => {
            objectChooses = []; // Reset objectChooses
            if (modal === 'Product') {
                objectChooses = []
                if (objectInput.name && Array.isArray(objectInput.name)) {
                    objectInput.name.forEach((name, index) => {
                        let objectInputItem = {
                            product_id: objectInput.product_id[index] || null,
                            variant_uuid: objectInput.variant_uuid[index] || null,
                            name: objectInput.name[index] || ''
                        };
    
                        objectChooses.push(objectInputItem);
                    });
                }
            } else if (modal === 'ProductCatalogue') {
                objectChooses = []
                if (objectInput.name && Array.isArray(objectInput.name)) {
                    objectInput.name.forEach((name, index) => {
                        let objectInputItem = {
                            product_catalogue_id: objectInput.product_catalogue_id[index] || null,
                            name: objectInput.name[index] || ''
                        };
    
                        objectChooses.push(objectInputItem);
                    });
                }

            }

            HT.productQuanityListProduct();
        };

        const bindModalEvents = (modal) => {
            const eventsMap = {
                Product: () => {
                    HT.chooseProductPromotion();
                    HT.confirmProductPromotion();
                },
                ProductCatalogue: () => {
                    HT.chooseProductCataloguePromotion();
                    HT.confirmProductCataloguePromotion();
                }
            };

            eventsMap[modal]?.();
        };

        // Khởi tạo objectChooses và bind sự kiện
        const modal = $('.preload_select_module_type').val();
        const objectInput = JSON.parse($('.input_object').val() || '{}');
        console.log(objectInput);

        initializeObjectChooses(modal, objectInput);
        bindModalEvents(modal);
    };

    HT.bindModalOffClick = () => {
        $(document).off('click', '.search-object-item');
        $(document).off('click', '.confirm-product-promotion');
    }

    HT.chooseProductPromotion = () => {
        $(document).on('click', '.search-object-item', function(e) {
            e.preventDefault();
            const _this = $(this);
            const checkbox = _this.find('input[type=checkbox]');
            const isChecked = checkbox.prop('checked');

            const objectItem = {
                product_id: _this.data('product_id'),
                variant_uuid: _this.data('variant_uuid') || null,
                name: _this.data('name')
            };

            if (isChecked) {
                checkbox.prop('checked', false);
                objectChooses = objectChooses.filter(
                    obj => obj.product_id !== objectItem.product_id || obj.variant_uuid !== objectItem.variant_uuid
                );
            } else {
                objectChooses.push(objectItem);
                checkbox.prop('checked', true);
            }

            console.log('Danh sách objectChooses:', objectChooses); // Debug
        });
    };

    HT.confirmProductPromotion = () => {
        const renderProductList = () => {
            const html = objectChooses.map(product => `
                <div class="fix-grid-6">
                    <div class="goods-item">
                        <span class="goods-item-name">${product.name}</span>
                        <button type="button" class="delete-goods-item product-item">
                            <i class="fa fa-close"></i>
                        </button>
                        <div class="hidden">
                            <input type="hidden" name="object[product_id][]" value="${product.product_id}">
                            <input type="hidden" name="object[variant_uuid][]" value="${product.variant_uuid || ''}">
                            <input type="hidden" name="object[name][]" value="${product.name}">
                        </div>
                    </div>
                </div>
            `).join('');
    
            const additionalHtml = `
                <div class="boxSearchInput fix-grid-6">
                    <p>Tìm theo tên, mã sản phẩm...</p>
                </div>
            `;
    
            $('.choose-product-list').html(html + additionalHtml).removeClass('hidden');
        };
    
        $(document).on('click', '.confirm-product-promotion', function (e) {
            e.preventDefault();
    
            if (objectChooses.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm!');
            } else {
                renderProductList();
                $('#findProduct').modal('hide');
            }
    
        });
        
        HT.deleteProductsItem();
        // Render nếu objectChooses đã có sẵn dữ liệu
        if (objectChooses.length > 0) {
            renderProductList();
        }
    };
    
    HT.chooseProductCataloguePromotion = () => {
        $(document).on('click', '.search-object-item', function (e) {
            e.preventDefault();
            let _this = $(this);
            let checkbox = _this.find('input[type=checkbox]');
            let isChecked = checkbox.prop('checked');
            let objectItem = {
                product_catalogue_id: _this.data('product_catalogue_id'),
                name: _this.data('name')
            };

            if (isChecked) {
                // Nếu đã được chọn, bỏ chọn và xóa phần tử khỏi mảng
                checkbox.prop('checked', false);
                objectChooses = objectChooses.filter(obj => obj.product_catalogue_id !== objectItem.product_catalogue_id);
            } else {
                // Nếu chưa được chọn, thêm phần tử vào mảng
                objectChooses.push(objectItem);
                checkbox.prop('checked', true);
            }

            console.log('Danh sách objectChooses:', objectChooses); // Debug
        });
    };
    
    HT.confirmProductCataloguePromotion = () => {
        const renderProductCatalogueList = () => {
            // Render danh sách sản phẩm đã chọn
            const productListHtml = objectChooses.map(product => `
                <div class="fix-grid-6">
                    <div class="goods-item">
                        <span class="goods-item-name">${product.name}</span>
                        <button type="button" class="delete-goods-item catalogue-item">
                            <i class="fa fa-close"></i>
                        </button>
                        <div class="hidden">
                            <input type="hidden" name="object[product_catalogue_id][]" value="${product.product_catalogue_id}">
                            <input type="hidden" name="object[name][]" value="${product.name}">
                        </div>
                    </div>
                </div>
            `).join('');
    
            // Thêm ô tìm kiếm
            const searchBoxHtml = `
                <div class="boxSearchInput fix-grid-6">
                    <p>Tìm theo tên, mã sản phẩm...</p>
                </div>
            `;
    
            $('.choose-product-list')
                .html(productListHtml + searchBoxHtml)
                .removeClass('hidden');
        };
    
        const handleConfirmPromotion = (e) => {
            e.preventDefault();
    
            if (objectChooses.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm!');
                return;
            }
    
            renderProductCatalogueList();
            $('#findProduct').modal('hide');
            HT.deleteProductsItem(); // Gọi hàm xóa các sản phẩm đã chọn
        };
    
        $(document).on('click', '.confirm-product-promotion', handleConfirmPromotion);
    
        if (objectChooses.length > 0) {
            renderProductCatalogueList();
        }
    
        HT.deleteProductCataloguesItem(); // Gọi hàm xóa catalogue item
    };
    
    HT.checkEmptyGoodList = () => {
        if (objectChooses.length === 0) {
            let boxSearchHtml = `
                <div class="boxSearchIcon">
                    <i class="fa fa-search"></i>
                </div>
                <div class="boxSearchInput fix-grid-6">
                    <p>Tìm theo tên, mã sản phẩm..</p>
                </div>
            `;
            $('.choose-product-list').html(boxSearchHtml);
        }
    };
    
    HT.deleteProductsItem = () => {
        // Gắn sự kiện xóa sản phẩm
        $(document).off('click', '.delete-goods-item.product-item').on('click', '.delete-goods-item.product-item', function (e) {
            e.preventDefault();
            e.stopPropagation();
    
            let _this = $(this);
            let productId = _this.closest('.goods-item').find('input[name="object[product_id][]"]').val();
            let productVariantId = _this.closest('.goods-item').find('input[name="object[variant_uuid][]"]').val();
    
            // Lọc và giữ lại các phần tử không trùng với sản phẩm bị xóa
            objectChooses = objectChooses.filter(obj => 
                obj.product_id != productId || obj.variant_uuid != productVariantId
            );
    
            // Xóa phần tử khỏi giao diện
            _this.closest('.fix-grid-6').remove();
    
            // Kiểm tra danh sách sau khi xóa
            HT.checkEmptyGoodList();
        });
    };
    
    HT.deleteProductCataloguesItem = () => {
        // Gắn sự kiện xóa danh mục sản phẩm
        $(document).off('click', '.delete-goods-item.catalogue-item').on('click', '.delete-goods-item.catalogue-item', function (e) {
            e.preventDefault();
            e.stopPropagation();
    
            let _this = $(this);
            let productCatalogueId = _this.closest('.goods-item').find('input[name="object[product_catalogue_id][]"]').val();
    
            // Lọc và giữ lại các phần tử không trùng với danh mục bị xóa
            objectChooses = objectChooses.filter(obj => 
                obj.product_catalogue_id != productCatalogueId
            );
    
            // Xóa phần tử khỏi giao diện
            _this.closest('.fix-grid-6').remove();
    
            // Kiểm tra danh sách sau khi xóa
            HT.checkEmptyGoodList();
        });
    };

    HT.paginationLinks = (links) => {
        if (links.length == 3) {
            return '';
        }

        let html = `
            <nav>
                <ul class="pagination">
        `;
    
        links.forEach(link => {
            if (!link.url) {
                html += `
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">${link.label === 'pagination.previous' ? '‹' : '›'}</span>
                    </li>
                `;
            } else {
                if (link.active) {
                    html += `
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">${link.label}</span>
                        </li>
                    `;
                } else {
                    html += `
                        <li class="page-item">
                            <a class="page-link" href="${link.url}" aria-label="${link.label}">
                                ${link.label === 'pagination.previous' ? '‹' : link.label === 'pagination.next' ? '›' : link.label}
                            </a>
                        </li>
                    `;
                }
            }
        });
    
        html += `
                </ul>
            </nav>
        `;
    
        return html;
    };

    HT.getPaginationLinks = () => {
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            let _this = $(this);
            
            let url = _this.attr('href');
            let urlParams = new URLSearchParams(url.split('?')[1]);
            
            let modelFromSelect = $('.select-product-and-quantity').val();
            let model = modelFromSelect ? modelFromSelect : 'Product';
            let page = urlParams.get('page');
    
            let option = {
                "page": page,
                "model": model,
                "keyword": $('.search-model').val(),
            };
            HT.loadProduct(option)
        });
    }

    HT.formatPrice = (price) => {
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    HT.loadProduct = (option) => {
        $.ajax({
            url: 'ajax/product/loadProductAnimation',
            type: 'GET',
            dataType: 'json',
            data: option,
            delay: 250, // Giảm tải server bằng cách delay trước khi gửi request
            success : function(res){
                HT.fillToObjectList(res);
            }
        })
    };
    
    HT.searchAjax = () => {
        $('.ajaxSearch').each(function () {
            let _this = $(this);
    
            // Lấy model mặc định từ thuộc tính data-model
            let defaultModel = _this.data('model');
    
            _this.select2({
                minimumInputLength: 2,
                placeholder: 'Nhập vào 2 ký tự để tìm kiếm',
                closeOnSelect: false, // Cho phép chọn nhiều nếu cần
                ajax: {
                    url: 'ajax/dashboard/findModelObject',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250, // Giảm tải server bằng cách delay trước khi gửi request
                    data: function (params) {
                        // Lấy giá trị model từ .select-product-and-quantity nếu có
                        let modelFromSelect = $('.select-product-and-quantity').val();
                        let model = modelFromSelect ? modelFromSelect : defaultModel;
    
                        return {
                            keyword: params.term, // Từ khóa tìm kiếm
                            model: model,        // Model từ giá trị hoặc mặc định
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(obj => ({
                                id: obj.id, // Trả về id
                                text: obj.name || obj.text || 'Không có tên', // Trả về name/text hoặc giá trị mặc định
                            })),
                        };
                    },
                    cache: true,
                },
            });
        });
    };

    // Gọi HT.seoPreview khi tài liệu đã sẵn sàng
    $(document).ready(function() {
        HT.promotionNeverEnd();
        HT.promotionSource();
        HT.promotionMultipleSelect2();
        HT.chooseCustomerCondition();
        HT.chooseApplyItem();
        HT.btnJs100();
        HT.deleteAmountRangeCondition();
        HT.renderOrderRangeConditionContainer();
        HT.searchAjax();
        HT.getPaginationLinks();
        HT.searchProducts();
        HT.selectProductAndQuality();
    });

})(jQuery);
