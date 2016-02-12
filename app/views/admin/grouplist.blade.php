<?php
//use \Lang;
//use \Message;
use Infrastructure\Constants;
?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Group List'; ?>
@stop
@section('content')
    <main id="main" role="main" class="displayhide">
        <?php //echo Form::hidden('ViewModel', json_encode($Model),$attributes = array('id'=>'ViewModel')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">Group List</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                    	<form class="form-inline" role="form" data-bind="with:$root.SearchModel">
                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                                <input type="text" tabindex="1" class="form-control" id="searchuser" data-bind="value:$data.textKeyWord" placeholder="Search Group ">
                            </div>
                            <div class="form-group col-md-4 col-sm-4 col-xs-12">
                               <select class="form-control" tabindex="5" style="width:100%" data-bind="options: $root.ActiveArray, optionsValue: 'IsActive', optionsText: 'IsActive',value: IsActive,optionsCaption:'Select Status' " name="IsActive"></select>
</select>
                            </div>
                            <button type="submit" tabindex="6" class="btn btn-info" id="filterBtn" class="btn btn-default" data-bind="click:$root.ApplyFilter">Search</button>
                            <button type="reset"  tabindex="7" value="Clear Filters" class="btn btn-warning"  data-bind="click:$root.ClearSearch">Clear</button>
                            
                        </form>
                    </div>
                    <div class="panel-body table-responsive">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.GroupListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th class="text-align-center width-5p">Sr.No</th>
                                <th style="width: 30%" class="text-align-center">Groups</th>
                                <th  class="text-align-center width-10p">No Of Users</th>
                                <th class="text-align-center width-10p">Enable</th>
                                <th  class="text-align-center width-5p">Action</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.GroupListArray(),visible:$root.GroupListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.DisplayName">Update software</td>
                                <td data-bind="text:$data.user_count"></td>
                                <td class="text-align-center" data-bind=""><input type="checkbox" data-bind="checked: $data.IsEnable,click: $root.EnableGroup"></td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs" title="Edit Group" data-bind="attr:{'href':'addgroup/'+$data.EncryptGroupID()}"><i class="fa fa-pencil fa-edit-color"></i></a>&nbsp;&nbsp;
                                        <a class="btn btn-default btn-xs" title="Delete Group" data-bind="click:$root.DeleteGroup"><i class="fa fa-times fa-delete-color"></i></a>
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
                            <div class="" data-bind="with:pager, visible: $data.GroupListArray().length > 0">
                                <ul class="pagination pagination-sm no-margin pull-right" data-bind="if:allPages().length > 0">
                                    <!-- <li><a href="#">&laquo;</a></li>
                                     <li><a href="#">1</a></li>
                                     <li><a href="#">2</a></li>
                                     <li><a href="#">3</a></li>
                                     <li><a href="#">&raquo;</a></li>-->
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/grouplist.js');?>"></script>
    <script type="text/javascript">
    	var AllRecords  = '<?php echo Constants::$AllRecords;?>';
        $('#grouplist').addClass('active');
        $('#groups').addClass('active');
    </script>
@stop