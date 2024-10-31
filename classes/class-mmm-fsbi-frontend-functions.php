<?php
class mmm_fsbi_frontend_functions 
{
	// ---------------------------------------------------------------------------------------------------------------------
	// 	FUNCTIONS
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------

		static function has_records($return = '')
		{
			$args 										= 	array
															(
																'post_type' 				=> MMM_FSBI_PLUGIN_ID_SHORT,
																'posts_per_page' 			=> -1,
																'update_post_term_cache' 	=> false, // don't retrieve post terms
																'update_post_meta_cache' 	=> false, // don't retrieve post meta
															);
			$the_query 									= 	new WP_Query($args);
			if ($return == 'num' && $the_query->have_posts()) :
				$return = $the_query->post_count;
			elseif ($the_query->have_posts()) :
				$return = true;
			else :
				$return = false;
			endif;
			return ($return);
		}
}