$(function ($) {
    'use strict';

    // Initialize the jQuery File Upload widget:
	var fileUploadWidget = $('.fileupload-widget');
	fileUploadWidget.fileupload({
		dataType: 'json',
		// Enable image resizing, except for Android and Opera,
		// which actually support image resizing, but fail to
		// send Blob objects via XHR requests:
		disableImageResize: /Android(?!.*Chrome)|Opera/
		    .test(window.navigator.userAgent),
		maxFileSize: fileUploadWidget.data('upload-max-size'),
		acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,

		imageMaxWidth: 400,
		imageMaxHeight: 400,

		done: function (e, data) {
			$('#upload-progress').fadeOut();

			$.each($('.user-avatar-image'), function(i, img) {
				console.log('image');
				console.log(img);

				img.src = data.result.url + '?t=' + new Date().getTime();
			});
        },
		fail: function (e, data) {
			$('#upload-progress').fadeOut();

			var responseText = jQuery.parseJSON(data.jqXHR.responseText);
			if (responseText && responseText.error) {
				alert('Upload failed: ' + responseText.error);
			} else {
				alert('Upload failed: ' + data.textStatus);
			}
        },
		progressall: function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#upload-progress .bar').css(
				'width',
				progress + '%'
			);
			$('#upload-progress').show();
		}
    });

});
