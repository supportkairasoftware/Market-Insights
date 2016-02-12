<?php
use Infrastructure\Common;
use Infrastructure\Constants;
//use \Lang;
//use \Message;
?>
@extends('layouts.sitemaster')
@section('Title')

@stop

@section('content')
    <main id="main" role="main" style="display: none;">
        <?php echo Form::hidden('$ScriptList', json_encode($ScriptList),$attributes = array('id'=>'ScriptList'));?>
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <form id="data" class="form-horizontal" role="form" method="POST" data-bind="with:$root.CallModel()">
                        <div class="panel-body">
                        
                        	<div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Created Date</label>
                                <div class="col-lg-4">
                                    <input class="form-control" tabindex="1" data-bind="newDateTimepicker:'',value:$data.CreatedDate" type="text" placeholder="Select Date" id="CreatedDate"/>
                                </div>
                            </div>
                        
                        	<div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Script</label>
                                <div class="col-lg-4">
                                    <select class="form-control" tabindex="2" style="width:100%" data-bind="options: $root.ScriptArray, optionsValue: 'ScriptID', optionsText: 'ScriptFull',value: ScriptID,optionsCaption:'Select Script'"></select>
                                </div>
                            </div>
                            
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Action</label>
                                <div class="col-lg-4">
                                    <select class="form-control" tabindex="3" style="width:100%" data-bind="options: $root.ActionArray, optionsValue: 'value', optionsText: 'Action',value: Action,optionsCaption:'Select Action'"></select>
                                </div>
                            </div>
                            
                        
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">InitiatingPrice</label>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" tabindex="4" id="inputError" maxlength="100" name="Title" data-bind="value:$data.InitiatingPrice" placeholder="InitiatingPrice">
                                </div>
                                
                            </div>
                            
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">T1</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="5" class="form-control" id="inputError" maxlength="100" name="Title" data-bind="value:$data.T1" placeholder="T1">
                                </div>
                            </div>
                            
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">T2</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="6" class="form-control" id="inputError" maxlength="100" name="Title" data-bind="value:$data.T2" placeholder="T2">
                                </div>
                            </div>
                            
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">SL</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="7" class="form-control" id="inputError" maxlength="100" name="Title" data-bind="value:$data.SL" placeholder="SL">
                                </div>
                            </div>
                            
                            <div class="form-group input-col">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Result</label>
                                <div class="col-lg-4">
                                   	<label><input type="checkbox" tabindex="8" value="1" data-bind="checked: ResultDescription,click:$root.ScriptValue" />T1</label>
									<label><input type="checkbox" tabindex="9" value="2" data-bind="checked: ResultDescription,click:$root.ScriptValue"" />T2</label>
									<label><input type="checkbox" tabindex="10" value="3" data-bind="checked: ResultDescription,click:$root.ScriptValue"" />SL</label>
									<label><input type="checkbox" tabindex="11" value="4" data-bind="checked: ResultDescription,click:$root.ScriptValue"" />partial</label>
									<label><input type="checkbox" tabindex="11" value="6" data-bind="checked: ResultDescription,click:$root.ScriptValue"" />close</label>
                                </div>
                            </div>
                            
                            <div class="form-group input-col" id="t1box" style="display:none">
                            	<label class="col-sm-2 control-label col-lg-2" for="inputError">T1 Time</label>
                            	<div class="col-lg-4">
                                    <input class="form-control" tabindex="12" data-bind="newDateTimepicker:'a',value:$data.t1date" type="text" placeholder="Select Date" id="t1datetimepicker"/>
                                </div>
                            </div>
                            
                            <div class="form-group input-col" id="t2box" style="display:none">
                            	<label class="col-sm-2 control-label col-lg-2" for="inputError">T2 Time</label>
                            	<div class="col-lg-4">
                                    <input class="form-control" tabindex="13" data-bind="newDateTimepicker:'b',value:$data.t2date" type="text" placeholder="Select Date" id="t2datetimepicker"/>
                                </div>
                            </div>
                            
                            <div class="form-group input-col" id="slbox" style="display:none">
                            	<label class="col-sm-2 control-label col-lg-2" for="inputError">SL Time</label>
                            	<div class="col-lg-4">
                                    <input class="form-control" tabindex="14" data-bind="newDateTimepicker:'c',value:$data.sldate" type="text" placeholder="Select Date" id="sldatetimepicker"/>
                                </div>
                            </div>
                            
                            <div class="form-group input-col" id="partialbox" style="display:none">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Partial</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="15" class="form-control" id="inputError" maxlength="100" name="Title" data-bind="value:$data.PartialValue" placeholder="Partial Value">
                                </div>
                                <div class="col-lg-4">
                                    <input class="form-control" tabindex="16" type="text" data-bind="newDateTimepicker:'d',value:$data.partialdate" placeholder="Select Date" id="partialdatetimepicker" placeholder="Partial Date"/>
                                </div>
                            </div>
                            <div class="form-group input-col" id="closebox" style="display:none">
                                <label class="col-sm-2 control-label col-lg-2" for="inputError">Close</label>
                                <div class="col-lg-4">
                                    <input type="text" tabindex="15" class="form-control" id="inputError" maxlength="100" name="Title" data-bind="value:$data.CloseValue" placeholder="close Value">
                                </div>
                                <div class="col-lg-4">
                                    <input class="form-control" tabindex="16" type="text" data-bind="newDateTimepicker:'d',value:$data.closedate" placeholder="Select Date" id="partialdatetimepicker" placeholder="close Date"/>
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
    <script src="<?php echo asset('/assets/js/pagejs/admin/addcall.js');?>"></script>
    <script src="<?php echo asset('/assets/js/jquery.datetimepicker.full.min.js');?>"></script>
    <script type="text/javascript">
    	
    	/*$(function(){
    		$('#t1datetimepicker').datetimepicker();
    		$('#t2datetimepicker').datetimepicker();
    		$('#sldatetimepicker').datetimepicker();
    		$('#partialdatetimepicker').datetimepicker();
    		$('#CreatedDate').datetimepicker();
    		
    	});*/
    </script>
@stop

