<?php
header("Content-Type: text/html; charset=UTF-8");

// Git Test
//	ini_set('display_errors',1);
//	ini_set('display_startup_errors',1);
//	error_reporting(-1);
require_once 'vendor\autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
			
define(SIGNIN, 0);
define(CATEGORY, 1);
define(COMMUNITY, 2);
define(POST, 3);	
define(COMMENT, 4);

define(SUBSCRIPTION, 5);
define(CREATE_COMMUNITY, 6);
define(SUBMIT_POST, 7);
define(SUBMIT_COMMENT, 8);

define(HOT_TOPIC, 9);
define(WEEKLY_TOPIC, 10);

define(LIKE_POST, 11);
define(SUBSCRIBE, 12);

define(TEST, 99);

$output = array();

//	$select = $_GET['select'];
$select = $_POST['select'];

switch($select) {
		case SIGNIN:			// Function Code : 0
			SignIn();
			break;
			
		case CATEGORY:			// Function Code : 1
			Category();
			break;
			
		case COMMUNITY:			// Function Code : 2
			Community();
			break;
			
		case POST:				// Function Code : 3
			Post();
			break;
			
		case COMMENT:			// Function Code : 4
			Comment();
			break;
			
		case SUBSCRIPTION:		// Function code : 5
			Subscription();
			break;
			
		case CREATE_COMMUNITY:	// Function code : 6
			Create_Community();
			break;	
			
		case SUBMIT_POST:		// Function Code : 7		
			Submit_Post();
			break;
			
		case SUBMIT_COMMENT:	// Function Code : 8
			Submit_Comment();
			break;
			
		case HOT_TOPIC:			// Function Code : 9
			Hot_Topic();
			break;
			
		case WEEKLY_TOPIC:		// Function Code : 10
			Weekly_Topic();
			break;
			
		case LIKE_POST:			// Function Code : 11
			Like_Post();
			break;
			
		case SUBSCRIBE:			// Function Code : 12
			Subscribe();
			break;	
			
		case TEST:				// Function Code : 99
			Test();
			break;
	}

// Sign In Fuction. Fuction Code : 0		
function SignIn() {
		require_once ('../topicsdb_connect.php'); // Connect to the db.

		$user_email = $_POST['user_email'];
		$user_name = $_POST['user_name'];

		$sql = "SELECT COUNT(*) AS count
				FROM [user]
				WHERE user_email = '$user_email' 
					AND user_name = '$user_name'";
		
		$stmt = sqlsrv_query($conn, $sql);

		if($stmt === false) {
			$output['status'] = false;	
	    } else {
	    	$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH);
		    
		    if($row['count'] == 0) {
    			$sql = "INSERT INTO [user] (user_email, user_name)
						VALUES ('$user_email', '$user_name')";
							
				$stmt = sqlsrv_query($conn, $sql);
					    	
		    	if($stmt === false) {
			    	$output['status'] = false;
		    	} else {
			    	$output['status'] = true;
		    	}
	    	} else {
		    	$output['status'] = true;
	    	}
	    	
	    	sqlsrv_free_stmt($stmt);
	    }
	    
		print(json_encode($output));
	    
		sqlsrv_free_stmt($stmt);	
		sqlsrv_close($conn);
	}

// Get Category Function. Function Code : 1
function Category() {
		require_once ('../topicsdb_connect.php'); // Connect to the db.
		
		$parent_id = $_POST['parent_id'];
		
		$sql = "SELECT *
				FROM [category]
				WHERE parent_id = $parent_id";
		
		$stmt = sqlsrv_query($conn, $sql);
		
		if($stmt === false) {
			$output['status'] = false;
		} else {
			$output['status'] = true;
			
			while ( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
				$output['result'][] = array(
					'categoryId' => $row['category_id'],
					'categoryName' => $row['category_name'],
					'parentId' => $row['parent_id'],
					'imageUri' => $row['image']
				);
			}
		}
		
		print(json_encode($output));
		
		sqlsrv_free_stmt($stmt);	
		sqlsrv_close($conn);
	}

