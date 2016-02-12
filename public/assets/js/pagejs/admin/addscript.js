var SaveScriptUrl = '/savescript';
var GetScriptListURL = '/scriptlist';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.IsEditMode = ko.observable(false);
    self.SegmentList=ko.observableArray();
    self.imageerror=ko.observable();
    self.keyimage=ko.observable(true);
    
    self.ScripModel = function(){
    	var u =this;
		u.ScriptID= ko.observable(0);
		u.SegmentID = ko.observable().extend({required:{message:window.Required_Segment}});;
		u.Script=ko.observable().extend({required:{message:window.Required_Script}});;        
    };
    self.setImage= function() {
    	self.keyimage(false);
	};
    
	self.Save = function(data) {
    	var error = ko.validation.group(data, {deep:true});
    	
        if (error().length == 0 && self.imageerror() == undefined) {
        	$('body').addClass("loading");
            delete data.errors;
            var formData = new FormData($("form#data")[0]);
debugger;
            AjaxCall(SaveScriptUrl, formData, "POST", "json", false, "", "", false).done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetScriptListURL;
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
        location.assign(baseUrl + GetScriptListURL);
    };
    
	self.PageLoad = function (data) {
        self.DataModel = ko.observable();
        if(data.ScriptDetails==null)
		 	self.ScriptDetailsModel = ko.observable(new self.ScripModel());
		else
		{
	 	  self.ScriptDetailsModel = ko.mapping.fromJS(data.ScriptDetails,{},ko.observable(new self.ScripModel()));
		}
        self.SegmentList=ko.mapping.fromJS(data.SegmentList);
    };
    
    self.PageLoad(responseData);
};

$(document).ready(function() {
    var data = $.parseJSON($("#ScriptModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});