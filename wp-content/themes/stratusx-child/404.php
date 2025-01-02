<style>
	.error-section{ background-color: #f3f4f6; display: flex; align-items: center; justify-content: center;  }
	.error-section .error-container { text-align: center; max-width: 600px;  width: 100%; padding: 16px; }
	.error-section .error-code { font-size: 6rem; font-weight: bold; color: #1f2937; } 
	.error-section .error-message { font-size: 1.5rem; font-weight: bold; color: #1f2937; margin-top: 8px; }
	.error-section .error-description { font-size: 0.875rem; color: #4b5563; margin-top: 8px; }
	.error-section .back-home { display: inline-flex; align-items: center; gap: 8px; margin: 16px auto 20px; transition: background-color 0.3s; background-color: var(--hr-secondary-color); color: #fff;   padding: 12px 32px; font-weight:500 }
	.error-section .back-home:hover { background-color: #4f46e5; color: #fff; }
	.error-section .divider { display: flex; align-items: center;margin: 0 auto; margin-top: 32px; width: 100%; max-width: 600px; }
	.error-section .divider-line { flex-grow: 1; height: 1px; background-color: #d1d5db; } 
	.error-section .divider-text { padding: 0 8px; font-size: 0.875rem; color: #6b7280; background-color: #f3f4f6; }
</style>

<section class="error-section">
	<div class="error-container">
		<div>
			<div class="error-code">404</div>
			<p class="error-message">Page not found</p>
			<p class="error-description">Sorry, we couldn't find the page you're looking for.</p>
		</div>
		<a href="/" class="back-home button">		
			Go back home	 
			<svg width="19" height="11" viewBox="0 0 19 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
				<path d="M1 5.5H18M18 5.5L13.5 1M18 5.5L13.5 10" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</a>
		<div class="divider">
			<div class="divider-line"></div>
			<div class="divider-text">If you think this is a mistake, please contact support</div>
			<div class="divider-line"></div>
		</div>
	</div>

</section>