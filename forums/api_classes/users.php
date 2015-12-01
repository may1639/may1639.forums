<?php
define("IN_MYBB", 1);
//require_once "./global.php";
//require_once "./users.php";
header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting

/** /
function return_error($id, $message, $name)
{
	$return_value = array('error_id' => $id, 'error_message' => $message, 'error_name' => $name);
	ob_start('ob_gzhandler');
	exit(json_encode($return_value));
}
/** /

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

/** /

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

/** /

function process_date($date)
{
	if((false === filter_input(INPUT_GET, $date, FILTER_VALIDATE_INT)) || $_GET[$date] < 0)
		return_error(400, $date, 'bad_parameter');
	return $_GET[$date];
}

/** /

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

/** /

function process_inname()
{
	if(!is_string($_GET["inname"]))
		return_error(400, 'inname', 'bad_parameter');
	return $_GET["inname"];
}

/** / 

function process_ids($ids)
{

	for($index=0; $index < count($ids); $index++)
	{
		if(!preg_match("/[0-9]+(;[0-9]+)* /", $ids[$index]))
		{
			return_error(404, 'no method found with this name', 'no_method');
		}
	}
}

/**/



/**
* The class for a user.  Hoping to migrate to separate file location when we know how.
* Data for this comes from: https://api.stackexchange.com/docs/types/user
*/
class User
{
	/** /
	var $about_me;
	var $accept_rate;
	/**/
	var $account_id;
	/** /
	var $age;
	var $answer_count;
	var $badge_counts;
	/**/
	var $creation_time;
	var $display_name;
	/** /
	var $down_vote_count;
	var $is_employee;
	/**/
	var $last_access_date;
	var $last_modified_date;
	/** /
	var $link;
	var $location;
	var $profile_image;
	var $question_count;
	/**/
	var $reputation;
	/** /
	var $reputation_change_day;
	var $reputation_change_month;
	var $reputation_change_quarter;
	var $reputation_change_week;
	var $reputation_change_year;
	var $timed_penalty_date;
	var $up_vote_count;
	/**/
	var $user_id;
	/** /
	var $user_type;
	var $view_count;
	/** /
	var $website_url;
	/**/

	function __construct($row)
	{
		$this->account_id = $row['uid'];
		$this->creation_time = $row['regdate'];
		$this->display_name = $row['username'];
		$this->last_access_date = $row['lastvisit'];
		$this->last_modified_date = $row['lastactive'];
		$this->reputation = $row['reputation'];
		$this->user_id = $row['uid'];
		$this->website_url = $row['website'];
	}

	static function get_query($ids, $id_type='user')
	{
		$type_to_col_mapping = array('user' => 'uid');
		$id_column = $type_to_col_mapping[$id_type];
		echo "!".process_order()."!";
		var_dump($_GET);
		var_dump($_SERVER);
	}

	/**/
	static function func($path)
	{

		echo "\n";
		var_dump($_SERVER);
		echo "\n";
		var_dump($_GET);
		echo "\n";
		var_dump($path);
		echo "\n";
	}
	/**/



/** /
	function __get($name)
	{
		switch ($name)
			{
				case "user_type" :
				echo $this->$user_type;
				break;
			}
	}

/**/

}
	

?>