<?php
	function OutputFile($FileName,$FilePath)
	{
		$FilePath = $FilePath."/".$FileName;
		if (file_exists($FilePath))
		{
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"".uniqid("",false).".pdf\"");
			header("Expires: 0");
			header("Cache-Control: must-revalidate");
			header("Pragma: public");
			header("Content-Length: ".filesize($FilePath));
			readfile($FilePath);
		}
	}

	function OutputFileAdmin($FileName,$FilePath,$FileExt=".pdf")
	{
		$FilePath = $FilePath."/".$FileName.$FileExt;
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".uniqid("",false).$FileExt."\"");
		header("Expires: 0");
		header("Cache-Control: must-revalidate");
		header("Pragma: public");
		header("Content-Length: ".filesize($FilePath));
		readfile($FilePath);
	}

	function ShowImage($Barcode)
	{/* Written By : Mohammad Kaiser Anwar */
		$Picture = array("","");
		if (file_exists("../".ImagePath."/".$Barcode."-1-sml.jpg") == true)
		{
			$Picture[0] = "../".ImagePath."/".$Barcode."-1-sml.jpg";
			$Picture[1] = "../".ImagePath."/".$Barcode."-1-lrg.jpg";
		}
		else
		{
			$Picture[0] = "../".ImagePath."/nopicture-sml.jpg";
			$Picture[1] = "../".ImagePath."/nopicture-lrg.jpg";
		}
		$Picture[1] = $Path."../product/imageviewer.php?Picture=".$Picture[1]."&Barcode=".$Barcode."&Height=".$ImageY."&Width=".$ImageX;
		return($Picture);
	}

	function ExtensionEx($strName)
	{
		$strExt = strrchr($strName, '.');
		return $strExt; 
	}

	function ExtensionRm($strName)
	{
		$strFile = substr($strName,0,strlen($strName) - strlen(strrchr($strName, '.')));
		return $strFile;
	}

	function DirCreate($DirPath)
	{/* Create Directory */
		if (!is_dir($DirPath))
		{
			return(mkdir($DirPath,0777));
		}
	}

	function DirRemove($DirPath)
	{/* Remove Directory */
		if (is_dir($DirPath))
		{
			rmdir($DirPath);
		}
	}

	function DirRename($DirPath,$DirOldName,$DirNewName)
	{/* Rename Directory */
		$DirOldName = $DirPath.$DirOldName;
		$DirNewName = $DirPath.$DirNewName;
		if (is_dir($DirOldName))
		{
			rename($DirOldName,$DirNewName);
		}
	}

	function GetImageWH($OldWidth,$OldHeight,$NewWidth,$NewHeight)
	{
		$Perporation = $OldHeight / $OldWidth;
		if ($Perporation > 1)
		{
			$Ratio = $OldHeight / $NewHeight;
			$NewWidth = round($OldWidth / $Ratio);
		}
		else if ($Perporation == 1)
		{
		}
		else if ($Perporation < 1)
		{
			$Ratio = $OldWidth / $NewWidth;
			$NewHeight = round($OldHeight / $Ratio);
		}
		return(array($NewWidth,$NewHeight));
	}

	function UploadFile($File,$Path)
	{
		switch ($_FILES[$File]['error'])
		{
			case 1:
				return("Problem :: File Exceeded Maximum File Size (2MB)");
				break;
			case 2:
				return("Problem :: File Exceeded Maximum File Size (2MB)");
				break;
			case 3:
				return("Problem :: File Only Partially Uploaded");
				break;
			case 4:
				return("Problem :: No file uploaded");
				break;
		}
		if (is_uploaded_file($_FILES[$File]['tmp_name']))
		{
			if (!copy($_FILES[$File]['tmp_name'],$Path))
			{
				return("Problem: Could Not Move File To Destination Directory");
			}
		}
		else
		{
			return("Problem: Possible File Upload Attack. Filename: ".$_FILES[$File]['name']);
		}
	}

	function ResizePicture($strPicName,$strNewPicName,$MaxSize)
	{/* Resize Picture */
		$Quality = 100;
		list($OldWidth,$OldHeight,$Extension) = getimagesize($strPicName);
		switch ($Extension)
		{
			case 1:
				$OldImage = imagecreatefromgif($strPicName);
				break;
			case 2:
				$OldImage = imagecreatefromjpeg($strPicName);
				break;
			case 3:
				$OldImage = imagecreatefrompng($strPicName);
				break;
		}
		if ($OldWidth > $OldHeight)
		{
			$Ratio = $OldWidth / $MaxSize;
		}
		else
		{
			$Ratio = $OldHeight / $MaxSize;
		}
		if ($Ratio > 1.00)
		{
			$NewHeight = sprintf("%d",$OldHeight / $Ratio);
			$NewWidth  = sprintf("%d",$OldWidth  / $Ratio);
		}
		else
		{
			$NewHeight = $OldHeight;
			$NewWidth  = $OldWidth;
		}
		$NewImage = imagecreatetruecolor($NewWidth,$NewHeight);
		imagecopyresampled($NewImage,$OldImage,0,0,0,0,$NewWidth,$NewHeight,$OldWidth,$OldHeight);
		if ($Extension == 3)
		{
			imagepng($NewImage,$strNewPicName,$Quality);
		}
		else
		{
			imagejpeg($NewImage,$strNewPicName,$Quality);
		}
	}

	function ImagickResize($FilePath,$NewFilePath,$Height,$Width,$Ext="png")
	{
		if (!extension_loaded('imagick'))
		{
			echo ('Imagick Not Installed');
			die();
		}
		$CurImg  = array($FilePath.".".$Ext);
		$Imagick = new Imagick($CurImg);
		$Imagick->resizeImage($Height,$Width,imagick::FILTER_LANCZOS, 1, TRUE);
		$Imagick->setImageFormat($Ext);
		$Imagick->writeImages($NewFilePath.".".$Ext,true);
	}

	function DeleteFile($strPath)
	{
		@unlink($strPath);
	}
?>