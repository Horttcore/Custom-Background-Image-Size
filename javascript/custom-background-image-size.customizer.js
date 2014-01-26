jQuery(document).ready(function(){

	plugin = {

		init:function(){

			// Cache elements
			plugin.backgroundImageSize = jQuery('input[name="_customize-radio-background-image-size"]');
			plugin.currentBackgroundImageSize = jQuery('input[name="_customize-radio-background-image-size"]:checked');
			plugin.heightContainer = jQuery('#customize-control-background-image-height');
			plugin.height = jQuery('#customize-control-background-image-height input');
			plugin.widthContainer = jQuery('#customize-control-background-image-width');
			plugin.width = jQuery('#customize-control-background-image-width input');

			// Bind events
			plugin.bindEvents();
			plugin.updateView( true );

		},

		bindEvents:function(){

			plugin.backgroundImageSize.change(function(){
				plugin.currentBackgroundImageSize = jQuery('input[name="_customize-radio-background-image-size"]:checked');
				plugin.updateView( false );
			});

		},

		updateView:function( init ){

			console.log( plugin.currentBackgroundImageSize.val() );

			if ( 'custom' == plugin.currentBackgroundImageSize.val() ) {
				plugin.heightContainer.show();
				plugin.widthContainer.show();
				if ( true !== init )
					plugin.width.focus();
			} else {
				plugin.heightContainer.hide();
				plugin.height.val('');
				plugin.widthContainer.hide();
				plugin.width.val('');
			}
		}

	}

	plugin.init();

});