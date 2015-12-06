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

	$query = User::get_query($ids);

	$results = $db->query($query);

	$query = paginate_query($query, mysql_num_rows($results));

	/** /
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

	/**/

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

echo "\nFIN";

/**/
//This line of code gzips everything presented as output
ob_start('ob_gzhandler');

//return JSON array
exit(json_encode($return_value));
/**/
?>