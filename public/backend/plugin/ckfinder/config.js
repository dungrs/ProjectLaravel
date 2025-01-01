/*
Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
For licensing, see license.txt or http://cksource.com/ckfinder/license
*/

CKFinder.customConfig = function( config )
{
	// Define changes to default configuration here.
	// For the list of available options, check:
	// http://docs.cksource.com/ckfinder_2.x_api/symbols/CKFinder.config.html

	// Sample configuration options:
	// config.uiColor = '#BDE31E';
	// config.language = 'fr';
	// config.removePlugins = 'basket';

	// Cấu hình định dạng tệp tin được phép tải lên
    config.fileTypes = {
        images: ['bmp', 'gif', 'jpeg', 'jpg', 'png', 'tiff', 'webp'], // Thêm WebP
        files: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar']
    };

    // Kích hoạt hiển thị các tệp tin WebP trong CKFinder
    config.extraAllowedContent = 'img[src|alt|title|width|height|data-*];';

};
