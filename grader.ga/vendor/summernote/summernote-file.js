/**
 * Copyright 2019 [nobsod | Mathieu Coingt].
 * Website: https://www.nobsod.fr
 * Email: dev@nobsod.fr
 * License: MIT
 *
 * Fork from summernote-audio : https://github.com/taalendigitaal/summernote-audio
 */
(function (factory) {
	/* Global define */
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else if (typeof module === 'object' && module.exports) {
		// Node/CommonJS
		module.exports = factory(require('jquery'));
	} else {
		// Browser globals
		factory(window.jQuery);
	}
}(function ($) {
	$.extend(true, $.summernote.lang, {
		'en-US': {
			file: {
				file: 'File',
				btn: 'File',
				insert: 'Insert File',
				selectFromFiles: 'Select from files',
				url: 'File URL',
				maximumFileSize: 'Maximum file size',
				maximumFileSizeError: 'Maximum file size exceeded.'
			}
		},
		'fr-FR': {
			file: {
				file: 'Fichier',
				btn: 'Fichier',
				insert: 'Insérer un fichier',
				selectFromFiles: 'Sélectionner depuis les fichiers',
				url: 'URL du fichier',
				maximumFileSize: 'Poids maximum du fichier',
				maximumFileSizeError: 'Poids maximum dépassé.'
			}
		},
	});
	
	$.extend($.summernote.options, {
		file: {
			icon: '<i class="note-icon-file"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="14px" height="14px"><path d="M 16 3.59375 L 15.28125 4.28125 L 8.28125 11.28125 L 9.71875 12.71875 L 15 7.4375 L 15 24 L 17 24 L 17 7.4375 L 22.28125 12.71875 L 23.71875 11.28125 L 16.71875 4.28125 L 16 3.59375 z M 7 26 L 7 28 L 25 28 L 25 26 L 7 26 z" style="fill:none;stroke:#111111;stroke-width:3;stroke-linecap:round;"/></svg></i>'
		},
		callbacks: {
			onFileUpload: null,
			onFileUploadError: null,
			onFileLinkInsert: null
		}
	});
	
	$.extend($.summernote.plugins, {
		/**
		 *  @param {Object} context - context object has status of editor.
		 */
		'file': function (context) {
			let self = this,
				// ui has renders to build ui elements
				// for e.g. you can create a button with 'ui.button'
				ui = $.summernote.ui,
				$note = context.layoutInfo.note,
				// contentEditable element
				$editor = context.layoutInfo.editor,
				$editable = context.layoutInfo.editable,
				$toolbar = context.layoutInfo.toolbar,
				// options holds the Options Information from Summernote and what we extended above.
				options = context.options,
				// lang holds the Language Information from Summernote and what we extended above.
				lang = options.langInfo;
			
			context.memo('button.file', function () {
				// Here we create a button
				let button = ui.button({
					// icon for button
					contents: options.file.icon,
					
					// tooltip for button
					tooltip: lang.file.file,
					click: function (e) {
						context.invoke('file.show');
					}
				});
				return button.render();
			});
			
			this.initialize = function () {
				// This is how we can add a Modal Dialog to allow users to interact with the Plugin.
				
				// get the correct container for the plugin how it's attached to the document DOM.
				let $container = options.dialogsInBody ? $(document.body) : $editor;
				
				let fileLimitation = '';
				if (options.maximumFileSize) {
					let unit = Math.floor(Math.log(options.maximumFileSize) / Math.log(1024));
					let readableSize = (options.maximumFileSize / Math.pow(1024, unit)).toFixed(2) * 1 +
						' ' + ' KMGTP'[unit] + 'B';
					fileLimitation = '<small>' + lang.file.maximumFileSize + ' : ' + readableSize + '</small>';
				}
				
				// Build the Body HTML of the Dialog.
				let body = [
					'<div class="form-group note-form-group note-group-select-from-files">',
					'<label class="note-form-label">' + lang.file.selectFromFiles + '</label>',
					'<input class="note-file-input note-form-control note-input" ',
					' type="file" name="files" accept="*/*">',
					'</div>',
					fileLimitation,
					'<div class="form-group note-group-image-url" style="overflow:auto;">',
					'<label class="note-form-label">' + lang.file.url + '</label>',
					'<input class="note-file-url form-control note-form-control note-input ',
					' col-md-12" type="text">',
					'</div>'
				].join('');
				
				// Build the Footer HTML of the Dialog.
				let footer = '<button href="#" class="btn btn-primary note-file-btn">' + lang.file.insert + '</button>';
				
				this.$dialog = ui.dialog({
					
					// Set the title for the Dialog. Note: We don't need to build the markup for the Modal
					// Header, we only need to set the Title.
					title: lang.file.insert,
					
					// Set the Body of the Dialog.
					body: body,
					
					// Set the Footer of the Dialog.
					footer: footer
					
					// This adds the Modal to the DOM.
				}).render().appendTo($container);
			};
			
			this.destroy = function () {
				ui.hideDialog(this.$dialog);
				this.$dialog.remove();
			};
			
			this.bindEnterKey = function ($input, $btn) {
				$input.on('keypress', function (event) {
					if (event.keyCode === 13)
						$btn.trigger('click');
				});
			};
			
			this.bindLabels = function () {
				self.$dialog.find('.form-control:first').focus().select();
				self.$dialog.find('label').on('click', function () {
					$(this).parent().find('.form-control:first').focus();
				});
			};
			
			/**
			 * @method readFileAsDataURL
			 *
			 * read contents of file as representing URL
			 *
			 * @param {File} file
			 * @return {Promise} - then: dataUrl
			 *
			 * @todo this method already exists in summernote.js so we should use that one
			 */
			this.readFileAsDataURL = function (file) {
				return $.Deferred(function (deferred) {
					$.extend(new FileReader(), {
						onload: function (e) {
							let dataURL = e.target.result;
							deferred.resolve(dataURL);
						},
						onerror: function (err) {
							deferred.reject(err);
						}
					}).readAsDataURL(file);
				}).promise();
			};
			
			this.createFile = function (url) {
				// IMG url patterns (jpg, jpeg, png, gif, svg, webp)
				let imgRegExp = /^.+.(jpg|jpeg|png|gif|svg|webp)$/;
				let imgBase64RegExp = /^data:(image\/jpeg|image\/png|image\/gif|image\/svg|image\/webp).+$/;
				
				// AUDIO url patterns (mp3, ogg, oga)
				let audioRegExp = /^.+.(mp3|ogg|oga)$/;
				let audioBase64RegExp = /^data:(audio\/mpeg|audio\/ogg).+$/;
				
				// VIDEO url patterns (mp4, ogc, webm)
				let videoRegExp = /^.+.(mp4|ogv|webm)$/;
				let videoBase64RegExp = /^data:(video\/mpeg|video\/mp4|video\/ogv|video\/webm).+$/;
				
				let $file;
				if (url.match(imgRegExp) || url.match(imgBase64RegExp)) {
					$file = $('<img>')
						.attr('src', url)
					;
				} else if (url.match(audioRegExp) || url.match(audioBase64RegExp)) {
					$file = $('<audio controls>')
						.attr('src', url)
					;
				} else if (url.match(videoRegExp) || url.match(videoBase64RegExp)) {
					$file = $('<video controls>')
						.attr('src', url)
					;
				} else {
					//We can't use this type of file. You have to implement onFileUpload into your Summernote
					console.log('File type not supported. Please define "onFileUpload" callback in Summernote.');
					return false;
				}
				
				$file.addClass('note-file-clip');
				
				return $file;
			};
			
			this.insertFile = function (src, param) {
				let $file = self.createFile(src);
				
				if (!$file) {
					context.triggerEvent('file.upload.error');
				}
				
				context.invoke('editor.beforeCommand');
				
				if (typeof param === 'string') {
					$file.attr('data-filename', param);
				}
				
				$file.show();
				context.invoke('editor.insertNode', $file[0]);
				
				context.invoke('editor.afterCommand');
			};
			
			this.insertFilesAsDataURL = function (files) {
				$.each(files, function (idx, file) {
					let filename = file.name;
					if (options.maximumFileSize && options.maximumFileSize < file.size) {
						context.triggerEvent('file.upload.error', lang.file.maximumFileSizeError);
					} else {
						self.readFileAsDataURL(file).then(function (dataURL) {
							return self.insertFile(dataURL, filename);
						}).fail(function () {
							context.triggerEvent('file.upload.error');
						});
					}
				});
			};
			
			this.show = function (data) {
				context.invoke('editor.saveRange');
				this.showFileDialog().then(function (data) {
					// [workaround] hide dialog before restore range for IE range focus
					ui.hideDialog(self.$dialog);
					context.invoke('editor.restoreRange');
					
					if (typeof data === 'string') { // file url
						// If onFileLinkInsert set
						if (options.callbacks.onFileLinkInsert) {
							context.triggerEvent('file.link.insert', data);
						} else {
							self.insertFile(data);
						}
					} else { // array of files
						// If onFileUpload set
						if (options.callbacks.onFileUpload) {
							context.triggerEvent('file.upload', data);
						} else {
							// else insert File as dataURL
							self.insertFilesAsDataURL(data);
						}
					}
				}).fail(function () {
					context.invoke('editor.restoreRange');
				});
			};
			this.showFileDialog = function () {
				return $.Deferred(function (deferred) {
					let $fileInput = self.$dialog.find('.note-file-input');
					let $fileUrl = self.$dialog.find('.note-file-url');
					let $fileBtn = self.$dialog.find('.note-file-btn');
					
					ui.onDialogShown(self.$dialog, function () {
						context.triggerEvent('dialog.shown');
						
						// Cloning FileInput to clear element.
						$fileInput.replaceWith($fileInput.clone().on('change', function (event) {
							deferred.resolve(event.target.files || event.target.value);
						}).val(''));
						
						$fileBtn.click(function (e) {
							e.preventDefault();
							deferred.resolve($fileUrl.val());
						});
						
						$fileUrl.on('keyup paste', function () {
							let url = $fileUrl.val();
							ui.toggleBtn($fileBtn, url);
						}).val('');
						
						self.bindEnterKey($fileUrl, $fileBtn);
						self.bindLabels();
					});
					ui.onDialogHidden(self.$dialog, function () {
						$fileInput.off('change');
						$fileUrl.off('keyup paste keypress');
						$fileBtn.off('click');
						
						if (deferred.state() === 'pending')
							deferred.reject();
					});
					ui.showDialog(self.$dialog);
				});
			};
		}
	});
}));
