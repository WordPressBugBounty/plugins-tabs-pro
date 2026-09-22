/**
 * TPTabs Ultimate Engine
 * Lightweight tab switcher supporting horizontal/vertical layouts, autoHeight, autoOrder, autoplay, and hash linking.
 */
(function($) {
	'use strict';

	if ($.fn.tpTabsEngine) {
		return;
	}

	$.fn.tpTabsEngine = function(options) {
		var settings = $.extend({
			initialTab: 1,
			event: 'click',
			direction: 'horizontal',
			autoHeight: true,
			animSpeed: 300,
			autoOrder: true,
			autoplayInterval: 0,
			inAnimation: 'fadeIn',
			useHash: true
		}, options);

		return this.each(function() {
			var $container    = $(this);
			var $navItems     = $container.find('.tptabs-nav li');
			var $tabs         = $container.find('.tptabs-nav li a');
			var $wrapper      = $container.find('.tptabs-content-wrapper');
			var $contents     = $wrapper.find('.tab_content');
			var autoplayTimer = null;

			if (!$contents.length) return;

			// 1. AutoOrder Indexing
			if (settings.autoOrder) {
				$tabs.each(function(index) {
					$(this).data('tab-index', index);
				});
				$contents.each(function(index) {
					$(this).data('tab-index', index);
				});
			}

			// Smooth Height Adjuster
			function updateHeight($target) {
				if (settings.autoHeight) {
					var newHeight = $target.outerHeight(true);
					$wrapper.stop().animate({ height: newHeight + 'px' }, settings.animSpeed);
				}
			}

			// 2. Determine Initial Tab State (including URL Hash check)
			var defaultIndex = Math.max(0, parseInt(settings.initialTab, 10) - 1);
			if (defaultIndex >= $contents.length) { defaultIndex = 0; }

			if (settings.useHash && window.location.hash) {
				var hash = window.location.hash;
				$tabs.each(function(index) {
					if ($(this).attr('href') === hash) {
						defaultIndex = index;
						return false;
					}
				});
			}

			$contents.hide().removeClass('active-tab-panel');
			$navItems.removeClass('active');

			$navItems.eq(defaultIndex).addClass('active');
			var $initialContent = settings.autoOrder ? $contents.eq(defaultIndex) : $($tabs.eq(defaultIndex).attr('href'));
			$initialContent.show().addClass('active-tab-panel');
			
			setTimeout(function() {
				updateHeight($initialContent);
			}, 50);

			// 3. Tab Switching Core
			function activateTab(index, updateHash) {
				var $targetNav     = $navItems.eq(index);
				var $targetContent = settings.autoOrder ? $contents.eq(index) : $($tabs.eq(index).attr('href'));

				if ($targetNav.hasClass('active')) return;

				$navItems.removeClass('active');
				$targetNav.addClass('active');

				$contents.stop(true, true).hide().removeClass('active-tab-panel');

				// Update URL hash if enabled
				if (settings.useHash && updateHash !== false) {
					var targetHref = $tabs.eq(index).attr('href');
					if (targetHref && targetHref.indexOf('#') === 0) {
						if (history.pushState) {
							history.pushState(null, null, targetHref);
						} else {
							window.location.hash = targetHref;
						}
					}
				}

				switch (settings.inAnimation) {
					case 'slideDown':
						$targetContent.slideDown(settings.animSpeed, function() {
							$(this).addClass('active-tab-panel');
							updateHeight($(this));
						});
						break;

					case 'zoomIn':
						$targetContent.css({ opacity: 0, transform: 'scale(0.96)' }).show();
						$targetContent.animate({ opacity: 1 }, settings.animSpeed, function() {
							$(this).css({ transform: 'none' }).addClass('active-tab-panel');
							updateHeight($(this));
						});
						break;

					case 'none':
						$targetContent.show().addClass('active-tab-panel');
						updateHeight($targetContent);
						break;

					case 'fadeIn':
					default:
						$targetContent.fadeIn(settings.animSpeed, function() {
							$(this).addClass('active-tab-panel');
							updateHeight($(this));
						});
						break;
				}
			}

			// 4. Trigger Bindings
			$tabs.on('click', function(e) {
				e.preventDefault();
				var idx = settings.autoOrder ? $(this).data('tab-index') : $tabs.index(this);
				activateTab(idx, true);
			});

			if (settings.event === 'hover') {
				$tabs.on('mouseenter', function() {
					var idx = settings.autoOrder ? $(this).data('tab-index') : $tabs.index(this);
					activateTab(idx, true);
				});
			}

			// Listen for back/forward browser button hash changes
			$(window).on('hashchange', function() {
				if (settings.useHash && window.location.hash) {
					var hash = window.location.hash;
					$tabs.each(function(index) {
						if ($(this).attr('href') === hash) {
							activateTab(index, false);
							return false;
						}
					});
				}
			});

			// 5. Autoplay & Hover Pause
			if (parseInt(settings.autoplayInterval, 10) > 0) {
				function startAutoplay() {
					stopAutoplay();
					autoplayTimer = setInterval(function() {
						var currentIndex = $navItems.filter('.active').index();
						var nextIndex    = (currentIndex + 1) % $navItems.length;
						activateTab(nextIndex, false);
					}, parseInt(settings.autoplayInterval, 10));
				}

				function stopAutoplay() {
					if (autoplayTimer) {
						clearInterval(autoplayTimer);
						autoplayTimer = null;
					}
				}

				startAutoplay();
				$container.on('mouseenter', stopAutoplay).on('mouseleave', startAutoplay);
			}

			// Resize Handler
			$(window).on('resize', function() {
				var $current = $contents.filter('.active-tab-panel');
				if ($current.length && settings.autoHeight) {
					$wrapper.css('height', $current.outerHeight(true) + 'px');
				}
			});
		});
	};
})(jQuery);