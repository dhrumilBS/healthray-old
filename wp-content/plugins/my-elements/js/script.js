// =============================================================================
//  Pure JS — no jQuery dependency
//  owl-carousel init still calls the owlCarousel() jQuery plugin directly
//  (owl carousel is a jQuery plugin and has no pure-JS equivalent bundled here)
// =============================================================================

// ─── Utility: pure-JS slideToggle ────────────────────────────────────────────
function slideDown(el) {
	el.style.display    = 'block';
	el.style.overflow   = 'hidden';
	el.style.height     = '0';
	el.style.transition = 'height 0.3s ease';
	var target = el.scrollHeight;
	requestAnimationFrame(function () {
		el.style.height = target + 'px';
		el.addEventListener('transitionend', function cleanup() {
			el.style.height    = '';
			el.style.overflow  = '';
			el.style.transition = '';
			el.removeEventListener('transitionend', cleanup);
		});
	});
}

function slideUp(el) {
	el.style.overflow   = 'hidden';
	el.style.height     = el.scrollHeight + 'px';
	el.style.transition = 'height 0.3s ease';
	requestAnimationFrame(function () {
		el.style.height = '0';
		el.addEventListener('transitionend', function cleanup() {
			el.style.display   = 'none';
			el.style.height    = '';
			el.style.overflow  = '';
			el.style.transition = '';
			el.removeEventListener('transitionend', cleanup);
		});
	});
}

function slideToggle(el, callback) {
	var hidden = el.style.display === 'none' || getComputedStyle(el).display === 'none';
	if (hidden) {
		slideDown(el);
		if (callback) el.addEventListener('transitionend', function once() {
			el.removeEventListener('transitionend', once);
			callback(true);
		});
	} else {
		slideUp(el);
		if (callback) el.addEventListener('transitionend', function once() {
			el.removeEventListener('transitionend', once);
			callback(false);
		});
	}
}

// ─── DOMContentLoaded ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

	// =========================================== BLOG FAQ ===========================================
	document.addEventListener('click', function (e) {
		var toggle = e.target.closest('.accordion-list > .elementor-toggle-item .elementor-tab-title');
		if (!toggle) return;
		e.preventDefault();

		var item    = toggle.closest('.elementor-toggle-item');
		var content = item ? item.querySelector('.elementor-tab-content') : null;
		if (!content) return;

		if (toggle.classList.contains('elementor-active')) {
			toggle.classList.remove('elementor-active');
			content.classList.remove('elementor-active');
			slideUp(content);
		} else {
			toggle.classList.add('elementor-active');
			content.classList.add('elementor-active');
			slideUp(content);   // close first (matches original behaviour)
			slideDown(content);
		}
	});

	// =========================================== owl-carousel ===========================================
	if (document.querySelector('.owl-carousel')) {
		owl_carousel();
	}
});

