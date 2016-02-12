var dateFormatToPassToServerSide = "YYYY-MM-DD";
var DateTimeFormat = "MM/DD/YYYY";
var DefaultCookieName = "DefaultMessage";
var exts = ["jpg", "jpeg", "png", "gif"];
var allowed_length=400000;
var error_message_header='Oops! Something went wrong';
var uploadphotoexts = ["jpg", "jpeg", "png", "gif", "JPG", "JPEG", "GIF", "PNG"];
var uploadphotoallowed_length = 400000000;
var uploadpdfexts = ["pdf"];
var AllText = 'All';
var docDefaultPageSize= 15;
var PageSizeFor10Records=10;
var docDefaultPageSchoolSize= 5;
var docDefaultPageSizeVideoDeshboard= 8;
var docDefaultPageNewsSize= 10;
var serverError = "Oops! Something went wrong on server, suspected error would be ";
var hideLoading = true;

function AjaxCall(url, postData, httpmethod, calldatatype, contentType, showLoading, hideLoadingParam, isAsync) {
    if (hideLoadingParam != undefined && !hideLoadingParam)
        hideLoading = hideLoadingParam;
    if (contentType == undefined)
        contentType = "application/x-www-form-urlencoded;charset=UTF-8";

    if (showLoading == undefined)
        showLoading = true;

    if (showLoading == false || showLoading.toString().toLowerCase() == "false")
        showLoading = false;
    else
        showLoading = true;


    if (isAsync == undefined)
        isAsync = true;

    return jQuery.ajax({
        type: httpmethod,
        url: baseUrl+url,
        data: postData,
        global: showLoading,
        dataType: calldatatype,
        contentType: contentType,
        async: isAsync,
        processData: false,
        beforeSend: function () { $('body').addClass("loading"); }, //$.blockUI();},//beforeSend: function() { if (showLoading) myApp.showPleaseWait(); },$('body').addClass("loading");
        error: function(xhr, textStatus, errorThrown) {
            if (!userAborted(xhr)) {
                if (xhr.status == 403) {
                    var isJson = false;
                    try {
                        var response = $.parseJSON(xhr.responseText);
                        isJson = true;
                    }
                    catch (e) { }
                    if (isJson && response != null && response.Type == "NotAuthorized" && response.Link != undefined)
                        window.location = baseUrl +response.Link;
                    else
                        window.location = window.baseUrl;
                }
                else {
                    var alertText = "";
                    switch (xhr.status){
                        case 404:
                            alertText =  serverError +  "'Method " + xhr.statusText + "'";
                            break;

                        case 200:
                            alertText = "";
                            break;

                        default :
                            alertText =  serverError + "'" + xhr.statusText + "'";
                            break;
                    }

                        alert(alertText);
                    OnError(alertText, "", "", "");
                }
            }
        }
    });
}

function OnError(message, file, line, error) {

    var apiUrl = baseUrl+'/javascripterror';
    if(line == undefined || line ==  ""){
        line = "";
    }
    if(file == undefined || file ==  ""){
        file = "";
    }
    if(error == undefined || error ==  ""){
        error = "";
    }
    else{
        error = error.stack;
    }
    //suppress browser error messages
    var suppressErrors = true;
    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: {
            errorMsg: message,
            errorLine: line,
            queryString: file,
            url: document.location.pathname,
            referrer: document.referrer,
            stack: error,
            userAgent: navigator.userAgent
        }
    });

    return suppressErrors;
}
window.onerror = function(message, file, line,error) {
    //api url
    alert(message);
    OnError(message, file, line, error);

};


ko.bindingHandlers.ApplyFileUpload = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
        var onErrorCallback = allBindings().onErrorCallback;
        var onSuccessCallback = allBindings().onSuccessCallback;
        var param = allBindings().paraMeter;
        var url = valueAccessor()();
        var acceptedFiles = allBindings().acceptedFiles;
        if (param!=undefined)
            url = url +'/'+ param;

        var loader = $(".loader");
        var maxUploadSize = parseInt(400);
        $(element).fileupload({
            url: baseUrl+url,
            maxFileSize: 400,
            type: 'POST',
            dataType: 'json',
            fail: onErrorCallback,
            done:onSuccessCallback,
            progress: function (e, data) {
                $(loader).html("");
            },
            beforeSend: function () { $('body').addClass("loading"); },
            send: function (filedata, data, formData) {
                var uploadErrors = [];
                var isValidImage = true;
                $.each(data.files, function (index, file) {

                    var extension = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();

                    if (acceptedFiles == 'image') {
                        if (extension !== 'jpg' && extension !== 'jpeg' && extension !== 'png' && extension !== 'gif' || file.size > maxUploadSize * 1024) {
                            uploadErrors.push(window.ImageFileAllowedMessage);
                            isValidImage = false;
                        }
                    }
                });
                if (uploadErrors.length > 0) {
                    ShowAlertMessage(uploadErrors.join("\n"), 'error', 'Error Message');
                    return false;
                }
                $(loader).html("<img  src='/assets/images/ajax-loader.gif'>");

            }
        });
    }
};

