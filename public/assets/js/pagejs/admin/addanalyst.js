var SaveAnalystUrl = '/saveanalyst';
var GetAnalystListURL = '/analystlist';
var SaveUploadImage = '/saveanalystimage'
var RemoveImageURL = '/removeanalystimage';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.IsEditMode = ko.observable(false);
    self.imageerror=ko.observable();
    self.keyimage=ko.observable(true);

    self.Analyst = function(){
        var d =this;
        d.AnalystID = ko.observable(0);
        d.Title = ko.observable().extend({required:{message:window.Required_Title}});
        d.Description = ko.observable();//.extend({required:{message:window.window.Required_Scriptad}});
        //d.PDF = ko.observable().extend({required:{message:window.Required_GroupName}});
        d.Image=ko.observable();
        d.fileData = ko.observable({dataURL: ko.observable()  });
        d.EncryptAnalystID = ko.observable();

    };
    self.AnalystModel = ko.observable(new self.Analyst());
	self.setImage= function() {
    	self.keyimage(false);
	};

    self.Save = function(data) {
    	var error = ko.validation.group(data, {deep:true});
    	
    	if(data.Image() == undefined && self.keyimage()){
			//self.imageerror("image is requied");
			self.imageerror(undefined);
			if (error().length > 0)
				error.showAllMessages(true);
        }else if($('#InputImage').val()){
        	var ext = $('#InputImage').val().split('.').pop().toLowerCase();
        	
			if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			    self.imageerror("Only .jpeg, .jpg, .png and .gif file are allowed.");
			}else{
				self.imageerror(undefined);
			}
			
			if (error().length > 0)
				error.showAllMessages(true);
        }else{
			self.imageerror(undefined);
		}
        
        if (error().length == 0 && self.imageerror() == undefined) {
        	$('body').addClass("loading");
            delete data.errors;
            var formData = new FormData($("form#data")[0]);

            AjaxCall(SaveAnalystUrl, formData, "POST", "json", false, "", "", true).done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetAnalystListURL;
                }
                else {
                    ShowAlertMessage(response.Message, 'error', window.ConfirmDialogSomethingWrong);
                    $('body').removeClass("loading");
                }
            });
        }
        else{
            error.showAllMessages(true);
        }
    };


    self.RemoveImage=function(data) {
        var postData = {
            Data: data
        };
        AjaxCall(RemoveImageURL, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
        	if(response.IsSuccess)
            {
            	self.AnalystModel().Image(response.Data.Image);
                ShowSuccessMessage(response.Message,'success','success');
            }
            else{
                ShowSuccessMessage(response.Message,'error','error');
            }
        });
        return true;
    };

    self.cancel = function(data) {
        location.assign(baseUrl + GetAnalystListURL);
    };
    self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data){
            ko.mapping.fromJS(data,{},self.AnalystModel);
            if(data.Image != "")
            	self.keyimage=ko.observable(false);
            
        }
    };
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#AnalystModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});