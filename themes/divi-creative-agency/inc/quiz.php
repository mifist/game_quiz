<?php
 /**
  * Added Quiz post_type
  * */
add_action('init', 'ca_quiz_init');
function ca_quiz_init(){
	register_post_type('jeux', array(
		'labels'    => array(
			'name'                  => 'Jeux', // The main name of the recording type
			'singular_name'         => 'Jeux', // separate name of the entry of type quiz
			'add_new'               => 'Ajouter de nouveaux',
			'add_new_item'          => 'Ajouter de nouveaux jeux',
			'edit_item'             => 'Éditer jeux',
			'new_item'              => 'Nouveau jeux',
			'view_item'             => 'Vue jeux',
			'search_items'          => 'Trouver jeux',
			'not_found'             => 'Pas trouvé jeux',
			'not_found_in_trash'    => 'Aucun jeux trouvé dans la poubelle',
			'parent_item_colon'     => '',
			'menu_name'             => 'Jeux'
		
		),
		'menu_icon'          => 'dashicons-format-status',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array('title','author','thumbnail', 'page-attributes')
	) );
}

//------------------------------ AJAX pagination for list of Game -------------------------------------------------


/**
 * Custom AJAX pagination
 * */
function vb_ajax_pager( $query = null ) {
	if (!$query)
		return;
	$paginate = paginate_links([
		'type'      => 'array',
		'format'  => '?paged=%#%',
		'current' => max( 1, $query->get('paged') ),
		'total'   => $query->max_num_pages,
		'prev_next'    => false,
	]);
	if ($query->max_num_pages > 1) : ?>
		<ul class="pagination quiz-custom-pagination">
			<?php foreach ( $paginate as $page ) :?>
				<li><?php echo $page; ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif;
}

add_action( 'wp_ajax_quiz_pagination', 'quiz_pagination_callback' );
add_action( 'wp_ajax_nopriv_quiz_pagination', 'quiz_pagination_callback' );
function quiz_pagination_callback() {

	check_ajax_referer( 'quiz_game-special-string', 'security' );
	$quiz_query_args = unserialize( stripslashes( $_POST['quiz_query'] ) );
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$quiz_query_args['paged'] = $page;
	
	$quiz_query = new WP_Query( $quiz_query_args );
	if( $quiz_query->have_posts() ) :?>
		<div class="quiz-container">
			<?php $quiz_key = 0; while ( $quiz_query->have_posts() ) : $quiz_query->the_post();
				$quiz_id = get_the_ID();
				$game_date = get_field('game_date', $quiz_id);
				$game_description = get_field('game_description', $quiz_id);
				$game_locked_msg = get_field('game_locked_msg', $quiz_id);
				$locked_link = get_field('game_locked_link', $quiz_id);
				$game_max_score = 17;
				$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $quiz_id ), 'full');
				$is_subscription = check_current_user_subscription();
				$game_score_arr = get_score_by_user_id($quiz_id)[0];
				$game_score = $game_score_arr->score;
				$game_score_total = $game_score_arr->total_score;
				$lock_quiz = false;
				if ( $page !== 1 ) {
					if ( !$is_subscription ) {
						$lock_quiz = true;
					} elseif ( $is_subscription ) {
						$lock_quiz = false;
					}
				} else {
					if ( $quiz_key == 0 && !$is_subscription ) {
						$lock_quiz = false;
					} elseif ( !$is_subscription ) {
						$lock_quiz = true;
					} elseif ( $is_subscription ) {
						$lock_quiz = false;
					}
				}
			?>
				<<?php echo !$lock_quiz ? 'a ' : 'div '; ?>
						class="quiz-item <?php echo $lock_quiz ? 'lock_quiz' : 'unlock_quiz'; ?>"
						<?php echo !$lock_quiz ? 'href="'.get_the_permalink($quiz_id).'"' : ''; ?>>
				
				<?php if( $game_score_arr && !$lock_quiz && is_user_logged_in() && $is_subscription ) : ?>
					<span class="quiz-item__max-score">
						<span class="score-label"><?php echo __('Meilleur score', ca_textdomain); ?>:</span>
						<span class="score-value"> <?php echo $game_score.' / '.$game_score_total; ?></span>
					</span>
				<?php elseif ( !$game_score_arr && !$lock_quiz && is_user_logged_in() && !$is_subscription ) : ?>
					<span class="quiz-item__max-score">
						<?php echo __('Pas de score', ca_textdomain); ?>
					</span>
				<?php endif; ?>
				
				<span class="quiz-item__image">
					<img src="<?php echo $large_image_url[0]; ?>" alt="<?php echo get_the_title($quiz_id); ?>" loading="lazy">
				</span>
				<span class="quiz-item__meta">
					<h3 class="quiz-item__title quiz-meta"><?php echo get_the_title($quiz_id); ?></h3>
					<?php echo $game_date ? '<span class="quiz-meta meta-date">'.$game_date.'</span>' : ''; ?>
				</span>
				<?php if( $lock_quiz ) : ?>
					<span class="lock-overlay">
						<span class="lock-overlay__lock-icon"></span>
						<?php echo $game_locked_msg ? '<span class="lock-overlay__message">'.$game_locked_msg.'</span>' : ''; ?>
						<?php if( $locked_link ) : ?>
							<a class="btn btn-subscribe lock-overlay__btn"
							   href="<?php echo $locked_link['url'] ?:'#'; ?>"
							   target="<?php echo $locked_link['target']; ?>">
								<?php echo $locked_link['title'] ?: __('S`abonner', ca_textdomain); ?>
							</a>
						<?php endif; ?>
					</span>
				<?php endif; ?>
				</<?php echo !$lock_quiz ? 'a' : 'div'; ?>>
		
			<?php $quiz_key++; endwhile;  ?>
		</div> <!-- .quiz-container -->
		
		<!-- Pagination -->
		<div class="quiz-pagination">
			<?php vb_ajax_pager( $quiz_query ); ?>
			<?php if (  $quiz_query->max_num_pages > 1 ) : ?>
				<script>
					var quiz_true_posts = '<?php echo serialize($quiz_query->query_vars); ?>';
					var quiz_max_pages = '<?php echo $quiz_query->max_num_pages; ?>';
				</script>
			<?php endif; ?>
		</div>
	
	<?php endif; wp_reset_postdata();
	
	die(); // this is required to return a proper result
}


