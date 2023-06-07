// JavaScript Document

$(function() {
	"use strict";
	function responsive_dropdown ()
	{
		// For Mobile Menu Dropdown
		$("#menu-main span.opener").on("click", function(){
			var menuopener = $(this);
			if (menuopener.hasClass("plus"))
			{
				if (menuopener.parents('.level2').length)
				{
					menuopener.parent().find('.second-sub-menu').slideDown(700);
				}
				else
				{
					menuopener.parent().find('.mobile-sub-menu').slideDown(700);
				}
				menuopener.removeClass('plus');
				menuopener.addClass('minus');
			}
			else
			{
				menuopener.parent().find('.mobile-sub-menu, .second-sub-menu').slideUp();
				menuopener.removeClass('minus');
				menuopener.addClass('plus');
			}
			return false;
		});
		jQuery( ".mobilemenu" ).on("click", function() {
			jQuery( ".mobilemenu-content" ).slideToggle();
			if ($(this).hasClass("openmenu"))
			{
				$(this).removeClass('openmenu');
				$(this).addClass('closemenu');
			}
			else
			{
				$(this).removeClass('closemenu');
				$(this).addClass('openmenu');
			}
			return false;
		});
		// For Sidebar JS
		$('.sidebar-box span.opener').on("click", function(){
			if ($(this).hasClass("plus")) {
				$(this).parent().find('.sidebar-contant').slideDown();
				$(this).removeClass('plus');
				$(this).addClass('minus');
			}
			else
			{
				$(this).parent().find('.sidebar-contant').slideUp();
				$(this).removeClass('minus');
				$(this).addClass('plus');
			}
			return false;
		});
		// For Footer
		$('.footer-static-block span.opener').on("click", function() {
			if ($(this).hasClass("plus"))
			{
				$(this).parent().find('.footer-block-contant').slideDown();
				$(this).removeClass('plus');
				$(this).addClass('minus');
			}
			else
			{
				$(this).parent().find('.footer-block-contant').slideUp();
				$(this).removeClass('minus');
				$(this).addClass('plus');
			}
			return false;
		});
		// For Navbar
		$('.navbar-toggle').on("click", function(){
			var menu_id = $('#menu');
			var nav_icon = $('.navbar-toggle i');
			if (menu_id.hasClass('menu-open'))
			{
				menu_id.removeClass('menu-open');
				nav_icon.removeClass('fa-times');
				nav_icon.addClass('fa-bars');
			}
			else
			{
				menu_id.addClass('menu-open');
				nav_icon.addClass('fa-times');
				nav_icon.removeClass('fa-bars');
			}
			return false;
		});
		// For Category Dropdown
		$('.btn-sidebar-menu-dropdown').on("click", function() {
			var sb_block = $('.sidebar-block');
			$('.cat-dropdown').slideToggle();
			if (sb_block.hasClass("open1"))
			{
				sb_block.addClass("close1").removeClass("open1");
			}
			else
			{
				sb_block.addClass("open1").removeClass("close1");
			}
			return false;
		});
		// For Content Dropdown
		$('.content-link').on("click", function() {
			$('.content-dropdown').toggle();
		});
	}

	function owlcarousel_slider()
	{
		//OWL Slider
		$('.roadmap-slider').owlCarousel({
			items: 4,
			nav: false,
			dots: false,
			loop:true,
			smartSpeed: 1000,
			responsiveClass:true,
			responsive:{
				0:{
					items:1,
				},
				471:{
					items:2,
				},
				768:{
					items:2,
				},
				992:{
					items:3,
				},
				1200:{
					items:4,
				}
			}
		});
		//Our-Team Slider
		$('.our-team').owlCarousel({
			items: 4,
			nav: true,
			dots: false,
			loop:true,
			smartSpeed: 1000,
			responsiveClass:true,
			responsive:{
				0:{
					items:1,
				},
				420:{
					items:2,
				},
				768:{
					items:2,
				},
				992:{
					items:3,
				},
				1200:{
					items:4,
				}
			}
		});
		//Blog
		$('#blog').owlCarousel({
			items: 3,
			nav: true,
			dots: false,
			loop:true,
			smartSpeed: 1000,
			responsiveClass:true,
			responsive:{
				0:{
					items:1,
				},
				768:{
					items:2,
				},
				1200:{
					items:3,
				}
			}
		});
		//App-Screen
		$('.app-screen').owlCarousel({
			loop: true,
			autoplay: true,
			autoplayTimeout: 2500,
			smartSpeed: 500,
			dots: false,
			nav: false,
			items: 1,
			responsiveClass: true,
			responsive:{
				0:{
					items:1,
				}, 
			}
		});
		return false;
	}

	function scrolltop_arrow()
	{
		//Page Scrollup
		//When distance from top = 250px fade button in/out
		var scrollup = $('.scrollup');
		var headertag = $('header');
		var mainfix = $('.main');
		$(window).scroll(function(){
			if ($(this).scrollTop() > 0)
			{
				scrollup.fadeIn(300);
			}
			else
			{
				scrollup.fadeOut(300);
			}
			// Header-Fixed
			if ($(this).scrollTop() > 40)
			{
				headertag.addClass("header-fixed");
			}
			else
			{
				headertag.removeClass("header-fixed");
			}
			// Main-Fixed
			if ($(this).scrollTop() > 0)
			{
				mainfix.addClass("main-fixed");
			}
			else
			{
				mainfix.removeClass("main-fixed");
			}
		});
		//On click scroll to top of page t = 1000ms
		scrollup.on("click", function(){
			$("html, body").animate({ scrollTop: 0 }, 1000);
			return false;
		});
	}
	// Countdown-Clock
	function countdown_clock()
	{
		$('.countdown-clock').downCount({
			date: '11/30/2021 23:59:59',
			offset: +10
		}, 
		function () {
			//alert('done!'); Finish Time limit
			return false;
		});
	}
	// Faq_Tab
	function faq_tab()
	{
		$("#tabs li a").on("click", function(e){
			var title = $(e.currentTarget).attr("title");
			$("#tabs li a , .tab_content li div").removeClass("selected");
			$(".tab-"+title +", .items-"+title).addClass("selected");
			$("#items").attr("class","tab-"+title);
			return false;
		});
	}
	// Video_Popup
	function video_popup()
	{
		if ($('.popup-youtube').length > 0)
		{
			$('.popup-youtube').magnificPopup({
				disableOn: 700,
				type: 'iframe',
				mainClass: 'mfp-fade',
				removalDelay: 160,
				preloader: false,
				fixedContentPos: false
			});
		}
	}
	// Doughnut chart
	var inView = false;
	function isScrolledIntoView(elem)
	{
		if ($('#chartjs-doughnut').length > 0)
		{
			var docViewTop = $(window).scrollTop();
			var docViewBottom = docViewTop + $(window).height();

			var elemTop = $(elem).offset().top;
			var elemBottom = elemTop + $(elem).height();
			return ((elemTop <= docViewBottom));
		}
	}

	var chartexe = false;
	$(window).scroll(function() {
		if (isScrolledIntoView('#chartjs-doughnut') && chartexe==false)
		{
	  		chartexe = true;
	  		if (inView)
	  		{
	  			return;
	  		}
	  		inView = true;
	  		$(function() {
	  			new Chart(document.getElementById("chartjs-doughnut"), {
	  				type: 'doughnut',
	  				data: {
	  					datasets: [{
	  						data: [20.1, 28.6, 33.8, 45.5, 72.2],
	  						backgroundColor: ['#f3545d','#fdaf4b','#1d7af3','#34e4ef','#e80ae5'], 
	  						borderWidth:2,
	  						borderColor:'#1f0e9c',

	  					}],

	  					labels: [
	  					'Private/Pre Sale',
	  					'Public ICO',
	  					'Team & Advisor',
	  					'Marketing & General',
	  					'Bounty'
	  					]
	  				},
	  				options: {
	  					responsive: true, 
	  					maintainAspectRatio: false,
	  					legend: {
	  						display: false,
	  					},
	  					pieceLabel: {
	  						render: 'value',
	  						fontColor: '#3d28ca',
	  						fontSize: 14,
	  						fontWeight: 'bold',
	  					},
	  					layout: {
	  						padding: {
	  							left: 0,
	  							right: 0,
	  							top: 0,
	  							bottom: 0
	  						}
	  					},
	  					animation:{
	  						animateRotate: true,
	  						render: true,
	  						easing: 'linear',
	  						duration: 1000,
	  					}
	  				}
	  			});
	  		});
	  	}
	  	else
	  	{
	  		inView = false;  
	  	}
	});

	$(document).ready(function() {
		scrolltop_arrow ();
		owlcarousel_slider();  
		responsive_dropdown();
		faq_tab ();
		countdown_clock (); 
		video_popup (); 
	});
});

$( window ).on( "load", function() {
	// Animate loader off screen 
	$(".se-pre-con").fadeOut("slow");
	// Animation WOW Start
	var wow = new WOW(
	{
		boxClass:     'wow',      // animated element css class (default is wow)
		animateClass: 'animate__animated', // animation css class (default is animated)
		offset:       100,          // distance to the element when triggering the animation (default is 0)
		mobile:       true,       // trigger animations on mobile devices (default is true)
		live:         true,       // act on asynchronously loaded content (default is true)
		callback:     function(box) {
			// the callback is fired every time an animation is started
			// the argument that is passed in is the DOM node being animated
		},
		scrollContainer: null // optional scroll container selector, otherwise use window
	}
);
wow.init();
});