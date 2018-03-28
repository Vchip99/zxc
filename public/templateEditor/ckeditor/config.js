/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config
	var base_url = window.location.origin;
	var fullUrl = window.location.host;
	var parts = fullUrl.split('.');
	if('localvchip' == parts[0] || 'vchipedu' == parts[0]){
		var type = 'images';
		var dir  = 'images';
	} else {
		var type = 'images';
		var dir  = 'images/'+parts[0];
	}
	config.filebrowserBrowseUrl = base_url +'/templateEditor/kcfinder/browse.php?opener=ckeditor&type=files&dir='+dir+'';
    config.filebrowserImageBrowseUrl = base_url +'/templateEditor/kcfinder/browse.php?opener=ckeditor&type='+type+'&dir='+dir+'';
    config.filebrowserFlashBrowseUrl = base_url +'/templateEditor/kcfinder/browse.php?opener=ckeditor&type=flash&dir='+dir+'';
    config.filebrowserUploadUrl = base_url +'/templateEditor/kcfinder/upload.php?opener=ckeditor&type=files&dir='+dir+'';
    config.filebrowserImageUploadUrl = base_url +'/templateEditor/kcfinder/upload.php?opener=ckeditor&type='+type+'&dir='+dir+'';
    config.filebrowserFlashUploadUrl = base_url +'/templateEditor/kcfinder/upload.php?opener=ckeditor&type=flash&dir='+dir+'';

	config.baseHref = base_url;
	 // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
	config.toolbar = [
		{ name: 'links', items: [ 'Link' ] },
		// { name: 'insert' },
		{ name: 'insert', items: [ 'Image', 'Youtube', 'EqnEditor'] },
		{ name: 'basicstyles', groups: [ 'basicstyles' ], items: [ 'Bold', 'Italic' ] },
		{ name: 'paragraph', groups: [ 'list' ], items: [ 'NumberedList', 'BulletedList' ] }
	];

	config.allowedContent = true;
	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	// config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	// config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	// config.autoParagraph = false;
	config.removePlugins = 'pastefromword';
	config.forcePasteAsPlainText = true;
	config.extraPlugins = 'youtube,eqneditor';

};
