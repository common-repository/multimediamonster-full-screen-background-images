<?php
class mmm_fsbi_frontend_widget 
{
	static function frontend_init()  // FIXME
	{
		// COMPLETE SETTINS ARRAY
		$values										= 	array();
		$pages 										= 	mmm_fsbi_settings::settings_pages_tabs();
		foreach( $pages as $pages_id => $pages_name )
		{
			foreach( $pages_name['page_tabs'] as $tab_key => $tab_val)
			{
				$values_defaults_pp_pt				= 	mmm_fsbi_settings::default_values($pages_id.'_'.$tab_key);
				$values_defaults_val_only_pp_pt		= 	mmm_fsbi_settings::default_values($pages_id.'_'.$tab_key, 'vals_only');
				$values_db_pp_pt					= 	get_option( MMM_FSBI_PLUGIN_ID_SHORT.'_'.$pages_id, $values_defaults_val_only_pp_pt );
				$values_combined_pp_pt 				= 	wp_parse_args((array) $values_db_pp_pt, $values_defaults_val_only_pp_pt);
				
				foreach( $values_combined_pp_pt as $settings_key => $settings_val)
				{
					$values[$settings_key]			=	$settings_val;
				}
			}
		}
		
		$orderby									=	'menu_order';
		if (isset($values['random']) && $values['random'] == 1)
		{
			$orderby								=	'rand';
		}
		$args 										= 	array
														(
															'post_type' 				=> MMM_FSBI_PLUGIN_ID_SHORT,
															'orderby'          			=> $orderby,
															'order'           	 		=> 'ASC',
															'posts_per_page' 			=> -1//,
															//'update_post_term_cache' 	=> false, // don't retrieve post terms
															//'update_post_meta_cache' 	=> false, // don't retrieve post meta
														);
		$the_query 									= 	new WP_Query($args);
		if ($the_query->have_posts()) :
		//echo '<pre>';
		//print_r($the_query);
		//echo '</pre>';
		?>
        <script type="text/javascript">

			jQuery(function($)
			{
				$.supersized.themeVars =
				{
					// Internal Variables
					progress_delay			:	false,				// Delay after resize before resuming slideshow
					thumb_page 				: 	false,				// Thumbnail page
					thumb_interval 			: 	false,				// Thumbnail interval
					image_path				: 	"<?php echo MMM_FSBI_PLUGIN_URL; ?>/images/",
																
					// General Elements							
					play_button				:	'#pauseplay',		// Play/Pause button
					next_slide				:	'#nextslide',		// Next slide button
					prev_slide				:	'#prevslide',		// Prev slide button
					next_thumb				:	'#nextthumb',		// Next slide thumb button
					prev_thumb				:	'#prevthumb',		// Prev slide thumb button
					
					slide_caption			:	'#slidecaption',	// Slide caption
					slide_current			:	'.slidenumber',		// Current slide number
					slide_total				:	'.totalslides',		// Total Slides
					slide_list				:	'#slide-list',		// Slide jump list							
					
					thumb_tray				:	'#thumb-tray',		// Thumbnail tray
					thumb_list				:	'#thumb-list',		// Thumbnail list
					thumb_forward			:	'#thumb-forward',	// Cycles forward through thumbnail list
					thumb_back				:	'#thumb-back',		// Cycles backwards through thumbnail list
					tray_arrow				:	'#tray-arrow',		// Thumbnail tray button arrow
					tray_button				:	'#tray-button',		// Thumbnail tray button
					
					progress_bar			:	'#progress-bar'		// Progress bar
																
				};
				$.supersized.themeOptions = 
				{					
					progress_bar			:	1,		// Timer for each slide											
					mouse_scrub				:	0		// Thumbnails move with mouse
				};	
				$.supersized({
					slideshow               :   1,												// Slideshow on/off
					autoplay				:	<?php echo $values['autoplay']; ?>,				// Slideshow starts playing automatically
					start_slide             :   1,												// Start slide (0 is random)
					stop_loop				:	0,												// Pauses slideshow on last slide
					random					: 	<?php echo $values['random']; ?>,				// Randomize slide order (Ignores start slide)
					transition              :   "<?php echo $values['transition']; ?>", 		// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
					new_window				:	1,												// Image links open in new window/tab
					pause_hover             :   <?php echo $values['pause_hover']; ?>,			// Pause slideshow on hover
					keyboard_nav            :   <?php echo $values['keyboard_nav']; ?>,			// Keyboard navigation on/off
					performance				:	1,												// 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
					image_protect			:	1,												// Disables image dragging and right click with Javascript
					
					// needed ifs
					<?php if ($values['slide_interval']) 		{ ?>slide_interval          :   <?php echo $values['slide_interval']; ?>,<?php } ?>			// Length between transitions
					<?php if ($values['transition_speed']) 		{ ?>transition_speed		:	<?php echo $values['transition_speed']; ?>,<?php } ?>			// Speed of transition
										   
					// Size & Position						   
					fit_portrait         	:   0,												// Portrait images will not exceed browser height
					fit_landscape			:   0,												// Landscape images will not exceed browser width
															   
					// Components							
					slide_links				:	'name',										// Individual links for each slide (Options: false, 'num', 'name', 'blank')
					thumb_links				:	1,												// Individual thumb links for each slide
					thumbnail_navigation    :   0,												// Thumbnail navigation,
												
					// Theme Options			   
					progress_bar			:	1,												// Timer for each slide							
					mouse_scrub				:	0,
					slides 					:	[<?php 
												$slide_array = array();
												if ($the_query->have_posts()) :
										
													while ($the_query->have_posts()) : $the_query->the_post();	
														if (has_post_thumbnail())
														{
															//$image_url 				= wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full_screen_background');
															$image_url 				= 	wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'mmm_full_screen_background');
															$image_thumbnail 		= 	wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');
															$subtitle 				= 	get_post_meta(get_the_ID(), '_mmm_fsbi_subtitle', true);
															$link 					= 	get_post_meta(get_the_ID(), '_mmm_fsbi_link', true);
															
															$content_block			= 	'';
															$content_block			.= 	'<div class="slide-content-container">';
																$content_block			.= 	'<div class="slide-content-text">';
																	$content_block			.= 	'<div class="slide-content-innertext">';
																		// target
																		$target 				= '';
																		$url_find 				= strpos(get_post_meta(get_the_ID(), '_mmm_fsbi_link', true),get_site_url());
																		if ($url_find === false)
																		{
																			$target 			= ' target="_blank"';
																		}
																		if (get_post_meta(get_the_ID(), '_mmm_fsbi_link', true))
																		{
																			$content_block			.= 	'<a href="'.get_post_meta(get_the_ID(), '_mmm_fsbi_link', true).'"'.$target.'>';
																		}
																			$content_block			.= 	'<h1>'.get_the_title().'</h1>';
																			$content_block			.= 	'<h2>'.get_post_meta(get_the_ID(), '_mmm_fsbi_subtitle', true).'</h2>';
																			$get_the_content		= 	str_replace(array("\r\n", "\r"), "\n", get_the_content());
																			$lines 					= 	explode("\n", $get_the_content);
																				$new_lines			= 	array();																				
																				foreach ($lines as $i => $line)
																				{
																					if(!empty($line))
																						$new_lines[] = ' '.trim($line);
																				}
																			$content_block			.= 	implode($new_lines);
																		if (get_post_meta(get_the_ID(), '_mmm_fsbi_link', true))
																		{
																			$content_block			.= 	'</a>';
																		}
																	$content_block			.= 	'</div>';
																$content_block			.= 	'</div>';
															$content_block			.= 	'</div>';
															$slide_array[] 			.= 	'{title : "' . str_replace('"', "'", $content_block) .'",image : "' . $image_url[0] .'", thumbnail: "' . $image_thumbnail[0] .'", content_block : "' . str_replace('"', "'", $content_block) . '"}';
														}

													endwhile;
												endif;
												echo join(',', $slide_array);
												?>]		
				});
			});
		</script>
        <?php
		wp_reset_query();
		endif;
	}
	static function frontend_footer()
	{
		// COMPLETE SETTINS ARRAY
		$values										= 	array();
		$pages 										= 	mmm_fsbi_settings::settings_pages_tabs();
		foreach( $pages as $pages_id => $pages_name )
		{
			foreach( $pages_name['page_tabs'] as $tab_key => $tab_val)
			{
				$values_defaults_pp_pt				= 	mmm_fsbi_settings::default_values($pages_id.'_'.$tab_key);
				$values_defaults_val_only_pp_pt		= 	mmm_fsbi_settings::default_values($pages_id.'_'.$tab_key, 'vals_only');
				$values_db_pp_pt					= 	get_option( MMM_FSBI_PLUGIN_ID_SHORT.'_'.$pages_id, $values_defaults_val_only_pp_pt );
				$values_combined_pp_pt 				= 	wp_parse_args((array) $values_db_pp_pt, $values_defaults_val_only_pp_pt);
				
				foreach( $values_combined_pp_pt as $settings_key => $settings_val)
				{
					$values[$settings_key]			=	$settings_val;
				}
			}
		}
		$records_found 										= 	mmm_fsbi_frontend_functions::has_records('num');
		
		if ($values['controls_next_prev'] == 1 || $values['controls_thumbs'] == 1) :
			?>
            <div id="controls-container" class="controls-container">
                <?php
	            if ($records_found == 1) :
				?>
                <div id="controls-wrapper" class="load-item">
                    <div id="controls">				
                        <div id="slidecaption"></div>	
                    </div>
                </div>
                <?php
                elseif ($records_found > 1) :
					
					if ($values['controls_next_prev'] == 1) :
						?>
                        <div id="prevthumb"></div>
                        <div id="nextthumb"></div>
                        
                        <a id="prevslide" class="load-item"></a>
                        <a id="nextslide" class="load-item"></a>
                        
                        <?php
					endif;
					
					if ($values['controls_thumbs'] == 1) :
						?>
                        <div id="thumb-tray" class="load-item">
                            <div id="thumb-back"></div>
                            <div id="thumb-forward"></div>
                        </div>
                        
                        <div id="progress-back" class="load-item">
                            <div id="progress-bar"></div>
                        </div>
                        
                        <div id="controls-wrapper" class="load-item">
                            <div id="controls">					
                                <a id="play-button"><img id="pauseplay" src="<?php echo MMM_FSBI_PLUGIN_URL; ?>/images/pause.png"/></a>                    
                                <div id="slidecounter">
                                    <span class="slidenumber"></span> / <span class="totalslides"></span>
                                </div>
                                <div id="slidecaption"></div>
                                <ul id="slide-list"></ul>	
                                <a id="tray-button"><img id="tray-arrow" src="<?php echo MMM_FSBI_PLUGIN_URL; ?>/images/button-tray-up.png"/></a>					
                            </div>
                        </div>
                        <?php
					endif;
					
				endif;
                ?>
            </div>
            <?php
		endif;
	}
}
?>