function BlockUI() {
    $('body').addClass("loading");
}

function UnBlockUI() {
   	$('body').removeClass("loading");
}

$(document).ajaxStop(function (jqXHR, settings) {
    if (hideLoading) {
    	UnBlockUI();
    	 $("#main").show();
    }
});

function userAborted(xhr) {
    return !xhr.getAllResponseHeaders();
}

ko.bindingHandlers.FileUpload = {
    init: function(element, valueAccessor, allBindingsAccessor) {
        var url = valueAccessor();
        var Send = allBindingsAccessor().Send;
        var Done = allBindingsAccessor().Done;

        $(element).fileupload({
            dataType: 'json',
            url: url,

            send: Send,
            done: Done
        });
    }
};

function SetMessageForPageLoad(data, cookieName) {
    if (cookieName == undefined) {
        cookieName = DefaultCookieName;
    }
    $.cookie(cookieName, JSON.stringify(data), { path: '/' });
}

var fileDownloadCheckTimer;
function finishDownload(tokenValueID,cookieName) {
    window.clearInterval(fileDownloadCheckTimer);
    UnBlockUI();
    $('#'+tokenValueID).val('');
    $.removeCookie(cookieName);//clears this cookie value
    $.cookie(cookieName, null, { path: '/' });
}

function blockUIForDownload(tokenValueID,cookieName) {

    BlockUI();
    var token = new Date().getTime(); //use the current timestamp as the token value
    $('#'+tokenValueID).val(token);
    fileDownloadCheckTimer = window.setInterval(function () {
        var cookieValue = $.cookie(cookieName, cookieValue, { expires: 1, path: '/',domain: 'localhost' });
        if (cookieValue == token) {
           finishDownload(tokenValueID,cookieName);
        }
    },1000);
}

function ShowPageLoadMessage(cookieName) {
    if (cookieName == undefined) {
        cookieName = DefaultCookieName;
    }
    if ($.cookie(cookieName) != null && $.cookie(cookieName) != "null") {
        ShowSuccessMessage($.cookie(cookieName),'success','Success');
    	$.cookie(cookieName, null, { path: '/' });
    }
}

$(document).ready(function () {
    $('input:first').focus();
    ShowPageLoadMessage();

});

var init=false;

var initDecorate=true;
ko.bindingHandlers.decorateErrorElement = {
	init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
		initDecorate=true;
	},
    update: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        if (valueAccessor != undefined && valueAccessor() != undefined && valueAccessor().isValid) {
            var valueIsValid = valueAccessor().isValid();
            var valueIsmodified = valueAccessor().isModified();

            if(allBindingsAccessor().multipleSelect!=undefined)
            {
                if(initDecorate)
                {
                    initDecorate=false;
                    return;
                }
                element=$(element).next('.ms-parent').children(".ms-choice");

            }
            var showToolTip = function (element) {
                element
                .attr("data-original-title", valueAccessor().error())
                .addClass("tooltip-danger").addClass('ko-validation').siblings('span.validationMessage').show();
                element.closest('.input-col').addClass('has-error');
                element.closest('.form-group').addClass('error');
                element.closest('.form-sub-group').addClass('error');
                element.closest('.input-group').addClass('error');
            };

            var hideToolTip = function (element) {
                element.removeClass("tooltip-danger").removeClass('ko-validation').siblings('span.validationMessage').hide();
                element.closest('.input-col').removeClass('has-error');
                element.closest('.form-group').removeClass('error');
                element.closest('.form-sub-group').removeClass('error');
                element.closest('.input-group').removeClass('error');
            };
            $('.tooltip-danger').on('focus', function () {
                $(this).closest('.input-group').removeClass('error');
                $(this).removeClass('tooltip-danger').removeClass('ko-validation');
            });
            if (valueIsmodified) {
                if (!valueIsValid) {
                    showToolTip($(element));
                } else {
                    hideToolTip($(element));
                }
            }
        }
	}
};

