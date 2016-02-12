var dateFormatToPassToServerSide = "YYYY/MM/DD";
var DateTimeFormat = "MM/DD/YYYY";
var DefaultCookieName = "DefaultMessage";
var apiurl='http://localhost:8080/laravel/public/index.php';

function CheckErrors(currentForm) {

    if (!jQuery(currentForm).valid()) {
        return false;
    }
    return true;
}

$(document).ready(function () {
    $('input:first').focus();
});

var myApp;
myApp = myApp || (function () {
//	
    var pleaseWaitDiv = $('<div class="modal bootstrap-dialog type-primary" id="myModal" style="height:100%;overflow:hidden;"><div class="modal-dialog" style="padding-top: 290px;width:100%; height:100%; position: absolute; margin-top:0; text-align:center;"><div class="modal-content" style="background:transparent; box-shadow:none; border:none;"><div class="modal-body" style="padding:0px"><img height="50" src="'+baseUrl+'/assets/images/ajax-loader.gif"/></div></div></div></div>');
    return {
        showPleaseWait: function () {
            $('body').css('height', '100%');
            pleaseWaitDiv.modal();

        },
        hidePleaseWait: function () {
            pleaseWaitDiv.modal('hide');
        }
    };
})();

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
        beforeSend: function() { if (showLoading) myApp.showPleaseWait(); },//$('body').addClass("loading");
        error: function(xhr, textStatus, errorThrown) {

            if (!userAborted(xhr)) {
                if (xhr.status == 403) {
                    var response = $.parseJSON(xhr.responseText);
                    if (response != null && response.Type == "NotAuthorized" && response.Link != undefined)
                        window.location = response.Link;
                    //else
                    //window.location = LoginindexUrl;    
                } else {
                    //  alert("An error has occured");
                }

            }

        }
    });

}

function UnBlockUI() {
    myApp.hidePleaseWait();
    //KeyPressNumericValidation();
};

