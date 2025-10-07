/* Optimized User Tracking Code */
document.addEventListener("DOMContentLoaded", () => {
	const originUrl = window.location.origin;
	const curUrl = window.location.href;
	const thisPage = encodeURIComponent(window.location.protocol + "//" + window.location.hostname + window.location.pathname);

	const getRefererUrl = () => {
		let refererUrl = document.referrer;
		if (refererUrl.includes("?")) {
			refererUrl = refererUrl.split("?")[0];
		}
		return refererUrl || "Direct";
	};

	const isUrlValid = (url) => {
		try {
			new URL(url);
			return true;
		} catch (_) {
			return false;
		}
	};

	const isMainLink = (url) => {
		try {
			const host = new URL(url).hostname.toLowerCase();
			return host.includes("healthray.com");
		} catch (_) {
			return false;
		}
	};

	const changeParametersForDirect = (encodedParams) => {
		const selectors = [".elementor-567", ".wrap", ".footer-section"];
		selectors.forEach((selector) => {
			document.querySelectorAll(selector + " a").forEach((a) => {
				const link = a.getAttribute("href");
				if (isUrlValid(link) && isMainLink(link)) {
					const separator = link.includes("?") ? "&" : "?";
					a.setAttribute("href", link + separator + encodedParams);
				}
			});
		});
	};

	const getUTMParams = () => {
		const params = new URLSearchParams(window.location.search);
		const allowedUTMs = ["utm_source", "utm_medium", "utm_campaign", "utm_term"];
		const utmParams = new URLSearchParams();

		allowedUTMs.forEach((key) => {
			if (params.has(key)) {
				utmParams.set(key, params.get(key));
			}
		});

		return utmParams.toString();
	};

	const referer = getRefererUrl();
	const utmString = getUTMParams();

	if (utmString) {
		changeParametersForDirect(utmString);
	} else {
		// No UTM found → add default tracking (without referer & origin_referer)
		const queryString =
			referer === "Direct"
				? `utm_source=Direct&utm_medium=${thisPage}`
				: !referer.includes(window.location.hostname)
				? `utm_source=website_organic&utm_medium=${encodeURIComponent(referer)}`
				: "";

		if (queryString) {
			changeParametersForDirect(queryString);
		}
	}
});
/* End Optimized User Tracking Code */