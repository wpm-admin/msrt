var smc_grapesjs_editor = grapesjs.init({
	//autorender: false,
	container : '#smc-grapejs',
	fromElement: true,
	height: '100%',
   storageManager: {
      id: 'gjs-',
      type: 'local',
      autosave: true,
      autoload: false,
      stepsBeforeSave: 1,
      storeComponents: false,
      storeStyles: false,
      storeHtml: true,
      storeCss: true,
	},
	assetManager: {
	},
	plugins: ['gjs-preset-newsletter']
});

// Add new Components
SMProductComponent();
SMCategoryComponent();
SMLinkComponent();

//smc_grapesjs_editor.render();
var smc_style_prefix = smc_grapesjs_editor.getConfig().stylePrefix;
var smc_panel_manager = smc_grapesjs_editor.Panels;
var smc_command_manager = smc_grapesjs_editor.Commands;
var smc_block_manager = smc_grapesjs_editor.BlockManager;

var smc_modal = smc_grapesjs_editor.Modal;
var smc_code_viewer = smc_grapesjs_editor.CodeManager.getViewer('CodeMirror').clone();

// Set CodeMirror Options
smc_code_viewer.set({
    codeName: 'htmlmixed',
    readOnly: 0,
    theme: 'default',
    autoBeautify: true,
    autoCloseTags: true,
    autoCloseBrackets: true,
    lineWrapping: true,
    styleActiveLine: true,
    smartIndent: true,
    indentWithTabs: true
});

// ------------ BUTTONS --------------------------------
smc_panel_manager.addButton('options', [{
		id: 'gjs-import-template',
		className: 'fa fa-download',
		attributes: {
			'title': 'Import Template'
		},
		command: function() {
			SMShowImportTemplateModal();
		}
	}, {
		id: 'gjs-import-info',
		className: 'fa fa-magic',
		attributes: {
			'title': 'Import Products'
		},
		command: function() {
			SMShowMultipleProductSearchModal();
		}
	}, {
		id: 'gjs-send-test',
		className: 'fa fa-send',
		attributes: {
			'title': 'Send Test'
		},
		command: function() {
			// save first content in textarea[name='html_content']
			SMSaveDesignEditorContent();
			SMShowTestEmailModal('html');
		}
	}, {
		id: 'gjs-save',
		className: 'fa fa-save',
		attributes: {
			'title': 'Save & Close'
		},
		command: function() {
			SMSaveDesignEditorContent();
			SMDesignEditor('hide');
		}
	}
]);

SMFixTooltip();
SMDisableGridLines();

SMAddOCXLogo();

// ---------- BLOCKS ----------------------------------
SMGenerateBlocks();

// Open Blocks Manager by default
smc_panel_manager.getButton('views', 'open-blocks').set('active', true);

// ------------ COMMANDS  -----------------------------
// Overwrite Assets - use OC FileManager
smc_grapesjs_editor.Commands.add('open-assets', {
	run(editor, sender, opts = {}) {
		SMShowFileManagerModal(opts);
	}
});

smc_grapesjs_editor.Commands.add('export-template', {
	run(editor, sender) {
		sender && sender.set('active', 0);

		var container = document.createElement('div');
		var textarea = document.createElement('textarea');

		container.appendChild(textarea);

		// init CodeMirror
		smc_code_viewer.init(textarea);

		code_viewer = smc_code_viewer.editor;

		smc_modal.setTitle('View code');

		// CLEAR FIRST
		smc_modal.setContent('');
		smc_modal.setContent(container);

		smc_code_viewer.setContent(SMGetDesignEditorContent(editor.getHtml(), editor.getCss()));

		smc_modal.open();

		code_viewer.refresh();

		// REMOVE THIS IF GENERATE PROBLEMS
		// work@ for CodeMirror GrapesJS autoupdate content without to click any button
		code_viewer.on('blur', function() {
			editor.DomComponents.getWrapper().set('content', '');
    		editor.setComponents(code_viewer.getValue().trim());
		});
	}
});

