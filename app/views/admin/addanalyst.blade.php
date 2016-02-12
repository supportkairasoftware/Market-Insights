<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
?>
@extends('layouts.sitemaster')
@section('Title')
    <?php isset($AnalystModel->AnalystID) ? print 'Edit Analyst View' : print 'Add Analyst View' ; ?>
@stop

@section('content')
    <main id="main" role="main" style="display: none;">
        <?php echo Form::hidden('$AnalystModel', json_encode($AnalystModel),$attributes = array('id'=>'AnalystModel'));?>
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <form id="data" class="form-horizontal" role="form" method="POST" data-bind="with:$root.AnalystModel()">
                        <header class="panel-heading">
                            <ul class="breadcrumb breadcrumb-subpages">
                                <li><a href="<?php echo URL::to('/analystlist'); ?>"><i class="fa fa-users">&nbsp;</i>Analyst View List</a></li>
                                <li class="active" data-bind="text:$data.AnalystID()?'Edit Analyst View':'Add Analyst View'"></li>
                            </ul>
                        </header>
                        <input type="hidden" id="AnalystID" name="AnalystID" data-bind="value:$data.AnalystID">
                        <div class="panel-body">
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Title</label>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" id="inputError" maxlength="100" name="Title" data-bind="value:$data.Title,decorateErrorElement:Title" placeholder="Title">
                                </div>
                            </div>
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Description</label>
                                <div class="col-lg-8">
                                    <input type="text" data-bind="CkEditor:Description" class="form-control span10" id="Description" name="Description2" />
                                    <input type="text" style="display: none;" data-bind="value:Description" class="form-control span10" id="finalDescription" name="Description" />
                                </div>
                            </div>
                          <!--  <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Upload Image</label>
                                <div class="col-lg-4">
                                    <input type="file" name="Image" id="InputImage" accept="image/*" data-bind="event:{change:$root.setImage}">
                                    <p class="help-block">Accept Only Image.</p>
                                    <span class = "validationMessage" data-bind="visible:$root.imageerror() != '',text:$root.imageerror()"></span>
                                </div>
                            </div>-->

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

                            <div class="form-group input-col" data-bind="visible:$data.AnalystID() && $data.Image">
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/addanalyst.js');?>"></script>
    <script src="<?php echo asset('/assets/js/ckeditor/ckeditor.js');?>"></script>
    <script type="text/javascript">
        window.Required_Title="{{ trans('messages.PropertyRequired',array('attribute'=>'Fundamental Title'))}}";
        $('#addanalyst').addClass('active');
        $('#analyst').addClass('active');

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
