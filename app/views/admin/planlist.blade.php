<?php
//use \Lang;
//use \Message;

?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Plans List'; ?>
@stop
@section('content')
    <main id="main" role="main" class="displayhide">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">Plans List</li>
                        </ul>
                    </header>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.PlanListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th  class="text-align-center width-5p">Sr.No</th>
                                <th  class="text-align-center">Plan Name</th>
                                <th  class="text-align-center">Amount</th>
                                <th  class="text-align-center">Discount</th>
                                <th  class="text-align-center width-10p">No Of Days</th>
                                <th  class="text-align-center width-10p">Trial</th>
                                <th  class="text-align-center width-10p">Enable</th>
                                <th style="width: 83px;"  class="text-align-center">Action</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.PlanListArray(),visible:$root.PlanListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.PlanName"></td>
                                <td class="text-align-right" data-bind="text:$data.Amount"></td>
                                <td class="text-align-center" data-bind="text:$data.Discount()+'%'"></td>
                                <td  class="text-align-center" data-bind="text:$data.NoOfDays"></td>
                                <td class="text-align-center" data-bind=""><input type="checkbox" data-bind="checked: $data.IsTrial,click: $root.UpdateTrail"></td>
                                <td class="text-align-center" data-bind=""><input type="checkbox" data-bind="checked: $data.IsEnable,click: $root.UpdatePlan"></td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs" title="Edit Plan" data-bind="attr:{'href':'addplan/'+$data.EncryptPlanID()}"><i class="fa fa-pencil fa-edit-color"></i></a>
                                        &nbsp;&nbsp;
                                        <a class="btn btn-default btn-xs" title="Delete Plan" data-bind="click:$root.DeletePlan"><i class="fa fa-times fa-delete-color"></i></a>
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
                            <div class="" data-bind="with:pager, visible: $data.PlanListArray().length > 0">
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/planlist.js');?>"></script>
    <script type="text/javascript">
        $('#planlist').addClass('active');
        $('#plan').addClass('active');
    </script>
@stop