$(document).ajaxStop(function (jqXHR, settings) {
    if (hideLoading) {
        UnBlockUI();
    }
    //KeyPressNumericValidation();

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

ko.bindingHandlers.datepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var endDate = $(element).attr('end-date') ? $(element).attr('end-date') : "";
        var startDate = $(element).attr('start-date') ? $(element).attr('start-date') : "";

        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions || { autoclose: true, language:window.culture, endDate: endDate, startDate: startDate };
        $(element).datepicker(options);

        //when a user changes the date, update the view model
        $(element).on("changeDate", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(event.date);
            }
            $(element).validate();
        });
    },
    update: function (element, valueAccessor) {
        var widget = $(element).data("datepicker");

        //when the view model is updated, update the widget
        if (widget){
            if (widget.dates.length == 0) {
                if (ko.utils.unwrapObservable(valueAccessor())) {
                    widget.date = moment(ko.utils.unwrapObservable(valueAccessor())).toDate(); //.locale("en-AU").format('l');
                    console.log(widget.date);
                    widget.setDate(widget.date);
                    //widget.setUTCDate(widget.date);
                }
            }
            widget.setDate();
        }
    }
};
ko.bindingHandlers.DateRange = {
    init: function(element, valueAccessor, allBindingsAccessor) {
        //var endDate = $(element).attr('end-date') ? $(element).attr('end-date') : "";
        //var startDate = $(element).attr('start-date') ? $(element).attr('start-date') : "";

        var startDate = "";
        var endDate = "";

        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions || { autoclose: true, language: window.culture, endDate: endDate, startDate: startDate };
        $(element).datepicker(options);

        //when a user changes the date, update the view model
        $(element).on("changeDate", function(event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(event.date);
                if($(element).hasClass('start-date'))
                    $('.end-date').datepicker('setStartDate', event.date);
                else if ($(element).hasClass('end-date'))
                    $('.start-date').datepicker('setEndDate', event.date);
            }
            $(element).validate();
        });
    },
    update: function(element, valueAccessor) {
        var widget = $(element).data("datepicker");

        //when the view model is updated, update the widget
        if (widget) {
            if (widget.dates.length == 0) {
                if (ko.utils.unwrapObservable(valueAccessor())) {
                    widget.date = moment(ko.utils.unwrapObservable(valueAccessor())).toDate(); //.locale("en-AU").format('l');
                    console.log(widget.date);
                    widget.setDate(widget.date);
                    //widget.setUTCDate(widget.date);
                }
            }
            var value = ko.utils.unwrapObservable(valueAccessor());
            if(value != undefined && value != '')
                widget.setDate(moment(value).toDate());

        }
    }
}
ko.bindingHandlers.dateRangepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions || { autoclose: true, language:window.culture, format: "mm/dd/yyyy", endDate: '+0d' };
        $(element).datepicker(options);

        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "changeDate", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(event.date);
            }
        });
    },
    update: function (element, valueAccessor) {
        var widget = $(element).data("datepicker");
        //when the view model is updated, update the widget
        if (widget) {
            if (ko.utils.unwrapObservable(valueAccessor())) {
                widget.date = moment(ko.utils.unwrapObservable(valueAccessor())).format('l');
                widget.setDate(widget.date);
            }
            widget.setDate();
        }
    }
};
ko.bindingHandlers.dateTimepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions ||{ format: 'mm/dd/yyyy hh:ii' };
        $(element).datetimepicker(options);
        // alert('h');
        //when a user changes the date, update the view model
        ko.utils.registerEventHandler(element, "changeDate", function (event) {
            var value = valueAccessor();
            if (ko.isObservable(value)) {
                value(moment(event.date).utc().format('YYYY/MM/DD H:mm'));
            }
        });
    },
    update: function (element, valueAccessor) {
        var widget = $(element).data("datetimepicker");
        //when the view model is updated, update the widget
        if (widget) {
            if (ko.utils.unwrapObservable(valueAccessor())) {
                widget.date = new Date(moment(ko.utils.unwrapObservable(valueAccessor())).format('YYYY/MM/DD H:mm'));
                widget.setDate(widget.date);
            }
        }
    }
};



function ShowMessages(resposne, callback, form,messageTimeOut) {
    ShowToaster(resposne, callback, form, messageTimeOut =5);
}

function ShowToaster(resposne, callback, form, messageTimeOut) {
    toastr.clear();
    toastr.options.closeButton = true;
    toastr.options.timeOut =messageTimeOut?messageTimeOut*1000:5000;
    toastr.options.positionClass = 'toast-top-right';
    toastr.options.onHidden = callback?callback:function(){};
    var IsSuccess = resposne.IsSuccess;
    var Message = resposne.Message;

    if(form!=null)
        ShowServerErros(form,resposne.ErrorMessages);

    if(jQuery.type(Message) == "object"){
        $messages = '<ul>';
        $.each(Message, function(i, obj){
            $messages += "<li>"+obj+"</li>";
        });
        $messages +="</ul>";
        ShowDialogMessage('Opps!!', 'type-danger', $messages);
    }else{

        if (IsSuccess == 1 && Message != undefined && Message != '') {
            toastr.success(Message,'');
        }
        else if (IsSuccess == 2 && Message != undefined && Message != '') {
            toastr.warning(Message, '');
        }
        else if (IsSuccess == 0 && Message != undefined && Message != '') {
            toastr.error(Message,'');
            //ShowDialogMessage(Messages.ErrorHeader, BootstrapDialog.TYPE_DANGER, message);
        }
    }
}



ko.bindingHandlers.select2 = {
    update: function (element, valueAccessor) {
        $(element).select2({
            allowClear: true

        }).on("change", function (e) {
            // mostly used event, fired to the original element when the value changes

            //
            $(element).validate();
        });
    }
};

