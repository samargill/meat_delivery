<?php
	function GenWatermark()
	{
		$images  = array($_SERVER['DOCUMENT_ROOT']."/PrimeMedic/images/watermark.png");
		$imagick = new Imagick($images);
		$imagick->scaleimage(900,0);
		$width   = $imagick->getimagewidth();
		$height  = $imagick->getimageheight();
		$draw = new ImagickDraw();
		$draw->setFillColor('#bdbdbd');
		$draw->setFontSize(22);
		$imagick->annotateImage($draw, $width/2, $height/2+35, 0, $GLOBALS['CertDate']);
		$imagick->rotateimage('white',45);
		$imagick->setImageFormat('png');
		$imagick->writeImages($_SERVER['DOCUMENT_ROOT']."/PrimeMedic/images/watermark-date.png",true);
	}
?>