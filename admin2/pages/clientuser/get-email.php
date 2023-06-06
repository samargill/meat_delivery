<?php
	$PagePath = "../../";
	include($PagePath."lib/variables.php");
	include($PagePath."lib/opencon.php");
	include($PagePath."lib/session.php");
	include($PagePath."lib/functions.php");
	include($PagePath."lib/email.php");
	include($PagePath."lib/PHPMailer/PHPMailer.php");
	include($PagePath."lib/PHPMailer/Exception.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	if (isset($_POST['BtnPressedEmail']))
	{
		$Query  = "SELECT email FROM clientuser WHERE clientid = ".$_SESSION[SessionID."ClientID"]." AND email = '".$_REQUEST['txtEmail']."'";
		$rstRow = mysqli_query($Conn,$Query);
		if (mysqli_num_rows($rstRow) > 0)
		{
			echo "Email-Exist";
			die();	
		}
		else
		{
			$Query = "SELECT name FROM clientuser WHERE clientid = ".$_SESSION[SessionID."ClientID"]."";
			$rstRow = mysqli_query($Conn,$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$objRow = mysqli_fetch_object($rstRow);
			}	

			$EmailCode = uuid();
			$_SESSION['Signup-UserEmailCode'] = $EmailCode;
			$_SESSION['Signup-UserEmail']= $_REQUEST['txtEmail'];
			

			$NewUserEmail = GetSignupUserEmail($objRow->name,$EmailCode);
			if (DBUserName == "root")
			{
				$NewUserEmailCode= "Test123";
				$_SESSION['Signup-UserEmailCode'] = $NewUserEmailCode;
				// $_SESSION['Signup-UserEmail'] = $NewUserEmail;
				echo "Done";
				die();
			}
			else
			{
				$PHPMailer = new PHPMailer();
				$PHPMailer->SendEmail(constant("FromEmail"),$_REQUEST['txtEmail'],"Email Verification Code",$NewUserEmail);
				echo "Done";
				die();
			}			
		}
	}

	if (isset($_POST['BtnPressedCode']))
	{
		$NewUserEmailCode = $_SESSION['Signup-UserEmailCode'];
		$NewUserEmail = $_SESSION['Signup-UserEmail'];
		if ($_REQUEST['txtCode'] == $NewUserEmailCode)
		{
			$Query = "UPDATE clientuser SET".
				" email = '".$NewUserEmail."'".
				" WHERE clientid = ".$_SESSION[SessionID."ClientID"]."";
			mysqli_query($Conn,$Query);	
			echo "Done";
			die();
		}
		else
		{
			echo "ErrorCode";
			die();	
		}
	}
?>