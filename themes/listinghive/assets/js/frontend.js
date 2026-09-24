(function($) {
	'use strict';

	var listingDetailsCount = 0,
		listingDetailsLabels = {
			show: 'Показать характеристики',
			hide: 'Скрыть характеристики'
		};

	function showListingImage(container, index) {
		var slides = container.querySelectorAll('.hp-listing__image-slide'),
			dots = container.querySelectorAll('.hp-listing__image-dot');

		if (!slides.length) {
			return;
		}

		index = Math.max(0, Math.min(index, slides.length - 1));
		container.dataset.imageIndex = index;

		slides.forEach(function(slide, slideIndex) {
			var active = slideIndex === index;
			slide.classList.toggle('hp-listing__image-slide--active', active);
			slide.setAttribute('aria-hidden', active ? 'false' : 'true');
		});

		dots.forEach(function(dot, dotIndex) {
			dot.classList.toggle('hp-listing__image-dot--active', dotIndex === index);
		});
	}

	function initListingDetails(scope) {
		var cards = scope.querySelectorAll ? Array.prototype.slice.call(scope.querySelectorAll('.hp-listing--view-block')) : [];

		if (scope.matches && scope.matches('.hp-listing--view-block')) {
			cards.unshift(scope);
		}

		cards.forEach(function(card) {
			var details = card.querySelector('.hp-listing__attributes--secondary'),
				button,
				detailsID;

			if (!details || details.dataset.detailsToggle) {
				return;
			}

			listingDetailsCount += 1;
			detailsID = 'listing-details-' + listingDetailsCount;
			details.id = detailsID;
			details.hidden = true;
			details.dataset.detailsToggle = 'true';

			button = document.createElement('button');
			button.type = 'button';
			button.className = 'hp-listing__details-toggle';
			button.setAttribute('aria-controls', detailsID);
			button.setAttribute('aria-expanded', 'false');
			button.textContent = listingDetailsLabels.show;
			details.parentNode.insertBefore(button, details);
		});
	}

	document.addEventListener('click', function(event) {
		var button = event.target.closest('.hp-listing__details-toggle'),
			details,
			isExpanded;

		if (!button) {
			return;
		}

		details = document.getElementById(button.getAttribute('aria-controls'));

		if (!details) {
			return;
		}

		isExpanded = button.getAttribute('aria-expanded') === 'true';
		button.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
		button.textContent = isExpanded ? listingDetailsLabels.show : listingDetailsLabels.hide;
		details.hidden = isExpanded;
	});

	initListingDetails(document);

	new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			mutation.addedNodes.forEach(function(node) {
				if (node.nodeType === 1) {
					initListingDetails(node);
				}
			});
		});
	}).observe(document.body, { childList: true, subtree: true });

	document.addEventListener('pointermove', function(event) {
		var container = event.target.closest('.hp-listing__image--preview'),
			bounds,
			index;

		if (!container || event.pointerType === 'touch' || Number(container.dataset.imageCount) < 2) {
			return;
		}

		bounds = container.getBoundingClientRect();
		index = Math.floor(((event.clientX - bounds.left) / bounds.width) * Number(container.dataset.imageCount));
		showListingImage(container, index);
	});

	document.addEventListener('pointerleave', function(event) {
		if (event.target.classList && event.target.classList.contains('hp-listing__image--preview')) {
			showListingImage(event.target, 0);
		}
	}, true);

	document.addEventListener('keydown', function(event) {
		var container = event.target.closest('.hp-listing__image--preview'),
			index;

		if (!container || (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight')) {
			return;
		}

		index = Number(container.dataset.imageIndex || 0) + (event.key === 'ArrowRight' ? 1 : -1);
		showListingImage(container, index);
		event.preventDefault();
	});

	$('body').imagesLoaded(function() {

		// Parallax
		hivetheme.getComponent('parallax').each(function() {
			var container = $(this),
				background = container.css('background-image'),
				offset = container.offset().top,
				speed = 0.25;

			if ($('#wpadminbar').length) {
				offset = offset - $('#wpadminbar').height();
			}

			if ($(window).width() >= 1024 && background.indexOf('url') === 0) {
				container.css('background-position-y', ($(window).scrollTop() - offset) * speed);

				$(window).on('scroll', function() {
					container.css('background-position-y', ($(window).scrollTop() - offset) * speed);
				});
			}
		});
	});
})(jQuery);
