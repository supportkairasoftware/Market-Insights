<?php
//use \Lang;
//use \Message;
use Infrastructure\Constants;
?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Calls List'; ?>
@stop
@section('CSS')

@stop
@section('content')
    <main id="main" role="main" class="displayhide">
        <?php echo Form::hidden('CallModel', json_encode($CallModel),$attributes = array('id'=>'CallModel')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">Calls List</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" role="form" data-bind="with:$root.SearchModel">
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="1" id="demo5" data-bind="value:Script" type="text" class="col-md-12 form-control" placeholder="Search Script..." autocomplete="off" />
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                              <!-- <input type="text"  tabindex="5" class="form-control" id="FromDate" data-bind="datepicker:FromDate,valueUpdate: 'afterkeydown'" placeholder="From Date">-->
                                <input tabindex="2" id="date" name="FromDate"  type="text" class="form-control" placeholder="From Date" data-bind="datepicker:FromDate,valueUpdate:'afterkeydown'" >
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input type="text" tabindex="3" class="form-control" id="ToDate" data-bind="datepicker:ToDate,valueUpdate:'afterkeydown'" placeholder="To Date">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <select  tabindex="4" class="form-control" style="width:100%"  data-bind="options: $root.SegmentListArray, value:SegmentID, optionsValue: 'SegmentID', optionsText: 'SegmentName', optionsCaption:'Select Segment'" name="SegmentID"></select>
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <select tabindex="5" class="form-control" style="width:100%" data-bind="options: $root.ActionListArray, optionsValue: 'value', optionsText: 'Action',value: Action , optionsCaption:'Select Action',decorateErrorElement: Action" name="Action"></select>
                            </div>                            
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <select tabindex="6" class="form-control" style="width:100%"  data-bind="options: $root.ResultListArray, value:ResultID, optionsValue: 'ResultID', optionsText: 'ResultName', optionsCaption:'Select Result'" name="ResultID"></select>
                            </div>
                            
                            <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-top5">
                                <select class="form-control" tabindex="7" style="width:100%" data-bind="options: $root.IsOpenArray, optionsValue: 'IsOpen', optionsText: 'IsOpen',value: IsOpen , optionsCaption:'Select Status'" name="IsOpen"></select>
                            </div>
                            <button type="reset"  tabindex="9" value="Clear Filters" class="btn btn-warning btn-clear"  data-bind="click:$root.ClearSearch">Clear</button>
                            <button type="submit" tabindex="8" class="btn btn-info btn-search" id="filterBtn" class="btn btn-default" data-bind="click:$root.ApplyFilter">Search</button>
                        </form>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.CallListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th style="width: 52px" class="text-align-center">Sr.No</th>
                                <th style="" class="text-align-center">Script</th>
                                <th style="width: 100px" class="text-align-center">Segment</th>
                                <th style="" class="text-align-center">Initiating</th>
                                <th style="" class="text-align-center">T1</th>
                                <th style="" class="text-align-center">T2</th>
                                <th style="" class="text-align-center">SL</th>
                                <th style="width: 60px" class="text-align-center">Action</th>
                                <th style="width: 80px" class="text-align-center">Result</th>
                                <th style="width:240px" class="text-align-center">Result Desc.</th>
                                <th style="width:94px" class="text-align-center">Date</th>
                                <th style="width: 60px" class="text-align-center">Status</th>
                                <th style="width: 60px" class="text-align-center">Delete</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.CallListArray(),visible:$root.CallListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.Script"></td>
                                <td data-bind="text:$data.SegmentName"></td>
                                <td data-bind="text:$data.InitiatingPrice"></td>
                                <td data-bind="text:$data.T1"></td>
                                <td data-bind="text:$data.T2"></td>
                                <td data-bind="text:$data.SL"></td>
                                <td data-bind="text:$data.Action"></td>
                                <td data-bind="text:$data.ResultName"></td>
                                <td data-bind="html:$data.ResultDescription"></td>
                                <td data-bind="text:moment($data.CreatedDate()).format('DD/MM/YYYY')"></td>
                                <td data-bind="text:$data.IsOpen"></td>
                                <td><div class="hidden-phone text-align-center">
                                    <a class="btn btn-default btn-xs" title="Delete Call" data-bind="click:$root.DeleteCall"><i class="fa fa-times fa-delete-color"></i></a>
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
                            <div class="" data-bind="with:pager, visible: $data.CallListArray().length > 0">
                                <ul class="pagination pagination-sm no-margin pull-right" data-bind="if:allPages().length > 0">
                                    <li data-bind="css:{'disabled':currentPage()== 1} "><a title="Previous" data-bind="click: previousPage">&laquo;</a></li>
                                    <!-- ko foreach: $data.pagesToShow() -->
                                    <li class="active" data-bind="css: { active: $data.pageNumber == $parent.currentPage() }">
                                        <a data-bind="attr: {title:$data.pageNumber},text: $data.pageNumber, click: $parent.gotoPage,attr:{disabled:$data.pageNumber === $parent.currentPage()}"></a>
                                    </li>
                                    <!-- /ko -->
                                    <li data-bind="css:{'disabled':currentPage() == allPages().length}">
                                        <a title="Next" data-bind="click: nextPage">&raquo;</a></li>
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
        var AllRecords  = '<?php echo Constants::$AllRecords;?>';
        function selectScript(val) {
            $("#searchScript").val(val);
            $("#suggesstion-box").hide();
            DM.SearchModel().Script(val);
        }
    </script>
    <script src="<?php echo asset('/assets/js/pagejs/pager.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/admin/calllist.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/bootstrap-typeahead.js');?>"></script>
    <script type="text/javascript">
        $('#calllist').addClass('active');
        $(function() {
            $('#demo5').typeahead({
                ajax: {
                    url: baseUrl+'/scriptlisturl',
                    method: 'post',
                },
            });
        });
    </script>

@stop