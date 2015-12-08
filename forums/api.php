<?php
define("IN_MYBB", 1);

require_once "./global.php";
require_once "./api_classes/util.php";
require_once "./api_classes/users.php";
require_once "./api_classes/questions.php";
require_once "./api_classes/answers.php";

header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting

$path = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));

	// $query = "SELECT * FROM package LIMIT 0, 100";

	// $results = $db->query($query);
	
	// $return_value = array();

	// while($row = mysql_fetch_array($results, MYSQL_ASSOC))
	// {		
	// 	array_push($return_value, $row);
	// }

// if(preg_match("/^questions\/[0-9]+(;[0-9]+)*\/answers$/", trim($_SERVER['PATH_INFO'], "/")))
// {
// 	$return_value = array('Items' =>  answers("question"));	
// }

// if(preg_match("/^answers\/[0-9]+(;[0-9]+)*\/questions$/", trim($_SERVER['PATH_INFO'], "/")))
// {
// 	$return_value = array('Items' =>  questions("answer"));	
// }


function users($id_type='user')//what should happen if the path starts with 'users'.
{
	global $path, $db;

	if (count($path) > 1)
	{
		switch($path[1])
		{
			case 'moderators':
				
				break;
			
			default:
				$ids = $path[1];
				process_ids($ids);
				break;
		}
	}

	if(isset($ids))
	{
		$ids = explode(";", $ids);
	}

	$query = User::get_query($ids, $id_type);
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

function answers($id_type='answer')
{
	global $path, $db;

	if (count($path) > 1)
	{
		switch($path[1])
		{
			// case 'moderators':
				
			// 	break;
			
			default:
				$ids = $path[1];
				process_ids($ids);
				break;
		}
	}

	// $results = $db->query("SELECT p.* FROM mybb_posts p, mybb_users u WHERE u.username=\"adcoats\" && p.uid = u.uid");
	
	if(isset($ids))
	{
		$ids = explode(";", $ids);
	}

	$query = Answer::get_query($ids, $id_type);
	// echo "string";
	// return $query;

	$query = paginate_query($query, mysql_num_rows($db->query($query)));

	$results = $db->query($query);
	
	$retval = array();

	while($row = mysql_fetch_array($results, MYSQL_ASSOC))
	{		
		array_push($retval, new Answer($row));
	}

	return $retval;
}

function questions($id_type='question')
{
	global $path, $db;

	if (count($path) > 1)
	{
		switch($path[1])
		{
			// case 'moderators':
				
			// 	break;
			
			default:
				$ids = $path[1];
				process_ids($ids);
				break;
		}
	}

	// $results = $db->query("SELECT p.* FROM mybb_posts p, mybb_users u WHERE u.username=\"adcoats\" && p.uid = u.uid");
	
	if(isset($ids))
	{
		$ids = explode(";", $ids);
	}
	
	$query = Question::get_query($ids, $id_type);

	$query = paginate_query($query, mysql_num_rows($db->query($query)));

	$results = $db->query($query);
	
	$retval = array();

	while($row = mysql_fetch_array($results, MYSQL_ASSOC))
	{		
		array_push($retval, new Question($row));
	}

	return $retval;
}

/**/

/** /
if(preg_match("/^answers\/[0-9]+(;[0-9]+)*\/questions$/", trim($_SERVER['PATH_INFO'], "/")))
{
	$return_value = array('Items' =>  questions("answer"));	
}
/**/

if(preg_match("/^questions\/[0-9]+(;[0-9]+)*\/answers$/", trim($_SERVER['PATH_INFO'], "/")))
{
	$return_value = array('Items' =>  answers("question"));	
}
else switch($path[0])//selects proper function to call.
{
	case 'users':
		$return_value = array("items" => users("user"));
		break;
	
	case 'answers':
		$return_value = array("items" => answers("answer"));
		break;

	case 'questions':
		$return_value = array("items" => questions("questions"));
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