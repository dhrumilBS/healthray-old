
document.addEventListener('DOMContentLoaded', function () {
	const base = '.content-editor ol.main-list>li';

	// Level 1
	document.querySelectorAll('.content-editor ol.main-list>li ol, .content-editor ol.main-list>li ul').forEach(el => {
		el.classList.add('level-1');
		el.setAttribute('role', 'list-1');
	});

	// Level 2
	document.querySelectorAll(
		'.content-editor ol.main-list>li ol.level-1>li ol,\
         .content-editor ol.main-list>li ol.level-1>li ul,\
         .content-editor ol.main-list>li ul.level-1>li ol,\
         .content-editor ol.main-list>li ul.level-1>li ul'
	).forEach(el => {
		el.classList.add('level-2');
		el.setAttribute('role', 'list-2');
	});

	// Level 3
	document.querySelectorAll(
		'.content-editor ol.main-list>li ol.level-1>li ol.level-2>li>ol,\
         .content-editor ol.main-list>li ol.level-1>li ol.level-2>li>ul,\
         .content-editor ol.main-list>li ol.level-1>li ul.level-2>li>ol,\
         .content-editor ol.main-list>li ol.level-1>li ul.level-2>li>ul,\
         .content-editor ol.main-list>li ul.level-1>li ol.level-2>li>ol,\
         .content-editor ol.main-list>li ul.level-1>li ol.level-2>li>ul,\
         .content-editor ol.main-list>li ul.level-1>li ul.level-2>li>ol,\
         .content-editor ol.main-list>li ul.level-1>li ul.level-2>li>ul'
	).forEach(el => {
		el.classList.add('level-3');
		el.setAttribute('role', 'list-3');
	});

	document.addEventListener("click", function (e) {
		if (!e.target.closest(".blog-category-more-list, .blog-category-more-link")) {
			document.querySelectorAll(".blog-category-more-link").forEach(btn => {
				btn.classList.remove("active");
				btn.parentElement.classList.remove("more-category-open");
			});
			document.querySelectorAll(".blog-category-more-list").forEach(list => {
				list.style.display = "none";
			});
		}
	});

	document.querySelectorAll(".blog-category-more-link").forEach(link => {
		link.addEventListener("click", function (e) {
			e.stopPropagation();
			this.classList.toggle("active");
			this.parentElement.classList.toggle("more-category-open");
			const list = this.parentElement.querySelector(".blog-category-more-list");
			list.style.display = (list.style.display === "block") ? "none" : "block";
		});
	});




	if (typeof siteData === 'undefined') return;
	const currentPageId = Number(siteData.pageId);
	const currentPageTitle = siteData.pageTitle;
	const isLoggedIn = siteData.isLoggedIn;
	const specificPageIds = siteData.pageIds || [];

	let popupTriggered = specificPageIds.includes(currentPageId) || isLoggedIn;

	const popupBg = document.getElementById('popupBackground');
	const popup = document.getElementById('myPopup');
	const closeBtn = document.getElementById('closePopup');
	const pageNameFields = document.querySelectorAll('.page-name');

	// 	pageNameFields.forEach(field => {
	// 		field.type = 'hidden';
	// 		field.value = currentPageTitle;
	// 	});

	function openPopup() {
		if (!popupBg || !popup) return;

		popupBg.style.display = 'block';
		popup.style.display = 'flex';
		popupTriggered = true;

		window.removeEventListener('scroll', handleScroll);
	}

	function closePopup() {
		if (popupBg) popupBg.style.display = 'none';
		if (popup) popup.style.display = 'none';
	}

	function handleScroll() {
		if (popupTriggered) return;
		const triggerPoint = document.body.scrollHeight / 2;
		if (window.scrollY >= triggerPoint) {
			openPopup();
		}
	}

	window.addEventListener('scroll', handleScroll);

	if (closeBtn) {
		closeBtn.addEventListener('click', closePopup);
	}

	if (document.querySelector('.hr-cta-btn')) {
		document.querySelector('.hr-cta-btn').addEventListener('click', function (e) {
			e.preventDefault();
			openPopup();
		});
	}

	document.addEventListener('keydown', e => {
		if (e.key === 'Escape') {
			closePopup();
		}
	});





	function getBtn(formEl) {
		return formEl.querySelector('input[type="submit"], button[type="submit"]');
	}
	function lockBtn(btn) {
		if (!btn) return;
		btn.disabled = true;
		btn.classList.add('sending');
		if (btn.tagName === 'INPUT') {
			btn.dataset.original = btn.value;
			btn.value = 'Sending...';
		} else {
			btn.dataset.original = btn.innerHTML;
			btn.innerHTML = 'Sending...';
		}
	}

	function resetBtn(btn) {
		if (!btn) return;
		btn.disabled = false;
		btn.classList.remove('sending');
		if (btn.tagName === 'INPUT') {
			btn.value = btn.dataset.original || 'Submit';
		} else {
			btn.innerHTML = btn.dataset.original || 'Submit';
		}
	}

	document.querySelectorAll('.wpcf7 form').forEach(form => {
		form.addEventListener('submit', function (e) {
			const btn = getBtn(this);
			if (!btn) return;

			// Prevent double submission
			if (btn.classList.contains('sending') || btn.disabled) {
				e.preventDefault();
				e.stopImmediatePropagation();
				return false;
			}
			lockBtn(btn);
		});
	});

	document.addEventListener('wpcf7invalid',    e => resetBtn(getBtn(e.target)));
	document.addEventListener('wpcf7mailfailed', e => resetBtn(getBtn(e.target)));
	document.addEventListener('wpcf7spam',       e => resetBtn(getBtn(e.target)));
	document.addEventListener('wpcf7aborted',    e => resetBtn(getBtn(e.target)));

	document.addEventListener('wpcf7mailsent', function (e) {
		const btn = getBtn(e.target);
		if (!btn) return;
		btn.disabled = true;
		if (btn.tagName === 'INPUT') {
			btn.value = '✓ Sent';
		} else {
			btn.innerHTML = '✓ Sent';
		}
		btn.classList.remove('sending');
		btn.classList.add('sent');
	});
});

