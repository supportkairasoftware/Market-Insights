
var SaveAnalystUrl = 'send.php';
var ViewModel=function() {

    var self=this;
	
    self.MobileNo=ko.observable().extend({required:true});
	
    self.SaveNo = function(data)
    {
	   var error = ko.validation.group(data, {deep:true});
       if(error().length == 0){
        //if(1==1){
            delete data.errors;
            AjaxCall(SaveAnalystUrl,ko.toJSON({ Data : data }) ,"POST" , "json" , "application/json").done(function(response){
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + GetPlanListURL;
                }
                else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }else{
            error.showAllMessages(true);
        }
    };
};

$(document).ready(function() {
    ko.applyBindings(new ViewModel());
});