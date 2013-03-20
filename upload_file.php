<?php

//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

require_once 'vendor\autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;

$allowedExts = array("jpg", "jpeg", "gif", "png");
$bits = explode(".", $_FILES["file"]["name"]);
$extension = end($bits);

$content = $_POST['content'];

if ((($_FILES["file"]["type"] == "image/gif")   || ($_FILES["file"]["type"] == "image/jpeg")
												|| ($_FILES["file"]["type"] == "image/png")
												|| ($_FILES["file"]["type"] == "image/pjpeg"))
												&& in_array($extension, $allowedExts)) {
	if ($_FILES["file"]["error"] > 0) {
	    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	} else {
		$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
		
		// Create blob REST proxy.
		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

	
//		$content = fopen($_FILES["file"]["tmp_name"], "rb");		
//		$content = fopen(".\Logo.png", "rb");
		$content = file_get_contents($_FILES["file"]["tmp_name"]);

		$blob_name = $_FILES["file"]["name"];
		
		try {
		    //Upload blob
		    $blobRestProxy->createBlockBlob("mycontainer", $blob_name, $content);
		    
		}
		catch(ServiceException $e){
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}
	}
} else {
	echo "Invalid file" . $_FILES["file"]["tmp_name"] . "<br>" . $_FILES["file"]["name"] . "<br>" . $content;
}

?>