<?php

header("access-control-allow-origin: *");
header('Content-Type: application/json');//JSON-formatting

function hello_world(){
	
	$message = array("items" => array(array("message" => "This is a test of the API.  I hope it works..."), array("message" => "This is the second element.  Hopefully it works too.")));
	
	return $message;
}


function add_nums($a, $b){
	
	$res = $a + $b;
	$message = array("items" => array(array("message" => $res)));
	return $message;
}


function diagnostic(){
	$message = array("Request Method" => $_SERVER["REQUEST_METHOD"], "Path Info" => $_SERVER["PATH_INFO"], "Parameters" => $_SERVER["QUERY_STRING"]);
	
	return $message;
}

// Found from http://blog.ijasoneverett.com/2013/02/rest-api-a-simple-php-tutorial/
$possible_url = array("hello_world", "add_nums", "diagnostic");

$value = "An error has occurred";

if (isset($_GET["action"]) && in_array($_GET["action"], $possible_url))
{
  switch ($_GET["action"])
    {
      case "hello_world":
        $value = hello_world();
        break;
	  case "add_nums":
		  if (isset($_GET["a"]) && isset($_GET["b"]))
			$value = add_nums($_GET["a"], $_GET["b"]);
		  else
			$value = array("items" => array(array("message" => "Missing Arguments")));
          break;
      case "diagnostic":
      	  $value = diagnostic();
      	  break;
    }
}

//This line of code gzips everything presented as output
ob_start('ob_gzhandler');

//return JSON array
exit(json_encode($value));

?>

