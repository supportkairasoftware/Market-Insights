var AnalystListUrl ='/getanalystlist';
var EnableAnalystUrl ='/enableanalyst';
var DeleteAnalystURL = '/deleteanalyst';

var DM;
var ViewModel=function() {
    var self=this;
    self.AnalystListArray = ko.observableArray();
    self.pager = new Pager();   
    self.SearchModel= ko.observable();
    
    self.ActiveArray = ko.observableArray([
        {"IsActive":"Published","value":"1"},{"IsActive":"Draft","value":"0"}
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
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
        self.PageLoad();
    };

    self.ApplyFilter = function(data){
    	self.pager.isSearch(true);
        self.pager.search();
    };
    

    /*self.deleteFundamental = function (data) {
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
    };*/

    self.PageLoad = function () {
        var param = {
            PageIndex: self.pager.currentPage()
        };
        
        if(self.pager.isSearch()){
            param.SearchParams = ko.toJS(self.SearchModel());
            self.pager.iPageSize(AllRecords);
        }
        
        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(AnalystListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.AnalystListArray,'', self.AnalystListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.AnalystListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };

    self.EnableAnalyst = function(data) {
        var postData = {
            Data: data
        };
        AjaxCall(EnableAnalystUrl, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
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
    
    self.DeleteAnalyst = function(data){
    	self.SearchModel(new self.SearchCategories());
        ShowConfirm("<b>"+ data.Title()+"</b>?", function () {
            AjaxCall(DeleteAnalystURL, ko.toJSON({ Data : data.AnalystID()}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + "/analystlist";
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Analyst');
	}

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);
};
$(document).ready(function() {
    DM = new ViewModel();
    ko.applyBindings(DM);
    $("#main").show();
});
