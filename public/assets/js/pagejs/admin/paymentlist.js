var PaymentListUrl ='/getuserpaymentlist';
var cookieFLName = 'cookieFLName';
var cookieSubmitBtnName = 'cookiePaymentHistoryBtn';
var DeletePamyentURL = '/deleteuserpayment';
var DM;

var ViewModel=function(responseData) {

    var self=this;
    self.pager = new Pager();
    self.pager.isSearch(false);
    self.SearchModel= ko.observable();
    self.UserPaymentListArray=ko.observableArray();
    //self.RefNoListArray=ko.observableArray();
    self.PlanListArray=ko.observableArray();
    /*self.TrialListArray = ko.observableArray([
        {"IsTrial":"Trial","value":"Trial"},{"IsTrial":"Paid","value":"Paid"}
    ]);*/

    self.ActiveArray = ko.observableArray([
        {"IsActive":"Active","value":"1"},{"IsActive":"InActive","value":"0"}
    ]);

    self.SearchCategories=function(){
        var sc = this;
        sc.textKeyWord = ko.observable();
        //sc.IsTrial = ko.observable();
        sc.IsActive = ko.observable();
        sc.ReferenceNo = ko.observable();
        sc.PlanName = ko.observable();
    };

    self.SearchModel = ko.observable(new self.SearchCategories());
	
	if($('#Plan').val() != ''){
		self.SearchModel().PlanName($('#Plan').val());
		self.SearchModel().IsActive('Active');
		self.pager.isSearch(true);
		$.cookie(cookieFLName, '', { path: '/' });
        $.cookie(cookieSubmitBtnName, '', { path: '/' });
	}

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
        $.cookie(cookieFLName, '', { path: '/' });
        $.cookie(cookieSubmitBtnName, '', { path: '/' });
    };

    self.PageLoad = function () {
        data=responseData;
       // ko.mapping.fromJS(data.RefNoListArray,{},self.RefNoListArray);
        ko.mapping.fromJS(data.PlanListArray,{},self.PlanListArray);

        var param = {
            PageIndex: self.pager.currentPage()
        };

        if(self.pager.isSearch()){
            param.SearchParams = ko.toJS(self.SearchModel());
            self.pager.iPageSize(AllRecords);
        }

       /* if(!self.pager.sort())
        {
            self.pager.sort("EndDate");
            self.pager.sortDirection("DESC");
        }*/
        param.SortIndex = self.pager.sort();
        param.SortDirection = self.pager.sortDirection();

        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(PaymentListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
            	ko.mapping.fromJS(response.Data.UserPaymentListArray,'', self.UserPaymentListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                var FLName = $.cookie(cookieFLName);
				if(FLName ){
	                self.SearchModel().textKeyWord(FLName);
	                var cookieSubmitBtnName =  $.cookie(cookieSubmitBtnName).cookiePaymentHistoryBtn ;
	                if(cookieSubmitBtnName != ''){
	                   document.getElementById("filterBtn").click();
	                }	
				}
                
                if(self.UserPaymentListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };
    
    self.DeleteUserPayment = function(data){
    	self.SearchModel(new self.SearchCategories());
        ShowConfirm("<b>"+ data.PlanName()+" ("+data.IsActive()+") plan for "+data.DisplayName()+" ("+data.Email()+")</b>?", function () {
            AjaxCall(DeletePamyentURL, ko.toJSON({ Data : data.PaymentHistoryID()}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + "/userpaymentlist";
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Payment');
	}
    
    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);

    /*-------------------- */
};
$(document).ready(function() {
    var data = $.parseJSON($("#PaymentModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
	$("#main").show();


});


