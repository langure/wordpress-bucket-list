(function ($) {
	'use strict';

	$(document).ready(function () {
		// Category filtering
		$('.wbl-filter-btn').on('click', function () {
			const $btn = $(this);
			const category = $btn.data('category');

			// Update active button
			$('.wbl-filter-btn').removeClass('active');
			$btn.addClass('active');

			// Filter cards with animation
			const $cards = $('.wbl-card');

			if (category === 'all') {
				$cards
					.removeClass('wbl-hidden')
					.css({
						opacity: '0',
						transform: 'scale(0.8)',
					})
					.animate(
						{
							opacity: '1',
						},
						{
							duration: 300,
							step: function (now) {
								$(this).css('transform', 'scale(' + (0.8 + now * 0.2) + ')');
							},
						}
					);
			} else {
				$cards.each(function () {
					const $card = $(this);
					const hasCategory = $card.hasClass('wbl-cat-' + category);

					if (hasCategory) {
						$card
							.removeClass('wbl-hidden')
							.css({
								opacity: '0',
								transform: 'scale(0.8)',
							})
							.animate(
								{
									opacity: '1',
								},
								{
									duration: 300,
									step: function (now) {
										$(this).css(
											'transform',
											'scale(' + (0.8 + now * 0.2) + ')'
										);
									},
								}
							);
					} else {
						$card.addClass('wbl-hidden');
					}
				});
			}
		});

		// Animate progress bars on scroll
		const animateProgressBars = function () {
			$('.wbl-progress-fill').each(function () {
				const $bar = $(this);
				const targetWidth = $bar.css('width');

				if (!$bar.data('animated')) {
					const elementTop = $bar.offset().top;
					const elementBottom = elementTop + $bar.outerHeight();
					const viewportTop = $(window).scrollTop();
					const viewportBottom = viewportTop + $(window).height();

					if (elementBottom > viewportTop && elementTop < viewportBottom) {
						$bar.data('animated', true);
						$bar.css('width', '0');
						setTimeout(function () {
							$bar.css('width', targetWidth);
						}, 100);
					}
				}
			});
		};

		// Trigger on scroll and load
		$(window).on('scroll', animateProgressBars);
		animateProgressBars();

		// Smooth scroll for links
		$('.wbl-link').on('click', function (e) {
			const href = $(this).attr('href');
			if (href && href.indexOf('#') === 0) {
				e.preventDefault();
				$('html, body').animate(
					{
						scrollTop: $(href).offset().top - 100,
					},
					500
				);
			}
		});

		// Keyboard navigation for filter buttons
		$('.wbl-filter-btn').on('keypress', function (e) {
			if (e.which === 13 || e.which === 32) {
				// Enter or Space
				e.preventDefault();
				$(this).click();
			}
		});

		// Add loading state class
		const addLoadingState = function ($element) {
			$element.addClass('wbl-loading');
		};

		const removeLoadingState = function ($element) {
			$element.removeClass('wbl-loading');
		};

		// Expose functions for potential AJAX operations
		window.wblFrontend = {
			addLoadingState: addLoadingState,
			removeLoadingState: removeLoadingState,
		};
	});
})(jQuery);
