<?php

	$displayValidateSubscription = (isset($_GET["vsubscribe"]) && strlen($_GET["vsubscribe"]) > 0);
	$tokenValidateSubscription = $displayValidateSubscription ? $_GET["vsubscribe"] : null;
	
	$displayChangePassword = (isset($_GET["fpassword"]) && strlen($_GET["fpassword"]) > 0);
	$tokenChangePassword = $displayChangePassword ? $_GET["fpassword"] : null;
	
?>

<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/bootstrap_4/assets/js/jquery.min.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/bootstrap_4/assets/js/popper.min.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/bootstrap_4/assets/js/toolkit.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/js/cookie.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/pwstrength-bootstrap/pwstrength-bootstrap.min.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/js/ytable.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/js/ytable-template-bootstrap4.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/js/moment.min.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/js/moment-localization-fr.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/js/functions.js"></script>
<script src="<?php echo _REF_TO_ROOT_DIR_; ?>/assets/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js"></script>

<script type="text/javascript">	
	
	moment.locale('fr');
	
	function resetMessageModal() {
		
		var $modal = $("#modal_message");
		
		$modal.find(".btncancel").off("click");
		$modal.find(".btncancel").html("Ok");
		$modal.find(".btnsave").off("click");
		$modal.find(".btnsave").html("Sauvegarder").css("display", "none");
		
		$modal.find(".btncancel").on("click", function() {
			$(this).closest('.modal[role="dialog"]').modal("hide");
		});
		
		var classBtn = $modal.find(".btnsave").attr("class").split(/\s+/);
		for(var i = 0; i < classBtn.length; i++) {
			if (classBtn[i].indexOf("btn-") == 0) $modal.find(".btnsave").removeClass(classBtn[i]);
		}
		
		var classBtn = $modal.find(".btncancel").attr("class").split(/\s+/);
		for(var i = 0; i < classBtn.length; i++) {
			if (classBtn[i].indexOf("btn-") == 0) $modal.find(".btncancel").removeClass(classBtn[i]);
		}
		
		$modal.find(".btncancel").addClass("btn-outline-dark");
		
	};
	
	function displayMessageModal(options) {
		
		resetMessageModal();
		
		var $modal = $("#modal_message");
		
		$modal.find(".modal-title").html(options.title);
		$modal.find(".modal-body").html(options.text);		
		
		if (options.save) {
			
			if (options.save.visible) {
				
				$modal.find(".btnsave").css("display", "");
				$modal.find(".btnsave").addClass((options.save.class ? options.save.class : 'btn-primary'));
				if (options.save.text) $modal.find(".btnsave").html(options.save.text);
				
				if ($.isFunction(options.save.fct)) {
					$modal.find(".btnsave").on("click", $.proxy(function(e) {
						var closeModal = $.proxy(function() { this.modal("hide"); }, $(e.currentTarget).closest('.modal[role="dialog"]') );
						if (options.save.fct(closeModal) === false) return;
						closeModal();
					}, options.save.fct));
				}
				
			}
			
		}
		
		if (options.cancel) {
			
			if (options.cancel.text) $modal.find(".btncancel").html(options.cancel.text);
			
		}
		
		$modal.modal("show");
		
	};
	
	function formatErrorOnModal(e, $modal, options) {
		
		resetMessageModal();
		
		var title = "Erreur lors de la sauvegarde";
		if (options && options.title) title = options.title;
		
		var msgError = '';
		if (options && options.qryError === true) {
			msgError = e.err + ' / ' + e.errMessage;
		}
		else {
			msgError = e.status + ' / ' + e.statusText;
		}
		
		var txt = "Une erreur est survenue lors de la sauvegarde.<br><small>Erreur : " + msgError + "</small><br><br>";
		txt += "Essayez encore après avoir vérifier que vous votre connection internet est correcte et si le problème persiste, veuillez contacter le support.";
		
		if (options && options.ajaxRetrieveData === true) {
			txt = "Une erreur est survenue lors de la communication avec le serveur.<br><small>Erreur : " + msgError + "</small><br><br>";
			txt += "Vérifiez votre connection internet ou contactez le support si le problème persiste.";
			if (!options.title) title = "Erreur de communication";
		}
		
		$modal.find(".modal-title").html(title);
		$modal.find(".modal-body").html(txt);

	}
	
	function WaitSplash(state) {
		
		if (state) {
			if ($(".wait-screen").length > 0) return;
			$("body").append('<div class="wait-screen"><div class="wait-screen-container"><div class="wait-screen-logo"><i class="fas fa-sync"></i></div></div></div>');
		}
		else {
			$(".wait-screen").remove();
		}
		
	}
	
	function displayLoginPart(part, showmodal) {

		$("#modal-login").attr("data-info", part);
		$("#modal-login").find(".div-info").css("display", "none");
		$(".modal-login").attr("data-reloadwhenclose", "");
		
		if (part == "subscribe") {
			$("#modal-login .modal-title").html("Inscrivez-vous et jouer");
			$("#modal-login .div-subscribe").css("display", "");
		}
		else if (part == "signin") {
			$("#modal-login .modal-title").html("Connectez-vous à votre compte");				
			$("#modal-login .div-login").css("display", "");
		}
		else if (part == "vsubscribe") {
			$("#modal-login .modal-title").html("Validation de votre compte");				
			$("#modal-login .div-vsubscribe").css("display", "");
			$(".modal-login").attr("data-reloadwhenclose", "1");
		}
		else if (part == "snewlink") {
			$("#modal-login .modal-title").html("Generation d'un nouveau lien de validation");				
			$("#modal-login .div-snewlink").css("display", "");
			$(".modal-login").attr("data-reloadwhenclose", "1");
		}
		else if (part == "changepassword") {
			$("#modal-login .modal-title").html("Modification de votre mot de passe");
			$("#modal-login .div-changepassword").css("display", "");
		}
		
		$("#modal-login .alert").css("display", "none");
		$("#modal-login .div-subscribe.div-info input").val("");
		$("#modal-login .div-info .app-auth").prop("disabled", false);
		
		$("#modal-login .div-login .btn-connect").prop("disabled", false);
		$("#modal-login .div-subscribe .btn-connect").prop("disabled", false);
		$("#modal-login .div-vsubscribe .btn-connect").prop("disabled", false);
		$("#modal-login .div-snewlink .btn-connect").prop("disabled", false);
		$("#modal-login .div-changepassword .btn-connect").prop("disabled", false);
		$("#modal-login .div-snewlink .btn-connect").css("display", "none");

		if (showmodal) $("#modal-login").modal("show");
		
	};
	
	$(function(){
			  
		while(window.BS&&window.BS.loader&&window.BS.loader.length){(window.BS.loader.pop())()};
		
		var strCookies = 'En poursuivant votre navigation, vous acceptez l\'utilisation de cookies ou technologies similaires, y compris de partenaires tiers pour la diffusion de publicité ciblée et de contenus pertinents au regard de vos centres d\'intérêts. <a href="">En savoir plus</a>.';
		strCookies += '<br><br>Afin de continuer à améliorer la protection de vos données personnelles, nous avons mis à jour notre politique de confidentialité. <a href="">En savoir plus</a>.';
		
		var cookieMgr = new cookieManager($("body"),{ "text": strCookies } );
		cookieMgr.initialize();
		
		$('.password-check:password').pwstrength({
			'ui': { 
				'progressExtraCssClasses': 'obepasswordprgbar',
				'progressBarExtraCssClasses': 'obe'
			},
			'i18n': {
				't': function(key) {
					switch(key) {
						case "veryWeak": return "La note de votre mot de passe est <b>très faible</b>";
						case "weak": return "La note de votre mot de passe est <b>faible</b>";
						case "normal": return "La note de votre mot de passe est <b>normal</b>";
						case "medium": return "La note de votre mot de passe est <b>moyenne</b>";
						case "strong": return "La note de votre mot de passe est <b>forte</b>";
						case "veryStrong": return "La note de votre mot de passe est <b>très forte</b>";
					}
					return key;
				}
			}/*,
			'common': {
				'onScore': function(options, word, score) {
					
					var $target = $(options.instances.viewports.$progressbar.context);
					var classparent = $target.attr("data-parent");
					var classbutton = $target.attr("data-button");
					
					var $btn = $target.closest("." + classparent).find("button." + classbutton);
					//if ($btn) $btn.prop("disabled", score < 0);
					if ($btn) $btn.prop("disabled", word.length < 6);
					
					return score;
					
				}
			}*/
		});
		
		$(".switch-to-subscribe").on("click", function() {
			if ($(this).hasClass("disabled")) return;
			displayLoginPart("subscribe", false);
		});
		
		$(".btn-signin").on("click", function() {
			
			var $this = $(this);
			var btnInfo = $this.attr("data-info");
			
			displayLoginPart(btnInfo, true);
			
		});
		
		$(".btn-disconnect").on("click", function() {
			
			var url = "code/ws/connect.php";
			
			$.ajax({
				type: 'POST',
				url: url,
				async: true,
				dataType: 'json',
				data: { 'action': 'disconnect' },
				complete: function() {
					location.reload();
				}
			});				
			
		});	
		
		$(".btn-play").on("click", function() {
			location.href = "play.php";
		});
		
		$(".btn-settings").on("click", function() {
			location.href = "settings.php";
		});
		
		//Validation si touche entree appuyee
		$(window).bind("keypress", function(e) {
			
			var authFocus = $(":focus").hasClass("app-auth");
			
			if (authFocus && e.keyCode == 13) {
				if ($(".modal-login").hasClass("show")) $(".btn-connect").trigger("click");
			}
			
		});	

		//When display modal login
		$(".modal-login").on("show.bs.modal", function() {
			$(".modal-login .alert.alert-danger").css("display", "none");
		}).on("hidden.bs.modal", function() {
			if ($(".modal-login").attr("data-reloadwhenclose") == "1") window.location = window.location.pathname;
		});
		
		//Click to connect
		$(".btn-connect").on("click", function() {
			
			var $this = $(this);
			var data = { 'action': 'login', 'src': $("#modal-login").attr("data-info") };
			
			var $checkitems = null;
			if ($(".modal-login .div-login").css("display") != "none") {
				$checkitems = $('.modal-login .div-login [data-name!=""]');
			}
			else if ($(".modal-login .div-vsubscribe").css("display") != "none") {
				$checkitems = $('.modal-login .div-vsubscribe [data-name!=""]');
			}
			else if ($(".modal-login .div-subscribe").css("display") != "none") {
				$checkitems = $('.modal-login .div-subscribe [data-name!=""]');
			}
			else if ($(".modal-login .div-snewlink").css("display") != "none") {
				$checkitems = $('.modal-login .div-snewlink [data-name!=""]');
			}
			else if ($(".modal-login .div-changepassword").css("display") != "none") {
				$checkitems = $('.modal-login .div-changepassword [data-name!=""]');
				data["t"] = <?php echo json_encode($tokenChangePassword); ?>;
			}
			
			$checkitems.each($.proxy(function(index, item) {
				var $this = $(item);
				var dataName = $this.attr("data-name");
				if ($this.attr("type") == "text" || $this.attr("type") == "password" || $this.attr("type") == "hidden") this[dataName] = $this.val();
				if ($this.attr("type") == "checkbox") this[dataName] = $this.is(":checked") ? "1" : "0";
			}, data));
		
			$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", true);
			$(".modal-dialog a").addClass("disabled");
			
			$.ajax({
				type: 'POST',
				url: "code/ws/connect.php",
				async: true,
				dataType: 'json',
				displayMessage: function(msg) {
					
					$(".modal-login .alert.alert-danger").css("display", "none");
					
					var $msg = $(".modal-login .alert.alert-success");
					if (msg) {
						$msg.html(msg);
						$msg.css("display", "block");
					}
					else {
						$msg.css("display", "none");
					}
					
				},
				displayError: function(msg) {
					
					$(".modal-login .alert.alert-success").css("display", "none");
					
					var $alert = $(".modal-login .alert.alert-danger");
					
					if (msg) {
						$alert.html(msg);
						$alert.css("display", "block");
					}
					else {
						$alert.css("display", "none");
					}
					
				},
				datasrc: data['src'],
				data: data,
				datasent: data,
				success: function(json) {
					
					if (this.datasrc == "signin") {
					
						if (json.err != 0) {
							this.displayError(json.errMessage);
						 }
						else {
							this.displayError("");
							if (json.data && json.data.redirect) {
								location.href = json.data.redirect;
							}
							else {
								location.reload();
							}
							
						}
						
						$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", false);
						$(".modal-dialog a").removeClass("disabled");
						
					}
					else if (this.datasrc == "subscribe") {
						
						if (json.err != 0) {
							this.displayError(json.errMessage);
							$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", false);
							$(".modal-dialog a").removeClass("disabled");
						 }
						else {
							this.displayError("");
							if (json.message) this.displayMessage(json.message);
							$(".modal-login .modal-body input, .modal-login").prop("disabled", false);
							$(".modal-dialog a").removeClass("disabled");
						}
						
					}
					else if (this.datasrc == "vsubscribe") {
						
						if (json.err != 0) {
							this.displayError(json.errMessage);
							$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", false);
							$(".modal-dialog a").removeClass("disabled");
						 }
						else {
							this.displayError("");
							if (json.message) this.displayMessage(json.message);
						}
						
						$("#modal-login .link_connectme").on("click", function() {
							displayLoginPart("signin", false);
							return false;
						});
						
						$("#modal-login .link_generatenewlink").on("click", $.proxy(function() {
							displayLoginPart("snewlink", false);
							$("#modal-login .div-snewlink #_suseremail").val(this.l);
							$("#modal-login .div-snewlink .btn-connect").trigger("click");
							return false;
						}, this.datasent));
						
					}
					else if (this.datasrc == "snewlink") {
						
						if (json.err != 0) {
							this.displayError(json.errMessage);
						}
						else {
							this.displayError("");
							if (json.message) this.displayMessage(json.message + '<br><div style="text-align:center"><button type="button" style="min-width: 200px" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Fermer</button></div>');
						}
						
					}
					else if (this.datasrc == "changepassword") {
						
						if (json.err != 0) {
							this.displayError(json.errMessage);
							$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", false);
							$(".modal-dialog a").removeClass("disabled");
						 }
						else {
							this.displayError("");
							if (json.message) this.displayMessage(json.message);
							//Reload
							$("#modal-login .link_reloadpage").on("click", function() { document.location.reload(true); });
						}
												
					}
					
				},
				error: function(e) {
					this.displayError("Error interne. Etes-vous bien connecté(e) ?");
					$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", false);
					$(".modal-dialog a").removeClass("disabled");
				}
			});

		});
		
		$(".link-forgot-mypassword").on("click", function() {
			
			if ($(this).hasClass("disabled")) return;
			
			$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", true);
			$(".modal-dialog a").addClass("disabled");
			
			var data = { 'action': 'forgotpassword', 'l': $("#modal-login #_userlogin").val() };
			
			$.ajax({
				type: 'POST',
				url: "code/ws/connect.php",
				async: true,
				dataType: 'json',
				displayMessage: function(msg) {
					
					$(".modal-login .alert.alert-danger").css("display", "none");
					
					var $msg = $(".modal-login .alert.alert-success");
					if (msg) {
						$msg.html(msg);
						$msg.css("display", "block");
					}
					else {
						$msg.css("display", "none");
					}
					
				},
				displayError: function(msg) {
					
					$(".modal-login .alert.alert-success").css("display", "none");
					
					var $alert = $(".modal-login .alert.alert-danger");
					
					if (msg) {
						$alert.html(msg);
						$alert.css("display", "block");
					}
					else {
						$alert.css("display", "none");
					}
					
				},
				data: data,
				datasent: data,
				success: function(json) {
					
					if (json.err != 0) {
						this.displayError(json.errMessage);
					}
					else {
						this.displayError("");
						if (json.message) this.displayMessage(json.message);
					}

					$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", false);
					$(".modal-dialog a").removeClass("disabled");
					
				},
				error: function(e) {
					this.displayError("Error interne. Etes-vous bien connecté(e) ?");
					$(".modal-login .modal-body input, .modal-login .btn-connect").prop("disabled", false);
					$(".modal-dialog a").removeClass("disabled");
				}
			});			
			
			return false;
		});
		
		$(".btn-video").on("click", function() {
			
			var videoTitle = "Video";
			var dataTitle = $(this).attr("data-title");
			if (dataTitle) videoTitle = dataTitle;
			
			$("#modal_video #modal_video_title").html('<i class="fas fa-video"></i> ' + videoTitle);
			
			var htmlPlayer = '<div style="text-align: center; padding-top: 10px;">';
				htmlPlayer += '<video class="video_player" controls width="90%">';
					htmlPlayer += '<source src="' + $(this).attr("data-video") + '" type="video/mp4">';
					htmlPlayer += 'Désolé votre navigateur n\'est pas capable de lire la vidéo';
				htmlPlayer += '</video>';
			htmlPlayer += '</div>';
			
			$("#modal_video .modal-body").html(htmlPlayer);
			
			$("#modal_video").off("shown.bs.modal hide.bs.modal").on("shown.bs.modal", function() {
				try { $("#modal_video .video_player").get(0).play(); }
				catch(error) {}
			}).on("hide.bs.modal", function() { 
				try { $("#modal_video .video_player").get(0).stop(); }
				catch(error) {}
				$("#modal_video .modal-body").html("");
			});
			$("#modal_video").modal("show");
			
		});
		
<?php if ($displayValidateSubscription) { ?>		

		$("#modal-login #_vusertoken").val("<?php echo $tokenValidateSubscription; ?>");
		displayLoginPart("vsubscribe", true);
		
<?php } ?>

<?php if ($displayChangePassword) { ?>		

		$("#modal-login #_cpusertoken").val("<?php echo $tokenChangePassword; ?>");
		displayLoginPart("changepassword", true);	

<?php } ?>
		
		var currentDate = new Date();
		$(".year").html(currentDate.getFullYear());
		
	});
				
</script>