function CheckPageError(form) {
    var isError=false;
    $('#'+form+' input,'+'#'+form+' select, '+'#'+form+' textarea, '+'#'+form+' button').each(
    function(index){
        var element = $(this);
        if(element.hasClass('tooltip-danger ko-validation'))
        {
            isError=true;
            return false;
        }
    }
    );
    return isError;
}

 ko.validation.configure({
        registerExtenders: true,
        messagesOnModified: true,
        insertMessages: true,
        parseInputAttributes: true,
        messageTemplate: null,
        grouping: { deep: true, observable: true, live: false }
    });

function ShowSuccessMessage(message, type, header) {
    $.msgGrowl({
        type: type ? type : 'success',
        text: message,
        position: 'top-center',
        lifetime: 5000
    });
}

function ShowConfirm(message, callback, header, successButtonText, cancelButtonText) {
    var success = "Yes";
    var cancel = "No";
    if (successButtonText != null)
        success = successButtonText;
    if (cancelButtonText != null)
        cancel = cancelButtonText;
    new BootstrapDialog({
        title: window.confirmdialogtitle,
        message: window.confirmdialogmessage + message,
        closable: true,
        data: {
            'callback': callback
        },
        buttons: [{
            label: success,
            cssClass: 'btn btn-danger',
            action: function (dialogItself) {
            dialogItself.close();
            typeof dialogItself.getData('callback') === 'function' && dialogItself.getData('callback')(false);
            }
            },
            {
            label: cancel,
            cssClass: 'btn btn-grey',
            action: function (dialogItself) {
            dialogItself.close();
            }
        }]
    }).open();
}

function ShowAlertMessage(message, type, header) {
    var classname;
    if (header)
        header = header;
    else
        header = '';

    switch (type) {
        case 'alert':
            classname = 'warning';
            break;
        case 'info':
            classname = 'info';
            break;
        case 'error':
            classname = 'error';
            break;
        default:
            classname = 'warning';
            type = 'alert';
            break;
    };

    BootstrapDialog.show({
        title: header,
        message: message,
        buttons: [{
            label: 'Close',
            cssClass: 'btn btn-danger',
            action: function(dialogItself){
                dialogItself.close();
            }
        }]
    });
}

function generateUUID(){
	$.cookie("enc_userid", enc_id);
    var d = new Date();
    var milliseconds=d.getMilliseconds();

    var userid = $.cookie('enc_userid');
    var str = milliseconds+userid;
	var hex_chr = "0123456789abcdef";
	hash='';
	for(j = 0; j < str.length; j++)
	hash += hex_chr.charAt((str >> (j * 8 + 4)) & 0x0F) +
		hex_chr.charAt((str >> (j * 8)) & 0x0F);
    var seconds=d.getSeconds();
    if (seconds < 10) { seconds = '0' + seconds; }
    var minutes=d.getMinutes();
    if (minutes < 10) { minutes = '0' + minutes; }
    var hours=d.getHours();
    if (hours < 10) { hours = '0' + hours; }
    var day=d.getDate();
    if (day < 10) { day = '0' + day; }
    var month=d.getMonth()+1;
    if (month < 10) { month = '0' + month; }
    var year=d.getFullYear();
    var uuid = ""+hash+""+"-"+""+seconds+""+minutes+""+hours+""+"-"+""+month+""+day+""+year+"";
    return uuid;
}
function generateUUID3(val){
    enc_id = val;
    return generateUUID();
}

function generateUUID2(val){
	enc_id = val.enc_userid;
    return generateUUID();
}

ko.bindingHandlers.CkEditor = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var value = valueAccessor();
        var ckobj;
        if ($(element).attr('id') == undefined) {
            $(element).attr('id', +new Date);
        }
        if ($(element).is("div")) {
            if ($(element).attr('contenteditable') == undefined) {
                $(element).attr("contenteditable", "true");
            }

            ckobj = CKEDITOR.inline($(element).attr('id'));
        } else {
            ckobj = CKEDITOR.replace($(element).attr('id'));
        }

        ckobj.setData(value());
        ckobj.on('change', function () {
            value(ckobj.getData());
        });
        element.ckobj = ckobj;
        element.isupdated = false;

    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        var value = valueAccessor();

        if (value() != element.ckobj.getData())
            element.ckobj.setData(value());
        if (element.isupdated) {
            $(".ckeditorvalidation").each(function (i, element) {
                if (!$(element).valid()) {
                    $(element).parent('div').addClass("tooltip-danger");//.attr("title", $(element).attr("data-val-required")).tooltip('show');
                } else {
                    $(element).parent('div').removeClass("tooltip-danger");//.tooltip('destroy');
                }
            });
        }
        element.isupdated = true;
    }
};

