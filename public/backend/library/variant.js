(function($) {
    "use strict";
    var HT = {};

    HT.setupProductVariant = () => {
        if ($('.turnOnVariant').length) {
            $(document).on('click', '.turnOnVariant', function() {
                let _this = $(this);
                // Kiểm tra nếu có input đã được chọn trong các sibling
                let price = $('input[name=price]').val();
                let code = $('input[name=code]').val();
                if (code == '' || price == '') {
                    alert('Bạn phải nhập vào Giá và Mã sản phẩm để sử dụng chức năng này!')
                    return false;
                }

                if (_this.siblings('input:checked').length === 0) { 
                    $('.variant-wrapper').removeClass('hidden');
                } else {
                    $(".variant-wrapper").addClass("hidden");
                }
            });
        }
    };

    HT.niceSelect = (element) => {
        if (element) {
            // Khởi tạo lại niceSelect cho phần tử được truyền vào
            $(element).niceSelect();
        } else {
            // Khởi tạo cho tất cả các niceSelect nếu không truyền tham số
            $('.niceSelect').niceSelect();
        }
    }

    HT.addVariant = () => {
        // Lấy số lượng variant-item hiện có ngay khi document sẵn sàng
        let currentVariantCount = $('.variant-item').length;
    
        // Giới hạn số lượng variant-item dựa trên số lượng phần tử của attributeCatalogueList
        let attributeCatalogueList = [];
        if (typeof attributeCatalogue === 'string') {
            try {
                attributeCatalogueList = JSON.parse(attributeCatalogue);
            } catch (e) {
                console.error('Invalid JSON:', e);
                return;
            }
        } else {
            attributeCatalogueList = attributeCatalogue;
        }
    
        let maxVariants = attributeCatalogueList.length;
    
        // Kiểm tra nếu đã đạt tới giới hạn thì ẩn nút add-variant ngay lập tức
        if (currentVariantCount >= maxVariants) {
            $('.add-variant').remove(); // Xóa nút nếu đã đạt giới hạn
        } else {
            // Chỉ thêm nút nếu nó chưa tồn tại
            if ($('.add-variant').length === 0) {
                $('.variant-foot').html(`<button type="button" class="add-variant">Thêm phiên bản mới</button>`);
            }
        }
    
        // Gán sự kiện click cho nút "Thêm phiên bản mới"
        $(document).off('click', '.add-variant').on('click', '.add-variant', function() {
            currentVariantCount = $('.variant-item').length;
    
            // Nếu số lượng hiện tại đã đạt tối đa, không cho thêm mới
            if (currentVariantCount >= maxVariants) {
                $('.add-variant').remove();
                return;
            }
    
            // Thêm mới nếu chưa đạt giới hạn
            let html = HT.renderVariantItem(attributeCatalogue);
            let $newElement = $(html).appendTo('.variant-body');
            $('.variantTable thead').html('')
            $('.variantTable tbody').html('')
            HT.niceSelect($newElement.find('.niceSelect'));
            HT.disabledAttributeCatalogueChoose(); // Cập nhật lại danh sách select
    
            // Kiểm tra lại sau khi thêm mới
            currentVariantCount = $('.variant-item').length;
            if (currentVariantCount >= maxVariants) {
                $('.add-variant').remove(); // Xóa nút nếu đã đạt giới hạn sau khi thêm
            } else {
                // Chỉ thêm nút nếu nó chưa tồn tại
                if ($('.add-variant').length === 0) {
                    $('.variant-foot').html(`<button type="button" class="add-variant">Thêm phiên bản mới</button>`);
                }
            }
        });
    }
    
    HT.renderVariantItem = (attributeCatalogue) => {
        let attributeCatalogueList = [];
    
        // Kiểm tra nếu attributeCatalogue là chuỗi JSON, nếu không thì không cần parse
        if (typeof attributeCatalogue === 'string') {
            try {
                attributeCatalogueList = JSON.parse(attributeCatalogue);
            } catch (e) {
                console.error('Invalid JSON:', e);
                return ''; // Trả về chuỗi rỗng nếu JSON không hợp lệ
            }
        } else {
            attributeCatalogueList = attributeCatalogue; // Nếu là object, giữ nguyên
        }
        
        let attributeCatalogueHtml = '';
        for (let i = 0; i < attributeCatalogueList.length; i++) {
            attributeCatalogueHtml += `
                <option value="${attributeCatalogueList[i].id}">${attributeCatalogueList[i].name}</option>
            `;
        }
    
        let html = `
            <div class="row mb20 variant-item">
                <div class="col-lg-4">
                    <div class="attribute-catalogue">
                        <select name="attributeCatalogue[]" class="choose-attribute niceSelect">
                            <option value="">Chọn nhóm thuộc tính</option>
                            ${attributeCatalogueHtml}
                        </select>
                    </div>
                </div>
                <div class="col-lg-7">
                    <input type="text" name="attribute_value[]" disabled class="fake-variant form-control" placeholder="Nhập giá trị thuộc tính">
                </div>
                <div class="col-lg-1">
                    <button type="button" class="remove-attribute btn btn-danger"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        `;
        
        HT.removeAttribute();
        HT.chooseVariantGroup();
        return html;
    };

    HT.chooseVariantGroup = () => {
        $(document).on('change', '.choose-attribute', function() {
            let _this = $(this)
            let attributeCatalogueId = _this.val()
            if (attributeCatalogueId != 0) {
                _this.parents('.col-lg-4').siblings('.col-lg-7').html(HT.select2Variant(attributeCatalogueId));
                $('.selectVariant').each(function(key, index) {
                    HT.getSelect2($(this));
                })
            } else {
                _this.parents('.col-lg-4').siblings('.col-lg-7').html(
                    `<input type="text" name="attribute[${attributeCatalogueId}]" disabled class="fake-variant form-control" placeholder="Nhập giá trị thuộc tính">`
                )
            }
        }) 
    }

    HT.createProductVariant = () => {
        $(document).on('change', '.selectVariant', function() {
            let attributes = []
            let variants = []
            let attributeTitle = []

            $('.variant-item').each(function() {
                let _this = $(this);
                let attr = [];
                let attrVariant = []
                let attributeCatalogueId = _this.find('.choose-attribute').val();
                let optionText = _this.find('.choose-attribute option:selected').text();
                let attribute = $('.variant-'+attributeCatalogueId).select2('data');

                for (let i = 0; i < attribute.length; i ++) {
                    let item = {}
                    let itemVariant = {}
                    item[optionText] = attribute[i].text;
                    itemVariant[attributeCatalogueId] = attribute[i].id
                    attr.push(item)
                    attrVariant.push(itemVariant);
                }
                attributes.push(attr)
                attributeTitle.push(optionText)
                variants.push(attrVariant)
            })
            
            attributes = attributes.reduce(
                (a, b) => a.flatMap(d => b.map( e => ( {...d, ...e } ) ) )
            )
            
            variants = variants.reduce(
                (a, b) => a.flatMap(d => b.map( e => ( {...d, ...e } ) ) )
            )
            
            // console.log(attributeTitle)
            // console.log(variants);
            // console.log(attributes)

            HT.createTableHeader(attributeTitle)
            let trClass = []
            attributes.forEach((item, index) => {
                let row = HT.createVariantRow(item, variants[index])
                let classModified = 'tr-variant-' + Object.values(variants[index]).join(', ').replace(/, /g, '-')
                trClass.push(classModified)

                if (!$('.table.variantTable tbody tr').hasClass(classModified)) {
                    $('.table.variantTable tbody').append(row)
                }
            })

            $('table.variantTable tbody tr').each(function() {
                const $row = $(this)
                const rowClasses = $row.attr('class')
                if (rowClasses) {
                    const rowClassesArray = rowClasses.split(' ')
                    let shouldRemove = false;
                    rowClassesArray.forEach(rowClass => {
                        if (rowClass == 'variant-row') {
                            return;
                        } else if (!trClass.includes(rowClass)) {
                            shouldRemove = true;
                        }
                    })

                    if (shouldRemove) {
                        $row.remove();
                    }
                }
            })

            // let html = HT.renderTableHtml(attributes, attributeTitle, variants)
            // HT.updateVariant();
            // $("table.variantTable").html(html)
        })
    }

    HT.createTableHeader = (attributeTitle) => {
        let thead = $('table.variantTable thead');
        let attributeTitleHtml = '';
        for (let i = 0; i < attributeTitle.length; i++) {
            attributeTitleHtml += `<td>${attributeTitle[i]}</td>`
        }

        let row = `
            <tr>
                <td>Hình ảnh</td>
                ${attributeTitleHtml}
                <td>Số lượng</td>
                <td>Giá tiền</td>
                <td>SKU</td>
            </tr>
        `
        thead.html(row);
        return thead;
    }

    HT.createVariantRow = (attributeItem, variantItem) => {
        let attributeString = Object.values(attributeItem).join(', ')
        let attributeIdString = Object.values(variantItem).join(', ')
        let classModified = attributeIdString.replace(/, /g, '-')

        let attributeTd = ''
        Object.values(attributeItem).forEach((value, index) => {
            attributeTd += `<td>${value}</td>`
        })

        let mainPrice = $('input[name=price]').val();
        let mainSKU = $('input[name=code]').val();
        let attributesHtml = `
                <tr class="variant-row tr-variant-${classModified}" style="cursor: pointer">
                    <td>
                        <span class="image img-cover">
                            <img class="imageVariant" src="https://th.bing.com/th/id/OIP.n2J-te2edVD91F8w6udMmgHaHa?rs=1&pid=ImgDetMain" alt="Image">
                        </span>
                    </td>
                    ${attributeTd}
                    <td class="td_quantity">-</td>
                    <td class="td_price">${mainPrice}</td>
                    <td class="td_sku">${mainSKU + "-" + classModified}</td>
                    <td class="hidden td-variant">
                        <input type="text" name="variant[quantity][]" class="variant_quantity">
                        <input type="text" name="variant[sku][]" class="variant_sku" value="${mainSKU + "-" + classModified}">
                        <input type="text" name="variant[price][]" class="variant_price" value="${mainPrice}">
                        <input type="text" name="variant[barcode][]" class="variant_barcode">
                        <input type="text" name="variant[file_name][]" class="variant_filename">
                        <input type="text" name="variant[file_url][]" class="variant_fileurl">
                        <input type="text" name="variant[album][]" class="variant_album">
                        <input type="text" name="productVariant[name][]" value="${attributeString}">
                        <input type="text" name="productVariant[id][]" value="${attributeIdString}">
                    </td>
                </tr>
            `
        return attributesHtml;  
    }

    HT.getSelect2 = (object) => {
        let option = {
            'attributeCatalogueId' : object.attr('data-catid')
        }
        $(object).select2({
            minimumInputLength: 2,
            placeholder: 'Nhập tối thiểu 2 kí tự để tìm kiếm',
            ajax: {
                url: 'ajax/attribute/getAttribute',
                type: 'GET',
                dataType: 'json',
                delay: 250,
                // Param là kí tự nhập vào
                data: function(params) {
                    return {
                        search: params.term,
                        option: option,
                    }
                },
                
                processResults: function(data){
                    // console.log(data)
                    return {
                        results: $.map(data, function (obj, i){
                            console.log(obj);
                            return obj
                        })
                    }
                },
                cache: true
            }
        });
    }
    
    HT.disabledAttributeCatalogueChoose = () => {
        // Bắt sự kiện khi thay đổi lựa chọn trong select
        $(document).on('change', '.choose-attribute', function() {
            HT.updateSelectOptions();
        });
    
        // Hàm để cập nhật lại các select khi có sự thay đổi
        HT.updateSelectOptions = () => {
            // Lấy tất cả các giá trị đã chọn từ các select
            let selectedValues = $('.choose-attribute').map(function() {
                return $(this).val(); // Lấy giá trị của từng select
            }).get();
        
            // Duyệt qua tất cả các select và cập nhật option
            $('.choose-attribute').each(function() {
                let $select = $(this); // Lấy từng select
                let currentValue = $select.val(); // Lưu giá trị hiện tại
                $select.find('option').each(function() {
                    let optionValue = $(this).val();
                    // Nếu optionValue đã được chọn ở một select khác, disable nó
                    if (selectedValues.includes(optionValue) && optionValue !== currentValue && optionValue !== '') {
                        $(this).attr('disabled', true);
                    } else {
                        $(this).removeAttr('disabled');
                    }
                });
                
                // Đảm bảo rằng tùy chọn "Chọn nhóm thuộc tính" luôn được chọn
                if (currentValue === '') {
                    $select.find('option[value=""]').prop('selected', true);
                }
        
                $select.niceSelect('update'); // Cập nhật lại niceSelect
            });
        };
    
        // Gọi hàm này ngay khi render để chắc chắn mọi select được cập nhật đúng
        HT.updateSelectOptions();
    };
    
    // Hàm để xóa phiên bản và kiểm tra lại số lượng
    HT.removeAttribute = () => {
        if ($('.remove-attribute').length) {
            $(document).on('click', '.remove-attribute', function() {
                // Xóa phần tử variant-item
                $(this).closest('.variant-item').remove();
    
                // Cập nhật lại các select options
                HT.disabledAttributeCatalogueChoose();
                HT.createProductVariant();
                
                // Kiểm tra lại và thêm nút "Thêm phiên bản mới" nếu cần
                let currentVariantCount = $('.variant-item').length;
                let attributeCatalogueList = [];
                if (typeof attributeCatalogue === 'string') {
                    try {
                        attributeCatalogueList = JSON.parse(attributeCatalogue);
                    } catch (e) {
                        console.error('Invalid JSON:', e);
                        return;
                    }
                } else {
                    attributeCatalogueList = attributeCatalogue;
                }
    
                let maxVariants = attributeCatalogueList.length;
                if (currentVariantCount < maxVariants) {
                    // Chỉ thêm nút nếu nó chưa tồn tại
                    if ($('.add-variant').length === 0) {
                        $('.variant-foot').html(`<button type="button" class="add-variant">Thêm phiên bản mới</button>`);
                    }
                }
            });
        }
    }

    HT.select2Variant = (attributeCatalogueId) => {
        let html = `
        <select name="attribute[${attributeCatalogueId}][]" id="" class="selectVariant form-control variant-${attributeCatalogueId}" multiple data-catid="${attributeCatalogueId}"></select>
        `
        return html;
    }

    HT.variantAlbum = () => {
        $(document).on('click', '.click-to-upload-variant' , function(e) {
            e.preventDefault();
            HT.browseVariantServerCkeditor()
        })
    }

    HT.browseVariantServerCkeditor = () => {
        let type = 'Images';
        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function(fileUrl, data, allFiles) {
            let html = '';
            console.log(allFiles.length);
            for (var i = 0; i < allFiles.length; i++) {
                var image = allFiles[i].url;
                html += `
                <li class="ui-state-default">
                    <div class="thumb">
                        <span class="span image img-scaledown">
                            <img src="${image}" alt="${image}">
                            <input type="hidden" name="variant_album[]" value="${image}">
                        </span>
                        <button class="delete-image"><i class="fa fa-trash"></i></button>
                    </div>
                </li>`;
            }
            $('#sortableVariant').append(html);
            $('.click-to-upload-variant').addClass('hidden');
            $('.upload-variant-list').removeClass('hidden');
        }

        finder.popup();
    }

    HT.deletePicture = () => {
        $(document).on('click', '.delete-image', function(e) {
            e.preventDefault();
            let _this = $(this);
            _this.parents('.ui-state-default').remove();
            if ($('.ui-state-default').length == 0) {
                $('.click-to-upload-variant').removeClass('hidden');
                $('.upload-varriant-list').addClass('hidden');
            }
        })
    }

    HT.switchChange = () => {
        $(document).on('change', '.js-switch', function() {
            let _this = $(this);
            let isChecked = _this.prop('checked');
            
            // Lấy phần tử cha gần nhất (parent())
            // Tìm phần từ có cùng cấp với phần tử cha (sibling)
            // Xong tìm bên trong phần tử (find)
            let targetElements = _this.parents('.col-lg-2').siblings('.col-lg-10').find('.disabled');
    
            if (isChecked) {
                // Nếu checkbox được chọn, loại bỏ thuộc tính disabled của các input bên trong
                targetElements.removeAttr('disabled');
            } else {
                // Nếu checkbox không được chọn, thêm thuộc tính disabled vào các input
                targetElements.attr('disabled', true);
            }
        });
    }

    HT.switchery = () => {
        $('.js-switch').each(function(){
            var switchery = new Switchery(this, { color: '#1AB394', size: 'small' });
        })
    }

    HT.updateVariant = () => {
        $(document).on('click', '.variant-row', function() {
            let _this = $(this)
            let variantData = {}
            // Tìm phần tử bắt đầu với class="variant"
            _this.find(".td-variant input[type=text][class^='variant_']").each(function() {
                let className = $(this).attr('class')
                variantData[className] = $(this).val();
            })
            
            let updateVariantBox = HT.updateVariantHtml(variantData);

            if ($('.updateVariantTr').length == 0) {
                _this.after(updateVariantBox)
                HT.switchery();
                HT.variantCancleUpdate();
            }
        })
    }

    HT.variantAlbumList = (album) => {
        let html = ''
        if (album.length) {
            for(let i = 0; i < album.length; i++) {
                if (album[0] !== '') {
                    html += `
                        <li class="ui-state-default">
                            <div class="thumb">
                                <span class="span image img-scaledown">
                                    <img src="${album[i]}" alt=${album[i]}">
                                    <input type="hidden" name="variant_album[]" value="${album[i]}">
                                </span>
                                <button class="delete-image"><i class="fa fa-trash"></i></button>
                            </div>
                        </li>
                    `
                }
            }
        }
        return html;
    }

    HT.updateVariantHtml = (variantData) => {
        let albumVariant = variantData.variant_album.split(',')
        let variantAlbumItem = HT.variantAlbumList(albumVariant);
        let html = `
            <tr class="updateVariantTr">
                <td colspan="10">
                    <div class="updateVariant ibox">
                        <div class="ibox-title">
                            <h5>Cập nhật thông tin phiên bản</h5>
                            <div class="uk-flex uk-flex-middle">
                                <button type="button" class="cancleUpdate btn btn-danger mr10">Hủy bỏ</button>
                                <button type="button" class="saveUpdateVariant btn btn-success">Lưu lại</button>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="click-to-upload-variant ${albumVariant[0] !== '' ? 'hidden' : ''}">
                                <div class="icon">
                                    <a href="" class="upload-variant-picture">
                                        <img style="height: 100px; width: 100px;" src="http://127.0.0.1:8000/backend/img/image.png" alt="" />
                                    </a>
                                </div>
                                <div class="small-text">
                                    Sử dụng nút chọn hình hoặc click vào đây để thêm hình ảnh
                                </div>
                            </div>
                            <div class="upload-variant-list ${variantAlbumItem.length > 0 ? '' : 'hidden'}">
                                <ul id="sortableVariant" class="clearfix data-album sortui ui-sortable">
                                    ${variantAlbumItem}
                                </ul>
                            </div>
                            <div class="row mt20 uk-flex uk-flex-middle">
                                <div class="col-lg-2 uk-flex uk-flex-middle" style="margin-top: 20px !important;">
                                    <label class="mr10" for="" style="margin-bottom: 0px !important">Tồn kho</label>
                                    <input type="checkbox" class="js-switch" ${variantData.variant_quantity ? 'checked' : ''} data-target="variant_quantity">
                                </div>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label for="" class="control-label">Số lượng</label>
                                            <input ${(variantData.variant_quantity == '') ? 'disabled': ''} type="text" id="variant_quantity" name="variant_quantity" value="${(variantData.variant_quantity ?? variantData.variant_quantity)}" class="form-control ${(variantData.variant_quantity == '') ? 'disabled': ''} int text-right">
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="" class="control-label">SKU</label>
                                            <input type="text" name="variant_sku" value="${variantData.variant_sku}" class="form-control text-right">
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="" class="control-label">Giá</label>
                                            <input type="text" name="variant_price" value="${variantData.variant_price}" class="form-control int text-right">
                                        </div>
                                        <div class="col-lg-3">
                                            <label for="" class="control-label">Barcode</label>
                                            <input type="text" name="variant_barcode" value="${variantData.variant_barcode}" class="form-control text-right">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt20 uk-flex uk-flex-middle">
                                <div class="col-lg-2 uk-flex uk-flex-middle" style="margin-top: 20px !important;">
                                    <label class="text-left" for="" style="margin-bottom: 0px !important">Quản lý File</label>
                                    <input type="checkbox" class="js-switch" ${variantData.variant_fileurl ? 'checked' : ''}  data-target="disabled">
                                </div>
                                
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="" class="control-label">Tên File</label>
                                            <input type="text" name="variant_filename" value="${variantData.variant_filename}" class="form-control ${(variantData.variant_filename == '') ? 'disabled': ''} text-right" ${(variantData.variant_filename == '') ? 'disabled': ''}>
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="" class="control-label">Đường dẫn</label>
                                            <input type="text" name="variant_fileurl" value="${variantData.variant_fileurl}" class="form-control ${(variantData.variant_fileurl == '') ? 'disabled': ''} text-right" ${(variantData.variant_fileurl == '') ? 'disabled': ''}>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
        return html;
    }

    HT.variantCancleUpdate = () => {
        $(document).on('click', '.cancleUpdate', function() {
            $('.updateVariantTr').remove();
        })
    }

    HT.variantSaveUpdate = () => {
        $(document).on('click', '.saveUpdateVariant', function() {
            let variant = {
                'quantity' : $('input[name=variant_quantity]').val(),
                'sku' : $('input[name=variant_sku]').val(),
                'price' : $('input[name=variant_price]').val(),
                'barcode' : $('input[name=variant_barcode]').val(),
                'filename' : $('input[name=variant_filename]').val(),
                'fileurl' : $('input[name=variant_fileurl]').val(),
                'album' : $("input[name='variant_album[]']").map(function() {
                    return $(this).val()
                }).get(),
            }

            $.each(variant, function(index, value) {
                $('.updateVariantTr').prev().find('.variant_' + index).val(value) 
            })
            HT.previewVarianTr(variant)
            
            $('.updateVariantTr').remove();
        })
    }

    HT.previewVarianTr = (variant) => {
        let option = {
            'quantity' : variant.quantity,
            'price' : variant.price,
            'sku' : variant.sku,
        }

        $.each(option, function(index, value) {
            $('.updateVariantTr').prev().find('.td_' + index).html(value)
        })

        $('.updateVariantTr').prev().find('.imageVariant').attr('src', variant.album[0])
    }

    HT.formatInputNumberVariant = () => {
        $(document).on('input', '.int', function() {
            let value = $(this).val().replace(/,/g, '');
        
            // Kiểm tra xem có phải là số không
            if (!isNaN(value)) {
                $(this).val(Number(value).toLocaleString('en'));
            }
        });
    }

    HT.setupMultipleSelect = (callback) => {
        if ($('.selectVariant').length) {
            let count = $('.selectVariant').length

            $('.selectVariant').each(function() {
                let _this = $(this)
                let attributeCatalogueId = _this.attr('data-catid')
                if (attribute != '') {
                    $.get('ajax/attribute/loadAttribute', {
                        attribute : attribute,
                        attributeCatalogueId : attributeCatalogueId
                    }, function(json) {
                        if (json.items != 'undefined' && json.items.length) {
                            for(let i = 0; i < json.items.length; i++) {
                                var option = new Option(json.items[i].text, json.items[i].id, true, true)
                                // trigger là phát sự kiện cho nó
                                _this.append(option).trigger('change');
                            }
                        }

                        if (--count === 0 && callback) {
                            callback();
                        }
                    })
                }

                HT.getSelect2(_this)
            })
        }
    }

    HT.productVariant = () => {
        variant = JSON.parse(atob(variant))
        console.log(variant)

        $('.variant-row').each(function(index, value) {
            let _this = $(this)
            let inputHiddenFields = [
                { name : 'variant[quantity][]', class : 'variant_quantity', value : variant.quantity[index] },
                { name : 'variant[sku][]', class : 'variant_sku', value : variant.sku[index] },
                { name : 'variant[price][]', class : 'variant_price', value : variant.price[index] },
                { name : 'variant[barcode][]', class : 'variant_barcode', value : variant.barcode[index] },
                { name : 'variant[file_name][]', class : 'variant_filename', value : variant.file_name[index] },
                { name : 'variant[file_url][]', class : 'variant_fileurl', value : variant.file_url[index] },
                { name : 'variant[album][]', class : 'variant_album', value : variant.album[index] },
            ]

            for (let i = 0; i < inputHiddenFields.length; i++) {
                _this.find("." + inputHiddenFields[i].class).val((inputHiddenFields[i].value) ? inputHiddenFields[i].value : 0)
            }

            let album = variant.album[index]
            let variantImage = (album) ? album.split(',')[0] : 'https://th.bing.com/th/id/OIP.n2J-te2edVD91F8w6udMmgHaHa?rs=1&pid=ImgDetMain';

            _this.find('.td_quantity').html(variant.quantity[index])
            _this.find('.td_price').html(variant.price[index])
            _this.find('.td_sku').html(variant.sku[index])
            _this.find('.imageVariant').attr('src', variantImage)
        })
    }

    $(document).ready(function() {
        HT.setupProductVariant();
        HT.addVariant();
        HT.niceSelect(); // Khởi tạo niceSelect cho các phần tử hiện có
        HT.disabledAttributeCatalogueChoose();
        HT.removeAttribute();
        HT.chooseVariantGroup();
        HT.createProductVariant();
        HT.variantAlbum();
        HT.deletePicture();
        HT.switchChange();
        HT.updateVariant();
        HT.variantCancleUpdate();
        HT.variantSaveUpdate();
        HT.formatInputNumberVariant();
        HT.setupMultipleSelect(
            () => {
                HT.productVariant()
            }
        );
    });

})(jQuery);
