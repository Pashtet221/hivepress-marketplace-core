(function ($) {
	'use strict';

	var apiPromise;

	function loadAPI() {
		if (window.ymaps) {
			return Promise.resolve(window.ymaps);
		}

		if (apiPromise) {
			return apiPromise;
		}

		apiPromise = new Promise(function (resolve, reject) {
			var script = document.createElement('script'),
				params = new URLSearchParams({
					apikey: hivepressGeolocationData.apiKey || '',
					lang: hivepressGeolocationData.language,
				});

			script.src = hivepressGeolocationData.apiURL + '?' + params.toString();
			script.async = true;
			script.onload = function () {
				window.ymaps.ready(function () {
					resolve(window.ymaps);
				});
			};
			script.onerror = reject;
			document.head.appendChild(script);
		});

		return apiPromise;
	}

	function setLocation(field, latitudeField, longitudeField, result) {
		var coordinates = result.geometry.getCoordinates();

		field.val(result.getAddressLine() || result.properties.get('name'));
		latitudeField.val(coordinates[0]);
		longitudeField.val(coordinates[1]);
	}

	function initLocation(container, ymaps) {
		container.find(hivepress.getSelector('location')).not('[data-yandex-ready]').each(function () {
			var location = $(this),
				form = location.closest('form'),
				field = location.find('input[type=text]'),
				latitudeField = form.find('input[data-coordinate=lat]'),
				longitudeField = form.find('input[data-coordinate=lng]'),
				regionField = form.find('input[data-region]'),
				button = location.find('a'),
				countries = location.data('countries') || [];

			location.attr('data-yandex-ready', 'true');

			field.autocomplete({
				minLength: 3,
				delay: 250,
				source: function (request, response) {
					var options = { results: 7 };

					if (countries.length === 1 && countries[0].toLowerCase() === 'ru') {
						options.boundedBy = [[41.185, 19.638], [81.858, 180]];
						options.strictBounds = true;
					}

					ymaps.suggest(request.term, options).then(function (items) {
						response(items.map(function (item) {
							return { label: item.displayName, value: item.value };
						}));
					}, function () {
						response([]);
					});
				},
				select: function (event, ui) {
					event.preventDefault();
					field.val(ui.item.value);

					ymaps.geocode(ui.item.value, { results: 1 }).then(function (result) {
						var object = result.geoObjects.get(0);

						if (object) {
							setLocation(field, latitudeField, longitudeField, object);
							regionField.val('');
						}
					});
				},
				open: function () {
					$(this).autocomplete('widget').width(field.outerWidth());
				},
			});

			field.on('input', function () {
				if (field.val().length <= 1) {
					form.find('input[data-coordinate]').val('');
					regionField.val('');
				}
			});

			field.on('focusout', function () {
				if (!latitudeField.val() || !longitudeField.val()) {
					field.val('');
				}
			});

			if (navigator.geolocation) {
				button.on('click', function (event) {
					event.preventDefault();
					navigator.geolocation.getCurrentPosition(function (position) {
						ymaps.geocode([position.coords.latitude, position.coords.longitude], { results: 1 }).then(function (result) {
							var object = result.geoObjects.get(0);

							if (object) {
								setLocation(field, latitudeField, longitudeField, object);
								regionField.val('');
							}
						});
					});
				});
			} else {
				button.hide();
			}
		});
	}

	function initMap(container, ymaps) {
		container.find(hivepress.getSelector('map')).not('[data-yandex-ready]').each(function () {
			var element = $(this),
				height = element.is('[data-height]') ? element.data('height') : element.width(),
				maxZoom = Number(element.data('max-zoom')),
				markerIcon = element.data('marker'),
				markers = element.data('markers') || [],
				geoObjects = [],
				map;

			element.attr('data-yandex-ready', 'true').height(height);

			map = new ymaps.Map(element.get(0), {
				center: [55.751244, 37.618423],
				zoom: 3,
				controls: ['zoomControl', 'fullscreenControl'],
			}, {
				maxZoom: maxZoom,
				suppressMapOpenBlock: true,
			});

			markers.forEach(function (data) {
				var options = {}, placemark;

				if (markerIcon) {
					options = {
						iconLayout: 'default#image',
						iconImageHref: markerIcon,
						iconImageSize: [50, 50],
						iconImageOffset: [-25, -25],
					};
				} else if (element.data('scatter')) {
					options.preset = 'islands#circleIcon';
					options.iconColor = '#3a77ff';
				}

				placemark = new ymaps.Placemark([Number(data.latitude), Number(data.longitude)], {
					balloonContent: data.content,
					hintContent: data.title,
				}, options);

				geoObjects.push(placemark);
			});

			if (geoObjects.length) {
				var clusterer = new ymaps.Clusterer({
					clusterDisableClickZoom: false,
					clusterOpenBalloonOnClick: true,
					gridSize: 64,
					maxZoom: Math.max(1, maxZoom - 1),
				});

				clusterer.add(geoObjects);
				map.geoObjects.add(clusterer);
				if (geoObjects.length === 1) {
					map.setCenter(geoObjects[0].geometry.getCoordinates(), Math.min(15, maxZoom - 1));
				} else {
					map.setBounds(clusterer.getBounds(), {
						checkZoomRange: true,
						zoomMargin: 50,
					});
				}
			}

			if (window.ResizeObserver) {
				new ResizeObserver(function () {
					map.container.fitToViewport();
				}).observe(element.get(0));
			}
		});
	}

	hivepress.initYandexGeolocation = function (container) {
		var locations = container.find(hivepress.getSelector('location')),
			maps = container.find(hivepress.getSelector('map'));

		if (!locations.length && !maps.length) {
			return;
		}

		loadAPI().then(function (ymaps) {
			initLocation(container, ymaps);
			initMap(container, ymaps);
		});
	};

	$(document).on('hivepress:init', function (event, container) {
		hivepress.initYandexGeolocation(container);
	});
})(jQuery);
