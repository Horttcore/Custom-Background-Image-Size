jQuery(document).ready(function(){

	plugin = {

		init:function(){

			plugin.form = jQuery('#save-background-options').closest('form');

			if ( undefined != plugin.form ) {

				plugin.container = false
				plugin.backgroundSizeInput = false;
				plugin.backgroundSizeCustom = false;
				plugin.backgroundImageHeight = false;
				plugin.backgroundImageWidth = false;
				plugin.currentBackgroundSize = false;
				plugin.preview = jQuery('#custom-background-image');
				plugin.saveValue = 'auto auto';
				plugin.sendForm = false;

				plugin.extendDOM();
			}

		},

		bindEvents:function(){

			// Toggle custom values
			plugin.currentBackgroundSize = plugin.container.find( 'input:checked' );
			plugin.updatePreview();
			plugin.backgroundSizeInput.change(function(){

				plugin.currentBackgroundSize = plugin.container.find( 'input:checked' );

				if ( 'custom' == plugin.currentBackgroundSize.val() ) {
					plugin.backgroundImageWidth.focus();
				} else {
					plugin.backgroundImageHeight.val('');
					plugin.backgroundImageWidth.val('');
				}

				plugin.updatePreview();

			});

			// Custom value preview update
			plugin.backgroundImageWidth.keyup(function(){
				plugin.backgroundSizeCustom.attr('checked','checked');
				plugin.updatePreview();
			});

			plugin.backgroundImageHeight.keyup(function(){
				plugin.backgroundSizeCustom.attr('checked','checked');
				plugin.updatePreview();
			});

			// Save
			plugin.form.submit(function(e){

				if ( false == plugin.sendForm ) {
					e.preventDefault();

					var data = {
						action:'set-background-image-size',
						backgroundImageSize: plugin.currentBackgroundSize.val(),
						backgroundImageHeight: plugin.backgroundImageHeight.val(),
						backgroundImageWidth: plugin.backgroundImageWidth.val()
					}

					jQuery.post(ajaxurl, data, function(response){
						plugin.sendForm = true;
						plugin.form.submit();
					});
				}

			});

		},

		extendDOM:function(){

			var autoChecked = ( 'auto auto' == customBackgroundImageSizeOptions.value ) ? 'checked="checked"' : '',
				customChecked = ( 'custom' == customBackgroundImageSizeOptions.value ) ? 'checked="checked"' : '',
				containChecked = ( 'contain' == customBackgroundImageSizeOptions.value ) ? 'checked="checked"' : '',
				coverChecked = ( 'cover' == customBackgroundImageSizeOptions.value ) ? 'checked="checked"' : '',

			html =
				'<tr class="background-image-size-container">' +
					'<th scope="row">' +
						customBackgroundImageSizeOptions.imageSize +
					'</th>' +
					'<td>' +
						'<fieldset>' +
							'<legend class="screen-reader-text"><span>' + customBackgroundImageSizeOptions.backgroundSize + '</span></legend>' +
							'<label><input name="background-size" ' + autoChecked + ' type="radio" value="auto auto">' + customBackgroundImageSizeOptions.auto + '</label> ' +
							'<label><input name="background-size" ' + containChecked + ' type="radio" value="contain">' + customBackgroundImageSizeOptions.contain + '</label> ' +
							'<label><input name="background-size" ' + coverChecked + ' type="radio" value="cover">' + customBackgroundImageSizeOptions.cover + '</label> ' +
							'<label>' +
								'<input name="background-size" ' + customChecked + ' type="radio" value="custom">' + customBackgroundImageSizeOptions.custom +
								': <input type="text" name="background-image-width" placeholder="' + customBackgroundImageSizeOptions.width + '" value="' + customBackgroundImageSizeOptions.backgroundImageWidth + '">' +
								'<input type="text" name="background-image-height" placeholder="' + customBackgroundImageSizeOptions.height + '" value="' + customBackgroundImageSizeOptions.backgroundImageHeight + '">' +
							'</label> ' +
						'</fieldset>' +
					'</td>' +
				'</tr>';

			jQuery('input[name="background-attachment"]')
				.parent()
				.parent()
				.parent()
				.parent()
				.after(html);

			plugin.container = jQuery('.background-image-size-container');
			plugin.backgroundSizeInput = plugin.container.find('input[name="background-size"]');
			plugin.backgroundSizeCustom = plugin.container.find('input[value="custom"]');
			plugin.backgroundImageHeight = plugin.container.find('input[name="background-image-height"]');
			plugin.backgroundImageWidth = plugin.container.find('input[name="background-image-width"]');

			plugin.bindEvents();

		},

		updatePreview:function(){

			plugin.saveValue = ( 'custom' == plugin.currentBackgroundSize.val() ) ? plugin.backgroundImageWidth.val() + ' ' + plugin.backgroundImageHeight.val() : plugin.currentBackgroundSize.val();

			plugin.preview.css({
				backgroundSize: plugin.saveValue,
			});

		}

	}

	plugin.init();

});