(function($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf-token"]').attr('content');

    HT.switchery = () => {
        $('.js-switch').each(function(){
            var switchery = new Switchery(this, { color: '#1AB394', size: 'small' });
        })
    }

    HT.select2 = () => {
        if ($('.setupSelect2').length) {
            $('.setupSelect2').select2();
        }
    }

    HT.sortui = () => {
        $("#sortable").sortable();
        $("#sortable").disableSelection();
    }

    HT.changeStatus = () => {
        $(document).on('change', '.status', function(e) {
            let _this = $(this);
            let option = {
                'value' : _this.val(),
                'modelId' : _this.attr('data-model-id'),
                'model' : _this.attr('data-model'),
                'field' : _this.attr('data-field'),
                '_token' : _token
            }
 
            $.ajax({
                url: '/ajax/dashboard/changeStatus',
                type: 'POST',  
                data: option,  
                dataType: 'json',  
                success: function(res) {
                    let inputValue = ((option.value == 1) ? 2 : 1)
                    if (res.flag == true) {
                        _this.val(inputValue)
                    }
                },
                erorr: function(jqXHR, textStatus, errorThrown) {
                    console.log('Lỗi: ' + textStatus + ' ' + errorThrown)
                }
            });
        })
    }

    HT.checkAll = () => {
        if ($('#checkAll').length) {
            $(document).on('click', '#checkAll', function() {
                // Thiết lập giá trị của thuộc tính
                let isChecked = $(this).prop('checked');

                $('.checkBoxItem').prop('checked', isChecked);
                $('.checkBoxItem').each(function() {
                    let _this  = $(this);
                    HT.checkBackground(_this);
                })
            })
        }
    }

    HT.checkBoxItem = () => {
        if ($(".checkBoxItem").length) {
            $(document).on('change', '.checkBoxItem', function() {
                var _this = $(this);
                HT.checkBackground(_this);
                HT.allChecked();
            })
        }
    }

    HT.checkBackground = (object) => {
        let isChecked = object.prop('checked');
        if (isChecked) {
            object.closest('tr').addClass('active-bg')
        } else {
            object.closest('tr').removeClass('active-bg')
        }
    }

    HT.allChecked = () => {
        let allChecked = $('.checkBoxItem:Checked').length === $('.checkBoxItem').length;
        $('#checkAll').prop('checked', allChecked);
    }

    HT.changeStatusAll = () => {
        if ($('.changeStatusAll').length) {
            $(document).on('click', '.changeStatusAll', function(e) {
                e.preventDefault();
    
                let _this = $(this);
                let id = [];
                $('.checkBoxItem').each(function() {
                    let checkBox = $(this);
                    if (checkBox.prop('checked')) {
                        id.push(checkBox.val());
                    }
                });
    
                let option = {
                    'value': _this.attr('data-value'),
                    'field': _this.attr('data-field'),
                    'model': _this.attr('data-model'),
                    'id': id,
                    '_token': _token
                };
    
                $.ajax({
                    url: '/ajax/dashboard/changeStatusAll',
                    type: 'POST',
                    data: option,
                    dataType: 'json',
                    success: function(res) {
                        let cssActive1 = "background-color: rgb(26, 179, 148); border-color: rgb(26, 179, 148); box-shadow: rgb(26, 179, 148) 0px 0px 0px 16px inset; transition: border 0.4s, box-shadow 0.4s, background-color 1.2s;";
                        let cssInactive1 = "box-shadow: rgb(223, 223, 223) 0px 0px 0px 0px inset; border-color: rgb(223, 223, 223); background-color: rgb(255, 255, 255); transition: border 0.4s, box-shadow 0.4s;";
                        let cssActive2 = "left: 13px; background-color: rgb(255, 255, 255); transition: background-color 0.4s, left 0.2s;";
                        let cssInactive2 = "left: 0px; background-color: rgb(255, 255, 255); transition: background-color 0.4s, left 0.2s;";
                
                        id.forEach(function(userId) {
                            var checkbox = $('.js-switch-' + userId)[0]; // Lấy phần tử checkbox DOM
                
                            // Kiểm tra nếu input bị disable, bỏ qua không làm gì cả
                            if ($(checkbox).prop('disabled')) {
                                return; // Bỏ qua checkbox này
                            }
                
                            if (option.value == 2) { // Khi giá trị là 2, set active
                                $(checkbox).val(1);
                                if (!checkbox.checked) {
                                    checkbox.checked = true;
                                    $(checkbox).next('.switchery').attr('style', cssActive1).find('small').attr('style', cssActive2);
                                }
                            } else { // Khi giá trị là 1, set inactive
                                $(checkbox).val(1);
                                if (checkbox.checked) {
                                    checkbox.checked = false;
                                    $(checkbox).next('.switchery').attr('style', cssInactive1).find('small').attr('style', cssInactive2);
                                }
                            }
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Lỗi: ' + textStatus + ' ' + errorThrown);
                    }
                });
            });
        }
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
    
    HT.setupDatepicker = () => {
        $('.datepicker').each(function() {
            const inputName = $(this).attr('name');
            const inputValue = $(this).data('value') || $(this).val() || new Date();
            $(this).datetimepicker({
                timepicker: true,
                format: 'd/m/y H:i',
                value: inputValue,
                minDate: new Date()
            });
        });
    };

    $(document).ready(function() {
        HT.switchery();
        HT.select2();
        HT.changeStatus();
        HT.checkAll();
        HT.checkBoxItem();
        HT.allChecked();
        HT.changeStatusAll();
        HT.sortui();
        HT.formatInputNumberVariant();
        HT.setupDatepicker();
    })
})(jQuery)