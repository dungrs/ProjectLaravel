(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;

	HT.swiperOption = (setting) => {
		// console.log(setting);
		let option = {}
		if(setting.animation.length){
			option.effect = setting.animation;
		}	
		if(setting.arrow === 'accept'){
			option.navigation = {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			}
		}
		if(setting.autoplay === 'accept'){
			option.autoplay = {
			    delay: 2000,
			    disableOnInteraction: false,
			}
		}
		if(setting.navigate === 'dots'){
			option.pagination = {
				el: '.swiper-pagination',
			}
		}
		return option
	}
	
	/* MAIN VARIABLE */
	HT.swiper = () => {
		if($('.panel-slide').length){
			let setting = JSON.parse($('.panel-slide').attr('data-setting'))
			let option = HT.swiperOption(setting)
			var swiper = new Swiper(".panel-slide .swiper-container", option);
		}
		
	}

	HT.swiperCategory = () => {
		var swiper = new Swiper(".panel-category .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 3,
			breakpoints: {
				415: {
					slidesPerView: 3,
				},
				500: {
				  slidesPerView: 3,
				},
				768: {
				  slidesPerView: 6,
				},
				1280: {
					slidesPerView: 10,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			
		});
	}

	HT.swiperProduct = () => {
		// Swiper for thumbnails
		var thumbsSwiper = new Swiper('.swiper-container-thumbs', {
			slidesPerView: 4, // Hiển thị 4 ảnh nhỏ
			spaceBetween: 10, // Khoảng cách giữa các slide nhỏ
			freeMode: true, // Kích hoạt chế độ kéo tự do
			watchSlidesVisibility: true, // Cập nhật trạng thái slide hiển thị
			watchSlidesProgress: true, // Cập nhật trạng thái tiến trình
		});
	
		// Swiper for main gallery
		var mainSwiper = new Swiper('.swiper-container', {
			spaceBetween: 10, // Khoảng cách giữa các slide chính
			navigation: {
				nextEl: '.swiper-button-next', // Nút "next"
				prevEl: '.swiper-button-prev', // Nút "prev"
			},
			pagination: {
				el: '.swiper-pagination', // Phân trang
				clickable: true, // Phân trang có thể nhấp
			},
			thumbs: {
				swiper: thumbsSwiper, // Liên kết với Swiper thumbnails
			},
			autoplay: {
				delay: 2000, // Tự động chuyển slide mỗi 2 giây
				disableOnInteraction: false, // Không tắt autoplay khi tương tác
			},
		});
	}

	HT.swiperBestSeller = () => {
		var swiper = new Swiper(".panel-bestseller .swiper-container", {
			loop: false,
			pagination: {
				el: '.swiper-pagination',
			},
			spaceBetween: 20,
			slidesPerView: 2,
			breakpoints: {
				415: {
					slidesPerView: 1,
				},
				500: {
				  slidesPerView: 2,
				},
				768: {
				  slidesPerView: 3,
				},
				1280: {
					slidesPerView: 4,
				}
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			
		});
	}

	HT.wow = () => {
		var wow = new WOW(
			{
			  boxClass:     'wow',      // animated element css class (default is wow)
			  animateClass: 'animated', // animation css class (default is animated)
			  offset:       0,          // distance to the element when triggering the animation (default is 0)
			  mobile:       true,       // trigger animations on mobile devices (default is true)
			  live:         true,       // act on asynchronously loaded content (default is true)
			  callback:     function(box) {
				// the callback is fired every time an animation is started
				// the argument that is passed in is the DOM node being animated
			  },
			  scrollContainer: null,    // optional scroll container selector, otherwise use window,
			  resetAnimation: true,     // reset animation on end (default is true)
			}
		  );
		  wow.init();


	}// arrow function

	HT.niceSelect = () => {
		if($('.nice-select').length){
			$('.nice-select').niceSelect();
		}
		
	}

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
	
	
	HT.changeQuantity = () => {
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
		HT.wow()
		HT.swiperCategory()
		HT.swiperBestSeller()
		HT.swiperProduct()
		HT.selectVariantProduct()
		HT.loadProductVariant()
		HT.changeQuantity();		
		/* CORE JS */
		HT.swiper()
		HT.niceSelect()		
	});


})(jQuery);



addCommas = (nStr) => { 
    nStr = String(nStr);
    nStr = nStr.replace(/\./gi, "");
    let str ='';
    for (let i = nStr.length; i > 0; i -= 3){
        let a = ( (i-3) < 0 ) ? 0 : (i-3);
        str= nStr.slice(a,i) + '.' + str;
    }
    str= str.slice(0,str.length-1);
    return str;
}