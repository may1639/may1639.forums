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
	$query = paginate_query($query, mysql_num_rows($db->query($query)));

	$results = $db->query($query);
	
	$retval = array();

	while($row = mysql_fetch_array($results, MYSQL_ASSOC))
	{
		$temp = new User($row);		
		array_push($retval, new User($row));
	}

	return $retval;
}


function answers()
{
	global $path, $db;

	if (count($path) > 1)
	{
		switch($path[1])
		{
			// case 'moderators':
				
			// 	break;
			
			default:
				$ids = explode(";", $path[1]);
				process_ids($ids);
				break;
		}
	}

	$results = $db->query("SELECT p.* FROM mybb_posts p, mybb_users u WHERE u.username=\"adcoats\" && p.uid = u.uid");
	
	$retval = array();

	while($row = mysql_fetch_array($results, MYSQL_ASSOC))
	{		
		array_push($retval, $row);
	}

	return $retval;
}

switch($path[0])//selects proper function to call.
{
	case 'users':
		$return_value = array("items" => users());
		break;
	
	case 'answers':
		$return_value = array("items" => answers());
		break;
	
	default:
		break;
}

if(!isset($return_value))
	return_error(400, 'method', 'method_not_recognized');

/** /

User::func($path);

echo "\nFIN";

/**/
//This line of code gzips everything presented as output
ob_start('ob_gzhandler');

//return JSON array
exit(json_encode($return_value));
/**/
?>