// ============================= owl-carousel (jQuery plugin wrapper) ===========
// owlCarousel() is a jQuery plugin — jQuery calls here are unavoidable.
// All surrounding DOM/event logic is pure JS.
function owl_carousel(e) {
	var carousels = document.querySelectorAll('.owl-carousel');
	carousels.forEach(function (el) {
		var $el = jQuery(el); // jQuery required by owl carousel plugin only

		var prev = '<span><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.99983 1L3.05284 4.87375C3.03612 4.89016 3.02283 4.90979 3.01375 4.93147C3.00468 4.95316 3 4.97646 3 5C3 5.02354 3.00468 5.04684 3.01375 5.06853C3.02283 5.09021 3.03612 5.10984 3.05284 5.12625L7 9" stroke="currentcolor" stroke-linecap="round"/></svg></span>';
		var next = '<span><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.00017 9L6.94716 5.12625C6.96388 5.10984 6.97717 5.09021 6.98625 5.06853C6.99532 5.04684 7 5.02354 7 5C7 4.97646 6.99532 4.95316 6.98625 4.93147C6.97717 4.90979 6.96388 4.89016 6.94716 4.87375L3 1" stroke="currentcolor" stroke-linecap="round"/></svg></span>';

		var prevText = el.dataset.prev_text || 'Prev';
		var nextText = el.dataset.next_text || 'Next';
		if (prevText) prev = prevText;
		if (nextText) next = nextText;

		$el.owlCarousel({
			items:              el.dataset.desk_num,
			loop:               el.dataset.loop === 'true' || el.dataset.loop === '1',
			margin:             parseInt(el.dataset.margin) || 0,
			dots:               el.dataset.dots === 'true' || el.dataset.dots === '1',
			autoplay:           el.dataset.autoplay === 'true' || el.dataset.autoplay === '1',
			autoplayTimeout:    5000,
			autoplayHoverPause: true,
			nav:                el.dataset.nav === 'true' || el.dataset.nav === '1',
			autoHeight:         el.dataset.autoheight === 'true' || el.dataset.autoheight === '1',
			navText:            [prev, next],
			responsiveClass:    true,
			responsive: {
				0:    { items: parseInt(el.dataset.mob_sm)  || 1 },
				480:  { items: parseInt(el.dataset.mob_num) || 1 },
				786:  { items: parseInt(el.dataset.tab_num) || 2 },
				1023: { items: parseInt(el.dataset.lap_num) || 3 },
				1199: { items: parseInt(el.dataset.desk_num) || 3 }
			}
		});

		// ── Custom toggle tabs ──────────────────────────────────────
		var toggleTabs = document.querySelectorAll('.custom-toggle-tabs');
		toggleTabs.forEach(function (tabContainer) {
			var firstLi = tabContainer.querySelector('li');
			if (firstLi) firstLi.classList.add('active');

			tabContainer.addEventListener('click', function (e) {
				var li = e.target.closest('li');
				if (!li) return;
				var items = tabContainer.querySelectorAll('li');
				items.forEach(function (i) { i.classList.remove('active'); });
				li.classList.add('active');
				var idx = Array.from(items).indexOf(li);
				$el.trigger('to.owl.carousel', [idx, 300]);
			});
		});

		$el.on('changed.owl.carousel', function (event) {
			var currentIndex = event.item.index;
			document.querySelectorAll('.custom-toggle-tabs li').forEach(function (li, i) {
				li.classList.toggle('active', i === currentIndex);
			});
		});

		// ── Custom pagination ───────────────────────────────────────
		var dots = document.querySelectorAll('.custom-pagination .dot');

		dots.forEach(function (dot) {
			dot.addEventListener('click', function () {
				var slideIndex = parseInt(this.dataset.slide);
				$el.trigger('to.owl.carousel', [slideIndex, 300]);
				dots.forEach(function (d) { d.classList.remove('active'); });
				dot.classList.add('active');
			});
		});

		$el.on('changed.owl.carousel', function (event) {
			var currentIndex = event.item.index - event.relatedTarget._clones.length / 2;
			var realIndex    = currentIndex < 0
				? event.item.count - 1
				: currentIndex % event.item.count;
			dots.forEach(function (d) { d.classList.remove('active'); });
			if (dots[realIndex]) dots[realIndex].classList.add('active');
		});

		if (dots[0]) dots[0].classList.add('active');
	});
}

// ============================= TOGGLE LINK  ->  custom-toggle.php =============
function customToggle() {
	document.querySelectorAll('.toggleLink').forEach(function (link) {
		link.addEventListener('click', function (e) {
			e.preventDefault();
			var toggleList = this.nextElementSibling;
			while (toggleList && !toggleList.classList.contains('toggle-list')) {
				toggleList = toggleList.nextElementSibling;
			}
			if (!toggleList) return;

			var anchor = this; // capture for callback
			slideToggle(toggleList, function (isVisible) {
				anchor.textContent = isVisible ? 'Read less -' : 'Read more +';
			});
		});
	});
}

