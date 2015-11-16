<?php

header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting

$return_value = "An error has occurred";
$valid_methods = array('users');
$path = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));

/**
* The class for a user.  Hoping to migrate to separate file location when I know how.
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

	function __construct($id)
	{
		$this->user_id = $id;
	}
}

function users()//what should happen if the path starts with 'users'.
{
	return array('items' => array(new User(1234567), new User(7654321)));
}

switch($path[0])//selects proper function to call.
{
	case 'users':
		$return_value = users();
		break;
	
	default:
		break;
}
//var_dump($return_value);

//This line of code gzips everything presented as output
ob_start('ob_gzhandler');

//return JSON array
exit(json_encode($return_value));

?>

