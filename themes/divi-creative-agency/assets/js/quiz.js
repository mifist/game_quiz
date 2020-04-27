(function ($) {
	"use strict"; // this code works in modern mode
	
	$.fn.equalHeight = function () {
		var tallest = 180;
		this.each(function () {
			var thisHeight = $(this).height();
			if (thisHeight > tallest) {
				tallest = thisHeight;
			}
		});
		return this.height(tallest);
	};
	
	/**
	* Get quiz list for pagination
	* */
	const quiz_html = ( select_wrap, ajax_data ) => {
		$.ajax({
			url : quiz_ajax_params.quiz_ajaxurl,
			type : 'POST',
			data : ajax_data,
			beforeSend: function( xhr){
				$('body').addClass('loading');
			},
			success: function( data ) {
				if ( data ) {
					select_wrap.html(data);
				}
				
			},
			complete: function () {}
		});
	};
	
	/**
	 *  Custom like counter for posts
	 * */
	const save_quiz_score = ( ajax_data ) => {
		
		$.ajax({
			url : quiz_ajax_params.quiz_ajaxurl,
			type : 'POST',
			data : ajax_data,
			beforeSend: function( xhr){
				$('body').addClass('loading');
			},
			success: function( res ) {
				//let data = $.parseJSON( res );
				if ( res ) {
					//console.log(res);
				}
			},
			complete: function () {}
		});
		
	};
	
	// show current step/hide other steps
	const updateStep = ( currentStep ) => {
		if(currentStep.hasClass('current')){
			currentStep.removeClass('current');
			currentStep.next('.quiz-step').addClass('current');
		}
	};
	const backToStep = ( currentStep ) => {
		var data_steps =  currentStep.attr('data-step');
		if(data_steps > 1){
			if(currentStep.hasClass('current')){
				currentStep.removeClass('current');
				currentStep.prev('.quiz-step').addClass('current');
				if ( currentStep.prev('.quiz-step').hasClass('valid') ) {
					currentStep.prev('.quiz-step').find('.answers').find('.quiz-answer').map((index, element) => {
						$(element).addClass('no-active');
					});
				}
			}
		}
	};
	const nextStep = ( currentStep, lastItems ) => {
		var data_steps = currentStep.attr('data-step'),
			data_steps_last =  lastItems.attr('data-step');
		if  ( data_steps !== data_steps_last ) {
			if ( currentStep.hasClass('current') ) {
				currentStep.removeClass('current');
				currentStep.next('.quiz-step').addClass('current');
			}
		}
	};
	
	
	$(window).on("load resize orientationChange", (function() {
		if ( $(window).width() > 640 ) {
			$(document).find('.quiz-container .quiz-item').equalHeight();
		}
	}));
	
	$(document).ready(function () {
		/*
		* Quiz pagination
		* */
		$(document).on('click', '.quiz-pagination a', function (e) {
			e.preventDefault();
			let _this = $(this),
				_page = $(_this).text();
			let select_wrap = $(document).find('.quiz-section'),
				ajax_data = {
					action: 'quiz_pagination',
					security : quiz_ajax_params.quiz_security,
					page: parseInt( _page ),
					quiz_query: quiz_true_posts,
					max_pages : parseInt( quiz_max_pages )
				};
			quiz_html( select_wrap, ajax_data );
		});
		
		
		/**
		 *  Custom like counter for posts
		 * */
		$(document).find( ".save_quiz_score" ).on('click', function (e) {
			e.preventDefault();
			let post_id = $(this).closest('.ca_single_game').data('game_id'),
				quiz_wrap = $(this).closest('.quiz-step'),
				current_score = quiz_wrap.find('.nuber-question > .total-quiz').text(),
				total_score = quiz_wrap.find('.nuber-question > .total-quiz').text(),
				ajax_data = {
					action: 'save_game_score',
					security : quiz_ajax_params.quiz_security,
					post_id : parseInt( post_id ),
					current_score : parseInt( current_score ),
					total_score : parseInt( total_score )
				};
			save_quiz_score( ajax_data );
		});
		
		
	/* Quiz func START */
		
		// show quiz wrapper
		$(document).find('.single-game__play-btn').on('click', function (e) {
			e.preventDefault();
			let _this = $(this),
				_quiz_wrap = $(_this).attr('href');
			 $(_quiz_wrap).addClass('show');
			_this.parents('.ca_single_game').find('.single-game__preview').removeClass('show').addClass('hide');
		});
		
		// Quiz global variables
		var quizSteps = $('#quiz-container .quiz-step'),
			lastItems = quizSteps.last(),
			totalScore = 0;
		
		/*
		* for each step in the quiz, add the selected answer value to the total score
		* if an answer has already been selected, subtract the previous value and
		* update total score with the new selected answer value toggle a visual active state
		* to show which option has been selected
		* */
		quizSteps.each(function () {
			var currentStep = $(this),
				quizCount = $('.quiz-step').length,
				back_to_step = currentStep.find('.quiz-step-back'),
				next_step = currentStep.find('.quiz-step-next'),
				hasBeenClicked = false,
				hasClickedAnswer = false,
				ansOpts = currentStep.children('.quiz-answer'),
				quizScore = 0,
				max_quizScore = 0,
				answer_media_src = '';
				answer_media_src = '';
		
			currentStep.on('click', '.quiz-answer', function (e) {
				if ( $(this).parent('.answers').hasClass('is_multiple') ) {
					$(this).toggleClass('active');
				} else {
					currentStep.find('.answers .quiz-answer').removeClass('active');
					$(this).toggleClass('active');
				}
				hasClickedAnswer = true;
				$(this).parents('.quiz-step').find('.error-msg').removeClass('show');
			});
			
			let song = currentStep.find(".media-audio").get(0);
			currentStep.find(".quiz-step__media-play:not(.btn-cta)").on("click",function(e) {
				e.preventDefault();
				song.play();
				$(this).addClass("active");
				currentStep.find(".quiz-step__media-pause").removeClass("active");
			});
			currentStep.find(".quiz-step__media-pause:not(.btn-cta)").on("click",function(e) {
				e.preventDefault();
				song.pause();
				$(this).addClass("active");
				currentStep.find(".quiz-step__media-play").removeClass("active");
			});
			
			back_to_step.on('click', function (e) {
				e.preventDefault();
				quizScore = 0;
				max_quizScore = 0;
				hasBeenClicked = true;
				backToStep( currentStep );
			});
			
			// for each option per step, add a click listener
			// apply active class and calculate the total score
			next_step.on('click', function (e) {
				e.preventDefault();
				
				let _this = $(this),
					parent = _this.parents('.quiz-step'),
					erroe_msg = parent.find('.error-msg'),
					answer_wrap = parent.find('.answers');
				
				if ( currentStep.hasClass('valid') ) {
					answer_wrap.find('.quiz-answer').map((index, element) => {
						$(element).addClass('no-active');
					});
				}
				
				answer_wrap.find('.quiz-answer').map((index, element) => {
					  let el = $(element),
						  answer = el.data('answer'),
						  answer_image = el.data('image');
					  
					if ( !el.hasClass('active') && !hasClickedAnswer ) {
						erroe_msg.addClass('show');
					} else if ( answer>0 ) {
						el.addClass('show-correct-answer');
						answer_media_src = answer_image;
						max_quizScore++;
						if ( el.hasClass('active') ) {
							el.addClass('correct');
							quizScore++;
						}
					} else if ( answer===0  ) {
						el.addClass('incorrect');
						if ( el.hasClass('active') ) {
							quizScore--;
						}
					}
					$(element).addClass('no-active');
					
				});
				
				if ( hasClickedAnswer && !_this.hasClass('validate') ) {
					//console.log({answer_media_src});
					if ( currentStep.hasClass('video-quiz')  ) {
						parent.find('.quiz-step__media-play').addClass('changed');
						parent.find('.quiz-step__controls .modal-media').map((index, element) => {
							let el = $(element);
							if ( !el.hasClass('correct') ) {
								el.removeClass('show');
							} else {
								el.addClass('show');
							}
						});
					} 
					
					if ( currentStep.hasClass('music-quiz') ) {
						parent.find('.quiz-step__media-play').addClass('changed');
						let _audio = parent.find('.quiz-step__controls .media-audio').get(0);
						_audio.pause();
						_audio.src = parent.find('.media-audio-correct').data('correct');
						_audio.load();
					}
					
					parent.find('.quiz-step__media.images > img').attr('src', answer_media_src);
					setTimeout(function () {
						_this.addClass('validate');
					}, 1000);
				}
				
				if ( _this.hasClass('validate') ) {
					currentStep.addClass('valid');
					hasBeenClicked = true;
					answer_media_src = '';
					if ( quizScore>0 && ( quizScore === max_quizScore ) && hasClickedAnswer ) {
						quizScore = 0;
						max_quizScore = 0;
						totalScore++;
					}
					nextStep(currentStep, lastItems );
				}
				
				//console.log({quizScore});
				//console.log({max_quizScore});
				//console.log({totalScore});
				
				if ( parent.hasClass('last_quiz_step') && _this.hasClass('validate') ) {
					_this.addClass('no-active');
					let post_id = parent.closest('.ca_single_game').data('game_id'),
						quiz_wrap = parent,
						current_score = totalScore,
						total_score = quiz_wrap.find('.nuber-question > .total-quiz').text(),
						maxScore = parent.find('.nuber-question > .total-quiz').text(),
						article = $(this).parents('.ca_single_game'),
						ajax_data = {
							action: 'save_game_score',
							security : quiz_ajax_params.quiz_security,
							post_id : parseInt( post_id ),
							current_score : parseInt( current_score ),
							total_score : parseInt( total_score )
						};
					//console.log({ajax_data});
					save_quiz_score( ajax_data );
					
					article.find('.preview-content__current-score .game-score').text(totalScore);
					article.find('.preview-content__current-score .game-score-total').text(maxScore);
					
					article.find('#quiz-container').removeClass('show').addClass('hide');
					article.find('.single-game__finish').addClass('show');
					
				}
			
			});
		
		});
	/* Quiz func END */
		
		/**
		 *  Custom modals
		 * */
		$(document).find('.btn-cta').click(function (e) {
			e.preventDefault();
			let href = $(this).attr('href'),
				modal = $(document).find(href);
			modal.addClass('show');
			modal.parents('.cta-modal-window').find('#cta_overlay').addClass('show');
			modal.find('.modal-media.show iframe').addClass('youtube-video');
		});
		
		// close modals
		$(document).find('.cta-close, #cta_overlay').click(function (e) {
			e.preventDefault();
			let _video_wrap = $(this).parents('.cta-modal-window').find('.modal-media.show'),
				reSrc = _video_wrap.find('iframe.youtube-video').attr("src");
			_video_wrap.find('iframe.youtube-video').attr("src", reSrc);
			
			$(document).find('.cta_modal, #cta_overlay').removeClass('show');
		});
		// end ->> Custom modals
		
		
	});
	
})(jQuery);

