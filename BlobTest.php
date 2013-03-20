<?php
//	ini_set('display_errors',1);
//	ini_set('display_startup_errors',1);
//	error_reporting(-1);
	
	require_once 'vendor\autoload.php';

	use WindowsAzure\Common\ServicesBuilder;
	use WindowsAzure\Common\ServiceException;
	
	$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
	
	// Create blob REST proxy.
	$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
	
	try {
	    // List blobs.
	    $blob_list = $blobRestProxy->listBlobs("image");
	    $blobs = $blob_list->getBlobs();
	
	    foreach($blobs as $blob)
	    {
	        echo $blob->getName().": ".$blob->getUrl()."<br />";
	    }
	}
	catch(ServiceException $e){
	    // Handle exception based on error codes and messages.
	    // Error codes and messages are here: 
	    // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
	    $code = $e->getCode();
	    $error_message = $e->getMessage();
	    echo $code.": ".$error_message."<br />";
	}
/*
	$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
	
	// Create blob REST proxy.
	$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

	try {
	    // List blobs.
	    $blob_list = $blobRestProxy->listBlobs("mycontainer");
	    $blobs = $blob_list->getBlobs();
	
	    foreach($blobs as $blob)
	    {
	        echo $blob->getName().": ".$blob->getUrl()."<br />";
	    }
	}
	catch(ServiceException $e){
	    // Handle exception based on error codes and messages.
	    // Error codes and messages are here: 
	    // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
	    $code = $e->getCode();
	    $error_message = $e->getMessage();
	    echo $code.": ".$error_message."<br />";
	}
		
//	createContainer();
	uploadBlob();
//	downloadBlob();
//	listBlobs();

	function createContainer(){
		require_once ('vendor\autoload.php');
		
		use WindowsAzure\Common\ServicesBuilder;
		use WindowsAzure\Blob\Models\CreateContainerOptions;
		use WindowsAzure\Blob\Models\PublicAccessType;
		use WindowsAzure\Common\ServiceException;
		
		// Create blob REST proxy.
		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
	
	
		$createContainerOptions = new CreateContainerOptions(); 
		
		// Set public access policy. Possible values are 
		// PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
		// CONTAINER_AND_BLOBS:     
		// Specifies full public read access for container and blob data.
		// proxys can enumerate blobs within the container via anonymous 
		// request, but cannot enumerate containers within the storage account.
		//
		// BLOBS_ONLY:
		// Specifies public read access for blobs. Blob data within this 
		// container can be read via anonymous request, but container data is not 
		// available. proxys cannot enumerate blobs within the container via 
		// anonymous request.
		// If this value is not specified in the request, container data is 
		// private to the account owner.
		$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
		
		// Set container metadata
		$createContainerOptions->addMetaData("key1", "value1");
		$createContainerOptions->addMetaData("key2", "value2");
		
		try {
		    // Create container.
		    $blobRestProxy->createContainer("mycontainer", $createContainerOptions);
		}
		catch(ServiceException $e){
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here: 
		    // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}
	}
	
	function uploadBlob() {
		require_once 'vendor\autoload.php';
	
		use WindowsAzure\Common\ServicesBuilder;
		use WindowsAzure\Common\ServiceException;
		
		$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
		
		// Create blob REST proxy.
		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
	
		$content = fopen(".\Logo.png", "rb");
		$blob_name = "myblob";
		
		try {
		    //Upload blob
		    $blobRestProxy->createBlockBlob("mycontainer", $blob_name, $content);
		}
		catch(ServiceException $e){
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here: 
		    // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}		
	}

	function listBlobs() {
		$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
		
		// Create blob REST proxy.
		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

		try {
		    // List blobs.
		    $blob_list = $blobRestProxy->listBlobs("mycontainer");
		    $blobs = $blob_list->getBlobs();
		
		    foreach($blobs as $blob)
		    {
		        echo $blob->getName().": ".$blob->getUrl()."<br />";
		    }
		}
		catch(ServiceException $e){
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here: 
		    // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}
	}

	function downloadBlob() {
		require_once 'vendor\autoload.php';
		
		use WindowsAzure\Common\ServicesBuilder;
		use WindowsAzure\Common\ServiceException;
		
		$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
		
		// Create blob REST proxy.
		$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
		
		try {
		    // Get blob.
		    $blob = $blobRestProxy->getBlob("mycontainer", "myblob");
		    fpassthru($blob->getContentStream());
		}
		catch(ServiceException $e){
		    // Handle exception based on error codes and messages.
		    // Error codes and messages are here: 
		    // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
		    $code = $e->getCode();
		    $error_message = $e->getMessage();
		    echo $code.": ".$error_message."<br />";
		}
	}
*/
?>