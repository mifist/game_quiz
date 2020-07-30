<?php
global $post;
$game_question_steps = get_row_index();
$max_step_count = count(get_field('game_questions_flexible'));
$post_id = $post->ID;
$question_image = get_sub_field('question_image');
$question_media_upload = get_sub_field('question_media_upload');
$question_media_url = get_sub_field('question_media_url');
$question_title = get_sub_field('question_title');
$is_multiple = get_sub_field('multiple_answer');

$correct_image = get_sub_field('correct_image') ?: $question_image;
$correct_media_upload = get_sub_field('correct_media_upload');
$correct_media_url = get_sub_field('correct_media_url');
?>
<section id="quiz_<?php echo $game_question_steps; ?>"
         class="quiz-step video-quiz <?php
         echo $game_question_steps==1 ? 'current' : '';
         echo $game_question_steps==$max_step_count?' last_quiz_step' :'';
         ?>"
         data-step="<?php echo $game_question_steps; ?>">
	<div class="quiz-step__controls">
		<a class="btn btn-cta quiz-step__media-play" href="#cta_modal_media_play_<?php echo $game_question_steps; ?>">
			<span class="name"><?php echo __('Lancer la vidéo', ca_textdomain); ?></span>
			<span class="icon"></span>
		</a>
		
		<div class="cta-modal-window">
			<div id="cta_overlay"></div>
			<div id="cta_modal_media_play_<?php echo $game_question_steps; ?>" class="cta_modal">
				<div class="cta-close"></div>
				<div class="modal-media show">
					<?php if( $question_media_upload ) : ?>
						<video poster="" id="bgvid" playsinline controls>
							<!--<source src="http://thenewcode.com/assets/videos/polina.webm" type="video/webm">-->
							<source src="<?php echo $question_media_upload; ?>" type="video/mp4">
						</video>
					<?php else : ?>
						<?php echo $question_media_url; ?>
					<?php endif; ?>
				</div>
				<div class="modal-media correct">
					<?php if( $correct_media_upload ) : ?>
						<video poster="" id="bgvid" playsinline controls>
							<!--<source src="http://thenewcode.com/assets/videos/polina.webm" type="video/webm">-->
							<source src="<?php echo $correct_media_upload; ?>" type="video/mp4">
						</video>
					<?php else : ?>
						<?php echo $correct_media_url; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="quiz-step__container">
		<div class="quiz-step__questions">
			<div class="quiz-step__media images">
				<img src="<?php echo $question_image; ?>" alt="<?php echo $question_title?:'Media'; ?>" loading="lazy">
			</div>
			<div class="quiz-step__content">
				<div class="quiz-step__meta">
					<div class="quiz-step__media-type"><?php echo __('Vidéo', ca_textdomain); ?></div>
					<div class="quiz-step__counter nuber-question">
						<span class="quiz-now"><?php echo $game_question_steps; ?></span>/
						<span class="total-quiz"><?php echo $max_step_count; ?></span>
					</div>
				</div>
				<?php echo $question_title ? '<h3 class="quiz-step__title">'.$question_title.'</h3>' : ''; ?>
				<?php if( have_rows('question_options') ): ?>
					<!-- Answer START -->
					<span class="error-msg"><?php echo __('Répondez à la question', ca_textdomain); ?></span>
					<ul class="answers <?php echo $is_multiple ? 'is_multiple' :''; ?>">
						<?php while( have_rows('question_options') ): the_row();
							$option_title = get_sub_field('option_title');
							$is_right = get_sub_field('option_right');
					
							?>
							
							<li class="quiz-answer"
							    data-answer="<?php echo $is_right?1:0; ?>"
							    data-image="<?php echo $correct_image; ?>">
								<span class="quiz-answer__icon"></span>
								<span class="quiz-answer__text"><?php echo $option_title; ?></span>
							</li>
						
						<?php endwhile; ?>
					</ul>
					<!-- Answer END -->
				<?php endif; ?>
			</div>
		</div>
		
		<div class="quiz-step__navigation">
			<?php
			// echo $game_question_steps>1?$game_question_steps-1:1;
			//echo $game_question_steps<$max_step_count?$game_question_steps+1:$game_question_steps; ?>
			<?php if( $game_question_steps !== 1 ) : ?>
				<a class="btn quiz-step-back btn-light-menu<?php echo $game_question_steps==1?' no-active':''; ?>"
				   href="#">Question Precedente</a>
			<?php endif; ?>
			<a class="btn quiz-step-next btn-light-menu"
			   href="#"><?php echo __('VALIDER MA REPONSE', ca_textdomain); ?></a>
		</div>
	</div>
</section>