// Get Community Function. Function Code : 2
function Community() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
	
	$category_id = $_POST['category_id'];
	
	$sql = "SELECT community.community_id, community.community_name, community.category_id, community.description, community.image, community.community_datetime, community.era, community.era_datetime, community.master, community.master_datetime, community.user_count, community.post_count
				FROM (SELECT community.community_id, community.community_name, community.category_id, community.description, community.image, community.community_datetime, community.era, community.era_datetime, community.master, community.master_datetime, community.user_count, post_count.post_count
						FROM (SELECT [community].community_id, [community].community_name, [community].category_id, [community].description, [community].image, [community].datetime AS community_datetime, [era].era, [era].datetime AS era_datetime, [master].user_email AS master, [master].datetime AS master_datetime, user_count.user_count
								FROM [community], [era], [master],
									(SELECT community_id, count(user_email) AS user_count
										FROM [subscription]
										GROUP BY community_id) AS user_count
										WHERE [community].community_id = user_count.community_id 
											AND [community].community_id = [era].community_id 
											AND [community].community_id = [master].community_id) AS community
										LEFT OUTER JOIN (SELECT community_id, count(post_id) AS post_count
															FROM [post]
															GROUP BY community_id) AS post_count on post_count.community_id = community.community_id ) AS community
				WHERE community.category_id = '$category_id'";
	
	$stmt = sqlsrv_query($conn, $sql);
	
	if($stmt === false) {
		$output['status'] = false;
	} else {
		$output['status'] = true;
		
		while ( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
			if($row['post_count'] == null) {
				$row['post_count'] = 0;
			}
			
			if($row['era'] == 0) {
				$row['era'] = 'Ancient';
			}
		
			$output['result'][] = array(
					'communityId' => $row['community_id'],
					'categoryId' => $row['category_id'],
					'communityName' => $row['community_name'],
					'description' => $row['description'],
					'image' => $row['image'],
					'communityDateTime' => $row['community_datetime'],
					'era' => $row['era'],
					'eraDateTime' => $row['era_datetime'],
					'communityMaster' => $row['master'],
					'masterDateTime' => $row['master_datetime'],
					'userCount' => $row['user_count'],
					'postCount' => $row['post_count']
			);
		}
	}
	
	print(json_encode($output));
	
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// Get Post Function, Function Code : 3
function Post() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
	
	$community_id = $_POST['community_id'];
	$user_email = $_POST['user_email'];
	
	$sql = "SELECT tmp.datetime, tmp.post_id, tmp.community_id, tmp.user_email, tmp.content, tmp.image, tmp.like_count, tmp.value
				FROM (SELECT [post].datetime, [post].post_id, [post].community_id, [post].user_email, [post].content, [post].image, like_table.like_count, like_table.value
						FROM (SELECT value_table.post_id, value_table.value, count_table.like_count
								FROM (SELECT post_id, SUM(CASE WHEN user_email = '$user_email' THEN 1 ELSE 0 END) AS value
										FROM [like]
										GROUP BY post_id) AS value_table, 
											(SELECT post_id, COUNT(value) AS like_count
												FROM [like]
												GROUP BY post_id) AS count_table
												WHERE value_table.post_id = count_table.post_id) AS like_table
				RIGHT OUTER JOIN [post] ON [post].post_id = like_table.post_id) AS tmp
				WHERE tmp.community_id = '$community_id'";	
	
	$stmt = sqlsrv_query($conn, $sql);
	
	if($stmt === false) {
		$output['status'] = false;
	} else {
		$output['status'] = true;
		
		while ( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
		
			if($row['like_count'] == null) {
				$row['like_count'] = 0;
			}
			
			if($row['value'] == null || $row['value'] == 0) {
				$row['value'] = false;
			} else {
				$row['value'] = true;
			}
			
			$output['result'][] = array(
				'postId' => $row['post_id'],
				'communityId' => $row['community_id'],
				'content' => $row['content'],
				'userEmail' => $row['user_email'],
				'imageUri' => $row['image'],
				'likeCount' => $row['like_count'],
				'value' => $row['value'],
				'dateTime' => $row['datetime']
			);
		}
	}
	
	print(json_encode($output));
	
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// Get Comment Function, Function Code : 4
function Comment() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
	
	$post_id = $_POST['post_id'];
	
	$sql = "SELECT *
			FROM [comment]
			WHERE post_id = $post_id
			ORDER BY datetime
			ASC";		
	
	$stmt = sqlsrv_query($conn, $sql);
	
	if($stmt === false) {
		$output['status'] = false;
	} else {
		$output['status'] = true;
		
		while ( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
			$output['result'][] = array(
				'commentId' => $row['comment_id'],
				'postId' => $row['post_id'],
				'userEmail' => $row['user_email'],
				'comment' => $row['comment'],
				'dateTime' => $row['datetime']
			);
		}
	}
	
	print(json_encode($output));
	
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// Get Subscription Function. Function Code : 5
function Subscription() {
		require_once ('../topicsdb_connect.php'); // Connect to the db.
		
		$user_email = $_POST['user_email'];

		$sql = "SELECT community.community_id, community.community_name, community.category_id, community.description, community.image, community.community_datetime, community.era, community.era_datetime, community.master, community.master_datetime, community.user_count, community.post_count
				FROM (SELECT community.community_id, community.community_name, community.category_id, community.description, community.image, community.community_datetime, community.era, community.era_datetime, community.master, community.master_datetime, community.user_count, post_count.post_count
						FROM (SELECT [community].community_id, [community].community_name, [community].category_id, [community].description, [community].image, [community].datetime AS community_datetime, [era].era, [era].datetime AS era_datetime, [master].user_email AS master, [master].datetime AS master_datetime, user_count.user_count
								FROM [community], [era], [master],
									(SELECT community_id, count(user_email) AS user_count
										FROM [subscription]
										GROUP BY community_id) AS user_count
										WHERE [community].community_id = user_count.community_id 
											AND [community].community_id = [era].community_id 
											AND [community].community_id = [master].community_id) AS community
										LEFT OUTER JOIN (SELECT community_id, count(post_id) AS post_count
															FROM [post]
															GROUP BY community_id) AS post_count on post_count.community_id = community.community_id ) AS community, [subscription]
				WHERE [subscription].community_id = community.community_id
					AND [subscription].user_email = '$user_email'";
		
		$stmt = sqlsrv_query($conn, $sql);
		
		if($stmt === false) {
			$output['status'] = false;
		} else {
			$output['status'] = true;
			
			while ( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
				if($row['post_count'] == null) {
					$row['post_count'] = 0;
				}
				
				if($row['era'] == 0) {
					$row['era'] = 'Ancient';
				}
				
				$output['result'][] = array(
					'communityId' => $row['community_id'],
					'categoryId' => $row['category_id'],
					'communityName' => $row['community_name'],
					'description' => $row['description'],
					'image' => $row['image'],
					'communityDateTime' => $row['community_datetime'],
					'era' => $row['era'],
					'eraDateTime' => $row['era_datetime'],
					'communityMaster' => $row['master'],
					'masterDateTime' => $row['master_datetime'],
					'userCount' => $row['user_count'],
					'postCount' => $row['post_count']
				);
			}
		}
		
		print(json_encode($output));
		
		sqlsrv_free_stmt($stmt);	
		sqlsrv_close($conn);
	}

