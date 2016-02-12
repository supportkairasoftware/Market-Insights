var SaveFundamentalUrl = '/savefundamental';
var GetFundamentalListURL = '/fundamentallist';
var RemoveImageURL = '/';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    window.self = self;
    self.IsEditMode = ko.observable(false);
    self.imageerror=ko.observable();
    self.keyimage=ko.observable(true);
    self.keyfile = ko.observable(true);
    self.pdferror=ko.observable();

    self.Fundamental = function(){
        var d =this;
        d.FundamentalID = ko.observable(0);
        d.Title = ko.observable().extend({required:{message:window.Required_Title}});
        d.Description = ko.observable();
        d.Image=ko.observable();
        //d.fileData = ko.observable({dataURL: ko.observable()  });
        d.PDF = ko.observable();
        d.EncryptFundamentalID = ko.observable();
    };

    self.FundamentalModel = ko.observable(new self.Fundamental());
    self.setImage= function() {
        self.keyimage(false);
    };

    self.setPDF= function() {
        self.keyfile(false);
    };

    self.Save = function(data) {
    	var error = ko.validation.group(data, {deep:true});
    	
        if(data.Image() == undefined && self.keyimage()){
            //self.imageerror("Image is required");
            self.imageerror(undefined);
        }else if($('#InputImage').val()){
			var ext = $('#InputImage').val().split('.').pop().toLowerCase();
        	
			if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			    self.imageerror("Only .jpeg, .jpg, .png and .gif file are allowed.");
			}else{
				self.imageerror(undefined);
			}
		}else{
			self.imageerror(undefined);
		}
        
        if(data.PDF() == undefined && self.keyfile()){
            //self.pdferror("Pdf is required");
            self.pdferror(undefined);
		}else if($('#InputPDF').val()){
			var ext = $('#InputPDF').val().split('.').pop().toLowerCase();
        	
			if($.inArray(ext, ['pdf']) == -1) {
			    self.pdferror("Only .pdf file are allowed.");
			}else{
				self.pdferror(undefined);
			}
		}else{
			self.pdferror(undefined);
        }
        
		//self.imageerror() == undefined &&
        if (error().length == 0 && self.pdferror() == undefined && self.imageerror() == undefined) {
        	$('body').addClass("loading");
        	delete data.errors;
        	var formData = new FormData($("form#data")[0]);
            AjaxCall(SaveFundamentalUrl, formData, "POST", "json", false, "", "", true).done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetFundamentalListURL;
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


    self.cancel = function(data) {
        location.assign(baseUrl + GetFundamentalListURL);
    };
    self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data){
            ko.mapping.fromJS(data,{},self.FundamentalModel);
            if(data.Image != "")
                self.keyimage=ko.observable(false);
            if(data.PDF != "")
                self.keyfile=ko.observable(false);

        }
    };
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#FundamentalModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});