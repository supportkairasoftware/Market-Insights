@extends('layouts.sitemaster')
@section('Title')<?php print 'SMS List'; ?>
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
                            <li class="active">SMS List</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" role="form" data-bind="with:$root.SearchModel">
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="1"  type="text" class="form-control" id="textKeyWord" data-bind="value:$data.textKeyWord" placeholder="Name / Mobile / City / State /Email">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <select tabindex="2" class="form-control" style="width:100%" data-bind="options: $root.ActionListArray, optionsValue: 'value', optionsText: 'Action',value: Action , optionsCaption:'Select SMS Status',decorateErrorElement: Action" name="Action"></select>
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="3" id="date" name="FromDate"  type="text" class="form-control" placeholder="From Date" data-bind="datepicker:startDate">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="4" id="date" name="FromDate"  type="text" class="form-control" placeholder="To Date" data-bind="datepicker:endDate">
                            </div>
                            <button type="reset" tabindex="6"   value="Clear Filters" class="btn btn-warning btn-clear"  data-bind="click:$root.ClearSearch">Clear</button>
                            <button type="submit"  tabindex="5" class="btn btn-info btn-search" id="filterBtn" class="btn btn-default" data-bind="click:$root.ApplyFilter">Search</button>
                        </form>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.SMSListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th  style="width: 52px" class="text-align-center">Sr.No</th>
                                <th  class="text-align-center">Message</th>
                                <th  class="text-align-center">Name</th>
                                <th  style="width: 100px" class="text-align-center">Mobile</th>
                                <th  class="text-align-center">Email</th>
                                <th  class="text-align-center">City</th>
                                <th  class="text-align-center">State</th>
                                <th  style="width: 52px" class="text-align-center width-5p">Sent</th>
                                <th  style="width: 95px" class="text-align-center">Sent Date</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.SMSListArray(),visible:$root.SMSListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.Message"></td>
                                <td data-bind="text:$data.Name"></td>
                                <td data-bind="text:$data.Mobile"></td>
                                <td data-bind="text:$data.Email"></td>
                                <td data-bind="text:$data.City"></td>
                                <td data-bind="text:$data.State"></td>
                                <td data-bind="text:$data.IsSent() == 1? 'Yes' : 'No'"></td>
                                <td data-bind="text:moment($data.SentDate()).format('DD/MM/YYYY')"></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="displayhide" id="nodata">
                            <div class="col-md-12 alert alert-warning ">
                                <i class="icon-large icon-warning-sign"></i>&nbsp;&nbsp;&nbsp;{{ trans('messages.NoRecord')}}
                            </div>
                        </div>
                        <div class="table-foot">
                            <div class="" data-bind="with:pager, visible: $data.SMSListArray().length > 0">
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/smslist.js');?>"></script>
    <script type="text/javascript">
        $('#sms').addClass('active');
    </script>
@stop