// Create Community Function. Function Code : 6	
function Create_Community() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
	
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$bits = explode(".", $_FILES["file"]["name"]);
	$extension = end($bits);
	
	$user_email = $_POST['user_email'];
	$community_name = $_POST['community_name'];
	$description = $_POST['description'];
	$category_id = $_POST['category_id'];
	
	$community_name = sql_textformat($community_name);
	$description = sql_textformat($description);

	if ($_FILES["file"]["size"] < 10240000 && $_FILES["file"]["size"] > 0) {
			
		$sql = "INSERT INTO [community] (community_name, description, category_id)
					OUTPUT inserted.community_id
					VALUES ('$community_name', '$description', '$category_id')";
		
		$stmt = sqlsrv_query($conn, $sql);
		
		if ($stmt === false) {
			$output['status'] = false;
		} else {		
			while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
				$community_id = $row['community_id'];
			}
			
		    $sql = "INSERT INTO [subscription] (user_email, community_id)
			    		VALUES ('$user_email', (SELECT community_id
    											FROM [community]
    											WHERE community_name = '$community_name'
    												AND category_id = '$category_id'))";

    		$stmt = sqlsrv_query($conn, $sql);

			if ($stmt === false) {
			    $output['status'] = false;
		    } else {
		    	
		    	$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
		
				// Create blob REST proxy.
				$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
		
				$content = file_get_contents($_FILES["file"]["tmp_name"]);	
				$blob_name = 'c' . $community_id;
				
				try {
				    //Upload blob
				    $blobRestProxy->createBlockBlob("image", $blob_name, $content);
		
			        $image_uri = "http://jays.blob.core.windows.net/image/" . $blob_name;	
		    	
			        $sql = "UPDATE [community]
							SET image = '$image_uri'
							WHERE community_id = '$community_id'";
							
					$stmt = sqlsrv_query($conn, $sql);
					
					if ($stmt === false) {
						$output['status'] = false;
					} else {
						$sql = "INSERT INTO [era] (era, community_id)
									VALUES ('0', '$community_id')";
									
						$stmt = sqlsrv_query($conn, $sql);
						
						if($stmt === false) {
							$output['status'] = false;
						} else {
							$sql = "INSERT INTO [master] (community_id, user_email)
										VALUES ('$community_id', '$user_email')";
							
							$stmt = sqlsrv_query($conn, $sql);
							
							if($stmt === false) {
								$output['status'] = false;
							} else {
								$output['status'] = true;
							}
						}
					}
    			}
    			catch(ServiceException $e){
				    $code = $e->getCode();
				    $error_message = $e->getMessage();
				    echo $code.": ".$error_message."<br />";
				    
				    $output['status'] = false;
				}
		    }
		}
	}
	
	sqlsrv_free_stmt($stmt);
	sqlsrv_close($conn);
	
	print(json_encode($output));	
}

