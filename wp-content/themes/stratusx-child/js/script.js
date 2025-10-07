window.addEventListener("load", function() {
	document.addEventListener("click", function(e) {
		e.target.closest(".blog-category-more-list, .blog-category-more-link") || (document.querySelectorAll(".blog-category-more-link").forEach(function(e) {
			e.classList.remove("active"), e.parentElement.classList.remove("more-category-open")
		}), document.querySelectorAll(".blog-category-more-list").forEach(function(e) {
			e.style.display = "none"
		}))
	});

	document.querySelectorAll(".blog-category-more-link").forEach(function(e) {
		e.addEventListener("click", function(e) {
			this.classList.toggle("active"), this.parentElement.classList.toggle("more-category-open");
			var t = this.parentElement.querySelector(".blog-category-more-list");
			"none" === t.style.display || "" === t.style.display ? t.style.display = "block" : t.style.display = "none", e.stopPropagation()
		})
	});
});