<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
?>
@extends('layouts.sitemaster')
@section('Title')
    <?php isset($ScriptModel['ScriptDetails']->ScriptID) ? print 'Edit Script' : print 'Add Script' ; ?>
@stop

@section('content')
    <main id="main" role="main" style="display: none;">
        <?php echo Form::hidden('$ScriptModel', json_encode($ScriptModel),$attributes = array('id'=>'ScriptModel'));?>
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <form id="data" class="form-horizontal" role="form" method="POST" data-bind="with:$root.ScriptDetailsModel">
                        <header class="panel-heading">
                            <ul class="breadcrumb breadcrumb-subpages">
                                <li><a href="<?php echo URL::to('/scriptlist'); ?>"><i class="fa fa-users">&nbsp;</i>Script List</a></li>
                                <li class="active" data-bind="text:ScriptID()?'Edit Script':'Add Script'"></li>
                            </ul>
                        </header>
                        <input type="hidden" id="ScriptID" name="ScriptID" data-bind="value:$data.ScriptID">
                        <div class="panel-body">
                        	<div class="form-group input-col">
								<label for="sel1" class="col-sm-2 control-label col-lg-2">Select Segment</label>
								<div class="col-lg-4">
								<select data-bind="options: $root.SegmentList, optionsValue: 'SegmentID', optionsText: 'SegmentName',value: SegmentID, optionsCaption:'Select Segment',decorateErrorElement:SegmentID"  name="SegmentID" class="form-control signup-select"></select>
								</div>
						    </div>
						    
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="script">Script</label>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" id="Script" maxlength="50" name="Script" data-bind="value:Script,decorateErrorElement:Script" placeholder="Script">
                                </div>
                            </div>
                            
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Upload Image</label>
                                <div class="col-lg-6">
                                    <div class="input-group">
                                            <span class="input-group-btn">
                                                <span class="btn btn-primary btn-file">
                                                    Browse&hellip;<input type="file" name="Image" id="InputImage" accept="image/*" data-bind="event:{change:$root.setImage}"><br/><br/>
                                                </span>
                                            </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                    <span class = "validationMessage" data-bind="visible:$root.imageerror() != '',text:$root.imageerror()"></span>
                                </div>
                            </div>

                            <div class="form-group input-col" data-bind="visible:$data.ScriptID()&& $data.Image">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Uploaded Image</label>
                                <div class="col-lg-4">
                                    <img  height="150px" width="250px" data-bind="attr:{src:$data.Image}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button type="submit" class="btn btn-success" data-bind="click:$root.Save">Save</button>
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/addscript.js');?>"></script>
    <script type="text/javascript">
        window.Required_Script="{{ trans('messages.PropertyRequired',array('attribute'=>'Script'))}}";
        window.Required_Segment="{{ trans('messages.PropertyRequired',array('attribute'=>'Segment name'))}}";
        $('#addscript').addClass('active');
        $('#script').addClass('active');
        $(document).on('change', '.btn-file :file', function() {
            var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });

        $(document).ready( function() {
            $('.btn-file :file').on('fileselect', function(event, numFiles, label) {

                var input = $(this).parents('.input-group').find(':text'),
                        log = numFiles > 1 ? numFiles + ' files selected' : label;

                if( input.length ) {
                    input.val(log);
                } else {
                    if( log ) alert(log);
                }

            });
        });
    </script>
@stop
