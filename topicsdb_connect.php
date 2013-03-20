<?php

   $serverName = "tcp:a5ema4ewde.database.windows.net,1433";
   $userName = 'topics_admin@a5ema4ewde';
   $userPassword = 'Xhvlrtm?!';
   $dbName = "topics_db";
  
   $connectionInfo = array("Database"=>$dbName, "UID"=>$userName, "PWD"=>$userPassword, "MultipleActiveResultSets"=>true);

   sqlsrv_configure('WarningsReturnAsErrors', 0);
   $conn = sqlsrv_connect( $serverName, $connectionInfo);
   if($conn === false)
   {
     FatalError("Failed to connect...");
   }
   
   if($conn === true) {
	   echo "Success to connect";
   }

function FatalError($errorMsg)
{
    Handle_Errors();
    die($errorMsg."\n");
}

function Handle_Errors()
{
    $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
    $count = count($errors);
    if($count == 0)
    {
       $errors = sqlsrv_errors(SQLSRV_ERR_ALL);
       $count = count($errors);
    }
    if($count > 0)
    {
      for($i = 0; $i < $count; $i++)
      {
         echo $errors[$i]['message']."\n";
      }
    }
}

?>