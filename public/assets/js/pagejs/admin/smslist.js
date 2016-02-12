var SMSListUrl ='/postsmslist';
var sms = '/smslist';


var DM;
var ViewModel=function() {
    var self=this;
    self.pager = new Pager();

    self.SMSListArray = ko.observableArray();
    self.ActionListArray = ko.observableArray([
        {"Action":"Sent","value":"1"},{"Action":"Pending","value":"0"}
    ]);
    
    self.pager.isSearch(false);

    self.SearchCategories=function(){
        var sc = this;
        sc.Action = ko.observable();
        sc.textKeyWord = ko.observable();
        sc.startDate = ko.observable();
        sc.endDate = ko.observable();
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
            if(param.SearchParams.startDate){
                param.SearchParams.startDate = moment(param.SearchParams.startDate).format('YYYY-MM-DD');
            }
            if(param.SearchParams.endDate){
                param.SearchParams.endDate = moment(param.SearchParams.endDate).format('YYYY-MM-DD');
            }
        }
        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(SMSListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.SMSListArray,'', self.SMSListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.SMSListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);
};
$(document).ready(function() {
    DM = new ViewModel();
    ko.applyBindings(DM);
    $("#main").show();
});