ko.bindingHandlers.select2Multiple = {
    update: function (element, valueAccessor) {
        $(element).val(valueAccessor()()).select2({
            allowClear: true,
            triggerChange:true
        }).on("change", function (e) {
            // mostly used event, fired to the original element when the value changes

            //
            valueAccessor()($(this).val());
            $(element).validate();
        });
    }
};


/*$('.input-group-addon').children('.fa.fa-calendar').end().attr("style", "cursor:pointer");
 $('.input-group-addon').children('.fa.fa-calendar').end().live('click', function () {
 $(this).closest('.input-group').children('input').datepicker('show');
 });
 */
function ParseJsonDate(jsondate) {
    return (eval((jsondate).replace(/\/Date\((\d+)\)\//gi, "new Date($1)")));
}

function ShowDialogMessage(header, type, message) {

    BootstrapDialog.show({
        type: type,
        title: header,
        message: message,
        closable: false,
        closeByBackdrop: false,
        closeByKeyboard:false,
        buttons: [{
            label: 'Ok',
            action: function (dialogItself) {
                dialogItself.close();
            }
        }]
    });

}

ko.bindingHandlers.required = {
    init: function (element, value) {
        $(element).rules("add", {
            required: true,
            messages: {
                required: value()
            }
        });
        $(element).change(function () {
            $(element).valid();

        });
        $.validator.unobtrusive.parse(element);


    }
    /*End For Custom Validation*/
};

ko.bindingHandlers.floatPercent = {
    update: function (element, valueAccessor, allBindingsAccessor) {
        var value = ko.utils.unwrapObservable(valueAccessor()),
            precision = ko.utils.unwrapObservable(allBindingsAccessor().precision) || ko.bindingHandlers.numericText.defaultPrecision,
            formattedValue = value.toFixed(precision);

        var sign = allBindingsAccessor().sign;

        ko.bindingHandlers.text.update(element, function () { return sign != undefined ? formattedValue + ' ' + sign : formattedValue; });
    },
    defaultPrecision: 1
};

var init=false;
var getChangefromChangeEvent=false;
ko.bindingHandlers.multipleSelect = {
    init:function(element, valueAccessor, allBindingsAccessor) {
        init=true;
        var options=allBindingsAccessor().multipleSelectOptions;
        $(element).multipleSelect(options);

        //var registerChangeEvent=allBindingsAccessor().registerChangeEvent==undefined?false:allBindingsAccessor().registerChangeEvent;


        ko.utils.registerEventHandler(element, 'change', function (element,data) {
            //
            var value = valueAccessor();
            if (ko.isObservable(value)) {

                value($(element.target).multipleSelect('getSelects'));
            }
            //valueAccessor()($(element.target).multipleSelect('getSelects'));
            getChangefromChangeEvent=true;
        });

    },
    update:function(element, valueAccessor, allBindingsAccessor) {
        //
        if(init)
        {
            $(element).multipleSelect('refresh');
            init=false;
        }

        if(!getChangefromChangeEvent)
        {
            var setSelects=valueAccessor()();

            if(setSelects==null)
                setSelects=[];
            setSelects.length && $(element).multipleSelect('setSelects',setSelects);
        }

        getChangefromChangeEvent=false;
    }
};



ko.bindingHandlers.customMultipleSelect = {
    init:function(element, valueAccessor, allBindingsAccessor) {
        ko.utils.registerEventHandler(element, 'change', function (element,data) {
            //
            var value = valueAccessor();
            if (ko.isObservable(value)) {

                value($(element.target).multipleSelect('getSelects'));
            }

        });

    }
};







ko.bindingHandlers.timePicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {

        var options = allBindingsAccessor().timePickerOptions || {};
        $(element).timepicker(options);


        ko.utils.registerEventHandler(element, "changeTime.timepicker", function (e) {

            var observable = valueAccessor();
            var time = e.time.value;


            if (time == "") {
                if (e.time.hours != "")
                    time = e.time.hours.toString();
                else
                    time = "0";

                if (e.time.minutes != "")
                    time = time + ":" + e.time.minutes.toString();
                else
                    time = time + ":00";
            }
            observable(time);


        });
    },
    update: function (element, valueAccessor) {

        var value = ko.utils.unwrapObservable(valueAccessor());
        if (value == '0:00' || value == '') {
            $(element).timepicker('setTime', '');
            valueAccessor(value);
        }
        else
            $(element).timepicker('setTime', value);
    }
};


$(".datefocus").click(function () {
    $(this).parents('div.input-group').find('input').focus();
});

$(".timefocus").click(function () {
    $(this).parents('div.input-group').find('input').focus();
});

function DateFocus(element) {
    jQuery(element).live("focus", function () {
        var date = jQuery(this);
        date.parents("div.controls").children("span.custom").remove();
    });
}

ko.bindingHandlers.datepicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        //initialize datepicker with some optional options
        var options = allBindingsAccessor().datepickerOptions || { autoclose: true };
        //var options = allBindingsAccessor().datepickerOptions || {};
        $(element).datepicker(options);
        // DateFocus(element);

        ko.utils.registerEventHandler(element, "change", function () {
            var observable = valueAccessor();
            if ($(element).datepicker("getDate") != "Invalid Date") {
                observable($(element).datepicker("getDate"));
                // $(element).valid();
            } else {
                observable(null);
            }
            // observable(moment(observable()).format(DateTimeFormat));

            //observable('');
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
                    $(element).val(date.format(DateTimeFormat));
                    //$(element).datepicker("setDate", date.format(DateTimeFormat));
                }
                //$(element).text(date.format("L"));
            }
        } else {
            // if (value - current !== 0) {
            $(element).datepicker("setDate", value);
            // }
        }
    }
};



