(function ($) {
    "use strict";

    const HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');
    let data = {

    }

    HT.editOrder = () => {
        $(document).on('click', '.edit-order', function () {
            let _this = $(this);
            let target = _this.data('target');
            let html = '';
            if (target === 'description') {
                html = HT.renderDescriptionOrder(_this);
                HT.updateDescription();
                HT.bindClickOutside();
            } else if (target === 'customerInfo') {
                html = HT.renderCustomerOrderInformation();
                setTimeout(() => {
                    HT.select2();
                }, 0)
                HT.bindClickOutside();
            }

            _this.closest('.ibox').find('.ibox-content').html(html);
        });
    };

    HT.provinceList = () => {
        const html = provinces.map(item => 
            `<option value="${item.id}"}>${item.name}</option>`
        ).join('');
        
        return html;
    };
    
    HT.loadCity = (province_id) => {
        if (province_id) {
            $(".province").val(province_id).trigger("change");
        }
    };

    HT.getLocation = () => {
        $(document).on('change', '.location', function() {
            let _this = $(this);
            // Khởi tạo một object option chứa thông tin cần thiết để gửi AJAX
            let option = {
                'data' : {
                    'location_id' : _this.val()  // ID của vị trí được chọn
                }, 
                'target' : _this.attr('data-target')  // Lấy giá trị của thuộc tính 'data-target'
            }

            // Gọi phương thức để gửi dữ liệu đến server và lấy danh sách vị trí mới
            HT.sendDataTogetLocation(option);
        })
    }

    HT.sendDataTogetLocation = (option) => {
        $.ajax({
            url: '/ajax/location/getLocation',  // URL để gửi yêu cầu
            type: 'GET',  // Phương thức gửi yêu cầu (GET)
            data: option,  // Dữ liệu cần gửi đến server
            dataType: 'json',  // Định dạng dữ liệu nhận về từ server (JSON)
            success: function(res) {  // Xử lý khi nhận được phản hồi thành công từ server
                // Chèn HTML trả về từ server vào thành phần mục tiêu
                $('.' + option.target).html(res.html);
                let district_id = $("input[name=district_id]").val();
                let ward_id = $("input[name=ward_id]").val();


                // Nếu có giá trị district_id và mục tiêu là 'districts', đặt giá trị cho phần tử và kích hoạt sự kiện 'change'
                if (district_id != '' && option.target == 'districts') {
                    $('.districts').val(district_id).trigger('change');
                }

                // Tương tự như trên nhưng cho 'wards'
                if (ward_id != '' && option.target == 'wards') {
                    $('.wards').val(ward_id).trigger('change');
                }
            },
            // Xử lý lỗi nếu có khi gửi yêu cầu AJAX
            erorr: function(jqXHR, textStatus, errorThrown) {
                console.log('Lỗi: ' + textStatus + ' ' + errorThrown)
            }
        });
    }

    HT.renderCustomerOrderInformation = () => {
        data = {
            fullname: $('.fullname').text(),
            email: $('.email').text(),
            phone: $('.phone').text(),
            address: $('.address').text(),
            ward_name: $('.ward_name').text(),
            district_name: $('.district_name').text(),
            province_name: $('.province_name').text(),
            ward_id: $("input[name=ward_id]").val() || '',
            province_id: $("input[name=province_id]").val() || '',
            district_id: $("input[name=district_id]").val() || '',
        };

        let html =  `
            <div class="form-row mb15">
                <label for="">Họ Tên</label>
                <input type="text" name="fullname" class="form-control" value="${data.fullname}"> 
            </div>
            <div class="form-row mb15">
                <label for="">Email</label>
                <input type="text" name="email" class="form-control" value="${data.email}"> 
            </div>
            <div class="form-row mb15">
                <label for="">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="${data.phone}"> 
            </div>
            <div class="form-row mb15">
                <label for="">Địa chỉ</label>
                <input type="text" name="address" class="form-control" value="${data.address}"> 
            </div>
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Thành phố</label>
                <select name="province_id" class="form-control setupSelect2 province location" data-target="districts" id="">
                    ${HT.provinceList(data.province_id)}
                </select>
            </div>
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Quận/Huyện</label>
                <select name="district_id" class="form-control setupSelect2 districts location" data-target="wards" id="">
                    <option value="0">[Chọn Quận\Huyện]</option>
                </select>
            </div>
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Phường/Xã</label>
                <select name="ward_id" class="form-control setupSelect2 wards" id="">
                    <option value="0">[Chọn Phường/Xã]</option>
                </select>
            </div>
            <div class="form-row">
                <button class="btn btn-primary save-info">Lưu lại</button>
            </div>
        `;

        setTimeout(() => {
            HT.loadCity(data.province_id)
        }, 0)

        return html;
    };
    
    HT.renderDescriptionOrder = (_this) => {
        let value = _this.closest('.ibox').find('.ibox-content').text().trim();
        return `
            <input class="form-control ajax-edit" name="description" data-field="description" value="${value}">
        `;
    };

    HT.updateDescription = () => {
        $(document).on('change', '.ajax-edit', function () {
            let _this = $(this),
                field = _this.data('field'),
                value = _this.val();

            let option = {
                id: $("input[name='order_id']").val(),
                payload: {
                    [field]: value,
                }
            };

            HT.ajaxUpdateOrderInfo(option, _this);
        });
    };

    HT.ajaxUpdateOrderInfo = (option, _this) => {
        $.ajax({
            url: 'ajax/order/update',
            method: 'POST',
            data: option,
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': _token // Add CSRF token to the request headers
            },
            success: function (res) {
                if (res.code == 10) {
                    if (_this.closest('.ibox').find('.edit-order').data('target') === 'description') {
                        HT.renderDescriptionHtml(option.payload, _this.closest('.ibox'));
                        data.description = option.payload.description;
                    } else if (_this.closest('.ibox').find('.edit-order').data('target') === 'customerInfo') {
                        data.fullname = res.order.fullname;
                        data.email = res.order.email;
                        data.phone = res.order.phone;
                        data.address = res.order.address;
                        data.ward_name = res.order.ward_name;
                        data.district_name = res.order.district_name;
                        data.province_name = res.order.province_name;
                        data.ward_id = res.order.ward_id ;
                        data.district_id = res.order.district_id;
                        data.province_id = res.order.province_id;
                        HT.renderCustomerInfoHtml(res.order);
                    }
                }
            },
        });
    };

    HT.renderCustomerInfoHtml = (order) => {
        let html = `
            <div class="custom-line">
                <strong>N: </strong>
                <span class="fullname">${order.fullname}</span>
            </div>
            <div class="custom-line">
                <strong>E: </strong>
                <span class="email">${order.email}</span>
            </div>
            <div class="custom-line">
                <strong>P: </strong>
                <span class="phone">${order.phone}</span>
            </div>
            <div class="custom-line">
                <strong>A: </strong>
                <span class="address">${order.address}</span>
            </div>
            <div class="custom-line">
                <strong>P: </strong>
                <span class="ward_name">${order.ward_name}</span>
            </div>
            <div class="custom-line">
                <strong>Q: </strong>
                <span class="district_name">${order.district_name}</span>
            </div>
            <div class="custom-line">
                <strong>T: </strong>
                <span class="province_name">${order.province_name}</span>
            </div>
        `
        $(".order-customer-info").html(html);
        $("input[name=province_id]").val(order.province_id);
        $("input[name=district_id]").val(order.district_id);
        $("input[name=ward_id]").val(order.ward_id);
    }

    HT.renderDescriptionHtml = (payload, target) => {
        let html = `
            <div class="description">
                ${payload.description}
            </div>
        `;

        target.find('.ibox-content').html(html);
    };

    HT.bindClickOutside = () => {
        $(document).on('click', function (event) {
            const $target = $(event.target);
            if (
                !$target.closest('.ibox[data-type="orderTarget"]').length
            ) { 
                const $input = $('.ajax-edit');
                if ($input.length) {
                    const field = $input.data('field');
                    const value = $input.val();
                    let option = {}
                    
                    if (field == 'description') {
                        option = {
                            id: $("input[name='order_id']").val(),
                            payload: {
                                [field]: value,
                            }
                        };
                        HT.ajaxUpdateOrderInfo(option, $input);
                    }
                }

                if (typeof data !== 'undefined' && Object.keys(data).length > 0) {
                    HT.renderCustomerInfoHtml(data);
                }
            }
        });
    };

    HT.select2 = () => {
        if ($('.setupSelect2').length) {
            $('.setupSelect2').select2();
        }
    };

    HT.saveCustomer = () => {
        $(document).on('click', '.save-info', function(e) {
            e.preventDefault();
            let _this = $(this);
            let option = {
                id: $("input[name='order_id']").val(),
                payload: {
                    fullname : $("input[name='fullname']").val(),
                    email : $("input[name='email']").val(),
                    phone : $("input[name='phone']").val(),
                    address : $("input[name='address']").val(),
                    ward_id : $("ward_id").val(),
                    district_id : $(".districts").val(),
                    province_id : $(".province").val(),
                    ward_id : $(".wards").val(),
                }
            };

            HT.ajaxUpdateOrderInfo(option, _this);
        })

    }

    HT.updateFeild = () => {
        $(document).on('click', '.updateField', function() {
            let _this = $(this)
            let option = {
                payload: {
                    [_this.data('field')] : _this.data('confirm')
                },
                id : $("input[name=order_id]").val(),
            }
            $.ajax({
                url: 'ajax/order/update',
                method: 'POST',
                data: option,
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': _token // Add CSRF token to the request headers
                },
                success: function (res) {
                    if (res.code == 10) {
                        HT.createOrderConfirmSection(_this);
                    }
                },
            })
        })

        if ($('.nofiConfirm').length && !$('.nofiCancel').length) {
            let button = `
                <button class="btn btn-danger updateField" data-field="confirm" data-confirm="cancel" data-title="ĐÃ HỦY THANH TOÁN ĐƠN HÀNG">Hủy đơn</button>
            `;
            $('.cancle-block').html(button)
        }
    }

    HT.createOrderConfirmSection = (_this) => {
        let button = `
            <button class="btn btn-danger updateField" data-field="confirm" data-confirm="cancel" data-title="ĐÃ HỦY THANH TOÁN ĐƠN HÀNG">Hủy đơn</button>
        `;
        let correctImage = '/backend/img/correct.png'

        if (_this.data('confirm') == 'confirm') {
            $('.confirm-block').html('<span class="nofiConfirm">Đã xác nhận</span>')
            $('.cancle-block').html(button)
        }

        if (_this.data('confirm') == 'cancel') {
            _this.closest('.cancle-block').html('<span class="nofiCancel">Đơn hàng đã bị hủy</span>')
        }

        $('.isConfirm').html(_this.data('title'))
        $('.confirm-box').find('img').attr('src', BASE_URL + correctImage);
    }

    HT.updateBadge = () => {
        $(document).on('change', '.updateBadge', function() {
            let _this = $(this)
            let option = {
                payload: {
                    [_this.data('field')] : _this.val(),
                },
                id : _this.parents('tr').find('.checkBoxItem').val(),
            }

            let confirmStatus = _this.parents('tr').find('.confirm').val()
            toastr.clear()
            
            if (confirmStatus != 'pending') {
                $.ajax({
                    url: 'ajax/order/update',
                    method: 'POST',
                    data: option,
                    dataType: 'JSON',
                    headers: {
                        'X-CSRF-TOKEN': _token // Add CSRF token to the request headers
                    },
                    success: function (res) {
                        if (res.code === 10){
                            toastr.success('Cập nhật trạng thái thành công', 'Thông báo từ hệ thống!')
                        } else {
                            toastr.error('Có vấn đề xảy ra! Hãy thử lại', 'Thông báo từ hệ thống!')
                        }
                    },
                })
            } else {
                let originalStatus = _this.siblings('.changeOrderStatus');
                _this.val(originalStatus.val());

                let option = _this.find(`option[value="${originalStatus.val()}"]`);
                if (option.length) {
                    option.text(originalStatus.data('title'));
                }

                _this.select2({
                    data: _this.select2('data')
                });
                toastr.error('Bạn phải xác nhận đơn hàng trước khi thực hiện cập nhật này!', 'Thông báo từ hệ thống!')
            }
        })
    }
 
    $(document).ready(function () {
        HT.select2();
        HT.editOrder();
        HT.saveCustomer();
        HT.getLocation();
        HT.updateFeild();
        HT.updateBadge();
    });

})(jQuery);