// Submit Post Function, Function Code : 7	
function Submit_Post() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
	
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$bits = explode(".", $_FILES["file"]["name"]);
	$extension = end($bits);
	
	$community_id = $_POST['community_id'];
	$user_email = $_POST['user_email'];
	$content = $_POST['content'];
	
	$content = sql_textformat($content);
	
	if ($_FILES["file"]["size"] < 10240000 && $_FILES["file"]["size"] > 0) {
			
		$sql = "INSERT INTO [post] (community_id, user_email, content)
				OUTPUT inserted.post_id 
				VALUES ('$community_id', '$user_email', '$content')";
		
		$stmt = sqlsrv_query($conn, $sql);
		
		if ($stmt === false) {
			$output['status'] = false;
		} else {		
			while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
				$post_id = $row['post_id'];
			}
			
			$connectionString = "DefaultEndPointsProtocol=http;AccountName=jays;AccountKey=WAstts5kAXqRxvmNPHz3LI9DV9OO4iyWlI/8Yj+mlSYPNdlhi8aZ7WFfG6cTHgtI8vJipO9TE4EZjuPjSSivPw==";
		
			// Create blob REST proxy.
			$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
	
			$content = file_get_contents($_FILES["file"]["tmp_name"]);	
			$blob_name = $post_id;
			
			try {
			    //Upload blob
			    $blobRestProxy->createBlockBlob("image", $blob_name, $content);
	
		        $image_uri = "http://jays.blob.core.windows.net/image/" . $blob_name;	
//				$sql = "INSERT INTO [post] (community_id, user_email, content, image)
//						OUTPUT inserted.post_id
//						VALUES ('$community_id', '$user_email', '$content', '$image_uri')";
				$sql = "UPDATE [post]
						SET image = '$image_uri'
						WHERE post_id = '$post_id'";
				   
			}
			catch(ServiceException $e){
			    $code = $e->getCode();
			    $error_message = $e->getMessage();
			    echo $code.": ".$error_message."<br />";
			    
			    $output['status'] = false;
			}
		}
		
		sqlsrv_free_stmt($stmt);
	} else {
		$sql = "INSERT INTO [post] (community_id, user_email, content)
				OUTPUT inserted.post_id 
				VALUES ('$community_id', '$user_email', '$content')";			
	}
	
	$stmt = sqlsrv_query($conn, $sql);
	
	if ($stmt === false) {
		$output['status'] = false;
	} else {
		$output['status'] = true;
		
		while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
			$output['result'][] = array(
				'postId' => $row['post_id']
			);
		}
	}
	
	sqlsrv_free_stmt($stmt);
	sqlsrv_close($conn);
	
	print(json_encode($output));
}