//------------------------------ Game shortcode template [jeux_list] -------------------------------------------------


/**
 * Jeux (quiz) list template
 * */
function quiz_template( $atts ) {
	$paged = get_query_var('paged') ?: 1;
	$loop_args = array(
		'post_type'      => 'jeux',
		'post_status'    => array( 'publish', 'private' ),
		'posts_per_page' => 9,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'paged'          => $paged,
	);
	$quiz_query = new WP_Query( $loop_args );
	global $quiz_key; $quiz_key = 0; ?>
	 <?php if( $quiz_query->have_posts() ) :?>
		<div class="quiz-container">
		<?php while ( $quiz_query->have_posts() ) : $quiz_query->the_post(); ?>
			<?php get_template_part( 'quiz-parts/quiz-loop' ); ?>
		<?php $quiz_key++; endwhile;  ?>
		</div> <!-- .quiz-container -->
	
		<!-- Pagination -->
		<div class="quiz-pagination">
			<?php vb_ajax_pager( $quiz_query ); ?>
			<?php if (  $quiz_query->max_num_pages > 1 ) : ?>
				<script>
					/*  quiz_true_posts - a serialized array containing all the necessary query parameters */
					var quiz_true_posts = '<?php echo serialize($quiz_query->query_vars);  ?>';
					var quiz_max_pages = '<?php echo $quiz_query->max_num_pages; ?>';
				</script>
			<?php endif; ?>
		</div>
		
	<?php endif; wp_reset_postdata(); ?>
<?php
}

/*
 * Added Jeux (quiz) list with pagination in shortcode
 * */
add_shortcode( 'jeux_list', 'quiz_shortcode' );
function quiz_shortcode( $atts ) {
	ob_start();
		echo '<div class="quiz-section">';
			quiz_template( $atts );
		echo '</div>';
	return ob_get_clean();
}

