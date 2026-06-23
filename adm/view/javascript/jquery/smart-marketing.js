$(document).delegate('.loading-mask-trigger', 'click', function() {
	$('.loading-mask-overlay').show();
});

$(document).delegate('#btn-smc-editor', 'click', function() {
	SMDesignEditor('show');
});

$(document).delegate('#modal-template .template-thumbnail', 'click', function() {
	var provider = $(this).attr('data-provider');
	var template_id = $(this).attr('data-template-id');

	$('#modal-template').modal('hide');

	SMImportTemplate(provider, template_id);
	SMDisableGridLines();
});

$(document).delegate('#modal-template .template-preview', 'click', function(e) {
	e.stopPropagation();

	var provider = $(this).parent().attr('data-provider');
	var template_id = $(this).parent().attr('data-template-id');

	$('#modal-template').modal('hide');

	SMPreviewTemplate(provider, template_id);
});

$(document).delegate('#template-previewer #close-preview', 'click', function() {
	$('#template-previewer').remove();
	$('#container').removeClass('hidden');

	$('#modal-template').modal('show');
});

$(document).delegate('.btn-send-test', 'click', function() {
	var email_type = (typeof($(this).attr('data-format')) != 'undefined' ? $(this).attr('data-format') : 'html');

	SMShowTestEmailModal(email_type);
});

$(document).ajaxComplete(function(event, request, settings) {
	if (settings.url.match(/.*smart_marketing\/(template)\/(manager)/)) {
		SMEqualHeightColumns();
	}
});

// ----- FUNCTIONS ------------------
function SMDesignEditor(action = 'show') {
	if (action == 'show') {
		$('.smc-editor').addClass('active');

		// scroll to top of page - fix colorpicker position
		//$('html, body').animate({ scrollTop: 0 }, 'fast');

		// minimize page content from background
		$('#container #content').addClass('minimize-bg-content');

	} else {
		$('.smc-editor').removeClass('active');

		// scroll back to editor
		//$('html, body').animate({ scrollTop: $('.design-editor').offset().top }, 'fast');

		// minimize page content from background
		$('#container #content').removeClass('minimize-bg-content');
	}
}

function SMShowFileManagerModal(opts) {
	$('#modal-image').remove();

	$.ajax({
		url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token'),
		dataType: 'html',
		beforeSend: function() {
			$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
			$('#button-image').prop('disabled', true);
		},
		complete: function() {
			$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
			$('#button-image').prop('disabled', false);
		},
		success: function(html) {
			$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

			$('#modal-image').modal('show');

			$('#modal-image').delegate('a.thumbnail', 'click', function(e) {
				e.preventDefault();

				if (opts.target.attributes.type == 'image') {
					opts.target.set('src', $(this).attr('href'));
				}

				if (opts.target.attributes.type == 'file') {
					opts.target.set('value', $(this).attr('href'));
				}

				$('#modal-image').modal('hide');
			});
		}
	});
}

function SMShowImportTemplateModal() {
	$('#modal-template').remove();

	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/template/manager&user_token=' + getURLVar('user_token'),
		dataType: 'html',
		beforeSend: function() {
			$('.loading-mask-overlay').show();
		},
		complete: function() {
			$('.loading-mask-overlay').hide();
		},
		success: function(html) {
			$('body').append('<div id="modal-template" class="modal">' + html + '</div>');

			$('#modal-template').modal('show');
		}
	});
}

function SMImportTemplate(provider, template_id) {
	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/template/import&user_token=' + getURLVar('user_token'),
		data: 'provider=' + encodeURIComponent(provider) + '&template_id=' + encodeURIComponent(template_id),
		dataType: 'json',
		beforeSend: function() {
			$('.loading-mask-overlay').show();
		},
		complete: function() {
			$('.loading-mask-overlay').hide();
		},
		success: function(json) {
			if (json['html_content']) {
				smc_grapesjs_editor.DomComponents.getWrapper().set('content', '');
				smc_grapesjs_editor.setComponents(json['html_content']);
			}

			if (json['plain_content']) {
				$('textarea[name=\'plain_content\']').val(json['plain_content']);
			}

			if (json['blocks']) {
				SMGenerateBlocks(json['blocks']);
			} else {
				// need to clear previous template modules (blocks)
				SMGenerateBlocks();
			}

			// Campaign Page ONLY
			if (getURLVar('route').match(/.*smart_marketing\/campaign/)) {
				if (provider == 'sendgrid') {
					$('input[name=\'template_id\']').val(template_id);
				} else {
					$('input[name=\'template_id\']').val('');
				}
			}
		}
	});
}