var initDecorate=true;
ko.bindingHandlers.decorateErrorElement = {
    init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        initDecorate=true;
    },
    update: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
        //if(!initDecorate)
        //{

        if (valueAccessor != undefined && valueAccessor() != undefined && valueAccessor().isValid) {

            var valueIsValid = valueAccessor().isValid();
            var valueIsmodified = valueAccessor().isModified();
            var errorType = allBindingsAccessor().ErrorType;


            if(allBindingsAccessor().multipleSelect!=undefined || allBindingsAccessor().customMultipleSelect!=undefined)
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
                    .addClass("tooltip-danger").addClass('ko-validation')
                    .tooltip();
                element.closest('.form-group').addClass('error');
                element.closest('.form-sub-group').addClass('error');
                element.closest('.input-group').addClass('error');




            };

            var hideToolTip = function (element) {
                element
                    .removeClass("tooltip-danger").removeClass('ko-validation')
                    .tooltip('destroy');
                element.closest('.form-group').removeClass('error');
                element.closest('.form-sub-group').removeClass('error');
                element.closest('.input-group').removeClass('error');
            };

            if (valueIsmodified) {

                if (!valueIsValid) {
                    showToolTip($(element));
                } else {
                    hideToolTip($(element));

                }
            }
        }
        //}
        //initDecorate=false;

    }
};



function ShowServerErros(form, errorObject)
{

    if(errorObject!=null)
    {
        var showToolTip = function (element,value) {
            element
                .attr("data-original-title", value)
                .addClass("tooltip-danger").addClass('ko-validation')
                .tooltip();
            element.closest('.form-group').addClass('error');
            element.closest('.input-group').addClass('error');


        };

        var hideToolTip = function (element) {

            if(element.hasClass('tooltip-danger'))
            {
                element
                    .removeClass("tooltip-danger").removeClass('ko-validation')
                    .tooltip('destroy');
                element.closest('.form-group').removeClass('error');
                element.closest('.input-group').removeClass('error');
            }
        };

        $('#'+form+' input,'+'#'+form+' select, '+'#'+form+' textarea').each(
            function(index){
                var input = $(this);
                var nameAttr=input.attr('name');

                if(nameAttr!=undefined)
                {
                    element=$(input).next('.ms-parent').children(".ms-choice");
                    if(element!=undefined && element.length>0)
                        input=element;


                    if(errorObject[nameAttr]!=undefined)
                        showToolTip(input,errorObject[nameAttr]);
                    else
                        hideToolTip(input);
                }
            }
        );



    }



}


