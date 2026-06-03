var disableSubmit = '';

document.addEventListener('DOMContentLoaded', function () {
	let disableSubmit = false;
	const submitBtn = document.querySelector('input[type="submit"], button[type="submit"]');
	const originalVal = submitBtn ? submitBtn.value : '';

	document.addEventListener('wpcf7beforeSendMail', () => {
		if (submitBtn) submitBtn.value = "Sent";
		disableSubmit = true;
	});

	document.addEventListener('wpcf7invalid', () => {
		if (submitBtn) submitBtn.value = originalVal;
		disableSubmit = false;
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

});

jQuery(function ($) {

	$('.main-menu > li').hover(
		function () {
			$(this)
				.children('.sub-menu')
				.stop(true, true)
				.fadeIn(220);
		},
		function () {
			$(this)
				.children('.sub-menu')
				.stop(true, true)
				.fadeOut(180);
		}
	);

});


/** ------------------------------
	 * UTM Helper
	 ------------------------------*/
function getURLParameter(name) {
	const params = new URLSearchParams(window.location.search);
	return params.get(name);
}

/** ------------------------------
	 * Contact Form 7: After Sent Action
	 ------------------------------*/
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
				const source = getURLParameter('utm_source') || '';
				const fbPage = siteData + '/thank-you-fb/';
				const defaultPage = siteData + '/thank-you/';
				const target = source === 'FacebookAds' ? fbPage : defaultPage;
				window.location.href = target;
			} catch (err) {
				console.error('Redirect error:', err);
			}
		}, 1000);
	}
}, false); 