// NEW Command for Search Product
smc_grapesjs_editor.Commands.add('search-product', {
	run(editor, sender) {
		SMShowProductSearchModal();
	},
	stop(editor, sender) {
	}
});

smc_grapesjs_editor.Commands.add('search-category', {
	run(editor, sender) {
		SMShowCategorySearchModal();
	},
	stop(editor, sender) {
	}
});

smc_grapesjs_editor.Commands.add('edit-link', {
   run(editor, sender) {
      SMShowEditLinkModal();
   },
   stop(editor, sender) {}
});

// FUNCTIONS -----------------------------------------

// COMPONENTS
function SMProductComponent() {
	var components = smc_grapesjs_editor.DomComponents;

	var default_type = components.getType('default');
	var default_model = default_type.model;
	var default_view = default_type.view;

	components.addType('product', {
	   model: default_model.extend({
	    	defaults: Object.assign({}, default_model.prototype.defaults, {
				toolbar: [{
				   attributes: {
						class: 'fa fa-search'
					},
				   command: 'search-product',
				},{
				   attributes: {
						class: 'fa fa-arrow-up'
					},
				   command: 'select-parent',
				},{
				   attributes: {
						class: 'fa fa-arrows'
					},
				   command: 'tlb-move',
				},{
				   attributes: {
						class: 'fa fa-clone'
					},
				   command: 'tlb-clone',
				},
				{
				   attributes: {
						class: 'fa fa-trash'
					},
				   command: 'tlb-delete',
				}],
	      }),
	  	},
	   {
			isComponent: function(element) {
	      	if (element.tagName == 'TABLE' && /product-item/.test(element.className)) {
					return {type: 'product'};
	      	}
	    	},
	   }),

	   // Define the View
		view: default_view.extend({
			initialize(o) {
			   default_view.prototype.initialize.apply(this, arguments);
			},

			openSearchModal() {
				SMShowProductSearchModal();
			},

			// Bind events
		  	events: {
				click: function(e) {
					$('#smc-grapejs').attr('data-client-top', e.pageY);
					$('#smc-grapejs').attr('data-client-left', e.pageX);
				}
		  	},

		  	// The render() should return 'this'
		  	render: function () {
			 	// Extend the original render method
			 	default_view.prototype.render.apply(this, arguments);

				return this;
		  },
		}),
	});
}

function SMCategoryComponent() {
	var components = smc_grapesjs_editor.DomComponents;

	var default_type = components.getType('default');
	var default_model = default_type.model;
	var default_view = default_type.view;

	components.addType('category', {
	   model: default_model.extend({
	    	defaults: Object.assign({}, default_model.prototype.defaults, {
				toolbar: [{
				   attributes: {
						class: 'fa fa-search'
					},
				   command: 'search-category',
				},{
				   attributes: {
						class: 'fa fa-arrow-up'
					},
				   command: 'select-parent',
				},{
				   attributes: {
						class: 'fa fa-arrows'
					},
				   command: 'tlb-move',
				},{
				   attributes: {
						class: 'fa fa-clone'
					},
				   command: 'tlb-clone',
				},
				{
				   attributes: {
						class: 'fa fa-trash'
					},
				   command: 'tlb-delete',
				}],
	      }),
	  	},
	   {
			isComponent: function(element) {
	      	if (element.tagName == 'TABLE' && /category-item/.test(element.className)) {
					return {type: 'category'};
	      	}
	    	},
	   }),

	   // Define the View
		view: default_view.extend({
			initialize(o) {
			   default_view.prototype.initialize.apply(this, arguments);
			},

			openSearchModal() {
				SMShowCategorySearchModal();
			},

			// Bind events
		  	events: {
				click: function(e) {
					$('#smc-grapejs').attr('data-client-top', e.pageY);
					$('#smc-grapejs').attr('data-client-left', e.pageX);
				}
		  	},

		  	// The render() should return 'this'
		  	render: function () {
			 	// Extend the original render method
			 	default_view.prototype.render.apply(this, arguments);

				return this;
		  },
		}),
	});
}

