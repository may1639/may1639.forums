<?php
define("IN_MYBB", 1);
header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting

function return_error($id, $message, $name)
{
	$return_value = array('error_id' => $id, 'error_message' => $message, 'error_name' => $name);
	ob_start('ob_gzhandler');
	exit(json_encode($return_value));
}

/**/

function process_order()
{
	if(isset($_GET["order"]))
	{
		if(!in_array($_GET["order"], array("asc", "desc")))
			return_error(400, 'order', 'bad_parameter');
		return $_GET["order"];
	}
	return "desc";
}

/**/

function process_sort($mapping)
{
	if(isset($_GET["sort"]))
	{
		if(!in_array($_GET["sort"], $mapping))
			return_error(400, 'sort', 'bad_parameter');
		return $_GET["sort"];
	}
	return "reputation";
}

/**/

function process_date($date)
{
	if((false === filter_input(INPUT_GET, $date, FILTER_VALIDATE_INT)) || $_GET[$date] < 0)
		return_error(400, $date, 'bad_parameter');
	return $_GET[$date];
}

/**/

function process_min_max($sort, $min_max)
{
	if($sort == "name")
	{
		if(!is_string($_GET[$min_max]))
			return_error(400, $min_max, 'bad_parameter');
	}
	else if(false === filter_input(INPUT_GET, $min_max, FILTER_VALIDATE_INT))
			return_error(400, $min_max, 'bad_parameter');
	return $_GET[$min_max];
}

/**/

function process_inname()
{
	if(!is_string($_GET["inname"]))
		return_error(400, 'inname', 'bad_parameter');
	return $_GET["inname"];
}

/**/

function process_ids($ids)
{

	for($index=0; $index < count($ids); $index++)
	{
		if(!preg_match("/[0-9]+(;[0-9]+)*/", $ids[$index]))
		{
			return_error(404, 'no method found with this name', 'no_method');
		}
	}
}

/**/




// function func($path)
// {
// 	echo "\n";
// 	var_dump($_SERVER);
// 	echo "\n";
// 	var_dump($_GET);
// 	echo "\n";
// 	var_dump($path);
// 	echo "\n";
// }

?>