<?php 

class cpac_columns_posttype extends cpac_columns
{
	function __construct( $post_type )
	{
		$this->type = $post_type;
	}
	
	/**
	 * Custom posts columns
	 *
	 * @since     1.0
	 */
	function get_custom_columns() 
	{
		$custom_columns = array(
			'column-featured_image' => array(
				'label'	=> __('Featured Image', CPAC_TEXTDOMAIN)
			),
			'column-excerpt' => array(
				'label'	=> __('Excerpt', CPAC_TEXTDOMAIN)
			),
			'column-order' => array(
				'label'	=> __('Page Order', CPAC_TEXTDOMAIN)
			),
			'column-post_formats' => array(
				'label'	=> __('Post Format', CPAC_TEXTDOMAIN)
			),
			'column-postid' => array(
				'label'	=> __('ID', CPAC_TEXTDOMAIN)
			),
			'column-page-slug' => array(
				'label'	=> __('Slug', CPAC_TEXTDOMAIN)
			),
			'column-attachment' => array(
				'label'	=> __('Attachment', CPAC_TEXTDOMAIN)
			),
			'column-attachment-count' => array(
				'label'	=> __('No. of Attachments', CPAC_TEXTDOMAIN)
			),
			'column-roles' => array(
				'label'	=> __('Roles', CPAC_TEXTDOMAIN)
			),
			'column-status' => array(
				'label'	=> __('Status', CPAC_TEXTDOMAIN)
			),
			'column-comment-status' => array(
				'label'	=> __('Comment status', CPAC_TEXTDOMAIN)
			),
			'column-ping-status' => array(
				'label'	=> __('Ping status', CPAC_TEXTDOMAIN)
			),
			'column-actions' => array(
				'label'	=> __('Actions', CPAC_TEXTDOMAIN),
				'options'	=> array(
					'sortorder'	=> false
				)
			),
			'column-modified' => array(
				'label'	=> __('Last modified', CPAC_TEXTDOMAIN)
			),
			'column-comment-count' => array(
				'label'	=> __('Comment count', CPAC_TEXTDOMAIN)
			),
			'column-author-name' => array(
				'label'			=> __('Display Author As', CPAC_TEXTDOMAIN),
				'display_as'	=> ''
			),
			'column-before-moretag' => array(
				'label'	=> __('Before More Tag', CPAC_TEXTDOMAIN)				
			)
		);
		
		// Word count support
		if ( post_type_supports( $this->type, 'editor') ) {
			$custom_columns['column-word-count'] = array(
				'label'	=> __('Word count', CPAC_TEXTDOMAIN)
			);
		}
		
		// Sticky support
		if ( $this->type == 'post' ) {		
			$custom_columns['column-sticky'] = array(
				'label'			=> __('Sticky', CPAC_TEXTDOMAIN)
			);
		}
		
		// Order support
		if ( post_type_supports( $this->type, 'page-attributes') ) {
			$custom_columns['column-order'] = array(
				'label'			=> __('Page Order', CPAC_TEXTDOMAIN),				
				'options'		=> array(
					'type_label' 	=> __('Order', CPAC_TEXTDOMAIN)
				)			
			);
		}
		
		// Page Template
		if ( $this->type == 'page' ) { 
			$custom_columns['column-page-template'] = array(
				'label'	=> __('Page Template', CPAC_TEXTDOMAIN)
			);	
		}
		
		// Post Formats
		if ( post_type_supports( $this->type, 'post-formats') ) {
			$custom_columns['column-post_formats'] = array(
				'label'	=> __('Post Format', CPAC_TEXTDOMAIN)
			);
		}
		
		// Taxonomy support
		$taxonomies = get_object_taxonomies( $this->type, 'objects');
		if ( $taxonomies ) {
			foreach ( $taxonomies as $tax_slug => $tax ) {
				if ( $tax_slug != 'post_tag' && $tax_slug != 'category' && $tax_slug != 'post_format' ) {
					$custom_columns['column-taxonomy-'.$tax->name] = array(
						'label'			=> $tax->label,
						'show_filter'	=> true,
						'options'		=> array(
							'type_label'	=> __('Taxonomy', CPAC_TEXTDOMAIN)
						)
					);				
				}
			}
		}
		
		// Custom Field support
		if ( $this->get_meta_keys() ) {
			$custom_columns['column-meta-1'] = array(
				'label'			=> __('Custom Field', CPAC_TEXTDOMAIN),
				'field'			=> '',
				'field_type'	=> '',
				'before'		=> '',
				'after'			=> '',
				'options'		=> array(
					'type_label'	=> __('Field', CPAC_TEXTDOMAIN),
					'class'			=> 'cpac-box-metafield'
				)			
			);
		}	
		
		// merge with defaults
		$custom_columns = $this->parse_defaults($custom_columns);
		
		return apply_filters('cpac-custom-posts-columns', $custom_columns);
	}
	
