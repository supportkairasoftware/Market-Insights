var CallListUrl ='/getcalllist';
var group = '/calllist';
var SearchScriptURL = baseUrl +'/searchscript';
var DeleteCallURL = '/deletecall';
var DM;
var ViewModel=function(responseData) {

    var self=this;
    self.pager = new Pager();
    self.pager.isSearch(false);
    self.SearchModel= ko.observable();
    self.CallListArray=ko.observableArray();
    self.ResultListArray=ko.observableArray();
    self.SegmentListArray=ko.observableArray();
    self.ActionListArray = ko.observableArray([
       {"Action":"Buy","value":"1"},{"Action":"Sell","value":"2"}
    ]);
    self.IsOpenArray = ko.observableArray([
        {"IsOpen":"Open","value":"1"},{"IsOpen":"Closed","value":"0"}
    ]);
    self.SearchCategories=function(){
        var sc = this;
        sc.Script = ko.observable();
        sc.Action = ko.observable();
        sc.IsOpen = ko.observable();
        sc.SegmentID = ko.observable();
        sc.ResultID = ko.observable();
        sc.IsOpen = ko.observable();
        sc.FromDate = ko.observable();
        sc.ToDate = ko.observable();

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
        data=responseData;
        //ko.mapping.fromJS(data,{},self.GroupListArrayForSearch);
        ko.mapping.fromJS(data.ResultListArray,{},self.ResultListArray);
        ko.mapping.fromJS(data.SegmentListArray,{},self.SegmentListArray);

        var param = {
            PageIndex: self.pager.currentPage()
        };

        if(self.pager.isSearch()){
            param.SearchParams = ko.toJS(self.SearchModel());
            if(param.SearchParams.FromDate){
                param.SearchParams.FromDate = moment(param.SearchParams.FromDate).format('YYYY-MM-DD');
            }
            if(param.SearchParams.ToDate){
                param.SearchParams.ToDate = moment(param.SearchParams.ToDate).format('YYYY-MM-DD');
            }

            self.pager.iPageSize(AllRecords);
        }

        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(CallListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.CallListArray,'', self.CallListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.CallListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };
    //self.PageLoad(responseData);
    
    self.DeleteCall = function(data){
		self.SearchModel(new self.SearchCategories());
        ShowConfirm("<b>"+ data.Script()+" ("+data.SegmentName()+")</b> "+data.IsOpen()+" call?", function () 			{
            AjaxCall(DeleteCallURL, ko.toJSON({ Data : data.CallID()}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + "/calllist";
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Call');
	}

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);

    /*-------------------- */

};
$(document).ready(function() {

    var data = $.parseJSON($("#CallModel").val());
    DM = new ViewModel(data);
    ko.applyBindings(DM);
    $("#main").show();

   /* $("#FromDate").datepicker({
        numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() + 1);
            $("#ToDate").datepicker("option", "minDate", dt);
        }
    });
    $("#ToDate").datepicker({
        numberOfMonths: 2,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            $("#FromDate").datepicker("option", "maxDate", dt);
        }
    });*/

    /*$(document).ready(function() { $("#selectUser").select2(); });*/

    $("#searchScript").keyup(function(){
        $.ajax({
            type: "POST",
            url: SearchScriptURL +'?script='+$(this).val(),
            dataType: "json",
            cache: false,
            contentType: "application/json",
            // Data:'keyword='+$(this).val(),
            beforeSend: function(){
                $("#searchScript").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data){
                $("#suggesstion-box").show();
                $("#suggesstion-box").html(data.Data.ScriptListWithHTML);
                $("#searchScript").css("background","#FFF");
            }
        });
    });
});


