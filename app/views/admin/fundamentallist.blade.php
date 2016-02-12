<?php
//use \Lang;
//use \Message;
use Infrastructure\Constants;

?>
@extends('layouts.sitemaster')
@section('Title')<?php print 'Technical Reports'; ?>
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
                            <li class="active">Technical Reports</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                       <form class="form-inline" role="form" data-bind="with:$root.SearchModel()">
                       		
                       		<div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input type="text" tabindex="1" class="form-control" id="searchuser" data-bind="value:$data.textKeyWord" placeholder="Search Fundamental ">
                            </div>
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <select tabindex="2" data-bind="value:Status,options:$root.StatusListArrayForSearch(),optionsValue:'StatusID',optionsText:'StatusName' ,optionsCaption:'Select Status'" class="form-control" style="width:100%"></select>
                            </div>
                            <button type="submit" tabindex="3" class="btn btn-info" data-bind="click:$root.ApplyFilter">Search</button>
                            <button type="reset" tabindex="4"  value="Clear Filters" class="btn btn-warning"  data-bind="click:$root.ClearSearch">Clear</button>
                        </form>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.FundamentalListArray().length > 0">
                            <tr data-bind="with:pager">
                                <th  class="text-align-center width-5p">Sr.No</th>
                                <th  class="text-align-center width-30p">Title</th>
                                <th  class="text-align-center width-25p">PDF</th>
                                <th  class="text-align-center width-25p">Image</th>
                                <th  style="width: 83px" class="text-align-center">Published</th>
                                <th  style="width: 83px" class="text-align-center">Action</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.FundamentalListArray(),visible:$root.FundamentalListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.Title"></td>
                                <td data-bind="text:$data.PDF"></td>
                                <td data-bind="text:$data.Image"></td>
                                <td class="text-align-center" data-bind=""><input type="checkbox" data-bind="checked: $data.IsEnable,click: $root.EnableFundamental"></td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs" title="Edit Fundamental" data-bind="attr:{'href':'addfundamental/'+$data.EncryptFundamentalID()}"><i class="fa fa-pencil fa-edit-color"></i></a>&nbsp;&nbsp;
                                        <a class="btn btn-default btn-xs" title="Delete Fundamental" data-bind="click:$root.DeleteFundamental"><i class="fa fa-times fa-delete-color"></i></a>
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
                            <div class="" data-bind="with:pager, visible: $data.FundamentalListArray().length > 0">
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
	<script type="text/javascript">
        var AllRecords = '<?php echo Constants::$AllRecords;?>';
	</script>
    <script src="<?php echo asset('/assets/js/pagejs/pager.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/admin/fundamentallist.js');?>"></script>
    <script type="text/javascript">
        $('#fundamentallist').addClass('active');
        $('#fundamental').addClass('active');
    </script>
@stop