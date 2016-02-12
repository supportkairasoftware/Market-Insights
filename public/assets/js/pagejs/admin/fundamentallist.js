var FundamentalListUrl ='/getfundamentallistlist';
var group = '/grouplist';
var DeleteGroupURL = '/deletegroup';
var EnableGroupUrl ='/enablefundamental';
var DeleteFundamentalURL = '/deletefundamental';
var DM;
var ViewModel=function() {
    var self=this;
    self.FundamentalListArray=ko.observableArray();
    self.StatusListArrayForSearch = ko.observableArray([{'StatusID':1,'StatusName':'Published'},{'StatusID':10,'StatusName':'Draft'}]);
    self.pager = new Pager();
    self.pager.isSearch(false);
	self.SearchModel= ko.observable();
	
	self.SearchCategories=function(){
        var sc = this;
        sc.textKeyWord = ko.observable();
        sc.Status = ko.observable();
    };

    self.SearchModel = ko.observable(new self.SearchCategories());
    
    /*self.getList = function(data){
        
        self.pager.iPageSize(docDefaultPageSize);
        self.pager.currentPage(defaultCurrentPage);
        self.PageLoad();
    };*/

    self.ClearSearch = function(){
        self.SearchModel(new self.SearchCategories());
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
        self.PageLoad();
    };
	
    self.deleteFundamental = function (data) {
        ShowConfirm("'"+ data.GroupName()+"'?", function () {
            AjaxCall(DeleteGroupURL, ko.toJSON({ Data : data.FundamentalID }), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + group;
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Fundamental');
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
        AjaxCall(FundamentalListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.FundamentalListArray,'', self.FundamentalListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.FundamentalListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };
    
    self.ApplyFilter = function(data){
        self.pager.isSearch(true);
        self.pager.search();
    };

    self.EnableFundamental = function(data) {
        var postData = {
            Data: data
        };
        AjaxCall(EnableGroupUrl, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
            if(response.IsSuccess) {
                ShowSuccessMessage(response.Message,'success','success');
            }
            else{
                ShowAlertMessage(response.Message,'error','Error!!!');
                data.IsEnable(0);
            }
        });
        return true;
    };
    
    self.DeleteFundamental = function(data){
    	self.SearchModel(new self.SearchCategories());
        ShowConfirm("<b>"+ data.Title()+"</b>?", function () {
            AjaxCall(DeleteFundamentalURL, ko.toJSON({ Data : data.FundamentalID()}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + "/fundamentallist";
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Fundamental');
	}

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);
};
$(document).ready(function() {
    DM = new ViewModel();
    ko.applyBindings(DM);
    $("#main").show();
});
