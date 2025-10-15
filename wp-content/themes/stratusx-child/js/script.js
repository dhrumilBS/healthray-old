window.addEventListener("load", function () {
	document.addEventListener("click", function (e) {
		e.target.closest(".blog-category-more-list, .blog-category-more-link") || (document.querySelectorAll(".blog-category-more-link").forEach(function (e) {
			e.classList.remove("active"), e.parentElement.classList.remove("more-category-open")
		}), document.querySelectorAll(".blog-category-more-list").forEach(function (e) {
			e.style.display = "none"
		}))
	});

	document.querySelectorAll(".blog-category-more-link").forEach(function (e) {
		e.addEventListener("click", function (e) {
			this.classList.toggle("active"), this.parentElement.classList.toggle("more-category-open");
			var t = this.parentElement.querySelector(".blog-category-more-list");
			"none" === t.style.display || "" === t.style.display ? t.style.display = "block" : t.style.display = "none", e.stopPropagation()
		})
	});
});

var disableSubmit = '';
jQuery(document).ready(function ($) {
	const val = $(':input[type="submit"]').val();
	$('.wpcf7').on('wpcf7_before_send_mail', function (event) {
		$(':input[type="submit"]').val("Sent");
		disableSubmit = false;
	});

	$('.wpcf7').on('wpcf7invalid', function (event) {
		$(':input[type="submit"]').val(val);
		disableSubmit = false;
	});
});

document.addEventListener('DOMContentLoaded', function () {

	const utmSource = getURLParameter('utm_source');
	const fbAdsThankYouPage = 'https://healthray.com/thank-you-fb/';
	const defaultThankYouPage = 'https://healthray.com/thank-you/';

	function getURLParameter(name) {
		name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
		var results = regex.exec(location.search);
		return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

	document.addEventListener('wpcf7mailsent', function (event) {
		const sentFormId = String(event.detail.contactFormId);
		console.log('sentFormId', sentFormId)
		const whitepaperFormId = '61816';

		// Debugging (remove later)
		console.log('Form submitted:', sentFormId);

		// Case 1: Whitepaper form - trigger secure PDF download
		if (sentFormId === whitepaperFormId) {
			const wrapper = document.querySelector(`[data-form-id="${whitepaperFormId}"]`);
			if (!wrapper) return console.warn('Wrapper not found for whitepaper form');

			const postId = wrapper.getAttribute('data-post-id');
			const nonce = wrapper.getAttribute('data-nonce');
			const ajaxUrl = window.ajax_obj;

			// Security: skip if missing required data
			if (!postId || !nonce) {
				alert('Invalid form data. Please reload and try again.');
				return;
			}

			fetch(ajaxUrl, {
				method: 'POST',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				body: new URLSearchParams({
					action: 'get_whitepaper_pdf',
					nonce: nonce,
					post_id: postId
				})
			})
				.then(res => res.ok ? res.json() : Promise.reject('Network error'))
				.then(data => {
				if (data.success && data.data.url) {
					// Open PDF securely in new tab
					window.open(data.data.url, '_blank');

					// Display confirmation message
					const message = document.createElement('div');
					message.classList.add('pdf-download-message');
					message.innerHTML = `<a href="${data.data.url}" target="_blank" class="pdf-btn">Download again</a>`;
					wrapper.appendChild(message);
				} else {
					alert('Sorry, unable to fetch your whitepaper. Please try again.');
				}
			})
				.catch(err => {
				console.error('Fetch error:', err);
				alert('An error occurred while fetching the whitepaper.');
			});
		}

		// Case 2: All other forms → redirect logic
		else {
			setTimeout(() => {
				try {
					const params = new URLSearchParams(window.location.search);
					const source = params.get('utm_source') || '';
					const fbPage = window.fbAdsThankYouPage || '/thank-you-fb';
					const defaultPage = window.defaultThankYouPage || '/thank-you';
					const target = source === 'fbads' ? fbPage : defaultPage;
					console.log('Redirecting to:', target);
					window.location.href = target;
				} catch (e) {
					console.error('Redirect error:', e);
				}
			}, 1000);
		}
	}, false);

});
