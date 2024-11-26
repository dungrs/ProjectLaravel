(function($) {
    "use strict"
    var HT = {};

    HT.seoPreview = () => {
        // Cập nhật meta title khi người dùng nhập
        $('input[name=meta_title]').on('keyup', function() {
            let input = $(this);
            let val = input.val();
            $('.meta-title').html(val);
        });

        // Điều chỉnh padding-left của seo-canonical theo chiều rộng của baseUrl
        $('.seo-canonical').css({
            'padding-left': parseInt($('.baseUrl').outerWidth()) + 10
        });

        // Cập nhật bản xem trước của canonical khi người dùng nhập
        $('.seo-canonical').on('keyup', function() {
            let input = $(this);
            let val = HT.removeUtf8(input.val());
            $('.canonical').html(BASE_URL + '/' + val + SUFFIX);
        });

        // Cập nhật meta description khi người dùng nhập
        $('textarea[name=meta_description]').on('keyup', function() {
            let input = $(this);
            let val = input.val();
            $('.meta-description').html(val);
        });

        // Trước khi submit form, sửa lại giá trị canonical
        $('form').on('submit', function() {
            let canonicalInput = $('input[name=canonical]');
            let val = HT.removeUtf8(canonicalInput.val());

            // Cập nhật lại giá trị canonical với dữ liệu đã được xử lý
            canonicalInput.val(val);
        });
    };

    // Hàm removeUtf8 để loại bỏ ký tự không hợp lệ và xử lý UTF-8
    HT.removeUtf8 = (str) => {
        // Chuyển về chữ thường
        str = str.toLowerCase();

        // Thay thế các ký tự có dấu thành không dấu
        str = str.replace(/à|á|ả|ạ|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
        str = str.replace(/è|é|ẻ|ẹ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
        str = str.replace(/ì|í|ỉ|ị|ĩ/g, "i");
        str = str.replace(/ò|ó|ỏ|ọ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
        str = str.replace(/ù|ú|ủ|ụ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
        str = str.replace(/ỳ|ý|ỷ|ỵ|ỹ/g, "y");
        str = str.replace(/đ/g, "d");

        // Loại bỏ các ký tự đặc biệt
        str = str.replace(/[^a-z0-9\s\-]/g, ''); // Giữ lại ký tự chữ cái thường, số, khoảng trắng, và dấu gạch ngang
        str = str.replace(/\s+/g, '-'); // Thay thế các khoảng trắng bằng dấu gạch ngang
        str = str.replace(/-+/g, "-"); // Thay thế nhiều dấu gạch ngang liên tiếp thành một dấu
        str = str.replace(/^-+|-+$/g, ""); // Loại bỏ dấu gạch ngang ở đầu và cuối chuỗi

        return str;
    };

    // Gọi HT.seoPreview khi tài liệu đã sẵn sàng
    $(document).ready(function() {
        HT.seoPreview();
    });

})(jQuery);
