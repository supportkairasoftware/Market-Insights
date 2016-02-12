var DefaultCookieName = "DefaultMessage";

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
        beforeSend: function () { $('body').addClass("loading"); }, //$.blockUI();},//beforeSend: function() { if (showLoading) myApp.showPleaseWait(); },$('body').addClass("loading"); 
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

function SetMessageForPageLoad(data, cookieName) {
    
    if (cookieName == undefined) {
        cookieName = DefaultCookieName;
    }
    $.cookie(cookieName, JSON.stringify(data), { path: '/' });
}

function ShowPageLoadMessage(cookieName) {
    if (cookieName == undefined) {
        cookieName = DefaultCookieName;
    }
    if ($.cookie(cookieName) != null && $.cookie(cookieName) != "null") {
        //ShowMessages($.parseJSON($.cookie(cookieName)));
    	ShowSuccessMessage($.cookie(cookieName),'success','Success');
    	//toastr.success(($.cookie(cookieName)));
        $.cookie(cookieName, null, { path: '/' });
    }
}

function SetCookie(data, cookieName) {
    if (cookieName == undefined) {
        cookieName = DefaultCookieName;
    }
    $.cookie(cookieName, data, { path: '/' });
}
function ShowSuccessMessage(message, type, header) {
        
      
        $.msgGrowl({
            type: type ? type : 'success',
            text: message,
            position: 'top-center',
            lifetime: 5000
        });
    }

function GetCookie(cookieName) {
    if ($.cookie(cookieName) != null) {
        var data = $.cookie(cookieName);
        $.cookie(cookieName, null, { path: '/' });
        return data;
    }

}

$(document).ready(function () {
    $('input:first').focus();
    ShowPageLoadMessage();    
});


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
window.ConfirmDialogSomethingWrong ="Oops! Something went wrong";
window.SuccessConfirm="Success";

function trim(str){
    var str=str.replace(/^\s+|\s+$/,'');
    return str;
}