// ============================= Alternative table  ->  ml-alternative.php ======
function alternative(container) {
	var select = container.querySelector('#jsonFiles');
	if (!select) return;

	var yesImage = '<img width="26" height="26" decoding="async" style="width:26px;height:26px;" src="https://healthray.com/wp-content/themes/stratusx-child/assets/right.webp" alt="true" class="entered lazyloaded">';
	var noImage  = '<img width="26" height="26" decoding="async" style="width:26px;height:26px;" src="https://healthray.com/wp-content/themes/stratusx-child/assets/cross.webp" alt="cross" class="entered lazyloaded">';

	function mapYesNo(val) {
		var v = val.trim().toLowerCase();
		return v === 'yes' ? yesImage : v === 'no' ? noImage : val;
	}

	function tableContent(fileName) {
		fetch(fileName)
			.then(function (res) { return res.json(); })
			.then(function (jsonData) {
				if (!jsonData.img) return;
				var tbody = document.querySelector('#jsonData tbody');
				if (!tbody) return;
				tbody.innerHTML = '';
				jsonData.content.forEach(function (value) {
					var tr = document.createElement('tr');
					tr.innerHTML =
						'<td class="feature">' + value.key + '</td>' +
						'<td class="our">'     + mapYesNo(value.our)   + '</td>' +
						'<td class="other">'   + mapYesNo(value.other) + '</td>';
					tbody.appendChild(tr);
				});
			})
			.catch(function (err) { console.error('alternative table fetch error:', err); });
	}

	tableContent(select.value);
}

// ============================= New slider  ->  ml_slider =======================
function new_slider() {
	var items         = document.querySelectorAll('.left-column .item');
	var sliderContents = document.querySelectorAll('.right-column .slider-content');

	items.forEach(function (item) {
		item.addEventListener('click', function () {
			items.forEach(function (i) { i.classList.remove('active'); });
			sliderContents.forEach(function (c) { c.classList.remove('active'); });
			item.classList.add('active');
			var target = document.getElementById(item.getAttribute('data-target'));
			if (target) target.classList.add('active');
		});
	});
}

// ============================= Tab bar  ->  healthray-tabs2 ===================
function tabbar() {
	var tabLinks = document.querySelectorAll('.tab-link');

	tabLinks.forEach(function (link) {
		link.addEventListener('click', function (e) {
			e.preventDefault();
			var activeLink  = document.querySelector('.tab-link.active');
			var activePanel = document.querySelector('.tab-panel.active');
			if (activeLink)  activeLink.classList.remove('active');
			if (activePanel) activePanel.classList.remove('active');
			link.classList.add('active');
			var targetPanel = document.querySelector(link.getAttribute('href'));
			if (targetPanel) targetPanel.classList.add('active');
		});
	});
}

// ============================= Elementor hooks (WP Rocket delay/defer safe) ===
function bindElementorHooks() {
	if (typeof elementorFrontend === 'undefined' || !elementorFrontend.hooks) return;

	elementorFrontend.hooks.addAction('frontend/element_ready/swiper_widget.default',         owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-doctor-reviews.default',     owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/slider_logo.default',           owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-ehr-product-slider.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-product-slider-2.default',   owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ML_slider_set.default',         owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-alternative.default',        alternative);
	elementorFrontend.hooks.addAction('frontend/element_ready/custom_toggle.default',         customToggle);
	elementorFrontend.hooks.addAction('frontend/element_ready/healthray-tabs.default',        healthrayTabs);
	elementorFrontend.hooks.addAction('frontend/element_ready/healthray-tabs2.default',       tabbar);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml_slider.default',             new_slider);
}

// WP Rocket delay-safe: elementor/frontend/init may have already fired
if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
	bindElementorHooks();
} else {
	// elementor fires this as a custom jQuery event on window — listen via native DOM too
	window.addEventListener('elementor/frontend/init', bindElementorHooks);
}