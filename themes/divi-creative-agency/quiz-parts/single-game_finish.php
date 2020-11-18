<?php
global $post;
global $quiz_key;
$game_id = $post->ID;
$finish_image = get_field('game_finish_image', $game_id);
$finish_description = get_field('game_finish_description', $game_id);
$is_free = get_field('is_this_game_free', $game_id);
$game_list_page_id = get_page_id_within_shortcode();
// score DB
$game_score_arr = get_score_by_user_id($game_id)[0];
$game_score = $game_score_arr->score;
$game_score_total = $game_score_arr->total_score;
$is_subscription = check_current_user_subscription();

?>
<div class="single-game__finish">
	<a class="single-game__back-btn btn btn-back btn-light-menu" href="<?php echo get_the_permalink( $game_list_page_id ); ?>">
		<span class="btn-back__icon"></span>
		<span class="btn-back__name"><?php echo __('Revenir a la liste des jeux', ca_textdomain); ?></span>
	</a>
	<?php if( $finish_image ) : ?>
		<div class="preview-content__image">
			<img src="<?php echo $finish_image; ?>" alt="<?php the_title(); ?>" loading="lazy">
		</div>
	<?php endif; ?>
	
	<div class="preview-content__wrap">
		
		<div class="preview-content__current-score">
			<span class="score-value">
				<span class="game-score"><?php echo $game_score; ?></span>/
				<span class="game-score-total"><?php echo $game_score_total; ?></span>
			</span>
			<span class="score-label"> : <?php echo __('La classe', ca_textdomain); ?>!</span>
		</div>
		
		<?php echo $finish_description ? '<div class="preview-content__description">'.$finish_description.'</div>' : ''; ?>
		
		<?php
		
		if( $game_score_arr ) : ?>
			<div class="preview-content__max-score">
				<span class="score-label"><?php echo __('Mon meilleur score', ca_textdomain); ?>: </span>
				<span class="score-value"><?php echo $game_score.'/'.$game_score_total; ?></span>
			</div>
		<?php else : ?>
			<div class="preview-content__max-score">
				<?php echo __('Pas de score', ca_textdomain); ?>
			</div>
		<?php endif; ?>
		 <div class="preview-content__buttons">
			 <a class="single-game__replay-btn btn btn-replay btn-light-menu"
			    href="<?php the_permalink($game_id); ?>">
				 <span class="btn-play__icon"></span>
				 <span class="btn-play__name"><?php echo __('Rejouer', ca_textdomain); ?></span>
			 </a>
			 <?php if( $is_subscription && is_user_logged_in() ) : ?>
				 <?php next_post_link( '<span class="btn btn-light-menu">%link</span>', 'Disney game suivant <span class="meta-nav"></span>' ); ?>
			 <?php endif; ?>
		 </div>
		
	</div>



</div>