	/**
	 * 	Get WP default supported admin columns per post type.
	 *
	 * 	@since     1.0
	 */
	function get_default_columns() 
	{
		// we need to change the current screen
		global $current_screen;
			
		// some plugins depend on settings the $_GET['post_type'] variable such as ALL in One SEO
		$_GET['post_type'] = $this->type;
		
		// to prevent possible warning from initializing load-edit.php 
		// we will set a dummy screen object
		if ( empty($current_screen->post_type) ) {
			$current_screen = (object) array( 'post_type' => $this->type, 'id' => '', 'base' => '' );			
		}		
		
		// for 3rd party plugin support we will call load-edit.php so all the 
		// additional columns that are set by them will be avaible for us		
		do_action('load-edit.php');
		
		// some plugins directly hook into get_column_headers, such as woocommerce
		$columns = get_column_headers( 'edit-' . $this->type );
		
		// get default columns		
		if ( empty($columns) ) {		
			
			// deprecated as of wp3.3
			if ( file_exists(ABSPATH . 'wp-admin/includes/template.php') )
				require_once(ABSPATH . 'wp-admin/includes/template.php');
				
			// introduced since wp3.3
			if ( file_exists(ABSPATH . 'wp-admin/includes/screen.php') )
				require_once(ABSPATH . 'wp-admin/includes/screen.php');
				
			// used for getting columns
			if ( file_exists(ABSPATH . 'wp-admin/includes/class-wp-list-table.php') )
				require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
			if ( file_exists(ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php') )
				require_once(ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php');			
			
			// #48 - In WP Release v3.5 we can use the following.
			// $table = new WP_Posts_List_Table(array( 'screen' => $post_type ));
			// $columns = $table->get_columns();
			
			// we need to change the current screen... first lets save original
			$org_current_screen = $current_screen;
			
			// prevent php warning 
			if ( !isset($current_screen) ) $current_screen = new stdClass;
			
			// overwrite current_screen global with our post type of choose...
			$current_screen->post_type = $this->type;
			
			// ...so we can get its columns		
			$columns = WP_Posts_List_Table::get_columns();				
			
			// reset current screen
			$current_screen = $org_current_screen;

		}
		
		if ( empty ( $columns ) )
			return false;
			
		// change to uniform format
		$columns = $this->get_uniform_format($columns);		

		// add sorting to some of the default links columns
		
		//	categories
		if ( !empty($columns['categories']) ) {
			$columns['categories']['options']['sortorder'] = 'on';
		}
		// tags
		if ( !empty($columns['tags']) ) {
			$columns['tags']['options']['sortorder'] = 'on';
		}
		
		return $columns;
	}
	
	/**
     * Get Meta Keys
     * 
	 * @since 1.5
     */
    public function get_meta_keys()
    {
        global $wpdb;
        		
		$fields = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.post_type = %s ORDER BY 1", $this->type ), ARRAY_N );
		
		return $this->maybe_add_hidden_meta($fields);
    }
	
	/**
	 * Get Label
	 *
	 * @since 1.5
	 */
	function get_label()
	{
		$posttype_obj 	= get_post_type_object( $this->type );
		
		return $posttype_obj->labels->singular_name;
	}
}