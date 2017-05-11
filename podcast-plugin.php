<?php
/*
 * Plugin Name: Podcasting plugin
 * Description: My First task and First Plugin
 * Plugin URI: 
 * Author: Starskie Villanueva
 * Author URI: 
 * Version: 0.1
 * Text Domain: dx-sample-plugin
*/

/**
 * Get some constants ready for paths when your plugin grows 
 * 
 */
 


define( 'DXP_VERSION', '0.1' );
define( 'DXP_PATH', dirname( __FILE__ ) );
define( 'DXP_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'DXP_FOLDER', basename( DXP_PATH ) );
define( 'DXP_URL', plugins_url() . '/' . DXP_FOLDER );
define( 'DXP_URL_INCLUDES', DXP_URL . '/inc' );



/*  The plugin base class - the root of all WP goods!  */
class DX_Plugin_Base {
	
	/*  Assign everything as a call from within the constructor */
		public function __construct() {
					
		// register meta boxes for Pages (could be replicated for posts and custom post types)
		add_action( 'add_meta_boxes', array( $this, 'dx_meta_boxes_callback' ) );
		
		// register save_post hooks for saving the custom fields
		add_action( 'save_post', array( $this, 'dx_save_sample_field' ) );
		
		
		// Register custom post types 
		add_action( 'init', array( $this, 'dx_custom_post_types_callback' ), 5 );
		
				
		// Translation-ready
		add_action( 'plugins_loaded', array( $this, 'dx_add_textdomain' ) );

		// to include the template
		add_filter( 'template_include', array($this, 'GetTemplate') );
		
						
	}	


	// this is for view template
	function GetTemplate( $sv_template )
	{
	    if ( is_page( 'pluginbase' ) ) {

	        $sv_template = locate_template( 'podcast-template-view.php'  );
			if ( '' != $sv_template ) {
				return $sv_template ;
			}
	    }
	    return $sv_template;
	}
		
	
		/*  Add admin CSS styles - available only on admin  */
	public function dx_add_admin_CSS( $hook ) {
		wp_register_style( 'samplestyle-admin', plugins_url( '/css/samplestyle-admin.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'samplestyle-admin' );
		
		if( 'toplevel_page_dx-plugin-base' === $hook ) {
			wp_register_style('dx_help_page',  plugins_url( '/help-page.css', __FILE__ ) );
			wp_enqueue_style('dx_help_page');
		}
	}
	
	/*  Adding bottom meta boxes to Pages   */
	public function dx_meta_boxes_callback() {
		// register side box
		add_meta_box( 
		        'dx_bottom_meta_box',
		        __( "Meta box", 'dxbase' ),
		        array( $this, 'dx_bottom_meta_box' ),
		        ''
		        );
		  	}
	// meta box function
	public function dx_bottom_meta_box( $post, $metabox) {
		_e("", 'dxbase');
		
		// Add some test data here - a custom field, that is
		$dx_audio_input = '';
		$dx_episode_input = '';
		if ( ! empty ( $post ) ) {
		$dx_audio_input = get_post_meta( $post->ID, 'dx_audio_input', true );
		$dx_episode_input = get_post_meta( $post->ID, 'dx_episode_input', true );
		}
			
		//   Adding input text for audio and text area for Episode notes
		?>
    		<p>
     		 <label for="dx-test-input">Audio Input</label>
     		 </br>
     		 <input type="text" name="dx_audio_input" value="<?php echo $dx_audio_input; ?>" />
    		</p>
  			<p>
    		<label for="dx-test-input">Episode Notes</label>
    		</br>	
     		<textarea name="dx_episode_input" cols="100" rows="3"><?php echo $dx_episode_input; ?></textarea>	
    		<?php
	  }	
	/*
	  Save the custom field from the side metabox
	  @param $post_id the current post ID
	  @return post_id the post ID from the input arguments
	  
	 */
	public function dx_save_sample_field( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$slug = 'pluginbase';
		// If this isn't a 'book' post, don't update it.
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
			return;
		}
		
		// If the custom field is found, update the postmeta record
		// Also, filter the HTML just to be safe
		if ( isset( $_POST['dx_audio_input']  ) ) {
			update_post_meta( $post_id, 'dx_audio_input',  esc_html( $_POST['dx_audio_input'] ) );
		}
		
		// for episode
		if ( isset( $_POST['dx_episode_input']  ) ) {
			update_post_meta( $post_id, 'dx_episode_input',  esc_html( $_POST['dx_episode_input'] ) );
		}

	}
	
	/*   Register custom post types */
	public function dx_custom_post_types_callback() {
		register_post_type( 'pluginbase', array(
			'labels' => array(
				'name' => __("Podcast", 'post type general name' , 'dxbase'),
				'singular_name' => __('Podcast', 'post type singular name' , 'dxbase'),
				'add_new' => _x("Add New", 'pluginbase', 'dxbase' ),
				'add_new_item' => sprintf( __( 'Add New %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'edit_item' => sprintf( __( 'Edit %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),				
				'new_item' => sprintf( __( 'New %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'all_items' => sprintf( __( 'All %s' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'view_item' => sprintf( __( 'View %s' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'search_items' => sprintf( __( 'Search %a' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'not_found' =>  sprintf( __( 'No %s Found' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'not_found_in_trash' => sprintf( __( 'No %s Found In Trash' , 'dxbase' ), __( 'Episodes' , 'dxbase' ) ),
				'parent_item_colon' => '',
				'menu_name' => __( 'Podcast' , 'dxbase' ),
				'filter_items_list' => sprintf( __( 'Filter %s list' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'items_list_navigation' => sprintf( __( '%s list navigation' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
				'items_list' => sprintf( __( '%s list' , 'dxbase' ), __( 'Episode' , 'dxbase' ) ),
			),
			
			'menu_icon' => 'dashicons-microphone',
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'has_archive' => true,
			'menu_position' => 40,
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'custom-fields',
				'page-attributes',
			),
			
		));	
	}
	


	/* 
		Initialize the Settings class
		Register a settings section with a field for a secure WordPress admin option creation. 
	*/
	public function dx_register_settings() {
		require_once( DXP_PATH . '/dx-plugin-settings.class.php' );
		new DX_Plugin_Settings();
	}
	/*   Add textdomain for plugin  */
	public function dx_add_textdomain() {
		load_plugin_textdomain( 'dxbase', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
		
	/*  Callback for getting a URL and fetching it's content in the admin page  */
}

// Initialize everything
$dx_plugin_base = new DX_Plugin_Base();



