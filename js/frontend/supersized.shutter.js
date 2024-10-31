/*

	Supersized - Fullscreen Slideshow jQuery Plugin for Wordpress
	Version : 3.2.7
	Site	: www.multimediamonster.nl
	
	Author	: Renske van der Heijden
	Company : MultiMediaMonster (www.multimediamonster.nl)
	License : MIT License / GPL License
	
*/

(function($)
{
	
 	// ---------------------------------------------------------------------------------------------------------------------
	// 	LOAD THE THEME
	//	@since				MultiMediaMonster 1.1
	/*
		Load the theme
	*/
	// ---------------------------------------------------------------------------------------------------------------------
	
	theme = 
	{
	 	
		// ---------------------------------------------------------------------------------------------------------------------
		// 	INITIAL PLACEMENT
		//	@since				MultiMediaMonster 1.1
		/*
			Initial Placement
		*/
		// ---------------------------------------------------------------------------------------------------------------------
	 	
			_init : function()
			{
				// Center Slide Links
				if (api.options.slide_links) $(vars.slide_list).css('margin-left', -$(vars.slide_list).width()/2);
				
				// Start progressbar if autoplay enabled
				if (api.options.autoplay == 1)
				{
					if (api.options.progress_bar) theme.progressBar();
				}
				else
				{
					if ($(vars.play_button).attr('src')) $(vars.play_button).attr("src", vars.image_path + "play.png");	// If pause play button is image, swap src
					if (api.options.progress_bar) $(vars.progress_bar).stop().css({left : -$(window).width()});	//  Place progress bar
				}			
				
				// ---------------------------------------------------------------------------------------------------------------------
				// 	Thumbnail Tray
				// ---------------------------------------------------------------------------------------------------------------------
				
					// Hide tray off screen
					$(vars.thumb_tray).css({bottom : -$(vars.thumb_tray).height()});
					
					// Thumbnail Tray Toggle
					$(vars.tray_button).toggle(function()
					{
						$(vars.thumb_tray).stop().animate({bottom : 0, avoidTransforms : true}, 300 );
						if ($(vars.tray_arrow).attr('src')) $(vars.tray_arrow).attr("src", vars.image_path + "button-tray-down.png");
						return false;
					},
					function()
					{
						$(vars.thumb_tray).stop().animate({bottom : -$(vars.thumb_tray).height(), avoidTransforms : true}, 300 );
						if ($(vars.tray_arrow).attr('src')) $(vars.tray_arrow).attr("src", vars.image_path + "button-tray-up.png");
						return false;
					});
					
					// Make thumb tray proper size
					$(vars.thumb_list).width($('> li', vars.thumb_list).length * $('> li', vars.thumb_list).outerWidth(true));	//Adjust to true width of thumb markers
					
					// Display total slides
					if ($(vars.slide_total).length)
					{
						$(vars.slide_total).html(api.options.slides.length);
					}
				
				// ---------------------------------------------------------------------------------------------------------------------
				// 	Thumbnail Tray Navigation
				// ---------------------------------------------------------------------------------------------------------------------
			
					if (api.options.thumb_links)
					{
						//Hide thumb arrows if not needed
						if ($(vars.thumb_list).width() <= $(vars.thumb_tray).width())
						{
							$(vars.thumb_back +','+vars.thumb_forward).fadeOut(0);
						}
						
						// Thumb Intervals
						vars.thumb_interval = Math.floor($(vars.thumb_tray).width() / $('> li', vars.thumb_list).outerWidth(true)) * $('> li', vars.thumb_list).outerWidth(true);
						vars.thumb_page = 0;
						
						// Cycle thumbs forward
						$(vars.thumb_forward).click(function()
						{
							if (vars.thumb_page - vars.thumb_interval <= -$(vars.thumb_list).width())
							{
								vars.thumb_page = 0;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}
							else
							{
								vars.thumb_page = vars.thumb_page - vars.thumb_interval;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}
						});
						
						// Cycle thumbs backwards
						$(vars.thumb_back).click(function()
						{
							if (vars.thumb_page + vars.thumb_interval > 0)
							{
								vars.thumb_page = Math.floor($(vars.thumb_list).width() / vars.thumb_interval) * -vars.thumb_interval;
								if ($(vars.thumb_list).width() <= -vars.thumb_page) vars.thumb_page = vars.thumb_page + vars.thumb_interval;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}
							else
							{
								vars.thumb_page = vars.thumb_page + vars.thumb_interval;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}
						});
						
					}
				
				// ---------------------------------------------------------------------------------------------------------------------
				// 	Navigation Items
				// ---------------------------------------------------------------------------------------------------------------------
				
					$(vars.next_slide).click(function()
					{
						api.nextSlide();
					});
				
					$(vars.prev_slide).click(function() 
					{
						api.prevSlide();
					});
				
					// Full Opacity on Hover
					if(jQuery.support.opacity)
					{
						$(vars.prev_slide +','+vars.next_slide).mouseover(function()
						{
						   $(this).stop().animate({opacity:1},100);
						}).mouseout(function(){
						   $(this).stop().animate({opacity:0.6},100);
						});
					}
				
					if (api.options.thumbnail_navigation)
					{
						// Next thumbnail clicked
						$(vars.next_thumb).click(function()
						{
							api.nextSlide();
						});
						// Previous thumbnail clicked
						$(vars.prev_thumb).click(function()
						{
							api.prevSlide();
						});
					}
				
					$(vars.play_button).click(function()
					{
						api.playToggle();						    
					});
					
				// ---------------------------------------------------------------------------------------------------------------------
				// 	Thumbnail Mouse Scrub
				// ---------------------------------------------------------------------------------------------------------------------
				
					if (api.options.mouse_scrub)
					{
						$(vars.thumb_tray).mousemove(function(e)
						{
							var containerWidth = $(vars.thumb_tray).width(),
								listWidth = $(vars.thumb_list).width();
							if (listWidth > containerWidth)
							{
								var mousePos = 1,
									diff = e.pageX - mousePos;
								if (diff > 10 || diff < -10)
								{ 
									mousePos = e.pageX; 
									newX = (containerWidth - listWidth) * (e.pageX/containerWidth);
									diff = parseInt(Math.abs(parseInt($(vars.thumb_list).css('left'))-newX )).toFixed(0);
									$(vars.thumb_list).stop().animate({'left':newX}, {duration:diff*3, easing:'easeOutExpo'});
								}
							}
						});
					}
				
				// ---------------------------------------------------------------------------------------------------------------------
				// 	Window Resize
				// ---------------------------------------------------------------------------------------------------------------------
				
					$(window).resize(function()
					{
						// Delay progress bar on resize
						if (api.options.progress_bar && !vars.in_animation)
						{
							if (vars.slideshow_interval) clearInterval(vars.slideshow_interval);
							if (api.options.slides.length - 1 > 0) clearInterval(vars.slideshow_interval);
							
							$(vars.progress_bar).stop().css({left : -$(window).width()});
							
							if (!vars.progressDelay && api.options.slideshow)
							{
								// Delay slideshow from resuming so Chrome can refocus images
								vars.progressDelay = setTimeout(function()
								{
									if (!vars.is_paused)
									{
										theme.progressBar();
										vars.slideshow_interval = setInterval(api.nextSlide, api.options.slide_interval);
									}
									vars.progressDelay = false;
								}, 1000);
							}
						}
						// Thumb Links
						if (api.options.thumb_links && vars.thumb_tray.length)
						{
							// Update Thumb Interval & Page
							vars.thumb_page = 0;	
							vars.thumb_interval = Math.floor($(vars.thumb_tray).width() / $('> li', vars.thumb_list).outerWidth(true)) * $('> li', vars.thumb_list).outerWidth(true);
							
							// Adjust thumbnail markers
							if ($(vars.thumb_list).width() > $(vars.thumb_tray).width())
							{
								$(vars.thumb_back +','+vars.thumb_forward).fadeIn('fast');
								$(vars.thumb_list).stop().animate({'left':0}, 200);
							}
							else
							{
								$(vars.thumb_back +','+vars.thumb_forward).fadeOut('fast');
							}
							
						}
					});	
			},
	 	
		// ---------------------------------------------------------------------------------------------------------------------
		// 	GO TO SLIDE
		//	@since				MultiMediaMonster 1.1
		/*
			Go To Slide
		*/
		// ---------------------------------------------------------------------------------------------------------------------
	 	
			goTo : function()
			{
				if (api.options.progress_bar && !vars.is_paused){
					$(vars.progress_bar).stop().css({left : -$(window).width()});
					theme.progressBar();
				}
			},
	 	
		// ---------------------------------------------------------------------------------------------------------------------
		// 	PLAY & PAUSE TOGGLE
		//	@since				MultiMediaMonster 1.1
		/*
			Play & Pause Toggle
		*/
		// ---------------------------------------------------------------------------------------------------------------------
	 	
			playToggle : function(state)
			{
				if (state =='play')
				{
					// If image, swap to pause
					if ($(vars.play_button).attr('src')) $(vars.play_button).attr("src", vars.image_path + "pause.png");
					if (api.options.progress_bar && !vars.is_paused) theme.progressBar();
				}
				else if (state == 'pause')
				{
					// If image, swap to play
					if ($(vars.play_button).attr('src')) $(vars.play_button).attr("src", vars.image_path + "play.png");
					if (api.options.progress_bar && vars.is_paused)$(vars.progress_bar).stop().css({left : -$(window).width()});
				}
				
			},
	 	
		// ---------------------------------------------------------------------------------------------------------------------
		// 	BEFORE SLIDE TRANSITION
		//	@since				MultiMediaMonster 1.1
		/*
			Before Slide Transition
		*/
		// ---------------------------------------------------------------------------------------------------------------------
	 	
			beforeAnimation : function(direction)
			{
				if (api.options.progress_bar && !vars.is_paused) $(vars.progress_bar).stop().css({left : -$(window).width()});
				
				// ---------------------------------------------------------------------------------------------------------------------
				// 	Update Fields
				// ---------------------------------------------------------------------------------------------------------------------		
				
					// Update slide caption
					if ($(vars.slide_caption).length)
					{
						(api.getField('title')) ? $(vars.slide_caption).html(api.getField('title')) : $(vars.slide_caption).html('');
					}
					// Update slide number
					if (vars.slide_current.length)
					{
						$(vars.slide_current).html(vars.current_slide + 1);
					}
				
				if (api.options.thumb_links) // Highlight current thumbnail and adjust row position
				{
					$('.current-thumb').removeClass('current-thumb');
					$('li', vars.thumb_list).eq(vars.current_slide).addClass('current-thumb');
					
					if ($(vars.thumb_list).width() > $(vars.thumb_tray).width()) // If thumb out of view
					{
						if (direction == 'next') // If next slide direction
						{
							if (vars.current_slide == 0)
							{
								vars.thumb_page = 0;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}
							else if ($('.current-thumb').offset().left - $(vars.thumb_tray).offset().left >= vars.thumb_interval)
							{
								vars.thumb_page = vars.thumb_page - vars.thumb_interval;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}					
						}
						else if(direction == 'prev') // If previous slide direction
						{
							if (vars.current_slide == api.options.slides.length - 1)
							{
								vars.thumb_page = Math.floor($(vars.thumb_list).width() / vars.thumb_interval) * -vars.thumb_interval;
								if ($(vars.thumb_list).width() <= -vars.thumb_page) vars.thumb_page = vars.thumb_page + vars.thumb_interval;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}
							 else if ($('.current-thumb').offset().left - $(vars.thumb_tray).offset().left < 0)
							 {
								if (vars.thumb_page + vars.thumb_interval > 0) return false;
								vars.thumb_page = vars.thumb_page + vars.thumb_interval;
								$(vars.thumb_list).stop().animate({'left': vars.thumb_page}, {duration:500, easing:'easeOutExpo'});
							}
						}
					}
				}
			},
	 	
		// ---------------------------------------------------------------------------------------------------------------------
		// 	AFTER SLIDE TRANSITION
		//	@since				MultiMediaMonster 1.1
		/*
			After Slide Transition
		*/
		// ---------------------------------------------------------------------------------------------------------------------
	 	
			afterAnimation : function()
			{
				if (api.options.progress_bar && !vars.is_paused) theme.progressBar();	//  Start progress bar
			},
	 	
		// ---------------------------------------------------------------------------------------------------------------------
		// 	PROGRESS BAR
		//	@since				MultiMediaMonster 1.1
		/*
			Progress Bar
		*/
		// ---------------------------------------------------------------------------------------------------------------------
	 	
			progressBar : function()
			{
				$(vars.progress_bar).stop().css({left : -$(window).width()}).animate({ left:0 }, api.options.slide_interval);
			}
	 
	 };
	 
})(jQuery);