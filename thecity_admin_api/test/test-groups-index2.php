<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Test Groups Index</title>
</head>

<body>
<?php
require_once('test-util.php'); 
require_once(dirname(__FILE__) . '/../lib/ca-main.php'); 

echo '<div class="apitest">';
echo '<h1>groups_index()</h1>';

$ca = new CityApi();
$ca->debug = true;
$ca->json = false;

$page = 1;

// args array can include
/*
page = [1,2,3,4,...]
search = ["downtown" | etc] <- optional group name search
under_group_id = [ 1234 | etc ] <- defaults to church group's ID
group_types = ["CG" | "Service" | "Campus" | etc] <- defaults to all group types
include_inactive = [ true | false ] <- defaults to false
include_addresses = [ true | false ]
include_composition = [ true | false ]
include_user_ids = [ true | false ]

*/
$results = $ca->groups_index(array('group_types' => 'Hope', 'include_addresses' => 'true')); 

//echo "<h2>results:</h2>$results";
//echo '<h2>var_dump results:</h2>';


echo '<h2>Formatted JSON results: </h2>';
echo '<pre>';
print_r($results);
//echo format_json($results);
echo '</pre>';

echo '<h1>groups_index("Test")</h1>';
$search = 'Test';
$results = $ca->groups_index(array('search' => $search)); 

//echo "<h2>results:</h2>$results";
//echo '<h2>var_dump results:</h2>';
//var_dump($results);

echo '<h2>Formatted JSON results: </h2>';
echo '<pre>';
echo format_json($results);
echo '</pre>';

echo '</div>';
?>

</body>
</html>
