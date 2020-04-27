<?php

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );
$show_navigation = get_post_meta( get_the_ID(), '_et_pb_project_nav', true );

?>

<div id="main-content">
<?php if ( ! $is_page_builder_used ) : ?>
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="game-<?php the_ID(); ?>" <?php post_class('ca_single_game'); ?>
			         data-game_id="<?php the_ID(); ?>">
				
				<!-- Game Preview -->
				<?php get_template_part( 'quiz-parts/single', 'game_preview' ); ?>
				<!-- END Game Preview -->
				
				<?php if( have_rows( 'game_questions_flexible' ) ): ?>
					<!--== Global Flexible Content -- START ==-->
					<div id="quiz-container">
						<?php while ( have_rows( 'game_questions_flexible' ) ) : the_row(); ?>
							<?php get_template_part( 'quiz-parts/part', 'game_questions' ); ?>
						<?php endwhile; ?>
					</div>
					<!--== Global Flexible Content -- END ==-->
				<?php endif; ?>
				
				<!-- Game Preview -->
				<?php get_template_part( 'quiz-parts/single', 'game_finish' ); ?>
				<!-- END Game Preview -->
				
<!--
				<div class="nav-single clearfix">
					<span class="nav-previous"><?php /*previous_post_link( '%link', '<span class="meta-nav">' . et_get_safe_localization( _x( '&larr;', 'Previous post link', 'Divi' ) ) . '</span> %title' ); */?></span>
					<span class="nav-next"><?php /*next_post_link( '%link', '%title <span class="meta-nav">' . et_get_safe_localization( _x( '&rarr;', 'Next post link', 'Divi' ) ) . '</span>' ); */?></span>
				</div>--><!-- .nav-single -->

				
			</article> <!-- .ca_single_game -->
		<?php endwhile; ?>
	</div> <!-- .container -->
<?php else : ?>
	<div class="entry-content">
		<?php the_content(); ?>
	</div> <!-- .entry-content -->
<?php endif; ?>

</div> <!-- #main-content -->

<?php

get_footer();
