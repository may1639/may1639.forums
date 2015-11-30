<?php
define("IN_MYBB", 1);
//require_once "./global.php";
//require_once "./users.php";
header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting
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
		/**/
		$this->account_id = $row['uid'];
		$this->creation_time = $row['regdate'];
		$this->display_name = $row['username'];
		$this->last_access_date = $row['lastvisit'];
		$this->last_modified_date = $row['lastactive'];
		$this->reputation = $row['reputation'];
		$this->user_id = $row['uid'];
		$this->website_url = $row['website'];
		
		/** /
		$this-> = $row[''];
		/**/
		
		//var_dump($row);
	}
}
/**/

?>