@extends('layouts.elmaherrormaster')
@section('title', 'Error Log')

@section('content')
    <?php 
		$data=$ProjectModel->Error;
	?>
	<table border="0" cellspacing="5" cellpadding="5">
        <tbody>
	        <tr>
	            <td>Minimum Date:</td>
	            <td><input type="text" id="min" name="min"></td>
	        </tr>
	        <tr>
	            <td>Maximum Date:</td>
	            <td><input type="text" id="max" name="max"></td>
	        </tr>
    	</tbody>
    </table>
	<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			
			<tr>
				<td>ID</td>
				<td>Error</td>
				<td>Date</td>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach($data as $value){
			?>
			<tr>
				<td><?php echo $value->ID; ?></td>
				<td><?php echo $value->Description; ?></td>
				<td><?php echo $value->CreatedDate; ?></td>
			</tr>
			<?php
				}
			?>
		</tbody>
	</table>
@endsection

@section('script')
<script type="text/javascript">
	$('#min').datepicker({
		autoclose:true,
		format:"yyyy-mm-dd",
	});
	$('#max').datepicker({
		autoclose:true,
		format:"yyyy-mm-dd",
	});
	
</script>
@endsection