<?php
//use \Lang;
//use \Message;
use Infrastructure\Constants;
?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Script List'; ?>
@stop
@section('content')
    <main id="main" role="main" style="display: none;">
    <?php echo Form::hidden('$segments', json_encode($segments),$attributes = array('id'=>'segments'));?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">Script List</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                    	<form class="form-inline" role="form" data-bind="with:$root.SearchModel">
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input type="text" tabindex="1" class="form-control" id="searchScript" data-bind="value:$data.ScriptName" placeholder="Search Script">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                            	<select tabindex="2" style="width:100%" class="form-control"  data-bind="options: $root.SegmentList, optionsValue: 'SegmentName', optionsText: 'SegmentName',value: $data.textKeyWord, optionsCaption:'Select Segment'"  name="SegmentID" class="form-control signup-select"></select>
                            </div>
                            
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                               <select class="form-control" tabindex="5" style="width:100%" data-bind="options: $root.ActiveArray, optionsValue: 'IsActive', optionsText: 'IsActive',value: IsActive ,optionsCaption:'Select Status'" name="IsActive"></select>
</select>
                            </div>
                            <button type="submit" tabindex="6" class="btn btn-info" id="filterBtn" class="btn btn-default" data-bind="click:$root.ApplyFilter">Search</button>
                            <button type="reset"  tabindex="7" value="Clear Filters" class="btn btn-warning"  data-bind="click:$root.ClearSearch">Clear</button>
                        </form>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.ScriptListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th style="width: 52px" class="text-align-center">Sr.No</th>
                                <th style="" class="text-align-center">Script Name</th>
                                <th style="" class="text-align-center">Segment</th>
                                <th style="width: 70px" class="text-align-center">Enabled</th>
                                <th class="text-align-center width-5p">Edit</th>
                                <th style="width: 60px" class="text-align-center">Delete</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.ScriptListArray(),visible:$root.ScriptListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.Script"></td>
                                <td data-bind="text:$data.SegmentName"></td>
                                <td class="text-align-center" data-bind=""><input type="checkbox" data-bind="checked: $data.IsEnable,click: $root.EnableAnalyst"></td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs" data-bind="attr:{'href':'addscript/'+$data.EncryptScriptID()}"><i class="fa fa-pencil fa-edit-color"></i></a>
                                    </div>
                                </td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs"  data-bind="click:$root.DeleteScript"><i class="fa fa-times fa-delete-color"></i></a>
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
                            <div class="" data-bind="with:pager, visible: $data.ScriptListArray().length > 0">
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/scriptlist.js');?>"></script>
    <script type="text/javascript">
    	var AllRecords  = '<?php echo Constants::$AllRecords;?>';
        $('#script').addClass('active');
        $('#scriptlist').addClass('active');
    </script>
@stop