ko.bindingHandlers.datepicker = {

    init: function (element, valueAccessor, allBindingsAccessor) {

        var startDate = allBindingsAccessor().StartDate || "";
        var endDate = allBindingsAccessor().EndDate || "";
        var options = allBindingsAccessor().datepickerOptions || { autoclose: true,endDate: endDate, startDate: startDate, format:'dd/mm/yyyy'};
        $(element).datepicker(options);

        $(element).on("change", function () {
            if($(this).val().length == 0  ) {
                var observable = valueAccessor();
                observable("");
            }
        });
        $(element).on("click", function () {
            $(element).datepicker("show");
        });
        if (valueAccessor()() == "0001-01-01T00:00:00") {

        }

        ko.utils.registerEventHandler(element, "changeDate", function () {
            var observable = valueAccessor();
            if ($(element).datepicker("getDate") != "Invalid Date") {
                observable($(element).datepicker("getDate"));
            } else {
                observable(null);
            }
        });
        ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
            $(element).datepicker("destroy");
        });
    },
    update: function (element, valueAccessor) {
        var value = ko.utils.unwrapObservable(valueAccessor()),
            current = $(element).datepicker("getDate");
        if (isNaN(value - current)) {
            if (value != null) {
                var date = moment(value);
                if (date.toString() == "Invalid date") {
                    $(element).val(null);
                } else {
                    $(element).datepicker("setDate", date.format(DateTimeFormat));
                }
            }
        } else {
            if (value - current !== 0) {
            	$(element).datepicker("setDate", value);
            }
        }
    }
};
ko.validation.makeBindingHandlerValidatable('datepicker');

function UpdateDateJS(data, format) {
    if (format == undefined) {
        format = dateFormatToPassToServerSide;
    }
    return moment(data).format(format);
}

function resize(file, max_width, max_height,  imageEncoding){
    var fileLoader = new FileReader(),
    canvas = document.createElement('canvas'),
    context = null,
    imageObj = new Image(),
    blob = null;
    canvas.id     = "hiddenCanvas";
    canvas.width  = max_width;
    canvas.height = max_height;
    canvas.style.visibility   = "hidden";
    document.body.appendChild(canvas);
    context = canvas.getContext('2d');

    if (file.type.match('image.*')) {
        fileLoader.readAsDataURL(file);
    } else {
        alert('File is not an image');
    }

    fileLoader.onload = function() {
        var data = this.result;
        imageObj.src = data;
    };

    fileLoader.onabort = function() {
        alert("The upload was aborted.");
    };

    fileLoader.onerror = function() {
        alert("An error occured while reading the file.");
    };
    imageObj.onload = function() {
    if(this.width == 0 || this.height == 0){
            alert('Image is empty');
        } else {
            context.clearRect(0,0,max_width,max_height);
            context.drawImage(imageObj, 0, 0, this.width, this.height, 0, 0, max_width, max_height);
            blob = dataURItoBlob(canvas.toDataURL(imageEncoding));
            upload(blob);
        }
    };
    imageObj.onabort = function() {
        alert("Image load was aborted.");
    };
    imageObj.onerror = function() {
        alert("An error occured while loading image.");
    };
}

function loaderDisplayTillFullyLoad(imageID,loaderImageID) {
    $('#'+imageID).on('load',function()
    {
      $('#'+loaderImageID).css('display','none');
        $('#'+imageID).css('display','block');
    });
}

function Awsfileupload(options){
    var form = options.form;
    var new_filename;
    var tempname;
    var AWSSettingsModel = options.AWSSettingsModel;
    var AllowedExts =options.AllwoedExts;
    var AlertMessage  =options.AlertMessage;
    var ResizeRequired  =options.ResizeRequired;
    form.fileupload({
        url: form.attr('action'),
        type: 'POST',
        datatype: 'xml',
        add: function (event, data) {
            $(".btn-signup1").attr('disabled','disabled');
            $(".cancel").attr('disabled','disabled');
            tempname = data.originalFiles[0].name;
            var filename = data.originalFiles[0].name.split(".");
            var ext = filename[filename.length - 1];

            if(AllowedExts.indexOf(ext) >= 0)
            {
                var options = {
                    file: data.files[0],
                    callback: function(canvasfile) {
                        var name = generateUUID3(AWSSettingsModel.enc_userid());
                        new_filename = (canvasfile.name);
                        if (AWSSettingsModel.folder())
                            new_filename = AWSSettingsModel.folder() + "/" + name + "." + ext;
                        $('.progress').show();
                        $('#key').val(new_filename);
                        data.files[0]=canvasfile;
                        data.submit();
                    }
                };
                if(ResizeRequired) {
                    ResizeFile(options);
                }else{
                    options.callback(data.files[0]);
                }
            }
            else
            {
                ShowAlertMessage(AlertMessage,'error',error_message_header);
            }
        },
        progress: function(e, data){
            $('#file').attr('disabled', 'disabled');
            model.EnableAddButton(false);
            var percent = Math.round((data.loaded / data.total) * 100);
            $('.bar').css('width', percent + '%');
        },
        fail: function(e, data) {
            $('.bar').css('width', '100%').addClass('red');
            $('.progress').addClass('red');
            $("#btn-remove").removeAttr('enabled');
            $('#file').removeAttr('enabled');
        },
        done: function (event, data) {
            window.onbeforeunload = null;
            options.successCallBack(new_filename,tempname);


        }
    });
}

