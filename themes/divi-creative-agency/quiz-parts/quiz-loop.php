<?php
global $quiz_key;
$quiz_id = get_the_ID();
$game_date = get_field('game_date', $quiz_id);
$game_description = get_field('game_description', $quiz_id);
$game_locked_msg = get_field('game_locked_msg', $quiz_id);
$locked_link = get_field('game_locked_link', $quiz_id);
$is_free = get_field('is_this_game_free', $quiz_id);
$game_max_score = 17;
$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $quiz_id ), 'full');
$is_subscription = check_current_user_subscription();
$game_score_arr = get_score_by_user_id($quiz_id)[0];
$game_score = $game_score_arr->score;
$game_score_total = $game_score_arr->total_score;
$lock_quiz = false;
if ( $quiz_key === 0 && !$is_subscription ) {
	$lock_quiz = false;
} elseif ( $quiz_key > 0 && !$is_subscription ) {
	$lock_quiz = true;
} elseif ( $is_subscription ) {
	$lock_quiz = false;
}

?>
<<?php echo !$lock_quiz ? 'a ' : 'div '; ?>
	class="quiz-item <?php echo $lock_quiz ? 'lock_quiz' : 'unlock_quiz'; ?>"
	<?php echo !$lock_quiz ? 'href="'.get_the_permalink($quiz_id).'"' : ''; ?>>

	<?php
	if( $game_score_arr && !$lock_quiz && is_user_logged_in() && $is_subscription ) : ?>
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