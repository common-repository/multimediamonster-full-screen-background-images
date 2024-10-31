<?php
class mmm_fsbi_register 
{
	
	// ---------------------------------------------------------------------------------------------------------------------
	// 	WHEN YOU ACTIVATE THE PLUGIN
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
	
		static function activate() 
		{
			add_option( 'activated_'.MMM_FSBI_PLUGIN_ID_LONG, 'slug-'.MMM_FSBI_PLUGIN_ID_LONG_MINUS );
		}
		static function load_this_plugin()
		{
			if ( is_admin() && get_option( 'activated_'.MMM_FSBI_PLUGIN_ID_LONG ) == 'slug-'.MMM_FSBI_PLUGIN_ID_LONG_MINUS )
			{
				delete_option( 'activated_'.MMM_FSBI_PLUGIN_ID_LONG );
			}
			if (function_exists('add_theme_support')) 
			{
				add_theme_support('post-thumbnails');
				add_image_size('mmm_full_screen_background', 2000, 1400, false);
			}
		}
		
	// ---------------------------------------------------------------------------------------------------------------------
	// 	WHEN YOU DEACTIVATE THE PLUGIN
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
		
		static function deactivate() 
		{
		}
		
	// ---------------------------------------------------------------------------------------------------------------------
	// 	WHEN YOU UNINSTALL THE PLUGIN
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
		
		static function uninstall() 
		{
			$pages 										= 	mmm_fsbi_settings::settings_pages_tabs();
			foreach( $pages as $pages_id => $pages_name )
			{
				delete_option( MMM_FSBI_PLUGIN_ID_SHORT.'_'.$pages_id );
			}
		}
	
	// ---------------------------------------------------------------------------------------------------------------------
	// 	LOADING THE TEXTDOMAIN
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
	
		static function plugin_load_textdomain() 
		{
			load_plugin_textdomain( 'mmm-fsbi-translated', false, MMM_FSBI_PLUGIN_TEXTDOMAIN ); 
		}
		
	// ---------------------------------------------------------------------------------------------------------------------
	// 	FRONTEND SCRIPTS
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
		
		static function frontend_scripts()
		{
			$records_found 										= 	mmm_fsbi_frontend_functions::has_records();
			if ($records_found == true)
			{
				wp_enqueue_script('jquery');
		
				wp_register_script('jquery-easing', MMM_FSBI_PLUGIN_URL . '/js/frontend/jquery.easing'.MMM_FSBI_PLUGIN_MINIFY.'.js');
				wp_enqueue_script('jquery-easing');
		
				wp_register_script('supersized', MMM_FSBI_PLUGIN_URL . '/js/frontend/supersized.3.2.7'.MMM_FSBI_PLUGIN_MINIFY.'.js');//.min
				wp_enqueue_script('supersized');
		
				wp_register_script('supersized-shutter', MMM_FSBI_PLUGIN_URL . '/js/frontend/supersized.shutter'.MMM_FSBI_PLUGIN_MINIFY.'.js');
				wp_enqueue_script('supersized-shutter');
			}
		}

	// ---------------------------------------------------------------------------------------------------------------------
	// 	FRONTEND STYLES
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------

		static function frontend_styles() 
		{
			$records_found 										= 	mmm_fsbi_frontend_functions::has_records();
			if ($records_found == true)
			{
				wp_register_style('supersized', MMM_FSBI_PLUGIN_URL . '/css/frontend/supersized'.MMM_FSBI_PLUGIN_MINIFY.'.css');
				wp_enqueue_style('supersized');	
				wp_register_style('supersized-shutter', MMM_FSBI_PLUGIN_URL . '/css/frontend/supersized.shutter'.MMM_FSBI_PLUGIN_MINIFY.'.css');
				wp_enqueue_style('supersized-shutter');
			}
		}
		
	// ---------------------------------------------------------------------------------------------------------------------
	// 	ADMIN STYLES
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------

		static function admin_styles() 
		{
			wp_enqueue_style( MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-admin-styles', MMM_FSBI_PLUGIN_URL . '/css/admin/style.css', array(), MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-admin-styles' );
			wp_enqueue_style( MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-admin-styles-copyright', MMM_FSBI_PLUGIN_URL . '/css/admin/style-copyright.css', array(), MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-admin-styles-copyright' );
		}
		
	// ---------------------------------------------------------------------------------------------------------------------
	// 	ADMIN SCRIPTS
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
		
		static function admin_scripts() 
		{
			// ---------------------------------------------------------------------------------------------------------------------
			// 	ADD SOME VARIABLES TO JAVASCRIPT
			// ---------------------------------------------------------------------------------------------------------------------
				
				$fsbi_js_vars																		=	MMM_FSBI_PLUGIN_ID_SHORT."_js_variables_array"; 
				${$fsbi_js_vars}																	=	array();
				${$fsbi_js_vars}[MMM_FSBI_PLUGIN_ID_SHORT.'_reset_confirm_question'] 				= 	__('Are you sure you want to reset the plugin? This is irreversible.', MMM_FSBI_PLUGIN_TRANSLATE); 
				
			// ---------------------------------------------------------------------------------------------------------------------
			// 	END ADD SOME VARIABLES TO JAVASCRIPT
			// ---------------------------------------------------------------------------------------------------------------------

			// load admin scripts
			wp_register_script( MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-scripts-header', MMM_FSBI_PLUGIN_URL . '/js/admin/scripts-header.js', array( 'jquery' ), MMM_FSBI_PLUGIN_ID_SHORT_MINUS, false );
			wp_enqueue_script( MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-scripts-header' );
			wp_localize_script( MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-scripts-header', $fsbi_js_vars, ${$fsbi_js_vars} );
			
			wp_register_script( MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-scripts-footer', MMM_FSBI_PLUGIN_URL . '/js/admin/scripts-footer.js', array( 'jquery' ), MMM_FSBI_PLUGIN_ID_SHORT_MINUS, true );
			wp_enqueue_script( MMM_FSBI_PLUGIN_ID_SHORT_MINUS.'-scripts-footer' );
		}
		
}