@extends('layouts.sitemaster')
@section('Title')<?php print 'User Devices List'; ?>
@stop
@section('content')
    <main id="main" role="main" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <header class="panel-heading">
                        <ul class="breadcrumb breadcrumb-subpages">
                            <li><a href="<?php echo URL::to('/dashboard'); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li class="active">User Devices</li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <form class="form-inline" role="form" data-bind="with:$root.SearchModel">
                            <div class="form-group col-md-3 col-sm-3 col-xs-12">
                                <input tabindex="1"  type="text" class="form-control" id="textKeyWord" data-bind="value:$data.textKeyWord" placeholder="User/Mobile/City/DeviceID/Email">
                            </div>
                            <button type="reset" tabindex="6"   value="Clear Filters" class="btn btn-warning btn-clear"  data-bind="click:$root.ClearSearch">Clear</button>
                            <button type="submit"  tabindex="5" class="btn btn-info btn-search" id="filterBtn" class="btn btn-default" data-bind="click:$root.ApplyFilter">Search</button>
                        </form>
                    </div>
                    <div class="panel-body table-responsive ">
                        <table class="table table-bordered table-responsive-margin table-hover">
                            <thead data-bind="visible:$root.UserDeviceListArray().length > 0">
                            <tr>
                                <th style="width: 52px" class="text-align-center">Sr.No</th>
                                <th  class="text-align-center">User Name</th>
                                <th  class="text-align-center">Email</th>
                                <th  class="text-align-center">Mobile</th>
                                <th  class="text-align-center">Device ID</th>
                                <th style="width: 60px" class="text-align-center">Delete</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach:$root.UserDeviceListArray(),visible:$root.UserDeviceListArray().length > 0">
                            <tr>
                                <td class="text-align-center" data-bind="text:$data.Index"></td>
                                <td data-bind="text:$data.DisplayName"></td>
                                <td data-bind="text:$data.Email"></td>
                                <td data-bind="text:$data.Mobile"></td>
                                <td data-bind="text:$data.DeviceID"></td>
                                <td><div class="hidden-phone text-align-center">
                                        <a class="btn btn-default btn-xs"  data-bind="click:$root.DeleteUserDevice"><i class="fa fa-times fa-delete-color"></i></a>
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

                    </div><!-- /.panel-body -->
                </div><!-- /.panel -->

            </div>
        </div>
    </main>
@stop
@section('script')
    <script src="<?php echo asset('/assets/js/pagejs/pager.js');?>"></script>
    <script src="<?php echo asset('/assets/js/pagejs/admin/userdevicelist.js');?>"></script>
    <script type="text/javascript">
        $('#userdevice').addClass('active');
    </script>
@stop