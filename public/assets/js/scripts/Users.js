var viewModel;

var UsersModel=function() {
	
	var self=this;
	
	self.Usersobj=function(){
		this.FirstName=ko.observable('');
		this.LastName=ko.observable('');
		this.UserName=ko.observable('');
		this.password=ko.observable('');
	};
	
	self.ConfirmPassword=ko.observable('');
	
};
			
						
$(document).ready(function(){
	viewModel = new UsersModel();
	ko.applyBindings(viewModel);
});