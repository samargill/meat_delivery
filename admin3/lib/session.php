<?php
	if (!isset($_SESSION[constant("SessionID")]))
	{
		header("Location: ".$PagePath."./?Err=1");
		exit;
	}
	if (time() - intval($_SESSION[constant("SessionID")."Time"]) > 7200)
	{
		header("Location: ".$PagePath."./?Err=1");
		exit;		
	}
	$Query = "UPDATE adminlogin".
		" SET lastactive = NOW()".
		" WHERE adminid = ".$_SESSION[SessionID];
	mysqli_query($Conn,$Query);
	$_SESSION[constant("SessionID")."Time"] = time();
	
	function CheckRight($OpType,$ActType="Redirect",$ChkRight=null,$UserID=null)
	{
		// OpType  = View, Add, Edit, Delete
		// ActType = Redirect, ShowError, Return true / false
		if ($ChkRight != null)
		{
			$RightList = $ChkRight;
		}
		else if (isset($GLOBALS['PageID']))
		{
			$RightList = $GLOBALS['PageID'];
		}
		if ($UserID == null)
		{
			$UserID = $_SESSION[SessionID];
		}
		if (isset($RightList))
		{
			$RightStatus = "False";
			if (is_array($RightList[0]))
			{
				$IsArray = true;
				$Count = count($RightList);
			}
			else
			{
				$IsArray = false;
				$Count = 1;
			}
			//echo("Count = ".$Count." | IsArray = ".$IsArray); die;
			for ($i = 0; $i < $Count && $RightStatus == "False"; $i++)
			{
				if ($IsArray)
					$CurPageRight = $RightList[$i];
				else
					$CurPageRight = $RightList;
				$Query = "SELECT rights FROM adminmenurights".
					" WHERE adminid = ".$UserID.
					" AND menuid = ".$CurPageRight[0]." AND subid = ".$CurPageRight[1]." AND subsubid = ".$CurPageRight[2];
				$rstRow = mysqli_query($GLOBALS['Conn'],$Query);
				if (mysqli_num_rows($rstRow) > 0)
				{
					$objRow = mysqli_fetch_object($rstRow);
					$PageRight = str_pad(decbin($objRow->rights),4,"0",STR_PAD_LEFT);
					if ($OpType == "View" && $PageRight[3] == "1")
					{
						$RightStatus = "True";
					}
					else if ($OpType == "Add" && $PageRight[2] == "1")
					{
						$RightStatus = "True";
					}
					else if ($OpType == "Edit" && $PageRight[1] == "1")
					{
						$RightStatus = "True";
					}
					else if ($OpType == "Delete" && $PageRight[0] == "1")
					{
						$RightStatus = "True";
					}
				}
			}
			if ($RightStatus == "True")
			{
				if ($ActType == "Return")
				{
					return(true);
				}
			}
			else
			{
				if ($ActType == "Return")
					return(false);
				else if ($ActType == "ShowError")
				{
					echo("ShowError(true,\"UnAuthorized!\",\"You Are Not Authorized To Perform This Operation ...\"); return(false);\n");
				}
				else
				{
					header("Location: ".$GLOBALS['PagePath']."unauthorized");
					exit;
				}
			}
		}
	}