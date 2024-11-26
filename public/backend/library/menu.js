(function($) {
    "use strict";

    const HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');

    HT.createMenuCatalogue = function() {
        $(document).on('submit', '.create-menu-catalogue', function(e) {
            e.preventDefault();
            let _form = $(this)
            let option = {
                "name" : _form.find("input[name=name]").val(),
                "keyword" : _form.find("input[name=keyword]").val(),
                '_token' : _token
            }

            $.ajax({
                url: 'ajax/menu/createCatalogue',
                type: 'POST',
                data: option,
                dataType: 'json',
                success: function(res) {
                    $('.error').text('');
                    if (res.code == 0) {
                        _form.find('.form-error').removeClass('alert-danger hidden').addClass('alert-success').html(res.messages)
                        const menuCatalogueSelect = $('select[name=menu_catalogue_id]')
                        menuCatalogueSelect.append(`<option value="${res.data.id}">${res.data.name}</option>`)
                    } else {
                        _form.find('.form-error').removeClass('alert-success hidden').addClass('alert-danger').html(res.messages)
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status === 422) {
                        let data = jqXHR.responseJSON.errors
                        console.log(data);
                        Object.keys(data).forEach(key => {
                            const messages = data[key];
                            messages.forEach(message => {
                                $(`.error.${key}`).text(message); // Hiển thị thông báo lỗi
                            });
            
                            // Thêm sự kiện để xóa lỗi khi bắt đầu nhập vào input đó
                            $(`[name="${key}"]`).on('input', function() {
                                $(`.error.${key}`).text('');
                            });
                        });
                    }
                }
            });
            
        });
    };

    HT.createMenuRow = () => {
        $(document).on('click', '.add-menu', function(e) {
            e.preventDefault();
            let _this = $(this)
            $('.menu-wrapper .notification').hide();
            $('.menu-wrapper').append(HT.menuRowHtml());
        })
    }

    HT.menuRowHtml = (option) => {
        let html = `
            <div class="row menu-item ${(option && typeof(option.canonical) !== 'undefined' ? option.canonical : "")}" style="margin-bottom: 20px">
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="menu[name][]" value="${option && typeof(option.name) !== 'undefined' ? option.name : ''}">
                </div>
                <div class="col-lg-4">
                    <input type="text" class="form-control" name="menu[canonical][]" value="${option && typeof(option.canonical) !== 'undefined' ? option.canonical : ''}">
                </div>
                <div class="col-lg-2">
                    <input type="text" class="form-control int" name="menu[order][]" value="0">
                </div>
                <div class="col-lg-2 text-center" style="margin-top: 5px;">
                    <a class="delete-menu text-danger"><i class="fa fa-trash"></i></a>
                </div>
                <input class="hidden" value="0" name="menu[id][]">
            </div>
        `;
        return html;
    }

    HT.deleteMenuRow = () => {
        $(document).on('click', '.delete-menu', function(e) {
            e.preventDefault();

            $(this).closest('.menu-item').remove();
            HT.checkMenuItemLength();
        })
    }

    HT.checkMenuItemLength = () => {
        if ($('.menu-item').length === 0) {
            $('.menu-wrapper .notification').show();
        }
    }

    HT.getMenu = () => {
        $(document).on('click', '.menu-module', function() {
            let _this = $(this); // Lưu đối tượng đã click
            let option = {
                "model" : _this.attr('data-model') // Lấy giá trị model từ data attribute
            }

            let menuRowClasses = HT.checkMenuRowExist();
            HT.sendAjaxGetMenu(option, _this, menuRowClasses); // Gọi sendAjaxGetMenu với _this
        });
    }
    
    HT.sendAjaxGetMenu = (option, _this, menuRowClasses) => {
        $.ajax({
            url: 'ajax/dashboard/getMenu',
            type: 'GET',
            data: option,
            dataType: 'JSON',
            beforeSend: function() {
                // Hiển thị loading hoặc không xóa dữ liệu ngay lập tức
                _this.closest('.panel').find('.menu-list').append('<p>Loading...</p>');
            },
            success: function(res) {
                let html = '';
                let data = res.data.data;
    
                // Tạo HTML cho từng mục trong menu
                for (let i = 0; i < data.length; i++) {
                    html += HT.renderModelMenu(data[i], menuRowClasses);
                }
    
                // Thêm các liên kết phân trang
                console.log(res.data.links);
                html += HT.menuLinks(res.data.links);
    
                // Xóa loading và thay thế nội dung menu-list
                _this.closest('.panel').find('.menu-list').html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching menu:', textStatus, errorThrown);
                _this.closest('.panel').find('.menu-list').html('<p>Error loading menu.</p>');
            }
        });
    }

    HT.menuLinks = (links) => {
        if (links.length == 3) {
            return '';
        }

        let html = `
            <nav>
                <ul class="pagination">
        `;
    
        links.forEach(link => {
            // Kiểm tra nếu link không có URL thì hiển thị dưới dạng disabled (ví dụ trang đầu/trang cuối)
            if (!link.url) {
                html += `
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true">${link.label === 'pagination.previous' ? '‹' : '›'}</span>
                    </li>
                `;
            } else {
                // Kiểm tra nếu link đang active (trang hiện tại)
                if (link.active) {
                    html += `
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">${link.label}</span>
                        </li>
                    `;
                } else {
                    // Hiển thị link cho các trang khác
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

    HT.renderModelMenu = (object, menuRowClasses) => {
        let checked = menuRowClasses.includes(object.canonical);
        let html = `
            <div class="m-item">
                <div class="uk-flex uk-flex-middle">
                    <input type="checkbox" ${ checked ? 'checked' : '' } value="${object.canonical}" name="object_${object.id}" class="m0 choose-menu" id="object_${object.id}">
                    <label for="object_${object.id}">${object.name}</label>
                </div>
            </div>
        `
        return html;
    }

    HT.checkMenuRowExist = () => {
        let menuRowClasses = $('.menu-item').map(function() {
            // Tách các class thành mảng và lấy phần tử cuối cùng
            let allClasses = $(this).attr('class').split(' ');
            return allClasses[allClasses.length - 1]; // Trả về phần tử cuối cùng
        }).get(); // Dùng .get() để chuyển từ jQuery object sang mảng thông thường
    
        return menuRowClasses;
    }

    HT.chooseMenu = () => {
        $(document).on('click', '.choose-menu', function() {
            let _this = $(this);
            let canonical = _this.val();
            let name = _this.siblings('label').text()
            let row = HT.menuRowHtml({
                name : name,
                canonical : canonical,
            })

            let isChecked = _this.prop('checked');
            if (isChecked === true) {
                $('.menu-wrapper .notification').hide();
                $('.menu-wrapper').append(row)
            } else {
                $('.menu-wrapper').find('.' + canonical).remove();
                HT.checkMenuItemLength();
            }
        })
    }

    HT.getPaginationMenu = () => {
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            let _this = $(this);
            
            let url = _this.attr('href');
            let urlParams = new URLSearchParams(url.split('?')[1]);
    
            let model = urlParams.get('model');
            let page = urlParams.get('page');
    
            let option = {
                "page": page,
                "model": model
            };
            let menuRowClasses = HT.checkMenuRowExist();
            HT.sendAjaxGetMenu(option, _this, menuRowClasses);
        });
    }

    let typingTimer;
    const doneTypingInterval = 1000; // Thời gian chờ để nhận diện đã dừng gõ (ms)

    HT.searchMenu = () => {
        $(document).on('keyup', '.search-menu', function(e) {
            let _this = $(this);
            // Sử dụng hàm trim() để loại bỏ khoảng trắng ở đâu và cuối
            let keyword = _this.val().trim();

            clearTimeout(typingTimer); // Hủy bỏ timer trước đó nếu có

            typingTimer = setTimeout(function() {
            let option = {
                search : keyword, // Tạo dữ liệu gửi đi nếu cần
                model : _this.closest('.panel-collapse').attr('id')
            };

            let menuRowClasses = HT.checkMenuRowExist();
            HT.sendAjaxGetMenu(option, _this, menuRowClasses);
            }, doneTypingInterval);
        });
    };

    HT.setupNestable = () => {
        // activate Nestable for list 2
        if ($('#nestable2')) {
            $('#nestable2').nestable({
                group: 1
            }).on('change', HT.updateMestableOutput);
        }
    }

    HT.updateOutput = (e) => {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    }

    HT.runUpdateNestableOutput = () => {
        // output initial serialised data
        HT.updateOutput($('#nestable2').data('output', $('#nestable2-output')));
    }

    HT.expandAndCollapse = () => {
        $('#nestable-menu').on('click', function (e) {
            var target = $(e.target),
                    action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
    }

    HT.updateMestableOutput = (e) => {
        var list = $(e.currentTarget),
            output = $(list.data('output'))

        let json = window.JSON.stringify(list.nestable('serialize'));
        if(json) {
            var option = {
                "json" : json,
                'menu_catalogue_id' : $('#nestable2').data('menucatalogueid'),
                "_token" : _token
            }

            $.ajax({
                url: 'ajax/menu/drag',
                type: 'POST',
                data: option,
                dataType: 'json',
                success: function(res) {
                    console.log(res);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    
                }
            });
        }

    }

    $(document).ready(function() {
        HT.createMenuCatalogue();
        HT.createMenuRow();
        HT.deleteMenuRow();
        HT.getMenu();
        HT.chooseMenu();
        HT.getPaginationMenu();
        HT.searchMenu()
        HT.setupNestable();
        HT.runUpdateNestableOutput();
        HT.expandAndCollapse();
        HT.updateMestableOutput();
    });

})(jQuery);