function CheckPageError(form)
{

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


function setEmptyIfDateInvalid(date){

    if((new Date(date)).toString()=='Invalid Date')
        return ''; //setEmptyIfDateInvalid
    return date;

}




function UpdateDateJS(data, format) {
    if (format == undefined) {
        format = dateFormatToPassToServerSide;
    }
    return moment(data).format(format);
};



function Convert12HrTo24Hr(selectedTime) {
    //Converting 12 hour time to 24 hour time is relatively simple. Add 12 to any hours from 1 PM to 11 PM, and change 12 AM to 0.
    var hour = Number(selectedTime.split(' ')[0].split(':')[0]);
    var minutes = selectedTime.split(' ')[0].split(':')[1];
    var meridian = selectedTime.split(' ')[1];
    if (meridian == "PM" && (hour >=1 && hour <= 11)) {
        hour += 12;
    }
    if (meridian == "AM" && (hour == 12)) {
        hour = 0;
    }
    return hour + ":" + minutes;
}

function GetDateTime(time,date)
{
    var str24hrTime=Convert12HrTo24Hr(time);

    if(date==undefined)
        date = new Date();
    else
        date = new Date(date);

    var date= new Date(date.getFullYear(), (date.getMonth() + 1), date.getDate(), str24hrTime.split(':')[0], str24hrTime.split(':')[1], 0,0);
    return date;
}


function ShowMessage(element, messageType, message) {

    $(element).html("<div class='alert alert-" + messageType + "' style='display: block;'>" +
    "<button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>×</span><span class='sr-only'>Close</span></button>" + message + "</div>");
};



function RegisterOnReady()
{
    $(".datefocus").click(function () {
        $(this).parents('div.input-group').find('input').focus();
    });

    $(".timefocus").click(function () {
        $(this).parents('div.input-group').find('input').focus();
    });

}



function showToolTip (element,error) {

    element
        .attr("data-original-title",error)
        .addClass("tooltip-danger").addClass('ko-validation')
        .tooltip();
    element.closest('.form-group').addClass('error');
    element.closest('.form-sub-group').addClass('error');
    element.closest('.input-group').addClass('error');




};
function hideToolTip(element) {
    element
        .removeClass("tooltip-danger").removeClass('ko-validation')
        .tooltip('destroy');
    element.closest('.form-group').removeClass('error');
    element.closest('.form-sub-group').removeClass('error');
    element.closest('.input-group').removeClass('error');
};



dropZonePackageUploadCount=0;
//var dropZonePackageUploadCount = 0;
ko.bindingHandlers.ApplyFileUpload = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {

        //var addCallback = allBindings().addedCallback;
        var onErrorCallback = allBindings().onErrorCallback;
        var onSuccessCallback = allBindings().onSuccessCallback;
        var param = allBindings().paraMeter;
        var url = valueAccessor()();
        var acceptedFiles = allBindings().acceptedFiles;
        if (param!=undefined)
            url = url +'/'+ param;

        var loader = $(element).parent("#fileParent").find(".loader");
        var maxUploadSize = parseInt(100000000);

        $(element).fileupload({
            url: url,
            //maxFileSize: 100000000,
            type: 'POST',
            dataType: 'json',
            fail: onErrorCallback,
            done:onSuccessCallback,
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                var progressbar = $(e.target).parents('.files').next().find('.ProgressBar');
                progressbar.parent().show();
                myApp.showPleaseWait();
                progressbar.css(
                    'width',
                    progress + '%'
                );
                progressbar.children('span').html(progress + '%');

                $(e.target).parents('.Uploadfile').next().find('.uploadMessage').hide();

                if (parseInt(progress) == 100) {
                    dropZonePackageUploadCount = dropZonePackageUploadCount - 1;
                    progressbar.css(
                        'width',
                        '0%'
                    );
                    progressbar.parent().hide();
                    $(loader).html("");
                }
            },
            send: function (filedata, data, formData) {

                var uploadErrors = [];
                var isValidImage = true;
                $.each(data.files, function (index, file) {

                    var extension = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();
                    if (acceptedFiles == 'image') {
                        if (extension !== 'jpg' && extension !== 'jpeg' && extension !== 'png' && extension !== 'gif') {
                            //uploadErrors.push(Languages.ImageFileAllowedMessage);
                            uploadErrors.push("Only .jpeg, .jpg, .phg, .gif format supported");
                            isValidImage = false;
                        }
                    }

                    if (file.size > maxUploadSize * 1024 * 1024) {
                        uploadErrors.push("File exceeds maximum allowed size of " + maxUploadSize + "MB");
                        isValidImage = false;
                    }
                });


                if (uploadErrors.length > 0) {
                    var errors = {IsSuccess:false,Message:uploadErrors};
                    //ShowAlertMessage(uploadErrors.join("\n"), 'error', 'Error Message');
                    ShowMessages(errors,null,'',4);

                    return false;
                }

                dropZonePackageUploadCount = dropZonePackageUploadCount + 1;
                $(loader).html("<img  src='/assets/images/ajax-loader.gif'>");

            }
        });
    }
};

