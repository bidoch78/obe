function cookieManager(div, labels) {

	this.divContainer = div;
	this.labels = labels;
	
	this.createCookie = function(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	};

	this.readCookie = function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	};

	this.eraseCookie = function(name) {
		this.createCookie(name,"",-1);
	};
	
	this.initialize = function() {
		
		if (this.divContainer.length == 0) return;
		if (this.readCookie("acceptcookie") == "ok") return;
	
		this.divContainer.find(".modal-cookies .cookies-label").html(this.labels["text"] ? this.labels["text"] : "??");	
		this.divContainer.find(".modal-cookies .btn-ok").on("click", $.proxy(function(e) {
			this.createCookie("acceptcookie", "ok", 30);
			this.divContainer.find(".modal-cookies").modal("hide");
		}, this))
		
		this.divContainer.find(".modal-cookies").modal("show");
		
	};
		
};