var PlanListUrl ='/getplanlist';
var plan = '/planlist';
var UpdatePlanUrl='/updateplan';
var UpdateTrial ='/updatetrial';
var DeletePlanUrl = "/deleteplan";
var DM;
var ViewModel=function() {
    var self=this;
    self.PlanListArray=ko.observableArray();
    self.pager = new Pager();

    self.PageLoad = function () {
        var param = {
            PageIndex: self.pager.currentPage()
        };
        param.PageSize = self.pager.selectedPageSize();
        AjaxCall(PlanListUrl, ko.toJSON({ Data: param }), "post", "json", "application/json").done(function (response) {
            if (response.IsSuccess) {
                ko.mapping.fromJS(response.Data.PlanListArray,'', self.PlanListArray);
                self.pager.currentPage(response.Data.CurrentPage);
                self.pager.iPageSize(response.Data.ItemsPerPage);
                self.pager.iTotalDisplayRecords(response.Data.ItemsPerPage);
                self.pager.iTotalRecords(response.Data.TotalItems);
                if(self.PlanListArray().length<=0)
                    $('#nodata').show();
                else
                    $('#nodata').hide();
            }
        });
    };

    self.UpdatePlan=function(data) {
        var postData = {
            Data: data
        };
        AjaxCall(UpdatePlanUrl, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
            if(response.IsSuccess) {
                ShowSuccessMessage(response.Message,'success','success');
            }
            else{
                ShowSuccessMessage(response.Message,'error','error');
            }
        });
        return true;
    };
    
    self.DeletePlan = function(data){
        ShowConfirm("'"+ data.PlanName()+"'?", function () {
            AjaxCall(DeletePlanUrl, ko.toJSON({ Data : data.PlanID()}), "post", "json", "application/json").done(function (response) {
                if (response.IsSuccess) {
                    SetMessageForPageLoad(response.Message);
                    location.href = baseUrl + "/planlist";
                }else{
                    ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                }
            });
        }, 'Delete Plan');
	}

    self.TotalPlanChecked = ko.computed(function() {
        var total = 0;
        ko.utils.arrayForEach(self.PlanListArray(), function(planTrial) {
            if(planTrial.IsTrial() == 1 || planTrial.IsTrial() == true)
                total += planTrial.IsTrial();
        });
        return total;
    },self.TotalPlanChecked);

    self.UpdateTrail = function(data) {
		window.activeplan = data;
        /*if(self.TotalPlanChecked() > 1){
            ShowAlertMessage('You can set only one plan as trial plan',window.ConfirmDialogSomethingWrong);
            data.IsTrial(false);
            return false;
        }*/

        var postData = {
            Data: data
        };

        AjaxCall(UpdateTrial, ko.toJSON(postData), "post", "json", "application/json").done(function (response) {
            if(response.IsSuccess) {
                ShowSuccessMessage(response.Message,'success','success');
            }
            else{
                //ShowSuccessMessage(response.Message,'error','error');
                ShowAlertMessage(response.Message,'error',window.ConfirmDialogSomethingWrong);
                window.activeplan.IsTrial(false);
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