ko.bindingHandlers.ApplyFileUploadRaise = {
    init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {

        //var addCallback = allBindings().addedCallback;
        var onErrorCallback = allBindings().onErrorCallback;
        var onSuccessCallback = allBindings().onSuccessCallback;
        var param = allBindings().paraMeter;
        var url = valueAccessor()();
        var acceptedFiles = allBindings().acceptedFiles;
        if (param!=undefined)
            url = url +'/'+ param;

        var loader = $(element).parent("#fileParent").find(".loader");
        var maxUploadSize = parseInt(100000000);

        $(element).fileupload({
            url: url,
            //maxFileSize: 100000000,
            type: 'POST',
            dataType: 'json',
            fail: onErrorCallback,
            done:onSuccessCallback,
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                var progressbar = $(e.target).parents('.files').next().find('.ProgressBar');
                progressbar.parent().show();
                myApp.showPleaseWait();
                progressbar.css(
                    'width',
                    progress + '%'
                );
                progressbar.children('span').html(progress + '%');

                $(e.target).parents('.Uploadfile').next().find('.uploadMessage').hide();

                if (parseInt(progress) == 100) {
                    dropZonePackageUploadCount = dropZonePackageUploadCount - 1;
                    progressbar.css(
                        'width',
                        '0%'
                    );
                    progressbar.parent().hide();
                    $(loader).html("");
                }
            },
            send: function (filedata, data, formData) {

                var uploadErrors = [];
                var isValidImage = true;
                $.each(data.files, function (index, file) {

                    var extension = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();

                        if (extension !== 'zip') {
                            //uploadErrors.push(Languages.ImageFileAllowedMessage);
                            uploadErrors.push("Only .zip formatted allowed");
                            isValidImage = false;
                        }
                        debugger;
                    if (file.size > maxUploadSize * 1024 * 1024) {
                        uploadErrors.push("File exceeds maximum allowed size of " + maxUploadSize + "MB");
                        isValidImage = false;
                    }
                });


                if (uploadErrors.length > 0) {
                    var errors = {IsSuccess:false,Message:uploadErrors};
                    debugger;
                    //ShowAlertMessage(uploadErrors.join("\n"), 'error', 'Error Message');
                    ShowMessages(errors,null,'',4);

                    return false;
                }
                debugger;

                dropZonePackageUploadCount = dropZonePackageUploadCount + 1;
                $(loader).html("<img  src='/assets/images/ajax-loader.gif'>");

            }
        });
    }
};


