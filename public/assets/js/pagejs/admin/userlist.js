var UserListUrl ='/postuserlist';
var user = '/userlist';
var UpdateUserUrl='/updateuser';
var DeleteUserURL = '/admin/deleteuser';
var DM;
var ViewModel=function() {
    var self=this;
    self.pager = new Pager();
    self.UserListArray=ko.observableArray();
    self.RoleListArray = ko.observableArray();
    self.GroupListArray = ko.observableArray();
    self.PlanListArray = ko.observableArray();
    self.pager.isSearch(false);

    self.SearchCategories=function(){
        var sc = this;
        sc.Name = ko.observable();
        sc.Email = ko.observable();
        sc.City = ko.observable();
        sc.State = ko.observable();
        sc.Mobile = ko.observable();
        sc.RoleID = ko.observable();
        sc.PlanName = ko.observable();
        sc.GroupID = ko.observable();
        sc.FromDate = ko.observable();
        sc.ToDate = ko.observable();
    };

    self.ClearSearch = function(){
        self.SearchModel(new self.SearchCategories());
        self.pager.isSearch(false);
        self.pager.iPageSize(docDefaultPageSize);
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

    self.SearchModel = ko.observable(new self.SearchCategories());

    self.PageLoad = function () {
        var param = {
            PageIndex: self.pager.currentPage()
        };
        if(self.pager.isSearch()) {
            param.SearchParams = ko.toJS(self.SearchModel());
            
            if(param.SearchParams.FromDate){
                param.SearchParams.FromDate = moment(param.SearchParams.FromDate).format('YYYY-MM-DD');
            }
            if(param.SearchParams.ToDate){
                param.SearchParams.ToDate = moment(param.SearchParams.ToDate).format('YYYY-MM-DD');
            }
        }
        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(UserListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.UserListArray,'', self.UserListArray);
                ko.mapping.fromJS(response.Data.RoleListArray,'', self.RoleListArray);
                ko.mapping.fromJS(response.Data.GroupListArray,'', self.GroupListArray);
                ko.mapping.fromJS(response.Data.PlanListArray,'', self.PlanListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.UserListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };

    self.UpdateUser = function(data)
    {
        var postData = {
            Data: data
        };
        AjaxCall(UpdateUserUrl, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
            if(response.IsSuccess)
            {
                ShowSuccessMessage(response.Message,'success','success');
            }
            else{
                ShowSuccessMessage(response.Message,'error','error');
            }
        });
        return true;
    };
    
    self.DeleteUser = function(data){
    	self.SearchModel(new self.SearchCategories());
        ShowConfirm("'"+ data.Name()+"'?", function () {
            AjaxCall(DeleteUserURL, ko.toJSON({ Data : data.UserID()}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + "/userlist";
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Script');
	}


    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);
};
$(document).ready(function() {
    DM = new ViewModel();
    ko.applyBindings(DM);
    $("#main").show();
});
