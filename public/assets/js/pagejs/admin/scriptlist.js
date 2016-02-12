var ScriptListUrl ='/getscriptlist';
var EnableScriptUrl ='/enablescript';
var DeleteScriptURL = '/deletescript';

var DM;
var ViewModel=function() {
    var self=this;
    self.ScriptListArray = ko.observableArray();
    self.pager = new Pager();	
	self.SearchModel= ko.observable();
	self.SegmentList=ko.observableArray();
    
    self.ActiveArray = ko.observableArray([
        {"IsActive":"Enabled","value":"1"},{"IsActive":"Disabled","value":"0"}
    ]);
        
	self.SearchCategories=function(){
        var sc = this;
        sc.textKeyWord = ko.observable();
        sc.ScriptName= ko.observable();
        sc.IsActive = ko.observable();
    };
    
    self.SearchModel = ko.observable(new self.SearchCategories());
    
    self.ClearSearch = function(){
        self.SearchModel(new self.SearchCategories());
		localStorage.setItem('scriptFilter', '');
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
        self.PageLoad();
    };
	
	if(localStorage.getItem('scriptFilter') != ''){
		self.SearchModel(ko.mapping.fromJSON(localStorage.getItem('scriptFilter'),{},new self.SearchCategories()));
		self.pager.isSearch(true);
	}

    self.ApplyFilter = function(data){
		localStorage.setItem('scriptFilter', ko.toJSON(self.SearchModel()));
    	self.pager.isSearch(true);
        self.pager.search();
    };
    
    self.PageLoad = function () {
        var param = {
            PageIndex: self.pager.currentPage()
        };
        
        if(self.pager.isSearch()){
            param.SearchParams = ko.toJS(self.SearchModel());
            self.pager.iPageSize(AllRecords);
        }
        
        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(ScriptListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.ScriptListArray,'', self.ScriptListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.ScriptListArray().length==0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };
    
    self.DeleteScript = function(data){
    	self.SearchModel(new self.SearchCategories());
        ShowConfirm("'"+ data.Script()+"'?", function () {
            AjaxCall(DeleteScriptURL, ko.toJSON({ ScriptID : data.ScriptID }), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + "/scriptlist";
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Script');
	}

    self.EnableAnalyst = function(data) {
        var postData = {
            Data: data
        };
        AjaxCall(EnableScriptUrl, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
            if(response.IsSuccess) {
                ShowSuccessMessage(response.Message,'success','success');
            }
            else{
                ShowSuccessMessage(response.Message,'error','error');
            }
        });
        return true;
    };

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);
};
$(document).ready(function() {
	var data = $.parseJSON($("#segments").val());
    DM = new ViewModel();
    DM.SegmentList=ko.mapping.fromJS(data);
    ko.applyBindings(DM);
    $("#main").show();
});
