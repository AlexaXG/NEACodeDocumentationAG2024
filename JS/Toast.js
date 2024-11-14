window.onload = function () {
	var toast = document.querySelector(".toast");
	if (toast) {
		setTimeout(function () {
			toast.style.opacity = "0";
			setTimeout(function () {
				toast.style.display = "none";
			}, 500);
		}, 3000);
	}
};
