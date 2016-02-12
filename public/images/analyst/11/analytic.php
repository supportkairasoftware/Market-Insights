<style>
    .form-control1 {
        background-color: #ffffff;
        background-image: none;
        border: 1px solid #cccccc;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
        color: #333333;
        display: inherit;
        font-size: 14px;
        height: 30px;
        line-height: 1.42857;
        padding: 5px 12px;
        transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
        width: 100%;
    }
    td, th {
        padding: 5px 0;
    }
</style>
<script type="text/javascript" src="https://www.google.com/jsapi"></script><link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
    $(function() {
        $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
    });
</script>

<script>
    $(function() {
        $( "#datepicker1" ).datepicker({ dateFormat: 'yy-mm-dd' }).val();
    });
</script>

<div class="container"><br><br>
    <div class="row">
<div class="col-md-6 col-xs-12">
        <form name="contactform" method="post" action="http://ems.kairasoftware.com/index.php/analytic">
            <table width="450px">
                <tr>
                    <td valign="top"><label for="Dimension">Enter Country Name:</label></td>
                    <td valign="top"><input type="text" name="countryname" class="form-control1"/></td>
                </tr>
                <tr><td><label>Select Start Date:</label></td><td><input type="text" name="startdate" id="datepicker" class="form-control1"></td></tr>
                <tr><td><label>Select End Date:</label></td><td><input type="text" name="enddate" id="datepicker1" class="form-control1"></td></tr>

                <tr>
                    <td colspan="2" style="text-align:right">
                        <input name="submit" type="submit" value="Search" class="btn btn-primary">
                    </td>
                </tr>

            </table>
        </form>


<?php
if(isset($_POST['submit'])) {
if(!empty($_POST['countryname'])) {

require 'gapi.class.php';

$metrics_value = 'pageviews';
$startdate_value = $_POST['startdate'];
$enddate_value = $_POST['enddate'];
$country_name = $_POST['countryname'];


$gaUsername = 'user.test191992@gmail.com';
$gaPassword = 'user@gmail';
$profileId = '105351422';
$dimensions = array('date');
$metrics = array($metrics_value);
//$sort = '-uniquePageviews';
$sort_metric = array('date');
$fromDate = date($startdate_value , strtotime('-2 days'));
$toDate = date($enddate_value);
$filter = 'ga:country =='.$country_name;

$ga = new gapi($gaUsername, $gaPassword);
$mostPopular = $ga->requestReportData($profileId,$dimensions, $metrics,$sort_metric, $filter, $fromDate, $toDate, 1,100);
$val='';
foreach($mostPopular as $mostPopularEntry) {
    $array = (array)$mostPopularEntry;
    $val.="['";
    $val.=date('j-n-y',strtotime($mostPopularEntry->dimensions['date']));
    $val.="',";
    $val.=$mostPopularEntry->metrics['pageviews'];
    $val.='],';

}
//echo $val;?>
    <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                    ['date', 'pageviews'],
                <?php  echo $val; ?>
            ]);
            var options = {
                title: '',
                hAxis: {showTextEvery: 1},
                pointSize: 5,
                vAxes: {0: {viewWindowMode:'explicit',

                    gridlines: {color: 'transparent'}
                },
                    1: {gridlines: {color: 'transparent'},
                        format:"#%"}
                },
                series: {0: {targetAxisIndex:0},
                    1:{targetAxisIndex:0},
                    2:{targetAxisIndex:1}
                },
                colors: ["orange"],
                chartArea:{left:100,top:100, width:500, height:150}
            };
            var chart = new google.visualization.LineChart(document.getElementById('chart_id'));
            chart.draw(data, options);
        }
    </script>

    <div id="chart_id" style="width: 800px; height: 300px;"></div>
<?php }
else {
require 'gapi.class.php';

$dimension_value = 'country';
$metrics_value = 'pageviews' ;
$startdate_value = $_POST['startdate'];
$enddate_value = $_POST['enddate'];

$gaUsername = 'user.test191992@gmail.com';
$gaPassword = 'user@gmail';
$profileId = '105351422';
$dimensions = array($dimension_value);
$metrics = array($metrics_value);
$fromDate = date($startdate_value , strtotime('-2 days'));
$toDate = date($enddate_value);

$ga = new gapi($gaUsername, $gaPassword);
$mostPopular = $ga->requestReportData($profileId, $dimensions, $metrics, null, null, $fromDate, $toDate, 1, 100);

$var =" <script type='text/javascript'>
        google.load('visualization', '1', {packages:['corechart']});

        google.setOnLoadCallback(drawChart);
        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Country', 'PageViews'],";
                foreach($mostPopular as $mostPopularEntry) {

                    $var .= "['".$mostPopularEntry->dimensions['country'] . "',";
                    $var .= $mostPopularEntry->metrics['pageviews'] . "],";
                }
$var .="]);

            var options = {
                title: 'Country Wise PageViews',
                'width':500,
                     'height':400

            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }

 </script>";
echo $var;?>
    <div id='piechart' style='width: 900px; height: 500px;'></div>
<?php
}
}

?>
</div>
</div>
</div>