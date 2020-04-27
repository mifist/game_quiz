<?php
global $post;
$post_id = $post->ID;
$layout = get_row_layout();
if( $layout == 'simple_question' ): ?>
	
	<!-- Simple Question -->
	<?php get_template_part( 'quiz-parts/part', 'simple_question' ); ?>
	<!-- End Simple Question -->

<?php elseif( $layout == 'video_question' ): ?>
	
	<!-- Video Question -->
	<?php get_template_part( 'quiz-parts/part', 'video_question' ); ?>
	<!-- End Video Question -->

<?php elseif( $layout == 'music_question' ): ?>
	
	<!-- Music Question -->
	<?php get_template_part( 'quiz-parts/part', 'music_question' ); ?>
	<!-- End Music Question -->

<?php endif; ?>