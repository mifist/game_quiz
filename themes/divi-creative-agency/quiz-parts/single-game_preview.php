<?php
global $post;
global $quiz_key;
$game_id = $post->ID;
$game_date = get_field('game_date', $game_id);
$game_description = get_field('game_description', $game_id);
$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $game_id ), 'full');
$game_list_page_id = get_page_id_within_shortcode();
$game_score_arr = get_score_by_user_id($game_id)[0];
$game_score = $game_score_arr->score;
$game_score_total = $game_score_arr->total_score;
$lock_quiz = check_current_user_subscription();
?>
<div class="single-game__preview show">
	 <a class="single-game__back-btn btn btn-back btn-light-menu" href="<?php echo get_the_permalink( $game_list_page_id ); ?>">
		  <span class="btn-back__icon"></span>
		  <span class="btn-back__name"><?php echo __('Revenir a la liste des jeux', ca_textdomain); ?></span>
	 </a>
	<div class="preview-content__image">
		<img src="<?php echo $large_image_url[0]; ?>" alt="<?php the_title(); ?>" loading="lazy">
	</div>
	<div class="preview-content__wrap">
		<div class="preview-content__meta">
			<h1 class="preview-content__title"><?php the_title(); ?></h1>
			<?php echo $game_date ? '<span class="preview-content__date">'.__('du').' <time>' .$game_date.'</time></span>': ''; ?>
		</div>
		<?php echo $game_description ? '<div class="preview-content__description">'.$game_description.'</div>' : ''; ?>
		<?php if( $game_score_arr && !$lock_quiz && is_user_logged_in() ) : ?>
			<div class="preview-content__max-score">
				<span class="score-label"><?php echo __('Meilleur score', ca_textdomain); ?>: </span>
				<span class="score-value"><?php echo $game_score.'/'.$game_score_total; ?></span>
			</div>
		<?php endif; ?>
		
		<a class="single-game__play-btn btn btn-play btn-light-menu" href="#quiz-container">
			<span class="btn-play__icon"></span>
			<span class="btn-play__name"><?php echo __('Jouer', ca_textdomain); ?></span>
		</a>
	</div>
	

	
</div>
