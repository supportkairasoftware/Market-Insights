var GroupListUrl ='/getusergrouplist';
var group = '/grouplist';
var UsergroupList ="/usergrouplist";
var DeleteGroupURL = '/deletegroup';
var SaveUserGroupUrl = '/saveUserGroup';
var SearchUserURL = baseUrl +'/searchuser';
var DM;
var ViewModel=function(responseData) {

    var self=this;
    self.UserGroupListArray=ko.observableArray();
    self.pager = new Pager();
    self.pager.isSearch(false);
    self.SearchModel= ko.observable();
    self.GroupListArrayForSearch=ko.observableArray();
    self.UserListArray=ko.observableArray();

    self.SearchCategories=function(){
        var sc = this;
        sc.textKeyWord = ko.observable();
        sc.Group = ko.observable();
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
	
	self.CloseModel=function(data){
		self.GroupModel(new self.UserGroup());
		$("#myModal").modal('hide');
		 $(function() {
            $('#demo5').typeahead({
                ajax: {
                    url: baseUrl+'/userlisturl',
                    method: 'post',
                },
                onSelect:function(item){
					window.DM.GroupModel().UserID(item.value);	
				},
                scrollBar:true,
                hint: true,
			  	highlight: true,
			  	minLength: 1
            });
        });
	}
	
    self.ApplyFilter = function(data){
        self.pager.isSearch(true);
        self.pager.search();
    };

    self.deleteGroup = function (data) {
        ShowConfirm(" This user : "+" ' "+ data.UserName()+" ' In this Group : "+ data.GroupName()+"?", function () {
            AjaxCall(DeleteGroupURL, ko.toJSON({ Data : data}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + UsergroupList;
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Group');
    };

    self.UserGroup=function(){
        var ug =this;
        ug.UserGroupID=ko.observable(0);
        ug.GroupID =ko.observable().extend({required:{message:window.Required_GroupName}});
        ug.UserID = ko.observable().extend({required:{message:window.Required_UserName}});
        ug.EncryptGroupID = ko.observable();
    };
    self.GroupModel = ko.observable(new self.UserGroup());

    self.SaveUserGroup = function(data)
    {
        var error = ko.validation.group(data, {deep:true});
        var errorName= data.UserID();
        if(error().length == 0 && data.UserID()){
            delete data.errors;
            AjaxCall(SaveUserGroupUrl,ko.toJSON({ Data : data }) ,"POST" , "json" , "application/json").done(function(response){
                if(response.IsSuccess){
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + UsergroupList;
                }
                else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }else{
        	if(errorName==undefined){
				$(".validationMessage.UserID").css('display','block');
			}
			else{
				$(".validationMessage.UserID").css('display','none');
			}
            error.showAllMessages(true);
        }
    };

    self.PageLoad = function () {
        data=responseData;
        //ko.mapping.fromJS(data,{},self.GroupListArrayForSearch);
        ko.mapping.fromJS(data.GroupListArray,{},self.GroupListArrayForSearch);
        ko.mapping.fromJS(data.UserListArray,{},self.UserListArray);

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
                ko.mapping.fromJS(response.Data.UserGroupListArray,'', self.UserGroupListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.UserGroupListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });

        if(data){
            ko.mapping.fromJS(data,{},self.GroupModel);
        }
    };
    //self.PageLoad(responseData);

    self.pager.getDataCallback = self.PageLoad;
    self.pager.selectedPageSize(docDefaultPageSize);

    /*-------------------- */


};
$(document).ready(function() {
    var data = $.parseJSON($("#UserGroupModel").val());
    window.DM = new ViewModel(data);
    ko.applyBindings(DM);
    $("#main").show();
    /*$(document).ready(function() { $("#selectUser").select2(); });*/

    $("#searchUser").keyup(function(){
        $.ajax({
            type: "POST",
            url: SearchUserURL +'?keyword='+$(this).val(),
            dataType: "json",
            cache: false,
            contentType: "application/json",
           // Data:'keyword='+$(this).val(),
            beforeSend: function(){
                $("#searchUser").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data){
                $("#suggesstion-box").show();
                $("#suggesstion-box").html(data.Data.UserListWithHTML);
                $("#searchUser").css("background","#FFF");
            }
        });
    });
});


