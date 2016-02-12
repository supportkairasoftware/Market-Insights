@extends('layouts.sitemaster')
@section('Title')<?php print 'Users List'; ?>
@stop
@section('content')
    <main id="main" role="main" style="display: none;">
        <?php //echo Form::hidden('ViewModel', json_encode($Model),$attributes = array('id'=>'ViewModel')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">Users List</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" role="form" data-bind="with:$root.SearchModel">
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="1" type="text" class="form-control" id="FirstName" data-bind="value:$data.Name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input  tabindex="2" type="text" class="form-control" id="searchgroup" data-bind="value:$data.Email" placeholder="Email">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="3" type="text" class="form-control" id="searchuser" data-bind="value:$data.City" placeholder="City">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="4" type="text" class="form-control" id="searchgroup" data-bind="value:$data.State" placeholder="State">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <input tabindex="5" type="text" class="form-control" id="searchuser"  data-bind="value:$data.Mobile" placeholder="Mobile">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <input tabindex="6" id="date" name="FromDate"  type="text" class="form-control" placeholder="From Date" data-bind="datepicker:FromDate,valueUpdate:'afterkeydown'" >
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <input type="text" tabindex="7" class="form-control" id="ToDate" data-bind="datepicker:ToDate,valueUpdate:'afterkeydown'" placeholder="To Date">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <select  tabindex="8"class="form-control" style="width:100%" data-bind="options: $root.PlanListArray, value:PlanName, optionsValue: 'PlanName', optionsText: 'PlanName', optionsCaption:'Select Plan'" name="PlanName"></select>
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <select  tabindex="9" class="form-control" style="width:100%"  data-bind="options: $root.GroupListArray, value:GroupID, optionsValue: 'GroupID', optionsText: 'GroupName', optionsCaption:'Select Group'" name="GroupID"></select>
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <select tabindex="10" class="form-control" style="width:100%" data-bind="options: $root.RoleListArray, optionsValue: 'RoleID', optionsText: 'RoleName',value: RoleID , optionsCaption:'Select Role',decorateErrorElement: RoleID" name="RoleID"></select>
                            </div>
                            <button type="reset"  tabindex="12" value="Clear Filters" class="btn btn-warning btn-clear"  data-bind="click:$root.ClearSearch">Clear</button>
                            <button type="submit" tabindex="11" class="btn btn-info btn-search" id="filterBtn" class="btn btn-default" data-bind="click:$root.ApplyFilter">Search</button>
                        </form>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.UserListArray().length > 0">
                            <tr data-bind="with:pager" class="table-head-color">
                                <th  class="text-align-center width-5p">Sr.No</th>
                                <th  class="text-align-center">Name</th>
                                <th class="text-align-center">Email</th>
                                <th  class="text-align-center">City</th>
                                <th  class="text-align-center">State</th>
                                <th  class="text-align-center">Mobile</th>
                                <th  class="text-align-center">Group Name</th>
                                <th  class="text-align-center">Plan</th>
                                <th  class="text-align-center">Created On</th>
                                <th  class="text-align-center width-6p">Verified</th>
                                <th style="width: 67px" class="text-align-center">Enable</th>
                                <th style="width: 83px" class="text-align-center">Action</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.UserListArray(),visible:$root.UserListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.Name"></td>
                                <td data-bind="text:$data.Email"></td>
                                <td data-bind="text:$data.City"></td>
                                <td data-bind="text:$data.State"></td>
                                <td style="white-space: nowrap" data-bind="text:$data.Mobile"></td>
                                <td data-bind="text:$data.Groups"></td>
                                <td data-bind="text:$data.PlanName"></td>
                                <td data-bind="text:moment($data.CreatedDate()).format('DD/MM/YYYY')"></td>
                                <td data-bind="text:$data.IsVerified() == 1? 'Yes' : 'No'"></td>
                                <td class="text-align-center" data-bind="">
                                <!-- ko if:$data.UserID()>1 -->
                                	<input type="checkbox" data-bind="checked: $data.IsEnable,click: $root.UpdateUser">
                                <!-- /ko -->	
                                <!-- ko if:$data.UserID()==1 -->
                                Yes
                                <!-- /ko -->	
                                </td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs" title="Edit User" data-bind="attr:{'href':'edituser/'+$data.EncryptUserID()}"><i class="fa fa-pencil fa-edit-color"></i></a>
                                     &nbsp;&nbsp;
                                    <!-- ko if:$data.UserID()>1 -->
                                        <a class="btn btn-default btn-xs" title="Delete User" data-bind="click:$root.DeleteUser"><i class="fa fa-times fa-delete-color"></i></a>
									<!-- /ko -->	
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="displayhide" id="nodata">
                            <div class="col-md-12 alert alert-warning ">
                                <i class="icon-large icon-warning-sign"></i>&nbsp;&nbsp;&nbsp;{{ trans('messages.NoRecord')}}
                            </div>
                        </div>
                        <div class="table-foot">
                            <div class="" data-bind="with:pager, visible: $data.UserListArray().length > 0">
                                <ul class="pagination pagination-sm no-margin pull-right" data-bind="if:allPages().length > 0">
                                    <li data-bind="css:{'disabled':currentPage()== 1} "><a title="Previous" data-bind="click: previousPage"><span class="arrow"></span> Prev</a></li>
                                    <!-- ko foreach: $data.pagesToShow() -->
                                    <li class="active" data-bind="css: { active: $data.pageNumber == $parent.currentPage() }">
                                        <a data-bind="attr: {title:$data.pageNumber},text: $data.pageNumber, click: $parent.gotoPage,attr:{disabled:$data.pageNumber === $parent.currentPage()}"></a>
                                    </li>
                                    <!-- /ko -->
                                    <li data-bind="css:{'disabled':currentPage() == allPages().length}">
                                        <a title="Next" data-bind="click: nextPage">Next <span class="arrow"></span></a></li>

                                </ul>
                            </div>
                        </div>
                    </div><!-- /.panel-body -->
                </div><!-- /.panel -->

            </div>
        </div>
    </main>
@stop
@section('script')
    <script src="<?php echo asset('/assets/js/pagejs/pager.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/admin/userlist.js');?>"></script>
    <script type="text/javascript">
        $('#userlist').addClass('active');
    </script>
@stop