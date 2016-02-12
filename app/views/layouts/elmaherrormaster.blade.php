<!DOCTYPE html>
<html>
    <head>
        <title>MarketInsights - @yield('title')</title>
    </head>
	
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
	<link href="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
	<link href="<?php echo asset('/assets/css/bootstrap-datepicker.css');?>" rel="stylesheet">
	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js"></script>
	<script src="<?php echo asset('/assets/js/bootstrap-datepicker.js');?>"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#example').dataTable();
		});
		$.fn.dataTableExt.afnFiltering.push(
			function( oSettings, aData, iDataIndex ) {
				var dateRange = $('#date-range').attr("value");
				var iFini = document.getElementById('min').value;
				var iFfin = document.getElementById('max').value;
				var iStartDateCol = 2;
				var iEndDateCol = 2;
				debugger;
				iFini=iFini.substring(6,10) + iFini.substring(3,5)+ iFini.substring(0,2);
				iFfin=iFfin.substring(6,10) + iFfin.substring(3,5)+ iFfin.substring(0,2);

				var datofini=aData[iStartDateCol].substring(6,10) + aData[iStartDateCol].substring(3,5)+ aData[iStartDateCol].substring(0,2);
				var datoffin=aData[iEndDateCol].substring(6,10) + aData[iEndDateCol].substring(3,5)+ aData[iEndDateCol].substring(0,2);

				if ( iFini === "" && iFfin === "" )
				{
					return true;
				}
				else if ( iFini <= datofini && iFfin === "")
				{
					return true;
				}
				else if ( iFfin >= datoffin && iFini === "")
				{
					return true;
				}
				else if (iFini <= datofini && iFfin >= datoffin)
				{
					return true;
				}
				return false;
			}
		);
 
		$(document).ready(function() {
		    var table = $('#example').DataTable();
		     
		    // Event listener to the two range filtering inputs to redraw on input
			$('#min').change( function() { table.draw(); } );
	 		$('#max').change( function() { table.draw(); } );
		});
	</script>
	
	

	<style>
		body { font-size: 140%; }
	</style>
    <body>
        <div class="container" style="margin-top:50px;">
            @yield('content')
        </div>
    </body>
    @yield('script')
</html>