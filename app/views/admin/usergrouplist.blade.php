<?php
use Infrastructure\Common;
use Infrastructure\Constants;
?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Users Group List'; ?>
@stop
@section('content')
    <main id="main" role="main" class="displayhide">
        <?php echo Form::hidden('UserGroupModel', json_encode($UserGroupModel),$attributes = array('id'=>'UserGroupModel')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">Users Group List</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                       <form class="form-inline" role="form" data-bind="with:$root.SearchModel()">
                       		<div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input type="text" tabindex="1" class="form-control" id="searchuser" data-bind="value:$data.textKeyWord" placeholder="Search User ">
                            </div>
                       		<div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <select name="GroupID" tabindex="2" data-bind="value:Group,options:$root.GroupListArrayForSearch(),optionsCaption:'Select Group',optionsValue:'GroupID',optionsText:'GroupName' " class="form-control" style="width:100%"></select>
                            </div>
                            <button type="submit" tabindex="3" class="btn btn-info" data-bind="click:$root.ApplyFilter">Search</button>
                            <button type="reset" tabindex="4"  value="Clear Filters" class="btn btn-warning"  data-bind="click:$root.ClearSearch">Clear</button>
							<a class="btn btn-success" tabindex="5" tabindex="4" style="float: right" data-toggle="modal" href="#myModal">Add User In Group</a>
                        </form>
                    </div>
                    <div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button  style="margin-top:-2px;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title">Add User In Group</h4>
                                </div>
                                <div class="modal-body">
                                    <form role="form" data-bind="with:$root.GroupModel()">
                                        <div class="form-group input-col">
                                            <label for="exampleInputEmail1">Group</label>
                                            <select name="GroupID" tabindex="1" data-bind="value:GroupID,options:$root.GroupListArrayForSearch(),optionsCaption:'Select Group',optionsValue:'GroupID',optionsText:'GroupName'" class="form-control"></select>
                                        </div>
                                        <div class="form-group input-col">
                                            <label for="exampleInputPasword1">User</label>
                                            <input id="demo5" type="text" class="col-md-12 form-control" placeholder="Search User..." autocomplete="off" />
                                            <span class="validationMessage UserID" style="display:none">User is required</span>
                                        </div>
                                        <div class="modal-footer" style="margin-top:20px">
                                            <button  class="btn btn-success" type="button" data-bind="click:$root.SaveUserGroup">Save</button>
                                            <button  id="close" data-bind="click:$root.CloseModel" class="btn btn-default" type="button">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.UserGroupListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th style="width: 52px" class="text-align-center">Sr.No</th>
                                <th style="" class="text-align-center">Group(s)</th>
                                <th style="" class="text-align-center">User</th>
                                <th style="" class="text-align-center">Email</th>
                                <th style="max-width: 150px" class="text-align-center">Mobile</th>
                                <th style="width: 60px" class="text-align-center">Delete</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.UserGroupListArray(),visible:$root.UserGroupListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.GroupName"></td>
                                <td data-bind="text:$data.UserName"></td>
                                <td data-bind="text:$data.Email"></td>
                                <td data-bind="text:$data.Mobile"></td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs"  data-bind="click:$root.deleteGroup"><i class="fa fa-times fa-delete-color"></i></a>
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
                            <div class="" data-bind="with:pager, visible: $data.UserGroupListArray().length > 0">
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
    <script>
        var AllRecords = '<?php echo Constants::$AllRecords;?>';
        window.Required_GroupName ="{{ trans('messages.PropertyRequired',array('attribute'=>'Group Name'))}}";
        window.Required_UserName ="{{ trans('messages.PropertyRequired',array('attribute'=>'User Name'))}}";
        function selectUser(i,val) {
            $("#searchUser").val(i);
            $("#suggesstion-box").hide();
            DM.GroupModel().UserID(val);
        }
    </script>
    
    <script src="<?php echo asset('/assets/js/pagejs/pager.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/admin/usergrouplist.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/bootstrap-typeahead.js');?>"></script>
    <script type="text/javascript">
        $('#usergroup').addClass('active');
        $('#groups').addClass('active');
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
    </script>
@stop