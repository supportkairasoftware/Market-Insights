var GroupListUrl ='/getgrouplist';
var group = '/grouplist';
var DeleteGroupURL = '/dltgroup';
var EnableGroupUrl ='/enablegroup';
var DM;
var ViewModel=function() {
    var self=this;
    self.GroupListArray=ko.observableArray();
    self.pager = new Pager();
    self.SearchModel= ko.observable();
    
    self.ActiveArray = ko.observableArray([
        {"IsActive":"Enabled","value":"1"},{"IsActive":"Disabled","value":"0"}
    ]);
        
	self.SearchCategories=function(){
        var sc = this;
        sc.textKeyWord = ko.observable();
        sc.IsActive = ko.observable();
    };
    
    self.SearchModel = ko.observable(new self.SearchCategories());
    
    self.getList = function(data){
        self.SearchModel(new self.SearchCategories());
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
        self.pager.currentPage(defaultCurrentPage);
        self.PageLoad();
    };
    
    self.ClearSearch = function(){
        self.SearchModel(new self.SearchCategories());
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
        self.PageLoad();
    };

    self.ApplyFilter = function(data){
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
        
        AjaxCall(GroupListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.GroupListArray,'', self.GroupListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.GroupListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };
    
    self.DeleteGroup = function (data) {
    	self.SearchModel(new self.SearchCategories());
        ShowConfirm("'"+ data.GroupName()+"'?", function () {
            AjaxCall(DeleteGroupURL, ko.toJSON({ Data : data.GroupID() }), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + group;
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Group');
    };

    self.EnableGroup = function(data) {
        self.SearchModel(new self.SearchCategories());
        var postData = {
            Data: data
        };
        AjaxCall(EnableGroupUrl, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
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
    DM = new ViewModel();
    ko.applyBindings(DM);
    $("#main").show();
});