function SMLinkComponent() {
   var components = smc_grapesjs_editor.DomComponents;

   var default_type = components.getType('link');
   var default_model = default_type.model;
   var default_view = default_type.view;

   components.addType('link', {
      model: default_model.extend({
         defaults: Object.assign({}, default_model.prototype.defaults, {
            toolbar: [{
                  attributes: {
                     class: 'fa fa-link'
                  },
                  command: 'edit-link',
               }, {
                  attributes: {
                     class: 'fa fa-arrow-up'
                  },
                  command: 'select-parent',
               }, {
                  attributes: {
                     class: 'fa fa-arrows'
                  },
                  command: 'tlb-move',
               }, {
                  attributes: {
                     class: 'fa fa-clone'
                  },
                  command: 'tlb-clone',
               },
               {
                  attributes: {
                     class: 'fa fa-trash'
                  },
                  command: 'tlb-delete',
               }
            ],
         }),
      }),

      // Define the View
      view: default_view.extend({
         // Bind events
         events: {
            click: function(e) {
               $('#smc-grapejs').attr('data-client-top', e.pageY);
               $('#smc-grapejs').attr('data-client-left', e.pageX);
            },

            dblclick: 'enableEditing'
         },
      }),
   });
}


// BLOCKS
function SMGenerateBlocks(template_blocks = []) {
	SMClearBlocks();

	if (template_blocks.length) {
		SMProcessBlocks(template_blocks);

		smc_panel_manager.getButton('views', 'open-blocks').set('active', true);

		setTimeout(function () {
			SMProcessTemplateBlockImages();
		}, 100);
	}

	// add standard Blocks
	SMGetDefaultBlocks();
}

function SMGetDefaultBlocks() {
	$.ajax({
		url: 'index.php?route=extension/module/smart_marketing/block&user_token=' + getURLVar('user_token'),
		dataType: 'json',
		//async: false,
		beforeSend: function() {
			//$('.loading-mask-overlay').show();
		},
		complete: function() {
			//$('.loading-mask-overlay').hide();
		},
		success: function(json) {
			if (json['success']) {
				SMProcessBlocks(json['blocks']);
			}
		}
	});
}

function SMProcessBlocks(blocks) {
	if (blocks) {
		blocks.forEach(function(block) {
			smc_block_manager.add(block.code, {
		       label: block.label,
		       category: block.category,
		       attributes: block.attributes,
		       content: block.content,
		   });
		});
	}
}

function SMGetTemplateBlocks(template_source) {
	console.log('SMGetTemplateBlocks');
}

function SMClearBlocks() {
	smc_block_manager.getAll().reset();
}

function SMProcessTemplateBlockImages() {
	$('.smc-editor .gjs-pn-views-container .gjs-block-category .gjs-template-block').each(function() {
		var block_image_source = (typeof($(this).attr('data-thumb')) != 'undefined' ? $(this).attr('data-thumb') : '');
		var lazy_image_source = (typeof($(this).attr('data-lazy')) != 'undefined' ? $(this).attr('data-lazy') : '');

		$(this).find('.gjs-block-image').remove();

		$(this).prepend('<div class="gjs-block-image"><img class="lazy" src="' + lazy_image_source + '" data-src="' + block_image_source + '" /></div>');
	});

	SMInitLazyLoading();
}

// FIXES AND HELPERS
function SMAddOCXLogo() {
	//$('.gjs-pn-commands').append('<div class="gjs-logo-ocx"><img src="https://www.oc-extensions.com/image/logo/logo.svg" /></div>');
}

function SMFixTooltip() {
	$('.gjs-pn-commands .gjs-pn-btn, .gjs-pn-options .gjs-pn-btn').each(function() {
		var tooltip_title = (typeof($(this).attr('title')) != 'undefined' ? $(this).attr('title') : '');

		$(this).attr('data-tooltip', tooltip_title);
		$(this).attr('data-tooltip-pos', 'bottom');
	});
}

function SMDisableGridLines() {
	$('.gjs-pn-options .gjs-pn-buttons .gjs-pn-btn.fa-square-o.gjs-pn-active').trigger('click');
}