function SMPreviewTemplate(provider, template_id) {
	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/template/preview&user_token=' + getURLVar('user_token'),
		data: 'provider=' + encodeURIComponent(provider) + '&template_id=' + encodeURIComponent(template_id),
		dataType: 'json',
		beforeSend: function() {
			$('.loading-mask-overlay').show();
		},
		complete: function() {
			$('.loading-mask-overlay').hide();
		},
		success: function(json) {
			$('#template-previewer').remove();

			if (json['success']) {
				$('body').prepend('<div id="template-previewer"><div class="template-info"><span class="template-name"></i> ' + json['name'] + '</span><a id="close-preview" href="javascript:void(0);">X</a></div><div class="template-content"><iframe frameborder="0" width="100%" height="100%" src=' + 'data:text/html,' + encodeURIComponent(json['html_content']) + '></iframe></div></div>');

				$('#container').addClass('hidden');
			}
		}
	});
}

function SMCheckTemplateAction() {
	var template_id = $('input[name=\'template_id\']').val();

	if (template_id != '') {
		$('#template-action-confirmation').show();
	}
}

function SMShowMultipleProductSearchModal() {
	$('#modal-search').remove();

	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/search&user_token=' + getURLVar('user_token'),
		dataType: 'html',
		beforeSend: function() {
			$('.loading-mask-overlay').show();
		},
		complete: function() {
			$('.loading-mask-overlay').hide();
		},
		success: function(html) {
			$('body').append('<div id="modal-search" class="modal modal-search-advanced">' + html + '</div>');

			$('#modal-search').modal('show');
		}
	});
}

function SMShowProductSearchModal() {
	$('#modal-product').remove();

	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/product&user_token=' + getURLVar('user_token'),
		dataType: 'html',
		beforeSend: function() {
			$('.loading-mask-overlay').show();
		},
		complete: function() {
			$('.loading-mask-overlay').hide();
		},
		success: function(html) {
			var top_offset = SMGetClientTop() - 75;

			$('body').append('<div id="modal-product" class="modal modal-transparent" style="top:' + top_offset + 'px;">' + html + '</div>');

			$('#modal-product').modal('show');
			
			SMSetModalPointer('product');

			$('#modal-product').delegate('a.thumbnail', 'click', function(e) {
				e.preventDefault();

				//opts.target.set('src', $(this).attr('href'));

				$('#modal-product').modal('hide');
			});
		}
	});
}

function SMShowCategorySearchModal() {
	$('#modal-category').remove();

	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/category&user_token=' + getURLVar('user_token'),
		dataType: 'html',
		beforeSend: function() {
			$('.loading-mask-overlay').show();
		},
		complete: function() {
			$('.loading-mask-overlay').hide();
		},
		success: function(html) {
			var top_offset = SMGetClientTop() - 75;

			$('body').append('<div id="modal-category" class="modal modal-transparent" style="top:' + top_offset + 'px;">' + html + '</div>');

			$('#modal-category').modal('show');
			
			SMSetModalPointer('category');

			$('#modal-category').delegate('a.thumbnail', 'click', function(e) {
				e.preventDefault();

				//opts.target.set('src', $(this).attr('href'));

				$('#modal-category').modal('hide');
			});
		}
	});
}

