<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");

	if(isset($_POST['BtnPressed']))
	{
		$Query  = "SELECT password FROM clientuser WHERE clientid = ".$_SESSION[SessionID."ClientID"]."";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) == 0)
		{
			echo "WrongProfile";
			die();	
		}
		else
		{
			$objRow = mysqli_fetch_object($rstRow);
			$Password = $objRow->password;
			if (password_verify($_REQUEST['txtOldPass'],$objRow->password) == false || trim($_REQUEST['txtOldPass']) == "")
			{
				echo "OldWrong";
				die();
			}
			$Query = "UPDATE clientuser SET".
				" password = '".password_hash($_REQUEST['txtNewPass'],PASSWORD_DEFAULT)."'".
				" WHERE clientid = ".$_SESSION[SessionID."ClientID"]."";
			mysqli_query($Conn,$Query);
			echo "Done";
			die();
		}
	}
?>