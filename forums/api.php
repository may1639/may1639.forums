<?php
define("IN_MYBB", 1);
require_once "./global.php";
header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting

$return_value = "An error has occurred";
$valid_methods = array('users');
$path = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));



/**
* The class for a user.  Hoping to migrate to separate file location when we know how.
* Data for this comes from: https://api.stackexchange.com/docs/types/user
*/
class User
{
	//var $about_me;
	var $accept_rate;
	var $account_id;
	var $age;
	//var $answer_count;
	var $badge_counts;
	var $creation_time;
	var $display_name;
	//var $down_vote_count;
	var $is_employee;
	var $last_access_date;
	var $last_modified_date;
	var $link;
	var $location;
	var $profile_image;
	//var $question_count;
	var $reputation;
	var $reputation_change_day;
	var $reputation_change_month;
	var $reputation_change_quarter;
	var $reputation_change_week;
	var $reputation_change_year;
	var $timed_penalty_date;
	//var $up_vote_count;
	var $user_id;
	var $user_type;
	//var $view_count;
	var $website_url;

	function __construct($row)
	{
		$this->$account_id = $row['uid'];
		$this->$creation_time = $row['regdate'];
		$this->$display_name = $row['username'];
		/*$this->$ = $row[''];
		$this->$ = $row[''];
		$this->$ = $row[''];
		$this->$ = $row[''];
		$this->$ = $row[''];
		$this->$ = $row[''];
		$this->$ = $row[''];*/
		
		//var_dump($row);
	}
}

function users($database)//what should happen if the path starts with 'users'.
{
	$order = "desc";
	if(isset($_GET["order"]))
	{
		if(!in_array($_GET["order"], array("asc", "desc")))
			return array("Error Message" => "The 'order' parameter is invalid.");
		$order = $_GET["order"];
	}
	$sort = "reputation";
	if(isset($_GET["sort"]))
	{
		if(!in_array($_GET["sort"], array("reputation", "creation", "name", "modified")))
			return array("Error Message" => "The 'sort' parameter is invalid.");
		$sort = $_GET["sort"];
	}

	if(isset($_GET["fromdate"]))
	{
		if($_GET["fromdate"] < 0)
			return array("Error Message" => "The 'fromdate' parameter is invalid.");
		$fromdate = $_GET["fromdate"];
	}

	if(isset($_GET["todate"]))
	{
		if($_GET["todate"] < 0)
			return array("Error Message" => "The 'todate' parameter is invalid.");
		$todate = $_GET["todate"];
	}

	if(isset($_GET["min"]))
	{
		if($sort == "name")
		{
			if(!is_string($_GET["min"]))
				return array("Error Message" => "The 'min' value is invalid.");
		}
		else if(false === filter_input(INPUT_GET, 'min', FILTER_VALIDATE_INT))
				return array("Error Message" => "The 'min' parameter is invalid.");
		$min = $_GET["min"];
	}

	if(isset($_GET["max"]))
	{
		if($sort == "name")
		{
			if(!is_string($_GET["max"]))
				return array("Error Message" => "The 'max' value is invalid.");
		}
		else if(false === filter_input(INPUT_GET, 'max', FILTER_VALIDATE_INT))
				return array("Error Message" => "The 'max' parameter is invalid.");
		$max = $_GET["max"];
	}

	if(isset($_GET["inname"]))
	{
		if(!is_string($_GET["inname"]))
			return array("Error Message" => "The 'inname' parameter is invalid.");
		$inname = $_GET["inname"];
	}
	$var_to_col_mapping = array("reputation" => "reputation", "creation" => "regdate", "name" => "username", "modified" => "lastactive");
	$query = "SELECT * FROM `mybb_users`";
	$fr = isset($fromdate);
	$to = isset($todate);
	$mn = isset($min);
	$mx = isset($max);
	$in = isset($inname);
	if($fr || $to || $mn || $mx || $in)
	{
		$query .= " WHERE";
		
		if($fr)
		{
			$query .= " ".$var_to_col_mapping["creation"].">".$fromdate;
			if($to || $mn || $mx || $in)
				$query .= " AND";
		}

		if($to)
		{
			$query .= " ".$var_to_col_mapping["creation"]."<".$todate;
			if($mn || $mx || $in)
				$query .= " AND";
		}

		if($mn)
		{
			$query .= " ".$var_to_col_mapping[$sort].">";
			if($order == "name")
				$query .= "\'".$min."\'";
			else
				$query .= $min;
			if($mx || $in)
				$query .= " AND";
		}

		if($mx)
		{
			$query .= " ".$var_to_col_mapping[$sort]."<";
			if($order == "name")
				$query .= "\'".$max."\'";
			else
				$query .= $max;
			if($in)
				$query .= " AND";
		}

		if($in)
			$query .= " ".$var_to_col_mapping["name"]." LIKE '%".$inname."%'";
	}

	$query .= " ORDER BY ".$var_to_col_mapping[$sort];
	if($order == "desc")
	{
		$query .= " DESC";
	}
	//$query = "SELECT * FROM `mybb_users` LIMIT 0 , 30";
	echo $query;
	$results = $database->query($query);
	while($row = mysql_fetch_array($results, MYSQL_ASSOC))
		var_dump($row);

	/*Perform after filtering statements*
	$page = 1;
	if(isset($_GET['page']))
	{
		$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
		if(false === $page)
			$page = 1;
		$page = max(1, $page);
	}

	$pagesize = 30;
	if(isset($_GET['pagesize']))
	{
		$pagesize = filter_input(INPUT_GET, 'pagesize', FILTER_VALIDATE_INT);
		if(false === $pagesize)
			$pagesize = 30;
		$pagesize = max(0, min(100, $pagesize));
	}
	/**/
}

switch($path[0])//selects proper function to call.
{
	case 'users':
		users($db);
		break;
	
	default:
		break;
}
/*
$results = $db->query("SELECT * FROM `mybb_users` LIMIT 0 , 30");

$all_users = array();
while ($row = mysql_fetch_array($results, MYSQL_ASSOC)) {
	var_dump($row);
	//array_push($all_users, new User($row));
}

//var_dump($all_users);

/*
//This line of code gzips everything presented as output
ob_start('ob_gzhandler');

//return JSON array
exit(json_encode($return_value));

/**/
?>

