(function($) {
    "use strict";
    var HT = {};  // Khởi tạo một object HT để chứa các phương thức

    // Phương thức để bắt sự kiện 'change' trên các thành phần có class 'location'
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

    // Phương thức gửi yêu cầu AJAX để lấy dữ liệu vị trí từ server
    HT.sendDataTogetLocation = (option) => {
        $.ajax({
            url: '/ajax/location/getLocation',  // URL để gửi yêu cầu
            type: 'GET',  // Phương thức gửi yêu cầu (GET)
            data: option,  // Dữ liệu cần gửi đến server
            dataType: 'json',  // Định dạng dữ liệu nhận về từ server (JSON)
            success: function(res) {  // Xử lý khi nhận được phản hồi thành công từ server
                // Chèn HTML trả về từ server vào thành phần mục tiêu
                $('.' + option.target).html(res.html);

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

    // Phương thức tự động tải và chọn tỉnh/thành phố nếu có sẵn giá trị province_id
    HT.loadCity = () => {
        if (province_id != '') {
            $(".province").val(province_id).trigger("change");
        }
    }

    // Khi document đã sẵn sàng (DOMContentLoaded)
    $(document).ready(function() {
        HT.getLocation();  // Gọi phương thức để lắng nghe sự kiện thay đổi vị trí
        HT.loadCity();  // Gọi phương thức để tự động tải tỉnh/thành phố nếu có
    })
})(jQuery)
