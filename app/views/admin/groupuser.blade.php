<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
?>
@extends('layouts.sitemaster')
@section('Title')
    <?php isset($GroupModel->GroupID) ? print 'Edit Group' : print 'Add Group' ; ?>
@stop

@section('content')
    <main id="main" role="main" class="displayhide">
        <?php echo Form::hidden('GroupModel', json_encode($GroupModel),$attributes = array('id'=>'GroupModel'));?>
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <form class="form-horizontal" role="form" method="POST" data-bind="with:$root.GroupModel()">
                        <header class="panel-heading">
                            <ul class="breadcrumb breadcrumb-subpages">
                                <li><a href="<?php echo URL::to('/grouplist'); ?>"><i class="fa fa-users">&nbsp;</i>Group List</a></li>
                                <li class="active" data-bind="text:$data.GroupID()?'Edit User Group':'Add User Group'"></li>
                            </ul>
                        </header>
                        <div class="panel-body">
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Group Name</label>
                                <div class="col-lg-4">
                                    <select data-bind="options: $root.groupListArray, optionsValue: 'GroupID', optionsText: 'GroupName',value: GroupID , optionsCaption:'Select Group',decorateErrorElement: GroupID" name="GroupID" class="form-control"></select>
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">User</label>
                                <div class="col-lg-4">
                                    <select data-bind="options: $root.userListArray, optionsValue: 'UserID', optionsText: 'User',value: UserID , optionsCaption:'Select User',decorateErrorElement: UserID" name="UserID" class="form-control"></select>
                                   {{-- <input type="text" class="form-control" id="inputError" maxlength="50" name="GroupName" data-bind="value:$data.GroupName,decorateErrorElement:GroupName" placeholder="Group Name">--}}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button type="submit" class="btn btn-success" data-bind="click:$root.SaveGroup">Save</button>
                                    <button type="button" class="btn btn-default" data-bind="click:$root.cancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div><!--row1-->
    </main>
@stop
@section('script')
    <script src="<?php echo asset('/assets/js/pagejs/admin/addgroup.js');?>"></script>
    <script type="text/javascript">
        window.Required_GroupName ="{{ trans('messages.PropertyRequired',array('attribute'=>'Group Name'))}}";
    </script>
@stop