/**
 * Get page_id where was called main shortcode, using $wpdb
 *
 * @return mixed
 */
function get_page_id_within_shortcode() {
	global $wpdb;
	$shortcode_name = 'jeux_list';
	$page_id = $wpdb->get_var('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_content LIKE "%['.$shortcode_name.']%" AND post_parent = 0');
	return $page_id;
}


//------------------------------ Save Game Score to the DB -------------------------------------------------


/*
 * Save quiz score to the DB
 * */
add_action( 'init', 'quiz_table_create');
function quiz_table_create() {
	global $wpdb;
	$table_name = $wpdb->prefix. "games_score";
	global $charset_collate;
	$charset_collate = $wpdb->get_charset_collate();
	global $db_version;
	if( $wpdb->get_var("SHOW TABLES LIKE $table_name") != $table_name)
	{ $create_sql = "CREATE TABLE " . $table_name . " (
		id INT(20) NOT NULL auto_increment,
		post_id INT(20) NOT NULL ,
		user_ip VARCHAR(40) NOT NULL ,
		user_id INT(20) NOT NULL ,
		score INT(20) NOT NULL ,
		total_score INT(20) NOT NULL ,
		PRIMARY KEY (id))$charset_collate;";
		require_once(ABSPATH . "wp-admin/includes/upgrade.php");
		dbDelta( $create_sql );
	}
	
	//register the new table with the wpdb object
	if ( !isset($wpdb->games_score) ) {
		$wpdb->games_score = $table_name;
		//add the shortcut so you can use $wpdb->stats
		$wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
	}
	
}

// The function that handles the AJAX request
function get_user_ip() {
	return $_SERVER['REMOTE_ADDR'];
}
	
	/**
 * Save Game score to the DB
 * */
