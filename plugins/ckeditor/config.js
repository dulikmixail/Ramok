/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	config.uiColor = '#999999';
  	//config.toolbar = 'Basic';
	
	config.toolbar_Basic = 
	[['Source'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar'],
    '/',
    ['Bold','Italic','Underline','Strike'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['Link','Unlink','Anchor'],
    '/',
    ['Format','Font','FontSize'],
    ['TextColor','BGColor']];

	config.resize_enabled = false;
	config.enterMode = CKEDITOR.ENTER_BR;
	config.removePlugins = 'elementspath';	
};
