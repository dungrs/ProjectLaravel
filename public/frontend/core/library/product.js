(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;

	HT.selectVariantProduct = () => {
		if ($('.choose-attribute').length) {
			$(document).on('click', '.choose-attribute', function (e) {
				e.preventDefault();
				let _this = $(this);
				let attribute_name = _this.text();
				_this.parents('.attribute-item').find('span').html(attribute_name);
				_this.parents('.attribute-value').find('.choose-attribute').removeClass('active');
				_this.addClass('active');
				HT.handleAttribute();
			});
		}
	}
	
	HT.handleAttribute = () => {
		let attribute_id = [];
		let activeCount = 0;
		let totalAttributes = $('.attribute-value').length;
	
		// Duyệt qua tất cả các `choose-attribute` để lấy giá trị `data-attributeid` nếu có class `active`
		$('.attribute-value .choose-attribute').each(function () {
			let _this = $(this);
			if (_this.hasClass('active')) {
				attribute_id.push(_this.data('attributeid'));
				activeCount++;
			}
		});
	
		// Kiểm tra xem tất cả các `attribute-value` có thẻ `a` được active hay chưa
		if (activeCount === totalAttributes) {
			$.ajax({
				url: 'ajax/product/loadVariant',
				type: 'GET',
				data: {
					'attribute_id': attribute_id,
					'product_id': $("input[name=product_id]").val(),
					'language_id': $("input[name=language_id]").val()
				},
				dataType: 'json',
				beforeSend: function () {
					// Bạn có thể hiển thị loader tại đây nếu cần
				},
				success: function (res) {
					let object = res.object;
					HT.setupVariantPrice(object);
					HT.setupVariantName(object);
					HT.setupVariantUrl(object, attribute_id);
				},
				error: function (err) {
					console.error(err);
				}
			});
		} else {
			console.log('Chưa chọn đầy đủ thuộc tính.');
		}
		
	}

	HT.setupVariantUrl = (object, attribute_id) => {
		let queryString = '?attribute_id=' + attribute_id.join(',')
		let productCanonical = $('.productCanonical').val();
		productCanonical = productCanonical + queryString;
		let stateObject = {attribute_id : attribute_id}
		history.pushState(stateObject, "Page Title", productCanonical)
	}

	HT.setupVariantPrice = (object) => {
		let price = object.price;
		if (object.promotion[0]) {
			let promotion = object.promotion[0];
			let discountValue = promotion.discountValue;
			let finalDiscount = promotion.finalDiscount;
			let priceDiscount = price - finalDiscount;

			// Định dạng giá sau khi khuyến mãi (với dấu phẩy)
			let formattedPriceDiscount = priceDiscount.toLocaleString('vi-VN'); // Định dạng giá
			let formattedPrice = price.toLocaleString('vi-VN'); // Định dạng giá cũ

			// Cập nhật giá sau khi áp dụng khuyến mãi
			$('.price-sale').text(formattedPriceDiscount + ' đ');
			$('.price-old').text(formattedPrice + ' đ'); // Hiển thị giá cũ
		} else {
			// Nếu không có khuyến mãi, chỉ hiển thị giá gốc
			let formattedPrice = price.toLocaleString('vi-VN'); // Định dạng giá
			$('.price-sale').text(formattedPrice + ' đ');
		}
	}

	HT.setupVariantName = (object) => {
		let productName = $('.product-name').val();
		let productVariantName = productName + ' ' + object.name
		$('.product-main-title span').html(productVariantName);
	}

	HT.loadProductVariant = () => {
		let attributeCatalogue = JSON.parse($('.attributeCatalogue').val())
		if (attributeCatalogue.length && typeof attributeCatalogue) {
			HT.handleAttribute();
		}
	}
	
	
	HT.changeProductQuantity = () => {
		$(document).on('click','.quantity-button', function(){
			let _this = $(this)
			let quantity = $('.quantity-text').val()
			let newQuantity = 0
			if(_this.hasClass('minus')){
				 newQuantity =  quantity - 1
			}else{
				 newQuantity = parseInt(quantity) + 1
			}
			if(newQuantity < 1){
				newQuantity = 1
			}
			$('.quantity-text').val(newQuantity)
		})
	}

	$(document).ready(function(){
		HT.selectVariantProduct()
		HT.loadProductVariant()
		HT.changeProductQuantity();		
	});

})(jQuery);