function AWSImageLoader(){
    $('.DisplaySlider').show();
    $.each(document.images, function () {
        var this_image = this;
        var src = $(this_image).attr('src') || '' ;
        var id=$(this_image).attr('id');
        $('#loader'+id).css('display','block');
        if(!src.length > 0){
            var lsrc = $(this_image).attr('lsrc') ;
            if(lsrc || lsrc.length > 0){
                var img = new Image();
                img.src = lsrc;
                $(img).load(function() {
                    this_image.src = this.src;
                    $('#loader'+id).css('display','none');
                });
            }
        }
    });
}

function blobToFile(theBlob, fileName) {
    theBlob.lastModifiedDate = new Date();
    theBlob.name = fileName;
    return theBlob;
}

function CheckSizeBeforeResizing(resizeFile, width, height, iteration, callback) {
    var maxHeight = window.DefaultUploadPhotoHeight;
    var maxWidth = window.DefaultUploadPhotoWidth;
    var maxIteration = 3;
    var proposedHeight = (height / 2);
    var proposedWidth = (width / 2);

    if (iteration < maxIteration && proposedHeight > maxHeight && proposedWidth > maxWidth) {
        canvasResize(resizeFile, {
            width: proposedWidth,
            height: proposedHeight,
            crop: false,
            quality: 100,
            rotate: 0,
            callback: function (data, width, height) {
                iteration++;
                var newfile = blobToFile(canvasResize('dataURLtoBlob', data), resizeFile.name);
                var options =
                {
                    file: newfile,
                    width: width,
                    height: height,
                    iteration: iteration,
                    callback: callback
                };
                ResizeFile(options);
            }
        });
    }
    else {
        callback(resizeFile);
    }
}

function ResizeFile(options) {
    var iteration = options.iteration == undefined ? 0 : options.iteration;
    if (options.width == undefined && options.height == undefined) {
        img = new Image();
        img.onload = function () {
            CheckSizeBeforeResizing(options.file, img.width, img.height, iteration, options.callback)
        };
        var _URL = window.URL || window.webkitURL;
        img.src = _URL.createObjectURL(options.file);
    }
    else {
        CheckSizeBeforeResizing(options.file, options.width, options.height, iteration, options.callback);
    }
}

function getArrayColumn(matrix, col){
    var column = [];
    for(var i=0; i<matrix.length; i++){
        column.push(matrix[i][col]);
    }
    return column;
}

var DeleteFileFromAWS = '/deleteawsfile';
window.ConfirmUploadMessage = "Do you want to save the uploaded files ? ";

function RemoveFileFromAws(async, UploadFilesArray){
    AjaxCall(DeleteFileFromAWS, ko.toJSON({Data:UploadFilesArray}), "post", "json", "application/json", true, undefined, async).done(function (response) {
        if(response.IsSuccess) {
            UploadFilesArray=[];
        }
    });
    return true;
}

function CheckOnloadBefore(UploadFilesArray,TempArray) {
    window.onunload = function () {
        if (UploadFilesArray.length > 0) {
            if (UploadFilesArray[0] != TempArray[0]) {
                RemoveFileFromAws(false, UploadFilesArray);
            }
        }
    };
    jQuery(window).bind('beforeunload', function (e) {
        if (UploadFilesArray.length > 0) {
            if (UploadFilesArray[0] != TempArray[0]) {
                e.returnValue = window.ConfirmUploadMessage;
                return window.ConfirmUploadMessage;
            }
        }
    });
}

function SearchObservableArrayWithKeyValue(ObservableArray,KeyName,KeyValue){
    ko.utils.arrayFirst(ObservableArray, function(item) {
        return item.KeyName==KeyValue;
    });
}
function isNumber(evt) {
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode;
    if(iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
        return false;

    return true;
}

ko.bindingHandlers.newDateTimepicker = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
    	debugger;
        $(element).datetimepicker();
    }
};