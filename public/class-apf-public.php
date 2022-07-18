<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/valentinzamarin
 * @since      1.0.0
 *
 * @package    Apf
 * @subpackage Apf/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Apf
 * @subpackage Apf/public
 * @author     zamarin  <zamarin.dev@gmail.com>
 */
class Apf_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */

	public function filter_shortcode( $atts ) {
		$filter_id = $atts['id'];		
		
		$post_count = get_post_meta( $filter_id, 'apf_count', true );
		$post_count = intval( $post_count );
		
		$this->APF_Filter_Form( $filter_id );
		echo '<div class="apf-response">';
		$this->APF_Shortcode_Query( $post_count );
		echo '</div>';
		
	}
	
	public function APF_Filter_Form( string $filter_id ) {
		$terms  = get_terms([
			'taxonomy' => 'category',
            'hide_empty' => false,
            'exclude' => [ 1 ],
		]);
        $form = '<form class="apf-filter" data-filter="' . $filter_id . '">';
        foreach ($terms as $term){
			$form .= '<label for="filter' . $filter_id . '-' . $term->slug . '">';
            $form .= '<input id="' . $term->slug . '" type="checkbox" name="' . $term->slug . '" value="'.$term->term_id.'" class="btn-filter" />';
            $form .= $term->name;
            $form .= '</label>';          
        }
        $form .= '</form>'; 
		echo $form;
	}
	public function APF_Shortcode_Query( int $post_count, int $paged = 1, array $categories  = []) { 

		$tax_query = array(
            'relation' => 'OR'
        );
		if( $categories ) {
			$tax_query[] =  array(
				'taxonomy' => 'category',
				'field' => 'id',
				'terms' => $categories,
				'include_children' => false
			);
		}

		$args = array(
            'post_type'      => $type,
            'posts_per_page' => $post_count,
			'tax_query'      => $tax_query,
			'paged'          => $paged,
        );  
		$query = new WP_Query( $args );

		$found_posts = $query->found_posts;       
		if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post(); 
                include APF_PLUGIN_DIR . '/public/partials/apf-public-display-post.php';
            }
        } else {
            get_template_part( 'content', 'none' );
        }
        wp_reset_postdata();
		
		$this->APF_Numeric_Pagination( $found_posts, $post_count );
		
	}

	public function APF_Filter_Result() {
		check_ajax_referer( 'apf', 'nonce' );
		$filter_id = $_POST['id'];

		if( isset( $_POST['page'] ) ){
			$paged = $_POST['page'];
		} else {
			$paged = 1;
		}
		if( isset( $_POST['categories'] ) && !empty( $_POST['categories'] ) ){
			$values = $_POST['categories'];
			$categories = explode(",",  $values);
		} else {
			$categories = [];
		}

		$post_count = get_post_meta( $filter_id, 'apf_count', true );

		$content = '';
		ob_start();
			$this->APF_Shortcode_Query( $post_count, $paged, $categories );
		$content = ob_get_contents();
		ob_end_clean();
		$result = [
			'content' => $content,
		];
		wp_send_json_success( $result );
	}

	public function APF_Numeric_Pagination( $found_posts, $post_count ) {
            
		$pages = ceil( $found_posts / $post_count);
		$pagination = '';
		ob_start(); 
		?>
			<nav aria-label="">
				<ul class="pagination"> 
						<?php
						for ($i = 1; $i <= $pages; $i++) {
							echo '<li class="page-item"><a data-page="'.$i.'" class="page-link apf-page" href="#">'.$i.'</a></li>';
						} 
					?> 
				</ul>
			</nav>
		<?php
		$pagination = ob_get_contents();
		ob_end_clean();
		
		if( intval( $pages ) !== 1 ) {
			echo $pagination;
		}   
	}

	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/apf-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/apf-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(  $this->plugin_name , 'apf', array(
			'nonce'    => wp_create_nonce( 'apf' ),
			'ajax_url' => admin_url( 'admin-ajax.php' )
	));


	}

}