add_action( 'wp_ajax_save_game_score', 'save_game_score_to_database_callback' );
add_action( 'wp_ajax_nopriv_save_game_score', 'save_game_score_to_database_callback' );
function save_game_score_to_database_callback() {
	check_ajax_referer( 'quiz_game-special-string', 'security' );
	global $post;
	$post_id =  isset($_POST['post_id']) ? intval( $_POST['post_id'] ) : $post->ID;
	$user_ip = get_user_ip() ?: 0;
	$user_id = get_current_user_id() ?: 0; // $_SERVER['REMOTE_ADDR']
	$score = isset($_POST['current_score']) ? intval( $_POST['current_score'] ) : 0;
	$total_score = isset($_POST['total_score']) ? intval( $_POST['total_score'] ) : 0;
	//check if post id and ip present
	global $wpdb;
	$row = $wpdb->get_results(
		"
			SELECT id
			FROM $wpdb->games_score
			WHERE post_id = '$post_id'
		"
	);

	if ( $row ) {
		$row_by_user = $wpdb->get_results(
			"
				SELECT user_ip
				FROM $wpdb->games_score
				WHERE post_id = '$post_id'
				AND (user_id = '$user_id' AND user_ip = '$user_ip')
			"
		);
		 
		if( empty( $row_by_user ) && $row_by_user !== $user_ip ) {
			//insert row
			// %d - number, %s - string
			$insert_array = array(
				'post_id'     => $post_id,      // %d
				'user_ip'     => $user_ip,      // %s
				'user_id'     => $user_id,      // %d
				'score'       => $score,        // %d
				'total_score' => $total_score,  // %d
			
			);
			$wpdb->query( $wpdb->prepare(
				"
					INSERT INTO $wpdb->games_score
					( post_id, user_ip, user_id, score, total_score )
					VALUES ( %d, %s, %d, %d, %d )
				",
				$insert_array
			) );
			
		} else {
			if ( $user_id === 0 ) {
				$user_score_arr = $wpdb->get_results(
					"
						SELECT score, total_score
						FROM $wpdb->games_score
						WHERE post_id = '$post_id'
						AND user_id = '$user_id'
					"
				);
				$user_score = $user_score_arr[0]->score;
				$user_total_score = $user_score_arr[0]->total_score;
				if ( $score >= $user_score || $total_score !== $user_total_score ) {
					$wpdb->update(
						$wpdb->games_score, // указываем таблицу
						array(
							'user_id'     => $user_id,      // %d
							'score'       => $score,        // %d
							'total_score' => $total_score,  // %d
						),
						array(
							'post_id' => $post_id,
							'user_ip' => $user_ip  // %s
						),
						array( '%d', '%d', '%d' ),
						array(
							'%d',
							'%s'
						)
					);
				}
			} else {
				 $user_score_arr = $wpdb->get_results(
					"
						SELECT score, total_score
						FROM $wpdb->games_score
						WHERE post_id = '$post_id'
						AND user_ip = '$user_ip'
					"
				);
				 $user_score = $user_score_arr[0]->score;
				 $user_total_score = $user_score_arr[0]->total_score;
				if ( $score >= $user_score || $total_score !== $user_total_score ) {
					$wpdb->update(
						$wpdb->games_score,
						array(
							'user_ip'     => $user_ip,      // %s
							'score'       => $score,        // %d
							'total_score' => $total_score,  // %d
						),
						array(
							'post_id' => $post_id,
							'user_id' => $user_id
						),
						array( '%s', '%d', '%d' ),
						array(
							'%d',
							'%d'
						)
					);
				}
			}
			
			
		}
	} else {
		//insert row
		// %d - number, %s - string
		$insert_array = array(
			'post_id'     => $post_id,      // %d
			'user_ip'     => $user_ip,      // %s
			'user_id'     => $user_id,      // %d
			'score'       => $score,        // %d
			'total_score' => $total_score,  // %d
		
		);
		
		$wpdb->query( $wpdb->prepare(
			"
				INSERT INTO $wpdb->games_score
				( post_id, user_ip, user_id, score, total_score )
				VALUES ( %d, %s, %d, %d, %d )
			",
			$insert_array
		) );
	}
	
	
	//calculate like count from db.
	$totalrow = $wpdb->get_results(
			"
				SELECT *
				FROM $wpdb->games_score
				WHERE post_id = '$post_id'
			"
	);
	$data = array(
		'post_id'     => $post_id,      // %d
		'user_ip'     => $user_ip,      // %s
		'user_id'     => $user_id,      // %d
		'score'       => $score,        // %d
		'total_score' => $total_score,  // %d
	);
	echo json_encode($data);
	//echo $user_id;
	die(); // this is required to return a proper result
}


function get_score_by_user_id( $post_id = '', $user_id = 0 ) {
	global $wpdb;
	global $post;
	// post_id, user_ip, user_id, score, total_score
	$post_id = $post_id ? $post_id : $post->ID;
	$user_id = $user_id ? $user_id : get_current_user_id();
	$user_ip = get_user_ip() ?: 0;
	
	if ( !is_user_logged_in() ) {
	    $user_score = $wpdb->get_results(
			"
				SELECT score, total_score
				FROM $wpdb->games_score
				WHERE post_id = '$post_id'
				AND user_ip = '$user_ip'
			"
		);
	} else {
		$user_score = $wpdb->get_results(
			"
				SELECT score, total_score
				FROM $wpdb->games_score
				WHERE post_id = '$post_id'
				AND user_id = '$user_id'
			"
		);
	}
	
	return $user_score;
}



//------------------------------ Show Game Score in the wp-admin ---------------------------------------

/**
 * Add blocks to the main column on the post and post pages. pages
 * */