function SMShowEditLinkModal() {
   $('#modal-link').remove();
   $('head').find('#position-pointer').remove();

   var top_offset = SMGetClientTop() - 75;

   var smc_grapesjs_link_attributes = smc_grapesjs_editor.getSelected().getAttributes();
   var current_link = smc_grapesjs_link_attributes.href;

   html = '<div id="modal-link" class="modal modal-transparent" style="top:' + top_offset + 'px;">';
   html += '	<div id="link-fetch" class="modal-dialog">';
   html += '		<div class="modal-content">';
   html += '    		<div class="modal-body">';
   html += '				<div class="row">';
   html += '					<div class="col-sm-10 col-sm-offset-1">';
   html += '           			<div class="link-edit action-container">';
   html += '							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
   html += '            			<input type="text" name="link" value="' + current_link + '" class="input-lg form-control" />';
   html += '           			</div>';
   html += '					</div>';
   html += '            </div>';
   html += '			</div>';
   html += '		</div>';
   html += '	</div>';
   html += '</div>';

   $('body').append(html);

   $('#modal-link').modal('show');

   SMSetModalPointer('link');

   $('#modal-link .link-edit input[name=\'link\']').on('blur', function() {
      smc_grapesjs_editor.getSelected().setAttributes({
         href: $(this).val()
      });
   });
}


function SMShowTestEmailModal(email_type = 'html') {
	// set email type in hidden input - to use it later
	$('input[name=\'test_email_type\']').val(email_type);

	$('#modal-send-test').remove();

	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/template/sendTest&user_token=' + getURLVar('user_token'),
		dataType: 'json',
		beforeSend: function() {
			$('.loading-mask-overlay').show();
		},
		complete: function() {
			$('.loading-mask-overlay').hide();
		},
		success: function(json) {
			$('body').append('<div id="modal-send-test" class="modal">' + json['output'] + '</div>');

			$('#modal-send-test').modal('show');
		}
	});
}

function SMSaveDesignEditorContent() {   
   var html_plus_css = smc_grapesjs_editor.runCommand('gjs-get-inlined-html');
	
   var html = SMGetHTMLFromCombinedContent(html_plus_css);	
   var css = SMGetCSSFromCombinedContent(html_plus_css);

   var template = SMGetDesignEditorContent(html, css, true);

   $('textarea[name=\'html_content\']').val(template).change();

   $('.design-editor').addClass('preview-expanded');

   $('.design-editor .design-preview').html('<div class="design-preview-overlay"></div><iframe frameborder="0" width="100%" height="100%" src="' + 'data:text/html,' + encodeURIComponent(template) + '"></iframe>');
}

function SMGetHTMLFromCombinedContent(content) {
	var ocx_html_end_position = content.indexOf('</ocx-template-html>');
	var default_html_end_position = content.indexOf('</html>');
	
	var position = (ocx_html_end_position > -1) ? ocx_html_end_position + 20 : ((default_html_end_position > -1) ? default_html_end_position + 7 : 0);

	var html = content.substr(0, position);

	html = SMMinimizeHtml(html);

    return html;
}