/** ------------------------------
 * Contact Form 7: After Sent Action
 * ------------------------------ **/
document.addEventListener('wpcf7mailsent', function (event) {
	const formId = String(event.detail.contactFormId);
	const whitepaperFormId = '61816';
	if (formId === whitepaperFormId) {
		const wrapper = document.querySelector(`[data-form-id="${whitepaperFormId}"]`);
		if (!wrapper) {
			console.warn('Wrapper missing for whitepaper form');
			return;
		}
		const postId = wrapper.getAttribute('data-post-id');
		const nonce = wrapper.getAttribute('data-nonce');
		const ajaxUrl = window.ajax_obj;
		if (!postId || !nonce) {
			alert('Invalid form data. Please reload and try again.');
			return;
		}
		fetch(ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				action: 'get_whitepaper_pdf',
				nonce: nonce,
				post_id: postId
			})
		}).then(res => res.ok ? res.json() : Promise.reject('Network error'))
			.then(data => {
			if (data.success && data.data.url) {
				window.open(data.data.url, '_blank');
				const msg = document.createElement('div');
				msg.classList.add('pdf-download-message');
				msg.innerHTML = `<a href="${data.data.url}" target="_blank" class="pdf-btn">Download again</a>`;
				wrapper.appendChild(msg);
			}
		})
			.catch(err => console.error('Fetch error:', err));
	} else {
		setTimeout(() => {
			try {
				const params = new URLSearchParams(window.location.search);
				const source = params.get('utm_source') || '';
				const fbPage = siteData.homeUrl + '/thank-you-fb/';
				const defaultPage = siteData.homeUrl + '/thank-you/';
				const target = source === 'FacebookAds' ? fbPage : defaultPage;
				window.location.href = target;
			} catch (err) {
				console.error('Redirect error:', err);
			}
		}, 1000);
	}
}, false);