<?php
	function OutputFile($FileName,$FilePath)
	{
		$FilePath = $FilePath."/".$FileName;
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".uniqid("",false).".pdf\"");
		header("Expires: 0");
		header("Cache-Control: must-revalidate");
		header("Pragma: public");
		header("Content-Length: ".filesize($FilePath));
		readfile($FilePath);
	}

	function ShowImage($Barcode)
	{
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
		//imagejpeg($NewImage,$strNewPicName,$Quality);
	}

	function DeleteFile($strPath)
	{
		@unlink($strPath);
	}

	function ImagickResize($FilePath,$NewFilePath,$Width,$Height,$Ext="png")
	{
		if (!extension_loaded('imagick'))
		{
			echo ('Imagick Not Installed');
			die();
		}
		$CurImg  = array($FilePath.".".$Ext);
		$Imagick = new Imagick($CurImg);
		// Change Main Image Extenxion To PNG 
		if ($Ext != "png")
		{
			$Imagick->setImageFormat('png');
			$Imagick->writeImages($FilePath.".png",true);
			unlink($FilePath.".".$Ext);
		}
		// Create Thumbnail Image
		$Imagick->thumbnailImage($Width, $Height, true, true);
		$Imagick->setImageFormat('png');
		$Imagick->writeImages($NewFilePath.".png",true);
		
		//$Imagick->resizeImage($Width, $Height, imagick::FILTER_LANCZOS, 1, TRUE);

		/*$imageprops = $Imagick->getImageGeometry();
		$width  = $imageprops['width'];
		$height = $imageprops['height'];
		if ($width > $height) 
		{
			$newHeight = 190;
			$newWidth  = (300 / $height) * $width;
		}
		else 
		{
			$newWidth = 300;
			$newHeight = (190 / $width) * $height;
		}
		// if ($width > $height)
		// {
		// 	$Ratio = $width / 300;
		// }
		// else
		// {
		// 	$Ratio = $height / 190;
		// }
		// if ($Ratio > 1.00)
		// {
		// 	$newHeight = sprintf("%d", $height / $Ratio);
		// 	$newWidth  = sprintf("%d", $width  / $Ratio);
		// }
		// else
		// {
		// 	$newHeight = $height;
		// 	$newWidth  = $width;
		// }
		$Imagick->resizeImage($newWidth,$newHeight, imagick::FILTER_LANCZOS, 1, true);
		$Imagick->writeImages($NewFilePath.".".$Ext,true);*/
	}

	function file_mime_type($file) 
	{
		// We'll need this to validate the MIME info string (e.g. text/plain; charset=us-ascii)
		$regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';
		/* Fileinfo extension - most reliable method
		 *
		 * Unfortunately, prior to PHP 5.3 - it's only available as a PECL extension and the
		 * more convenient FILEINFO_MIME_TYPE flag doesn't exist.
		 */
		if (function_exists('finfo_file'))
		{
			$finfo = finfo_open(FILEINFO_MIME);
			if (is_resource($finfo)) // It is possible that a FALSE value is returned, if there is no magic MIME database file found on the system
			{
				$mime = @finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);
				/* According to the comments section of the PHP manual page,
				 * it is possible that this function returns an empty string
				 * for some files (e.g. if they don't exist in the magic MIME database)
				 */
				if (is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$file_type = $matches[1];
					return $file_type;
				}
			}
		}

		/* This is an ugly hack, but UNIX-type systems provide a "native" way to detect the file type,
		 * which is still more secure than depending on the value of $_FILES[$field]['type'], and as it
		 * was reported in issue #750 (https://github.com/EllisLab/CodeIgniter/issues/750) - it's better
		 * than mime_content_type() as well, hence the attempts to try calling the command line with
		 * three different functions.
		 *
		 * Notes:
		 *	- the DIRECTORY_SEPARATOR comparison ensures that we're not on a Windows system
		 *	- many system admins would disable the exec(), shell_exec(), popen() and similar functions
		 *	  due to security concerns, hence the function_exists() checks
		 */
		if (DIRECTORY_SEPARATOR !== '\\')
		{
			$cmd = 'file --brief --mime ' . escapeshellarg($file['tmp_name']) . ' 2>&1';

			if (function_exists('exec'))
			{
				/* This might look confusing, as $mime is being populated with all of the output when set in the second parameter.
				 * However, we only neeed the last line, which is the actual return value of exec(), and as such - it overwrites
				 * anything that could already be set for $mime previously. This effectively makes the second parameter a dummy
				 * value, which is only put to allow us to get the return status code.
				 */
				$mime = @exec($cmd, $mime, $return_status);
				if ($return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$file_type = $matches[1];
					return $file_type;
				}
			}

			if ( (bool) @ini_get('safe_mode') === FALSE && function_exists('shell_exec'))
			{
				$mime = @shell_exec($cmd);
				if (strlen($mime) > 0)
				{
					$mime = explode("\n", trim($mime));
					if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
					{
						$file_type = $matches[1];
						return $file_type;
					}
				}
			}

			if (function_exists('popen'))
			{
				$proc = @popen($cmd, 'r');
				if (is_resource($proc))
				{
					$mime = @fread($proc, 512);
					@pclose($proc);
					if ($mime !== FALSE)
					{
						$mime = explode("\n", trim($mime));
						if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
						{
							$file_type = $matches[1];
							return $file_type;
						}
					}
				}
			}
		}

		// Fall back to the deprecated mime_content_type(), if available (still better than $_FILES[$field]['type'])
		if (function_exists('mime_content_type'))
		{
			$file_type = @mime_content_type($file['tmp_name']);
			if (strlen($file_type) > 0) // It's possible that mime_content_type() returns FALSE or an empty string
			{
				return $file_type;
			}
		}

		return $file['type'];
}
?>