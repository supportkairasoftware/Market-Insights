var SaveCallDataUrl = '/savecalldata';

var ViewModel=function(responseData) {

    var self=this;
    
    self.Call= function(){
		var u=this;
		u.ScriptID=ko.observable();
		u.InitiatingPrice=ko.observable();
		u.Action=ko.observable();
		u.T1=ko.observable();
		u.T2=ko.observable();
		u.SL=ko.observable();
		u.ResultDescription=ko.observableArray();
		u.ResultValue=ko.observable();
		u.t1date=ko.observable();
		u.t2date=ko.observable();
		u.sldate=ko.observable();
		u.partialdate=ko.observable();
		u.CreatedDate=ko.observable();
	}
	self.CallModel= ko.observable(new self.Call());
	
	self.ScriptValue=function(data){
		var test=self.CallModel().ResultDescription();
		debugger;
		if(jQuery.inArray( "1",  test) >=0){
			$("#t1box").removeAttr("style");
		}
		else{
			$("#t1box").css("display","none");
		}
		
		if(jQuery.inArray( "2",  test) >=0){
			$("#t2box").removeAttr("style");
		}
		else{
			$("#t2box").css("display","none");
		}
		
		if(jQuery.inArray( "3",  test) >=0){
			$("#slbox").removeAttr("style");
		}
		else{
			$("#slbox").css("display","none");
		}
		
		if(jQuery.inArray( "4",  test) >=0){
			$("#partialbox").removeAttr("style");
		}
		else{
			$("#partialbox").css("display","none");
		}
		
		if(jQuery.inArray( "6",  test) >=0){
			$("#closebox").removeAttr("style");
		}
		else{
			$("#closebox").css("display","none");
		}
		return true;
	}
    
    self.ActionArray = ko.observableArray([
        {"Action":"Buy","value":"1"},{"Action":"Sell","value":"2"}
    ]);
    
    self.ResultDesc=ko.observableArray([{id:'1', itemName: 'T1' },
            {id:'2', itemName: 'T2' },{id:'3', itemName: 'SL' },{id:'4', itemName: 'partial' }]);
    
    self.ScriptArray=ko.observableArray();
    
    self.Save = function(data) {
    	AjaxCall(SaveCallDataUrl, ko.toJSON({ Data: data }), "post", "json", "application/json").done(function (response) {
            if(response.IsSuccess) {
            	SetMessageForPageLoad(response.Message);
            	self.CallModel(new self.Call());
            	debugger;
            }
            else{
                ShowSuccessMessage(response.Message,'error','error');
            }
        });
    };

    self.PageLoad = function (data) {
        if(data){
            ko.mapping.fromJS(data,{},self.ScriptArray);
            
        }
    };
    self.PageLoad(responseData);
    
    
};

$(document).ready(function() {
    var data = $.parseJSON($("#ScriptList").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
});