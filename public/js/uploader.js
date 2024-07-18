"use strict";
var uploader = {
    uploadDivID: 'cms-uploader',
    inputImageID: '',
    uploadDiv: document.getElementById(this.uploaderID),
    fileFieldID: 'file-uploader',
    extraUrl: '/upload/image',
    fileFieldObj: null,
    previewObj: null,
    previewID: 'image-preview',
    options: {},
    params: {},
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },

    data: new FormData(),

    init: function () {
        if (this.uploadDiv == null) {
            this.uploadDiv = document.getElementById(this.uploadDivID);
        }

        var input = document.createElement("input");
        input.setAttribute('type', "file");
        input.setAttribute('name', "file");
        input.setAttribute('id', "file-uploader");
        input.style.visibility = "hidden";

        if (this.uploadDiv == null) {
            this.uploadDiv = document.createElement("div");
            this.uploadDiv.setAttribute('id', "cms-uploader");
            this.uploadDiv.style.visibility = "hidden";
            document.body.appendChild(this.uploadDiv);
        }

        this.uploadDiv.appendChild(input);
        this.fileFieldObj = document.getElementById(this.fileFieldID);
    },

    submit: function () {
        $.each(uploader.params, function (key, value) {
            uploader.data.append(key, value);
        });

        $.ajax({
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                if (uploader.isTinymce) {
                    uploader.notification = tinyMCE.activeEditor.notificationManager.open({
                        text: 'Anh/Chị chờ tệp tin upload xong đã nhé (!).',
                        //timeout: 2000,
                        progressBar: true
                    });

                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            uploader.notification.progressBar.value(percentComplete);
                        }
                    }, false);
                }
                return xhr;
            },
            url: settings.baseUrl + uploader.extraUrl,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            type: 'post',
            data: uploader.data,
            headers: uploader.headers,
            success: function (response) {
                uploader.success(response);
            }
        });
    },

    trigger: function (inputImageID, isTinymce, previewID, type) {
        uploader.extraUrl = '/upload/image';
        if (typeof(type) != 'undefined') {
            this.type = type;
            this.params.type = type;
            uploader.extraUrl = '/upload/media';
        }

        if (typeof(isTinymce) == 'undefined') {
            this.isTinymce = false;
        } else {
            this.isTinymce = isTinymce;
        }

        this.isTinymce = (typeof(isTinymce) == 'undefined') ? false : isTinymce;
        if (typeof(previewID) == 'object') {
            this.previewObj = previewID;
        } else {
            this.previewID = (typeof(previewID) == 'undefined') ? 'image-preview' : previewID;
            this.previewObj = $('#' + this.previewID);
        }

        this.inputImageID = inputImageID;
        this.fileFieldObj.click();
        this.fileFieldObj.onchange = function () {
            uploader.onchangeUpload();
        }
    },

    onchangeUpload: function () {
        //turnOnOverlay();
        this.params.ratio = $("#" + this.inputImageID).parent().find(".crop-ratio").val();
        this.params.useOriginal = $("#" + this.inputImageID).parent().find(".use-original").prop("checked");

        this.data = new FormData();
        this.data.append('attachment', $('#' + uploader.fileFieldID)[0].files[0]);
        this.submit();
    },

    uploadMultiple: function (obj) {
        $.each($('#multiple-file')[0].files, function (i, file) {
            uploader.data.append('file-' + i, file);
        });
        this.params.ratio = $(obj).next().val();
        uploader.extraUrl = '/upload/multiple';
        this.submit();
    },

    success: function (response) {
        //turnOffOverlay();
        if (this.isTinymce) {
            tinyMCE.activeEditor.notificationManager.close();
        }

        var url = '';

        if (response.status == true) {
            if (typeof(response.task) !== 'undefined' && response.task == 'upload/multiple') {
                document.getElementById("multiple-file").value = "";
                this.data = new FormData();
                if (response.html != '') {
                    $('#slider-images').append(response.html);
                }
                return;
            }

            if (this.type == 'media') {
                if (this.isTinymce) {
                    var sourceType = response.type == 'video' ? 'video/mp4' : 'audio/mpeg';
                    var caption = prompt("Nhập mô tả cho media", "");
                    var insertHTML = '';
                    if ((caption != null)) {
                        insertHTML += '<div class="text-center">';
                        insertHTML += ' <figure class="d-inline-block text-center mx-auto">';
                        insertHTML += '     <'+ response.type +' width="500" height="320" class="mw-100" controls><source src="/storage/' + response.path + '" type="'+ sourceType +'"></'+ response.type +'>';
                        insertHTML += '     <figcaption class="d-block text-center bg-light text-primary">' + caption + '</figcaption>';
                        insertHTML += ' </figure>';
                        insertHTML += '</div><p></p>';
                    } else {
                        insertHTML += '<'+ response.type +' width="500" height="320" class="mw-100" controls><source src="/storage/' + response.path + '" type="'+ sourceType +'"></'+ response.type +'>';
                    }

                    tinyMCE.activeEditor.focus();
                    tinyMCE.activeEditor.execCommand('mceInsertRawHTML', false, insertHTML);
                    tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                    tinyMCE.activeEditor.selection.collapse(false);
                } else {
                    $("#product-type").val(1);
                    $('#product-video').val(response.path);
                    var video = document.getElementById('video');
                    var source = document.createElement('source');
                    source.setAttribute('src', response.path);
                    video.appendChild(source);
                    $(video).fadeIn('slow');
                    video.play();
                    setTimeout(function () {
                        video.pause();
                        /*source.setAttribute('src', '');
                         video.load();
                         video.play();*/
                    }, 3000);

                    this.hideProgressBar();
                    return;
                }
            } else {
                if (this.isTinymce) {
                    var caption = prompt("Nhập mô tả cho ảnh", "Ảnh minh họa");
                    var insertHTML = '';
                    if ((caption != null)) {
                        insertHTML += '<div class="text-center">';
                        insertHTML += ' <figure class="d-inline-block text-center mx-auto image">';
                        //insertHTML += '     <img class="img-responsive" src="' + settings.cdnUrl + response.medium_images[0] + '" alt="' + caption + '" loading="lazy"/>';
                        insertHTML += '     <img src="' + settings.cdnUrl + response.medium_images[0] + '" srcset=" ' + settings.cdnUrl + response.thumbnail_images[0] + '  480w, ' + settings.cdnUrl + response.medium_images[0] + '  768w, ' + settings.cdnUrl + response.large_images[0] + '  992w" sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, 992px" alt="' + caption + '"  loading="lazy">';

                        // insertHTML += '     <picture>';
                        // insertHTML += '         <source srcset="baby-zoomed-out.jpg" media="(min-width: 1000px)" />';
                        // insertHTML += '         <source srcset="baby.jpg" media="(min-width: 600px)" />';
                        // insertHTML += '         <img src="baby-zoomed-in.jpg" alt="Baby Sleeping" />';
                        // insertHTML += '     </picture>';

                        insertHTML += '     <figcaption class="d-block text-center bg-light text-primary">' + caption + '</figcaption>';
                        insertHTML += ' </figure>';
                        insertHTML += '</div><p></p>';
                    } else {
                        insertHTML += '     <img class="img-responsive" src="' + settings.cdnUrl + response.medium_images[0] + '" alt="' + caption + '" loading="lazy"/>';
                    }

                    tinyMCE.activeEditor.execCommand('mceInsertRawHTML', false, insertHTML);
                    tinyMCE.activeEditor.focus();
                    tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                    tinyMCE.activeEditor.selection.collapse(false);

                } else {
                    //$('#' + this.inputImageID).val((typeof(response.medium_images) == 'undefined') ? response.images[0] : response.medium_images[0]);
                    //$('#' + this.inputImageID).val(this.params.useOriginal ? response.images[0] : response.large_images[0]);
                    $('#' + this.inputImageID).val(response.images[0]);

                    if (this.params.useOriginal) {
                        url = settings.cdnUrl + response.images[0];
                    } else {
                        url = (typeof (response.medium_images) == 'undefined') ? (settings.cdnUrl + response.images[0]) : settings.cdnUrl + response.medium_images[0];
                    }

                    this.previewObj.attr('src', url);
                    this.previewObj.fadeIn('slow');
                }
            }
        } else {
            if (typeof(response.errors) != 'undefined') {
                alert(response.errors[0]);
            }
        }

        this.fileFieldObj.value = "";
        this.params = {};
    },
    hideProgressBar: function () {
        $('.' + uploader.inputImageID + "-progress .progress-bar").attr("aria-valuenow", 0);
        $('.' + uploader.inputImageID + "-progress .progress-bar").css("width", 0 + '%');
        $('.' + uploader.inputImageID + '-progress').fadeOut('slow');
    },
    initFrame: function (obj) {
        //var str="http://www.youtube.com/v/NLqAFFIVEbY?fs=1&hl=en_US";
        var url = obj.value;
        var pattern = /(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/g
        var matches = pattern.exec(url);
        if (!Array.isArray(matches)) {
            return false;
        }
        var youtubeVdieoID = matches[1];
        var html = '';
        if (youtubeVdieoID !== '') {
            html = '<div class="mw-100"></div><iframe type="text/html" src="https://www.youtube.com/embed/' + youtubeVdieoID + '?autoplay=1" frameborder="0"></iframe></div>';
        }
        document.getElementById('youtube-frame').innerHTML = html;
        $('#product-youtube_url').val(youtubeVdieoID);
        $("#product-type").val(3);
    },

    setCropData: function (obj) {
        $(obj).closest(".uploader").find('.crop-ratio').val($(obj).data('type'));
        $(obj).closest(".uploader").find(".dropdown-toggle").text($(obj).text());
    }
};

jQuery(window).on('load', function () {
    uploader.init();
});