function SMMinimizeHtml(html) {
   html = html.replace(/id=".*?"/g, '');
   html = html.replace(/box-sizing: border-box;/g, '');
   html = html.replace(/border-top-color: currentcolor; border-top-style: none; border-top-width: 0px;/g, '');
   html = html.replace(/border-right-color: currentcolor; border-right-style: none; border-right-width: 0px;/g, '');
   html = html.replace(/border-bottom-color: currentcolor; border-bottom-style: none; border-bottom-width: 0px;/g, '');
   html = html.replace(/border-left-color: currentcolor; border-left-style: none; border-left-width: 0px;/g, '');
   html = html.replace(/border-image-outset: 0; border-image-repeat: stretch; border-image-slice: 100%; border-image-source: none; border-image-width: 1;/g, '');
   html = html.replace(/line-height: 100%; outline-color: currentcolor; outline-style: none; outline-width: medium; text-decoration-line: none; text-decoration-style: solid; text-decoration-color: currentcolor; text-decoration-thickness: auto;/g, '');

   html = html.replace(/style=\"\"/g, '');
   html = html.replace(/style=\"\s+\"/g, '');

   html = html.replace(/<\s+/g, '<');
   html = html.replace(/\s+>/g, '>');
   html = html.replace(/<\/\s+/g, '</');

   return html;
}

function SMGetCSSFromCombinedContent(content) {
	var ocx_html_end_position = content.indexOf('</ocx-template-html>');
	var default_html_end_position = content.indexOf('</html>');
	
	var position = (ocx_html_end_position > -1) ? ocx_html_end_position + 20 : ((default_html_end_position > -1) ? default_html_end_position + 7 : 0);

	var css = content.substr(position);

	/* do other things here if required */
	
	css = css.replace(/<style>/g, '');
	css = css.replace(/<style type="text\/css">/g, '');
	css = css.replace(/<\/style>/g, '');

    return css;
}


// GJS split code in 2 parts: html and css at the end
// here - add back css in <head> or <ocx-template-head>
function SMGetDesignEditorContent(html, css, inline) {
	var inline_css = (typeof(inline) != 'undefined') ? inline : false;

	// insert before </ocx-template-head> OR </head>
	var ocx_head_end_position = html.indexOf('</ocx-template-head>');
	var default_head_end_position = html.indexOf('</head>');

	var position = (ocx_head_end_position > -1) ? ocx_head_end_position : ((default_head_end_position > -1) ? default_head_end_position : 0);

	var template = html.substr(0, position) + '<style type="text/css">' + css + '</style>' + html.substr(position);

	// clear other things after </html> or </ocx-template-html>
	if (inline_css) {
		var ocx_html_end_position = template.indexOf('</ocx-template-html>');
		var default_html_end_position = template.indexOf('</html>');

		var position = (ocx_html_end_position > -1) ? ocx_html_end_position + 20 : ((default_html_end_position > -1) ? default_html_end_position + 7 : 0);

		template = template.substr(0, position);
	}

	return template;
}

function SMGetTemplateWithFetchedProduct(component_id, product_info) {
	var html = smc_grapesjs_editor.getHtml();
	var css = smc_grapesjs_editor.getCss();

	var template = SMGetDesignEditorContent(html, css);

	var ocx_temp = $('<ocx-temp>').html(template);

	// do all changes in component based on product_info
	var selected_item = $(ocx_temp).find('[product-fetch-id=' + component_id + ']');

	SMSetProductBlockInfo(selected_item, product_info);

	// return modified content to string
	var processed_html = $(ocx_temp).html();

	return processed_html;
}

function SMGetTemplateWithFetchedProducts(products) {
	var html = smc_grapesjs_editor.getHtml();
	var css = smc_grapesjs_editor.getCss();

	var template = SMGetDesignEditorContent(html, css);

	var ocx_temp = $('<ocx-temp>').html(template);

	$(ocx_temp).find('.product-item').each(function(index){
		if (typeof products[index] !== 'undefined') {
			SMSetProductBlockInfo($(this), products[index]);
		}
	});

	// return modified content to string
	var processed_html = $(ocx_temp).html();

	return processed_html;
}

function SMSetProductBlockInfo(selected_item, product_info) {
	// replace image/name/button link
	$(selected_item).find('.product-url').attr('href', product_info.link.replace(/&amp;/g, "&"));

	// replace image
	$(selected_item).find('.product-image').attr('src', product_info.image);

	// replace background image
	//$(selected_item).find('.product-background').attr('background', product_info.image);
	//$(selected_item).find('.product-background').css('background-image', 'url(\'' + product_info.image + '\')');

	$(selected_item).find('.product-name').text(product_info.name);
	$(selected_item).find('.product-description').text(product_info.description);

	$(selected_item).find('.product-price').text(product_info.price);

	if (product_info.price_new && product_info.price_old) {
		$(selected_item).find('.product-price-new').text(product_info.price_new);
		$(selected_item).find('.product-price-old').text(product_info.price_old).css('display', 'initial');
		$(selected_item).find('.product-discount-amount').text(product_info.discount_amount).css('display', 'initial');
		$(selected_item).find('.product-discount-percentage').text(product_info.discount_percentage).css('display', 'initial');
	} else {
		$(selected_item).find('.product-price-new').text(product_info.price);
		$(selected_item).find('.product-price-old').css('display', 'none');
		$(selected_item).find('.product-discount-amount').css('display', 'none');
		$(selected_item).find('.product-discount-percentage').css('display', 'none');
	}
}

function SMGetTemplateWithFetchedCategory(component_id, category_info) {
	var html = smc_grapesjs_editor.getHtml();
	var css = smc_grapesjs_editor.getCss();

	var template = SMGetDesignEditorContent(html, css);

	var ocx_temp = $('<ocx-temp>').html(template);

	// do all changes in component based on product_info
	var selected_item = $(ocx_temp).find('[category-fetch-id=' + component_id + ']');

	SMSetCategoryBlockInfo(selected_item, category_info);

	// return modified content to string
	var processed_html = $(ocx_temp).html();

	return processed_html;
}

function SMSetCategoryBlockInfo(selected_item, category_info) {
	// replace image/name/button link
	$(selected_item).find('.category-url').attr('href', category_info.link.replace(/&amp;/g, "&"));

	// replace image
	$(selected_item).find('.category-image').attr('src', category_info.image);

	// replace background image
	//$(selected_item).find('.category-background').attr('background', category_info.image);
	//$(selected_item).find('.category-background').css('category-image', 'url(\'' + category_info.image + '\')');

	$(selected_item).find('.category-name').text(category_info.name);
	$(selected_item).find('.category-description').text(category_info.description);
}

function checkItemsCounterStatus(action) {
	$('.gjs-frame').contents().find('head').find('#smart-marketing-iframe-css').remove();
	$('.gjs-frame').contents().find('body').removeClass('ocx-counter-enabled');

	if (action == 'minimize') {
		$('.gjs-frame').contents().find('head').append('<link type="text/css" href="view/stylesheet/smart-marketing-iframe.css" rel="stylesheet" media="screen" id="smart-marketing-iframe-css" />');
		$('.gjs-frame').contents().find('body').addClass('ocx-counter-enabled');
	}

	if (action == 'maximize') {
		// already remove on each calll
	}
}

function SMGetClientTop() {
   var top = $('#smc-grapejs').attr('data-client-top');
   var scrollTop = $('#smc-grapejs .gjs-frame').contents().find('body').scrollTop();

   return top - scrollTop;
}

function SMGetClientLeft() {
   return $('#smc-grapejs').attr('data-client-left');
}

function SMSetModalPointer(modal_type) {
   var input_width = $('#modal-' + modal_type + ' .input-lg').outerWidth();
   var source_x = SMGetClientLeft();

   var pointer_left_offset = parseInt(input_width / 2);
   var diff = source_x - input_width;

   if (diff < -100 || diff > 100) {
      if (diff < 0) {
         pointer_left_offset = parseInt(Math.abs(diff) / 2);
      } else {
         pointer_left_offset = parseInt(input_width - diff / 2);
      }
   }

   $('head').append('<style id="position-pointer">.modal-transparent .modal-content .action-container:after { left: ' + pointer_left_offset + 'px !important; }</style>');
}

function SMEqualHeightColumns() {
	var SMEqualHeightColumns = function () {
		$(".equal-height-columns").each(function() {
			heights = [];

			$(".equal-height-column", this).each(function() {
				$(this).removeAttr("style");
				heights.push($(this).height()); // write column's heights to the array
			});

			$(".equal-height-column", this).height(Math.max.apply(Math, heights)); //find and set max
		});
	}

	SMEqualHeightColumns();

	$(window).resize(function() {
		SMEqualHeightColumns();
	});
}

function SMInitLazyLoading() {
	$(".lazy").recliner({
		attrib: "data-src",
		throttle: 100,
		threshold: 200,
		live: true
	});
}

function SMInitLazyUpdate() {
	$(window).trigger("lazyupdate");
}

// for isodebounce so filtering doesn't happen every millisecond
function debounce( fn, threshold ) {
	var timeout;

	return function debounced() {
		if ( timeout ) {
		  clearTimeout( timeout );
		}

		function delayed() {
		  fn();
		  timeout = null;
		}

		timeout = setTimeout( delayed, threshold || 100 );
	}
}
