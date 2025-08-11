$(document).ready(function () {
	let showStoreInfoBtns = document.querySelectorAll('.show-store-info__js');
	
	if (showStoreInfoBtns.length) {
		for (let i = 0; i < showStoreInfoBtns.length; i++) {
			showStoreInfoBtns[i].addEventListener("click", function(event) {
				let productId = this.dataset.productId;
				
				if (productId) {
					universal.product.showStoreInfo(productId);
				}
			});
		}
	}
});