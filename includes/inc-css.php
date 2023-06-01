<?php
	if ($PageID > 0)
	{
		$Query = "SELECT seotitle, seokeywords, seodescription".
			" FROM webpages WHERE pageid = ".$PageID;
		$rstPage = mysqli_query($Conn,$Query);
		$objPage = mysqli_fetch_object($rstPage);
		$SeoTitle = $objPage->seotitle;
		$SeoKeywords = $objPage->seokeywords;
		$SeoDescription = $objPage->seodescription;
	}
	if (!isset($SeoTitle))
	{
		if (isset($objSeo))
			$SeoTitle = $objSeo->seotitle;
		else
			$SeoTitle = $Page." - ".WebsiteTitle;
	}
	if (!isset($SeoKeywords))
	{
		if (isset($objSeo))
			$SeoKeywords = $objSeo->seokeywords;
		else
			$SeoKeywords = "";
	}
	if (!isset($SeoDescription))
	{
		if (isset($objSeo))
			$SeoDescription = $objSeo->seodescription;
		else
			$SeoDescription = "";
	}
	if (!isset($Robots))
	{
		$Robots = "index, follow";
	}
?>
	<meta charset="utf-8">
	<title><?php echo(SetCompany(TrimText($SeoTitle,0)));?></title>
	<meta name="robots" value="<?php echo($Robots);?>" />
	<meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />	
	<meta name="keywords" content="<?php echo(SetCompany(TrimText($SeoKeywords,0)));?>"/>
	<meta name="description" content="<?php echo(SetCompany(TrimText($SeoDescription,0)));?>"/>
	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="assets/imgs/theme/favicon.svg">
	<link rel="apple-touch-icon" href="assets/imgs/theme/favicon.svg">
	<!-- Template CSS -->
	<!-- <link rel="stylesheet" href="assets/css/plugins/animate.min.css"> -->
	<link rel="stylesheet" href="assets/css/main.css?v=5.6">