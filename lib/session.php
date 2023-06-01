<?php
	if (defined("AppView") && isset($_REQUEST['AppPatientID']) && isset($_REQUEST['AppFamilyID']))
	{
		SetAppViewSession();
	}
	if ($LoginReq == true)
	{
		if (!isset($_SESSION[SessionID]))
		{
			if (isset($PageLink))
			{
				$_SESSION['Login-Referer'] = $PageLink;
			}
			else
			{
				unset($_SESSION['Login-Referer']);
			}
			header("Location: ".$Path."login");
			exit;
		}
	}
	if (isset($_SESSION[SessionID]))
	{
		if (time() - intval($_SESSION[SessionID."-Time"]) > 1800)
		{
			UnsetSession();
			if ($LoginReq == true && !isset($_SESSION['Signin']))
			{
				unset($_SESSION['Login-Referer']);
				header("Location: ".$Path."login");
				exit;
			}
		}
		$_SESSION[constant("SessionID")."-Time"] = time();
	}
?>
<?php
	function SetAppViewSession()
	{
		if (defined("AppView") && isset($_REQUEST['AppPatientID']) && isset($_REQUEST['AppFamilyID']))
		{
			$Query = "SELECT firstname FROM patientfamily".
				" WHERE patientid = ".$_REQUEST['AppPatientID']." AND familyid = ".$_REQUEST['AppFamilyID'];
			//echo("<br><br>".$Query); die;
			$rstRow = mysqli_query($GLOBALS["Conn"],$Query);
			if (mysqli_num_rows($rstRow) > 0)
			{
				$objRow = mysqli_fetch_object($rstRow);
				$_SESSION[SessionID] = $_REQUEST['AppPatientID'];
				$_SESSION[SessionID."-FamilyID"] = $_REQUEST['AppFamilyID'];
				$_SESSION[SessionID."-FamilyName"] = $objRow->firstname;
				$_SESSION[SessionID."-Time"] = time();
			}
		}
	}
	function UnsetSession()
	{
		unset($_SESSION[SessionID]);
		unset($_SESSION[SessionID."-Time"]);
		unset($_SESSION[SessionID."-FirstName"]);
		unset($_SESSION[SessionID."-LastName"]);
	}
?>