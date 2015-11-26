<?php
define("IN_MYBB", 1);
require_once "./global.php";
require_once "./api_classes/users.php";
header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting
$valid_methods = array('users');
$path = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));

function return_error($id, $message, $name)
{
	$return_value = array('error_id' => $id, 'error_message' => $message, 'error_name' => $name);
	ob_start('ob_gzhandler');
	exit(json_encode($return_value));
}

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

function process_sort()
{
	if(isset($_GET["sort"]))
	{
		if(!in_array($_GET["sort"], array("reputation", "creation", "name", "modified")))
			return_error(400, 'sort', 'bad_parameter');
		return $_GET["sort"];
	}
	return "reputation";
}

function process_date($date)
{
	if((false === filter_input(INPUT_GET, $date, FILTER_VALIDATE_INT)) || $_GET[$date] < 0)
		return_error(400, $date, 'bad_parameter');
	return $_GET[$date];
}

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

function process_inname()
{
	if(!is_string($_GET["inname"]))
		return_error(400, 'inname', 'bad_parameter');
	return $_GET["inname"];
}

function users($database)//what should happen if the path starts with 'users'.
{
	$order = process_order();
	
	$sort = process_sort();
	
	if(isset($_GET["fromdate"]))
		$fromdate = process_date('fromdate');

	if(isset($_GET["todate"]))
		$todate = process_date('todate');

	if(isset($_GET["min"]))
		$min = process_min_max($sort, 'min');

	if(isset($_GET["max"]))
		$max = process_min_max($sort, 'max');

	if(isset($_GET["inname"]))
		$inname = process_inname();
	
	$var_to_col_mapping = array("reputation" => "reputation", "creation" => "regdate", "name" => "username", "modified" => "lastactive");
	
	$query = "SELECT * FROM `mybb_users`";
	
	if(isset($fromdate) || isset($todate) || isset($min) || isset($max) || isset($inname))
	{
		$use_and = false;
		$query .= " WHERE";
		
		if(isset($fromdate))
		{
			$query .= " ".$var_to_col_mapping["creation"].">".$fromdate;
			$use_and = true;
		}

		if(isset($todate))
		{
			if($use_and)
				$query .= " AND";
			$query .= " ".$var_to_col_mapping["creation"]."<".$todate;
			$use_and = true;
		}

		if(isset($min))
		{
			if($use_and)
				$query .= " AND";
			$query .= " ".$var_to_col_mapping[$sort].">";
			if($order == "name")
				$query .= "\'".$min."\'";
			else
				$query .= $min;
			$use_and = true;
		}

		if(isset($max))
		{
			if($use_and)
				$query .= " AND";
			$query .= " ".$var_to_col_mapping[$sort]."<";
			if($order == "name")
				$query .= "\'".$max."\'";
			else
				$query .= $max;
			$use_and = true;
		}

		if($in)
		{
			if($use_and)
				$query .= " AND";
			$query .= " ".$var_to_col_mapping["name"]." LIKE '%".$inname."%'";
		}
	}

	$query .= " ORDER BY ".$var_to_col_mapping[$sort];
	
	if($order == "desc")
		$query .= " DESC";

	$results = $database->query($query);

	$pagesize = 30;
	
	if(isset($_GET['pagesize']))
	{
		if(false === filter_input(INPUT_GET, 'pagesize', FILTER_VALIDATE_INT))
			return_error(400, 'pagesize', 'bad_parameter');
		
		$pagesize = max(0, min(100, $_GET["pagesize"]));
	}

	if(($pagesize < 1) || (mysql_num_rows($results) < 1))
		return array();

	$pagesize = min($pagesize, mysql_num_rows($results));
	
	$hard_limit = mysql_num_rows($results) / $pagesize;
	
	if(0 < mysql_num_rows($results) % $pagesize)
		$hard_limit++;
	
	$page = 1;
	
	if(isset($_GET["page"]))
	{
		if(false === filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT))
			return_error(400, 'page', 'bad_parameter');
		
		$page = max(1, min($hard_limit, $_GET["page"]));
	}
	
	$query .= " LIMIT ".$pagesize." OFFSET ".($page - 1) * $pagesize;

	$results = $database->query($query);
	
	$retval = array();
	
	while($row = mysql_fetch_array($results, MYSQL_ASSOC))
	{
		array_push($retval, new User($row));
		//var_dump($row);
	}

	return $retval;
}

switch($path[0])//selects proper function to call.
{
	case 'users':
		$return_value = array("items" => users($db));
		break;
	
	default:
		break;
}

if(!isset($return_value))
	return_error(400, 'method', 'method_not_recognized');

//var_dump(json_encode($return_value));

/**/
//This line of code gzips everything presented as output
ob_start('ob_gzhandler');

//return JSON array
exit(json_encode($return_value));
/**/
?>