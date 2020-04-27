<?php
global $post;
$game_question_steps = get_row_index();
$max_step_count = count(get_field('game_questions_flexible'));
$post_id = $post->ID;
$question_image = get_sub_field('question_image');
$question_title = get_sub_field('question_title');
$is_multiple = get_sub_field('multiple_answer');
?>
<section id="quiz_<?php echo $game_question_steps; ?>"
         class="quiz-step simple-quiz <?php
         echo $game_question_steps==1 ? 'current' : '';
         echo $game_question_steps==$max_step_count?' last_quiz_step' :'';
         ?>"
         data-step="<?php echo $game_question_steps; ?>">
	<div class="quiz-step__controls"></div>
	<div class="quiz-step__container">
		<div class="quiz-step__questions">
			<div class="quiz-step__media images">
				<img src="<?php echo $question_image; ?>" alt="<?php echo $question_title?:'Media'; ?>" loading="lazy">
			</div>
			<div class="quiz-step__content">
				<div class="quiz-step__meta">
					<div class="quiz-step__media-type"><?php echo __('Question', ca_textdomain); ?></div>
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
							$correct_image = get_sub_field('correct_image');
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
			<a class="btn quiz-step-back btn-light-menu<?php echo $game_question_steps==1?' no-active':''; ?>"
			   href="#">Question Precedente</a>
			<a class="btn quiz-step-next btn-light-menu"
			   href="#">VAlder ma reponse</a>
		</div>
	</div>
</section>



