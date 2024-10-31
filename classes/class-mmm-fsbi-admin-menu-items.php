<?php
class mmm_fsbi_admin_menu_items 
{

	// ---------------------------------------------------------------------------------------------------------------------
	// 	THE ADMIN MENU ITEMS
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
		
		static function metaboxes_to_add()
		{
			$metaboxes_to_add				= 	array();
			$metaboxes_to_add[]				= 	array(
													'id'		=>	'subtitle',
													'title'		=> 	'Subtitle'
												);
			$metaboxes_to_add[]				= 	array(
													'id'		=>	'link',
													'title'		=> 	'Link title and subtitle to ... (http://...)'
												);
			return ($metaboxes_to_add);
		}
		static function add_metaboxes()
		{
			$metaboxes_to_add 				= 	self::metaboxes_to_add();
			foreach($metaboxes_to_add as $metaboxes_to_add_key => $metaboxes_to_add_array)
			{
				add_meta_box(
					MMM_FSBI_PLUGIN_ID_SHORT.'_metabox_'.$metaboxes_to_add_array['id'], 
					$metaboxes_to_add_array['title'], 
					MMM_FSBI_PLUGIN_ID_SHORT.'_admin_menu_items::add_metafields', 
					MMM_FSBI_PLUGIN_ID_SHORT,
					'normal', 
					'high',
					array( 'metabox_id' => $metaboxes_to_add_array['id'])
				);
			}
		}
		static function add_metafields($post, $metabox) 
		{
			$metabox_id 					= 	MMM_FSBI_PLUGIN_ID_SHORT.'_'.$metabox['args']['metabox_id'];
			$value 							= 	get_post_meta($post->ID, '_'.$metabox_id, true);
			echo '<input type="hidden" name="'.$metabox_id.'meta_noncename" id="'.$metabox_id.'meta_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
			echo '<input type="text" name="_'.$metabox_id.'" value="' . $value  . '" class="widefat" />';
		}
		static function metaboxes_save($post_id, $post) 
		{
			$metaboxes_to_add 				= 	self::metaboxes_to_add();
			foreach($metaboxes_to_add as $metaboxes_to_add_key => $metaboxes_to_add_array)
			{
				$metabox_id 				= 	MMM_FSBI_PLUGIN_ID_SHORT.'_'.$metaboxes_to_add_array['id'];
				if ( !wp_verify_nonce( $_POST[$metabox_id.'meta_noncename'], plugin_basename(__FILE__) ))
				{
					return $post->ID;
				}
				if ( !current_user_can( 'edit_post', $post->ID ))
					return $post->ID;
				
				$meta_values['_'.$metabox_id] 	= 	$_POST['_'.$metabox_id];				
				foreach ($meta_values as $key => $value)
				{ 
					if( $post->post_type == 'revision' ) return; 
					$value 						=	implode(',', (array)$value); 
					if(get_post_meta($post->ID, $key, FALSE))
					{ 
						update_post_meta($post->ID, $key, $value);
					}
					else 
					{ 
						add_post_meta($post->ID, $key, $value);
					}
					if(!$value) delete_post_meta($post->ID, $key); 
				}
			}
		}
		static function add_admin_menu_items ()
		{
			$labels = array(
				'name' 						=> 	MMM_FSBI_PLUGIN_NAME,
				'singular_name' 			=> 	_x('Backgrounds', 'post type singular name', MMM_FSBI_PLUGIN_TRANSLATE),
				'new_item' 					=> 	__('New Background', MMM_FSBI_PLUGIN_TRANSLATE),
				'add_new'					=> 	__('New Background', MMM_FSBI_PLUGIN_TRANSLATE),
				'add_new_item' 				=> 	__('Add New Background', MMM_FSBI_PLUGIN_TRANSLATE),
				'view_item' 				=> 	__('View Background', MMM_FSBI_PLUGIN_TRANSLATE),
				'edit_item' 				=> 	__('Edit Background', MMM_FSBI_PLUGIN_TRANSLATE),
				'search_items' 				=> 	__('Search Backgrounds', MMM_FSBI_PLUGIN_TRANSLATE),
				'not_found' 				=> 	__('No Backgrounds found', MMM_FSBI_PLUGIN_TRANSLATE),
				'not_found_in_trash' 		=> 	__('No Backgrounds found in the trash', MMM_FSBI_PLUGIN_TRANSLATE),
				'parent_item_colon'			=> 	__('Parent Background:', MMM_FSBI_PLUGIN_TRANSLATE)
			);
		
			$args = array(
				'labels' 					=> 	$labels,
				'public'					=> 	false,
				'publicly_queryable'		=> 	true,
				'show_ui' 					=> 	true,
				'singular_label' 			=> 	__('Backgrounds', MMM_FSBI_PLUGIN_TRANSLATE),
				'query_var' 				=> 	true,
				'rewrite' 					=> 	true,
				'capability_type' 			=> 	'post',
				'hierarchical' 				=> 	false,
				'has_archive' 				=> 	false,
				'menu_position' 			=> 	102,
				'register_meta_box_cb' 		=> 	MMM_FSBI_PLUGIN_ID_SHORT.'_admin_menu_items::add_metaboxes',
				'supports' 					=> 	array
												(
													'title',
													'editor',
													'thumbnail'
												),
				'menu_icon'					=> 	MMM_FSBI_PLUGIN_URL.'/images/admin/menu-icon.png'
			);
			register_post_type(MMM_FSBI_PLUGIN_ID_SHORT, $args);
		}	
		
	// ---------------------------------------------------------------------------------------------------------------------
	// 	THE ADMIN MENU ITEMS (with changing the first name
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------

		static function add_admin_menu_subitems ()
		{
			$pages 							= 	mmm_fsbi_settings::settings_pages_tabs();
			foreach( $pages as $pages_id => $pages_name )
			{
				$pages_id_minus 			= 	str_replace('_', '-', $pages_id);
				add_submenu_page('edit.php?post_type='.MMM_FSBI_PLUGIN_ID_SHORT, '', $pages_name['page_title'], 'manage_options', MMM_FSBI_PLUGIN_ID_LONG_MINUS.'-'.$pages_id_minus, MMM_FSBI_PLUGIN_ID_SHORT.'_admin_pages::settings_pages');
			}
			global $submenu;
			if (isset($submenu['edit.php?post_type='.MMM_FSBI_PLUGIN_ID_SHORT]))
			{
				$submenu['edit.php?post_type='.MMM_FSBI_PLUGIN_ID_SHORT][5][0] = __('All backgrounds', MMM_FSBI_PLUGIN_TRANSLATE);
			}
		}
		
	// ---------------------------------------------------------------------------------------------------------------------
	// 	ADD SETTINGS PAGE TO PLUGIN PAGE
	// 	@since									MultiMediaMonster
	// ---------------------------------------------------------------------------------------------------------------------
		
		static function add_plugin_settings_link($actions, $file) 
		{
			if(false !== strpos($file, MMM_FSBI_PLUGIN_ID_LONG_MINUS))
			{
			 	$actions['settings']						= 	'<a href="edit.php?post_type='.MMM_FSBI_PLUGIN_ID_SHORT.'&page='.MMM_FSBI_PLUGIN_ID_LONG_MINUS.'-settings-general">'.__('Settings general', MMM_FSBI_PLUGIN_TRANSLATE).'</a>';
			}
			return $actions; 
		}
}