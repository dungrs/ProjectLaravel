(function($) {
    "use strict"
    var HT = {};
    let typingTimer;
    const doneTypingInterval = 1000; // Thời gian chờ để nhận diện đã dừng gõ (ms)

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
                { class: 'order_amount_range_from', name: 'amountFrom[]', value: HT.formatNumberWithCommas(newTo.toString()), attribute: { readonly: false } },
                { class: 'order_amount_range_to', name: 'amountTo[]', value: 0, attribute: { readonly: false } }
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
                            <input type="text" name="amountValue[]" class="form-control int" placeholder="0" value="0">
                            <select name="amountType[]" class="setupSelect2">
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
        $(document).on('change', '.promotionMethod', function () {
            let _this = $(this);
            let option = _this.val();
    
            const actions = {
                order_amount_range: HT.renderOrderAmountRange,
                product_and_quantity: HT.renderProductAndQuantity,
                // product_quantity_range: () => console.log('product_quantity_range'),
                // goods_discount_by_quantity: () => console.log('goods_discount_by_quantity'),
            };
    
            (actions[option] || HT.removePromotionContainer)();
        });
    };
    
    HT.renderOrderAmountRange = () => {
        let html = `
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
                                <input type="text" name="amountFrom[]" class="form-control int" placeholder="0" value="0">
                            </td>
                            <td class="order_amount_range_to">
                                <input type="text" name="amountTo[]" class="form-control int" placeholder="0" value="0">
                            </td>
                            <td class="discountType">
                                <div class="uk-flex uk-flex-middle">
                                    <input type="text" name="amountValue[]" class="form-control int" placeholder="0" value="0">
                                    <select name="amountType[]" id="" class="setupSelect2">
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
                <button class="btn btn-success btn-custom btn-js-100" value="" type="button">Thêm điều kiện</button>
            </div>
        `

        HT.renderPromotionContainer(html)
    };

    HT.renderProductAndQuantity = () => {
        // Lấy dữ liệu từ thẻ HTML
        const productData = JSON.parse(document.getElementById('productData').dataset.products);

        // Tạo các option từ productData
        const options = Object.entries(productData).map(([key, val]) => {
            return `<option value="${key}">${val}</option>`;
        }).join('');

        let html = `
            <div class="product-and-quantity">
                <div class="choose-module mt20">
                    <div class="fix-label" style="color: blue;">Sản phẩm áp dụng</div>
                    <select name="" id="" class="setupSelect2 select-product-and-quantity">
                        ${options}
                    </select>
                </div>
                <div class="product-and-quantity">
                    <table class="table table-striped mt20">
                        <thead>
                            <tr>
                                <th class="text-right" style="width: 400px;">Sản phẩm mua</th>
                                <th class="text-right" style="width: 80px;">SL tối thiểu</th>
                                <th class="text-right">Giới hạn KM</th>
                                <th class="text-right">Chiết khấu</th>
                                <th></th>
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="">
                                    <select name="" data-model="Product" class="form-control int ajaxSearch" value="" multiple>
                                        
                                    </select>
                                </td>
                                <td class="">
                                    <input type="text" name="" class="form-control int" placeholder="1" value="0">
                                </td>
                                <td>
                                    <input type="text" name="" class="form-control int" placeholder="0" value="0">
                                </td>
                                <td class="discountType">
                                    <div class="uk-flex uk-flex-middle">
                                        <input type="text" name="amountValue[]" class="form-control int" placeholder="0" value="0">
                                        <select name="amountType[]" id="" class="setupSelect2">
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
                    <button class="btn btn-success btn-custom btn-js-100" value="" type="button">Thêm điều kiện</button>
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

    HT.searchProducts = () => {
        $(document).on('keyup', '.search-model', function(e) {
            let _this = $(this);
            // Sử dụng hàm trim() để loại bỏ khoảng trắng ở đâu và cuối
            let keyword = _this.val().trim();
            let model = $('.select-product-and-quantity').val();

            clearTimeout(typingTimer); // Hủy bỏ timer trước đó nếu có
            typingTimer = setTimeout(function() {
            let option = {
                keyword : keyword, // Tạo dữ liệu gửi đi nếu cần
                model : 'Product',
            };

            HT.loadProduct(option);
            }, doneTypingInterval);
        });
    };

    HT.productQuanityListProduct = () => {
        $(document).on('click', '.product-quantity', function(e) {
            e.preventDefault();
            let option = {
                keyword : $('.search-model').val(),
                model : "Product"
            }
            HT.loadProduct(option);
        })
    }

    HT.fillToObjectList = (data) => {
        const mapping = {
            "Product": HT.fillProductToList,
            "ProductCatalogue": HT.fillProductCatalogueToList
        };
    
        if (mapping[data.model]) {
            mapping[data.model](data.objects); // Chuyển sang data.data thay vì data.objects
        }
    };
    
    HT.fillProductToList = (objects) => {
        const products = objects.data;
        const container = $('.search-list');  // Lưu trữ phần tử container để tránh gọi DOM nhiều lần
        container.html('');
        // Kiểm tra nếu có sản phẩm
        if (products.length) {
            let htmlContent = '';  // Khởi tạo một biến để lưu nội dung HTML
    
            // Tạo HTML cho từng sản phẩm
            products.forEach(product => {
                const formattedPrice = HT.formatPrice(product.price);  // Định dạng giá
                let inventory = (typeof product.inventory != 'undefined') ? inventory : 0
                let couldSell = (typeof product.couldSell != 'undefined') ? couldSell : 0
    
                htmlContent += `
                    <div class="search-object-item" data-product_id=${product.id} data-variant_id=${(product.product_variant_id) ?? ''} data-name="${product.name}">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="object-info">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="uk-flex uk-flex-middle">
                                        <input type="checkbox" name="product-${product.id}" value="${product.id + '_' + (product.product_variant_id) ?? ''}" class="input-checkbox">
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
                                <div class="price">${formattedPrice}</div>  <!-- Hiển thị giá đã định dạng -->
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

            htmlContent += HT.paginationLinks(objects.links)
            container.append(htmlContent);
        }
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
            
            // $('.select-product-and-quantity').val()
            let model = 'Product';
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
        HT.productQuanityListProduct();
        HT.getPaginationLinks();
        HT.searchProducts();
    });

})(jQuery);
