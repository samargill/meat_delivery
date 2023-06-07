	<aside class="main-sidebar sidebar-dark-primary elevation-4">
		<a href="#" class="brand-link">
			<img src="<?php echo($PagePath) ?>dist/img/logo-mini.png" class="brand-image img-circle elevation-3" style="opacity: .8">
			<span class="brand-text font-weight-light">AdminLTE 3</span>
	    </a>
		<div class="sidebar">
			<div class="user-panel mt-3 pb-3 mb-3 d-flex">
				<div class="image">
					<img src="<?php echo($PagePath);?>dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
				</div>
				<div class="info">
					<a href="#" class="d-block"><?php echo($_SESSION[constant("SessionID")."Name"]);?></a>
				</div>
			</div>
			<nav class="mt-2">
				<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
					<?php
						$Query = "SELECT M.menuid, M.menuname, M.menulink, M.menuicon".
							" FROM adminmenu M ".
							" INNER JOIN adminmenurights MR ON M.menuid = MR.menuid".
							" WHERE M.subid = 0 AND M.menustatus = 0".
							" AND MR.adminid = ".$_SESSION[SessionID]." AND MR.subid = 0".
							" ORDER BY M.sorting";
						$rstRow = mysqli_query($Conn,$Query);
						while ($objRow = mysqli_fetch_object($rstRow))
						{
							if ($objRow->menulink != "")
							{
					?>
					<li class="nav-item">
						<a href="<?php echo($PagePath.$objRow->menulink);?>" class="nav-link <?php if ($PageMenu == $objRow->menuname) echo("active ");?>">
							<i class="nav-icon fas <?php echo($objRow->menuicon);?>"></i> <p><?php echo($objRow->menuname);?></p>
						</a>
					</li>
					<?php
							}
							else
							{
					?>
					<li class="nav-item has-treeview <?php if ($PageMenu == $objRow->menuname) echo("menu-open");?>">
						<a href="#" class="nav-link <?php if ($PageMenu == $objRow->menuname) echo("active");?>">
							<i class="nav-icon fa <?php echo($objRow->menuicon);?>"></i>
							<p>
								<?php echo($objRow->menuname);?>
								<i class="right fa fa-angle-left pull-right"></i>
							</p>
						</a>
						<ul class="nav nav-treeview">
						<?php
								$Query = "SELECT M.menuid, M.menuname, M.menulink, M.menuicon".
									" FROM adminmenu M".
									" INNER JOIN adminmenurights MR ON M.menuid = MR.menuid AND M.subid = MR.subid".
									" WHERE M.menuid = ".$objRow->menuid.
									" AND M.subid > 0 AND M.menustatus = 0".
									" AND MR.adminid = ".$_SESSION[SessionID]." AND MR.subid > 0".
									" ORDER BY M.sorting, M.menuid, M.subid";
								$rstPro = mysqli_query($Conn,$Query);
								while ($objPro = mysqli_fetch_object($rstPro))
								{
									if (substr($objPro->menulink,0,6) == "Popup:")
									{
										$MenuLink = "javascript:MenuPopup('".$PagePath.substr($objPro->menulink,6)."');";
									}
									else
									{
										$MenuLink = $PagePath.$objPro->menulink;
									}
									$SubActive = "";
									if (isset($PageName))
									{
										if ($PageName == $objPro->menuname)
										{
											$SubActive = "active";
										}
									}
						?>
							<li class="nav-item">
								<a href="<?php echo($MenuLink);?>" class="nav-link <?php echo($SubActive);?>"><i class="far fa-circle nav-icon"></i><p><?php echo($objPro->menuname);?></p></a>
							</li>
						<?php
								}
						?>
						</ul>
					</li>
					<?php
							}
						}
					?>
				</ul>
			</nav>
		</div>
	</aside>