add_action('add_meta_boxes', 'game_score_custom_box');
function game_score_custom_box(){
	$screens = array( 'jeux' );
	add_meta_box( 'game_score_section', 'Game Score', 'game_score_section_callback', $screens );
}
//  HTML block code
function game_score_section_callback( $post, $meta ){
	$screens = $meta['args'];
	global $wpdb;
	// We use nonce for verification
	wp_nonce_field( plugin_basename(__FILE__), 'game_score_section_noncename' );
	
	// fields value
	$total_rows = $wpdb->get_results(
		"
			SELECT *
			FROM $wpdb->games_score
			WHERE post_id = '$post->ID'
		"
	);
	// Data entry form fields
	
	?>
	<style type="text/css">
		.score-wrap {
			display: block;
			width: 100%;
			height: auto;
		}
		.row {
			display: flex;
			align-items: center;
			justify-content: flex-start;
			margin: 0;
			
		}
		.row.first {
			font-weight: 600;
			text-transform: uppercase;
		}
		.row.first li {
		   border-bottom: 2px solid #333;
		}
		.row li {
			flex: 1;
			align-self: flex-end;
			padding: 15px 10px;
			border-bottom: 1px solid gray;
			margin-bottom: 0;
		}
		.row li a {
			text-decoration: none;
			display: block;
		}
		.row li .nicename {
			font-style: italic;
			margin-left: 5px;
		}
		.empty-score {
			margin-top: 25px;
			text-align: center;
		}
	</style>
	<?php if( $total_rows ) : ?>
		 <div class="score-wrap">
		   
		   <?php foreach ( $total_rows as $key => $row ) :
			   $user_ip = $row->user_ip;
			   $user_id = $row->user_id;
			   $score = $row->score;
			   $total_score = $row->total_score;
			   $user = get_user_by('id', $user_id);
			   $user_display_name = $user->data->display_name;
			   $user_nicename = $user->data->user_nicename;
			   $user_login = $user->data->user_login;
			   $user_email = $user->data->user_email;
		   
		   ?>
		   <?php if( $key == 0 ) : ?>
		       <ul class="row first">
				   <li class="user"><?php echo __('Users / IP', ca_textdomain); ?></li>
				   <li class="score"><?php echo __('Score', ca_textdomain); ?></li>
		        </ul>
		   <?php endif; ?>
		   <ul class="row">
		      <li class="user">
			      <?php if( $user ) : ?>
			        <?php echo $user_display_name; ?>
			        <span class="nicename">( <?php echo $user_login; ?> )</span>
				      <?php if( $user_email ) : ?>
				           <a class="email" href="mailto:<?php echo $user_email; ?>"><?php echo $user_email; ?></a>
				      <?php endif; ?>
					
			      <?php else : ?>
				      <?php echo $user_ip; ?>
			      <?php endif; ?>
		      </li>
			   <li class="score">
				   <?php echo $score .' / '.$total_score; ?>
			   </li>
		   </ul>
		   <?php endforeach; ?>
	   </div>
	<?php else : ?>
		<h3 class="empty-score"><?php echo __('Le score du jeu est vide', ca_textdomain); ?></h3>
	<?php endif; ?>
	
	<?php
}

// check user subscribe by WooCommerce Subscriptions
function check_current_user_subscription() {
	
	// Check if user is even logged in, if not exit
	if ( !is_user_logged_in() ) return false;
	
	$current_user   = wp_get_current_user(); // get current WP_User
	$user_id    = $current_user->ID; // get user id
	
	$is_subscription = wcs_user_has_subscription( $user_id ); // check if user has subscription
	if ( $is_subscription )
	{
		$subscriptions = wcs_get_users_subscriptions( $user_id ); // get array of all subscriptions
		// Check if there is one subscription or multiple subscriptions per user
		if ( count( $subscriptions ) > 1 ) {
			
			// Example if you wanted to loop through all subscriptions, in the case of the user having multiple subscriptions
			foreach ( $subscriptions as $sub_id => $subscription ) {
				if ( $subscription->get_status() == 'active' ) {
					// Do something
					return true;
				} else {
					return false;
				}
			}
		} else { // Only 1 subscription
			
			$subscription  = reset( $subscriptions ); // gets first and only value
			if ( $subscription->get_status() == 'active' ) {
				// Do something
				return true;
			} else {
				return false;
			}
		}
	}
	
}

/**
 * @snippet       Translate a String in WooCommerce
 */
add_filter( 'woocommerce_subscriptions_product_price_string', 'wc_subscriptions_custom_price_string' );
add_filter( 'woocommerce_subscription_price_string', 'wc_subscriptions_custom_price_string' );
function wc_subscriptions_custom_price_string( $pricestring ) {
	$newprice = str_replace( '/ month', '/ mois', $pricestring );
	return $newprice;
}


