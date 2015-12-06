<?php
define("IN_MYBB", 1);
require_once "./global.php";
require_once "./api_classes/util.php";
require_once "./api_classes/users.php";
header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting

$path = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));


function users()//what should happen if the path starts with 'users'.
{
	global $path, $db;

	if (count($path) > 1)
	{
		switch($path[1])
		{
			case 'moderators':
				
				break;
			
			default:
				$ids = explode(";", $path[1]);
				process_ids($ids);
				break;
		}
	}

	/** /

	$order = process_order();
	
	$sort = process_sort(array("reputation", "creation", "name", "modified"));
	
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

	$var_to_col_mapping = array("ids" => "uid", "reputation" => "reputation", "creation" => "regdate", "name" => "username", "modified" => "lastactive");
	
	$query = "SELECT * FROM `mybb_users`";
	
	if(isset($fromdate) || isset($todate) || isset($min) || isset($max) || isset($inname) || isset($ids))
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
			if($sort == "name")
				$query .= "'".$min."'";
			else
				$query .= $min;
			$use_and = true;
		}

		if(isset($max))
		{
			if($use_and)
				$query .= " AND";
			$query .= " ".$var_to_col_mapping[$sort]."<";
			if($sort == "name")
				$query .= "'".$max."'";
			else
				$query .= $max;
			$use_and = true;
		}

		if(isset($inname))
		{
			if($use_and)
				$query .= " AND";
			$query .= " ".$var_to_col_mapping["name"]." LIKE '%".$inname."%'";
			$use_and = true;
		}

		if(isset($ids) && (0 < count($ids)))
		{
			if($use_and)
				$query .= " AND";
			$query .= " ".$var_to_col_mapping["ids"]." IN (";
			for ($index=0; $index < count($ids); $index++)
			{
				if(0 < $index)
				{
					$query .= ", ";
				}
				$query .= "".$ids[$index];
			}
			$query .= ")";
		}
	}

	$query .= " ORDER BY ".$var_to_col_mapping[$sort];
	
	if($order == "desc")
		$query .= " DESC";

	echo "\n".$query."\n";
	/**/

	$query = User::get_query($ids);

	$results = $db->query($query);

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

	$results = $db->query($query);
	
	$retval = array();

	while($row = mysql_fetch_array($results, MYSQL_ASSOC))
	{
		$temp = new User($row);		
		array_push($retval, new User($row));
	}

	return $retval;
}



switch($path[0])//selects proper function to call.
{
	case 'users':
		$return_value = array("items" => users());
		break;
	
	default:
		break;
}

if(!isset($return_value))
	return_error(400, 'method', 'method_not_recognized');

/** /

echo "V";

/**/
//This line of code gzips everything presented as output
ob_start('ob_gzhandler');

//return JSON array
exit(json_encode($return_value));
/**/
?>