// Submit Comment Function, Function Code : 8	
function Submit_Comment() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.

	$post_id = $_POST['post_id'];	
	$user_email = $_POST['user_email'];
	$comment = $_POST['comment'];
	
	$comment = sql_textformat($comment);
	
	$sql = "INSERT INTO [comment] (post_id, user_email, comment)
			VALUES ('$post_id', '$user_email', '$comment')";
			    
	$stmt = sqlsrv_query($conn, $sql);

	if ($stmt === false) {
	    $output['status'] = false;
    } else {
    	$output['status'] = true;	    	
    }

	print(json_encode($output));
    
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// GET Hot Topic Function, Function Code : 9
function Hot_Topic() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
	
	$community_id = $_POST['community_id'];
	$user_email = $_POST['user_email'];
	
	if($community_id == 0) {
		$sql = "SELECT tmp.datetime, tmp.post_id, tmp.community_id, tmp.user_email, tmp.content, tmp.image, tmp.like_count, tmp.value
				FROM (SELECT [post].datetime, [post].post_id, [post].community_id, [post].user_email, [post].content, [post].image, like_table.like_count, like_table.value
						FROM (SELECT value_table.post_id, value_table.value, count_table.like_count
								FROM (SELECT post_id, SUM(CASE WHEN user_email = '$user_email' THEN 1 ELSE 0 END) AS value
										FROM [like]
										GROUP BY post_id) AS value_table, 
											(SELECT post_id, COUNT(value) AS like_count
												FROM [like]
												GROUP BY post_id) AS count_table
												WHERE value_table.post_id = count_table.post_id) AS like_table
				RIGHT OUTER JOIN [post] ON [post].post_id = like_table.post_id) AS tmp, [subscription]
				WHERE tmp.community_id = [subscription].community_id AND [subscription].user_email = '$user_email' AND tmp.like_count >= 1 ORDER BY tmp.datetime ASC";
				
	} else {
		$sql = "SELECT tmp.datetime, tmp.post_id, tmp.community_id, tmp.user_email, tmp.content, tmp.image, tmp.like_count, tmp.value
				FROM (SELECT [post].datetime, [post].post_id, [post].community_id, [post].user_email, [post].content, [post].image, like_table.like_count, like_table.value
						FROM (SELECT value_table.post_id, value_table.value, count_table.like_count
								FROM (SELECT post_id, SUM(CASE WHEN user_email = '$user_email' THEN 1 ELSE 0 END) AS value
										FROM [like]
										GROUP BY post_id) AS value_table, 
											(SELECT post_id, COUNT(value) AS like_count
												FROM [like]
												GROUP BY post_id) AS count_table
												WHERE value_table.post_id = count_table.post_id) AS like_table
				RIGHT OUTER JOIN [post] ON [post].post_id = like_table.post_id) AS tmp
				WHERE tmp.community_id = '$community_id' AND tmp.like_count >= 1 ORDER BY tmp.datetime ASC";		
	}
	
	$stmt = sqlsrv_query($conn, $sql);
	
	if($stmt === false) {
		$output['status'] = false;
	} else {
		$output['status'] = true;
		
		while ( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
		
			if($row['like_count'] == null) {
				$row['like_count'] = 0;
			}
			
			if($row['value'] == null || $row['value'] == 0) {
				$row['value'] = false;
			} else {
				$row['value'] = true;
			}
			
			$output['result'][] = array(
				'postId' => $row['post_id'],
				'communityId' => $row['community_id'],
				'content' => $row['content'],
				'userEmail' => $row['user_email'],
				'imageUri' => $row['image'],
				'likeCount' => $row['like_count'],
				'value' => $row['value'],
				'dateTime' => $row['datetime']
			);
		}
	}
	
	print(json_encode($output));
	
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// GET Weekly Topics Function, Function Code : 10
function Weekly_Topic() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
	
	$community_id = $_POST['community_id'];
	$user_email = $_POST['user_email'];
	
	$sql = "SELECT tmp.datetime, tmp.post_id, tmp.community_id, tmp.user_email, tmp.content, tmp.image, tmp.like_count, tmp.value, tmp.weekly_topics
			FROM (SELECT [post].datetime, [post].post_id, [post].community_id, [post].user_email, [post].content, [post].image, [post].weekly_topics,  like_table.like_count, like_table.value
					FROM (SELECT value_table.post_id, value_table.value, count_table.like_count
							FROM (SELECT post_id, SUM(CASE WHEN user_email = '$user_email' THEN 1 ELSE 0 end) AS VALUE
									FROM [like]
									GROUP BY post_id) AS value_table, (SELECT post_id, count(value) as like_count
																		FROM [like]
																		GROUP BY post_id) AS count_table
					WHERE value_table.post_id = count_table.post_id) as like_table
				RIGHT OUTER JOIN [post] on [post].post_id = like_table.post_id) AS tmp
			WHERE tmp.community_id = '$community_id' and tmp.weekly_topics IS NOT NULL";
	
	$stmt = sqlsrv_query($conn, $sql);
	
	if($stmt === false) {
		$output['status'] = false;
	} else {
		$output['status'] = true;
		
		while ( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
			if($row['like_count'] == null) {
				$row['like_count'] = 0;
			}
			
			if($row['value'] == null || $row['value'] == 0) {
				$row['value'] = false;
			} else {
				$row['value'] = true;
			}
		
			$output['result'][] = array(
				'postId' => $row['post_id'],
				'communityId' => $row['community_id'],
				'userEmail' => $row['user_email'],
				'imageUri' => $row['image'],
				'likeCount' => $row['like_count'],
				'value' => $row['value'],
				'dateTime' => $row['datetime'],
				'week' => $row['week']
			);
		}
	}
	
	print(json_encode($output));
	
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// Like Post Function, Function code : 11
function Like_Post() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.

	$post_id = $_POST['post_id'];	
	$user_email = $_POST['user_email'];
	$value = $_POST['value'];
	
	$comment = sql_textformat($comment);
	
	$sql = "INSERT INTO [like] (post_id, user_email, value)
			VALUES ('$post_id', '$user_email', '$value')";
			    
	$stmt = sqlsrv_query($conn, $sql);

	if ($stmt === false) {
	    $output['status'] = false;
    } else {
    	$output['status'] = true;	    	
    }

	print(json_encode($output));
    
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// Subscribe Function, Function code : 12
function Subscribe() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.

	$user_email = $_POST['user_email'];
	$community_id = $_POST['community_id'];	
	
	$comment = sql_textformat($comment);
	
	$sql = "INSERT INTO [subscription] (user_email, community_id)
			VALUES ('$user_email', '$community_id')";
			    
	$stmt = sqlsrv_query($conn, $sql);

	if ($stmt === false) {
	    $output['status'] = false;
    } else {
    	$output['status'] = true;	    	
    }

	print(json_encode($output));
    
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}

// Transfrom SQL Text Format
function sql_textformat($text){
	$text = str_replace("'", "''", $text);
	return $text;
}

//Test Function, Function Code : 99
function Test() {
	require_once ('../topicsdb_connect.php'); // Connect to the db.
			
	$sql = "SELECT *
			FROM [user]";
			
	$stmt = sqlsrv_query($conn, $sql);

	if ($stmt === false) {
	    FatalError("Failed to Sign In: ".$sql);
	    $output['status'] = false;
    } else {
    	$output['status'] = true;
    	
		while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH) ) {
			$output['result'][] = array(
				'name' => $row['user_name'],
				'email' => $row['user_email']
			);
		}
    }
    
	print(json_encode($output));
    
	sqlsrv_free_stmt($stmt);	
	sqlsrv_close($conn);
}
?>