<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/valentinzamarin
 * @since      1.0.0
 *
 * @package    Apf
 * @subpackage Apf/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Apf
 * @subpackage Apf/admin
 * @author     zamarin  <zamarin.dev@gmail.com>
 */
class Apf_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	private $post_types = array();
	private $pagination_type = [
		'num' => 'Numeric',
		'loadmore' => 'Loadmore',
	];

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$args = array(
			'public'   => true,
			'_builtin' => true
		);
		
		$output = 'names'; // 'names' or 'objects' (default: 'names')
		$operator = 'and'; // 'and' or 'or' (default: 'and')
		
		$this->post_types = get_post_types( $args, $output, $operator );
	}

	public function register_filter_post_types () {

		register_post_type('apf_post_type', array(
			'labels'             => array(
				'name'               => 'Filter',
				'singular_name'      => 'Filter',
				'add_new'            => 'Add filter',
				'add_new_item'       => 'Add new filter',
				'edit_item'          => 'Edit filter',
				'new_item'           => 'New filter',
				'view_item'          => 'View filter',
				'search_items'       => 'Search filter',
				'not_found'          => 'Not Found',
				'not_found_in_trash' => 'Not Found in trash',
				'parent_item_colon'  => '',
				'menu_name'          => 'Advanced Filter'
	
			  ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'page',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' )
		) );

	}

	public function filter_post_type_meta( $post ) {
		add_meta_box(
			'filter_meta',
			'Settings',
			function ( $post ) {
				$post_type    = get_post_meta( $post->ID, 'apf_post_type', true );
				$filter_count = get_post_meta( $post->ID, 'apf_count', true );
				$pagination   = get_post_meta( $post->ID, 'apf_pagination', true );
				wp_nonce_field( 'apf_save_meta', 'apf_nonce' );
				?>
				<div>
				<label>
					Post type
					<?php
						echo '<select name="apf_post_type" class="widefat">';
						echo '<option value="null">All</option>';
						foreach ( $this->post_types as $type ) :
						$selected = '';
						if ( $type == $post_type ) {
							$selected = 'selected="selected"';
						}
						echo '<option value="' . $type . '"' . selected( 'apf_post_type', $type ) . $selected . ' >' . $type . '</option>';
						endforeach;
						echo '</select>';
						?>
				

				</label>
				<label>
					Pagination
					<?php
						echo '<select name="apf_pagination" class="widefat">';
						foreach ( $this->pagination_type as $type ) :
						$selected = '';
						if ( $type == $pagination ) {
							$selected = 'selected="selected"';
						}
						echo '<option value="' . $type . '"' . selected( 'apf_pagination', $type ) . $selected . ' >' . $type . '</option>';
						endforeach;
						echo '</select>';
						?>
				

				</label>
				<label>
					Post count
					<input type="number"
							name="apf_count"
							value="<?php echo esc_attr( $filter_count ); ?>"
							class="widefat js-counting-input"/>
				</label>
				</div>
				<?php
			},
			'apf_post_type',
			'normal',
			'low'
		);
	}

	public function filter_save_post_meta( $post ) {
		global $post;

		if ( empty( $_POST['apf_nonce'] ) || ! wp_verify_nonce( $_POST['apf_nonce'], 'apf_save_meta' ) ) {
			return true;
		}

		$keys = array(
			'apf_post_type',
			'apf_count',
			'apf_pagination',
		);

		foreach ( $keys as $key ) {
			if ( ! empty( $_POST[ $key ] ) ) {
				update_post_meta( $post->ID, $key, $_POST[ $key ] );
			} else {
				delete_post_meta( $post->ID, $key );
			}
		}

		return true;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Apf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Apf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/apf-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Apf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Apf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/apf-admin.js', array( 'jquery' ), $this->version, false );

	}

}
