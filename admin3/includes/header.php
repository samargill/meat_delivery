
	<nav class="main-header navbar navbar-expand navbar-dark navbar-navy">
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
			</li>
		</ul>
		<ul class="navbar-nav ml-auto">
			<li class="nav-item dropdown notifications-menu" >
				<a class="nav-link" data-toggle="dropdown" id="notifications-menu" href="#">
					<i class="far fa-bell"></i>
					<span class="badge badge-warning navbar-badge" id="notifications-count">0</span>
				</a>
				<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="DropMenu" >
					<a href="#" class="dropdown-item dropdown-footer">You have no notifications</a>
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link" data-toggle="dropdown" href="#">
					<img src="<?php echo($PagePath);?>dist/img/avatar5.png" class="img-circle elevation-2" style="margin-right: 10px;" width="30px" alt="User Image">
					<span class="hidden-xs">
						<i class="fa fa-angle-down"></i>
					</span>
				</a>
				<div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
					<a href="" class="dropdown-item" style="cursor: context-menu;" >
						<div class="media">
							<img src="<?php echo($PagePath);?>dist/img/avatar5.png"  class="img-circle mr-3" width="40px" alt="User Image">
							<div class="media-body">
								<h3 class="dropdown-item-title text-primary" style="margin-top: 10px;" >
									<?php
										echo($_SESSION[constant("SessionID")."Name"]);
									?>
								</h3>
							</div>
						</div>
					</a>
					<div class="dropdown-divider"></div>
					<a href="<?php echo($PagePath) ?>main?Signout" class="dropdown-item">
						<div class="media">
							<div class="img-circle mr-3 text-primary " style="font-size: 20px;" >
								<i class="fas fa-sign-out-alt"></i>
							</div>
							<div class="media-body">
								<h3 class="dropdown-item-title text-primary" style="margin-top: 6px;" >
									Logout
								</h3>
							</div>
						</div>
					</a>
				</div>
			</li>
		</ul>
	</nav>