<?php
	if ($LoginReq == true)
	{
		if (!isset($_SESSION[SessionID."-Rapid-Client"]))
		{
			if (isset($PageLink))
			{
				$_SESSION['Login-Referer'] = $PageLink;
			}
			else
			{
				unset($_SESSION['Login-Referer']);
			}
			header("Location: ".$Path."rapid-testing/login");
			exit;
		}
	}
	if (isset($_SESSION[SessionID."-Rapid-Client"]))
	{
		if (time() - intval($_SESSION[SessionID."-Rapid-Client-Time"]) > 1800)
		{
			UnsetSession();
			if ($LoginReq == true && !isset($_SESSION['Signin']))
			{
				unset($_SESSION['Login-Referer']);
				header("Location: ".$Path."rapid-testing/login");
				exit;
			}
		}
		$_SESSION[SessionID."-Rapid-Client-Time"] = time();
	}
	function UnsetSession()
	{
		unset($_SESSION[SessionID."-Rapid-Client"]);
		unset($_SESSION[SessionID."-Rapid-Client-Name"]);
		unset($_SESSION[SessionID."-Rapid-Client-Type"]);
		unset($_SESSION[SessionID."-Rapid-Client-Time"]);
	}
?>