/*
 ko.extenders.numeric = function(target, precision) {
 var result = ko.computed({
 read: function() {
 return target().toFixed(precision);
 },
 write: target
 });

 result.raw = target;
 return result;
 };
 */



ko.extenders.numeric = function(target, precision) {
    //create a writeable computed property to intercept writes to our observable 
    var result = ko.computed({
        read: target,
        write: function(newValue) {

            var current = target(),
                valueToWrite = Math.round(isNaN(newValue) ? 0 : parseFloat(newValue) * Math.pow(10, precision)) / Math.pow(10, precision);
            valueToWrite=isNaN(valueToWrite) ? 0:valueToWrite;
            if (valueToWrite !== current) {
                target(valueToWrite);
            } else {
                //if the rounded value is the same, but a different value was written, force a notification for the current field to pick it up
                if (newValue != current) {
                    target.notifySubscribers(valueToWrite);
                }
            }
        }
    }).extend({ notify: "always" });

    //initialize with current value to make sure it is rounded appropriately
    result(target());

    //return the new computed property
    return result;
};

function showbootstrapmessage (message , buttons){
    BootstrapDialog.show({
        message: message,
        buttons: buttons
    });
}

function ShowPageLoadMessage(cookieName) {
    if (cookieName == undefined) {
        cookieName = DefaultCookieName;
    }
    if ($.cookie(cookieName) != null && $.cookie(cookieName) != "null") {
        //ShowMessages($.parseJSON($.cookie(cookieName)));
        var message=$.parseJSON($.cookie(cookieName))
        ShowSuccessMessage(message,'success','Success');
        //toastr.success(($.cookie(cookieName)));
        $.cookie(cookieName, null, { path: '/' });
    }
}

function ShowSuccessMessage(message, type, header) {
    ShowMessages(message,null,null,5)
}
function SetMessageForPageLoad(data, cookieName) {
    if (cookieName == undefined) {
        cookieName = DefaultCookieName;
    }
    $.cookie(cookieName, JSON.stringify(data), { path: '/' });
}

function GetCookie(cookieName) {
    if ($.cookie(cookieName) != null) {
        var data = $.cookie(cookieName);
        $.cookie(cookieName, null, { path: '/' });
        return data;
    }

}

$(document).ready(function () {
    ShowPageLoadMessage();
});




ko.bindingHandlers.ApplyRating = {

    init: function (element, valueAccessor, allBindingsAccessor) {

        //element=$(element).parent(".applycolorpicker");
        // set default value
        var value = ko.utils.unwrapObservable(valueAccessor());
        $(element).val(value);

        //initialize datepicker with some optional options
        var options = allBindingsAccessor().ratingOptions || {};

        $(element).rating(options);

        //handle the field changing
        ko.utils.registerEventHandler(element, "rating.change", function (event, value, caption) {
            var observable = valueAccessor();
            observable(value);
        });

        //handle disposal (if KO removes by the template binding)
        ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
            $(element).rating("destroy");
        });

    },
    update: function (element, valueAccessor, allBindingsAccessor) {
        var value = ko.utils.unwrapObservable(valueAccessor());
        $(element).val(value);
        $(element).change();
    }
};

function AsyncConfirmYesNo(title, msg, yesFn, noFn) {
    var $confirm = $("#modalConfirmYesNo");
    $confirm.modal('show');
    $("#lblTitleConfirmYesNo").html(title);
    $("#lblMsgConfirmYesNo").html(msg);
    $("#btnYesConfirmYesNo").off('click').click(function () {
        yesFn();
        $confirm.modal("hide");
    });
    $("#btnNoConfirmYesNo").off('click').click(function () {
        noFn();
        $confirm.modal("hide");
    });

    $("#modalConfirmYesNo").on('hide.bs.modal', function (e) {
//    	noFn();
    })
}
function isNumber(evt) {
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode
    if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
        return false;

    return true;
}



