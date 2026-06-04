<?php

	/* https://www.shutterstock.com/fr/image-photo/image-happy-friends-playing-basketball-on-152417072?src=LBzmfbEYiUnxcpkZiGFZhw-1-14 */

	$baseDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	define("_REF_TO_ROOT_DIR_", ".");

	include_once($baseDir . "config.php");
	include_once($baseDir . "common.php");
	
?>
<!DOCTYPE html>
<html lang="en">

	<head>
	
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="">

		<title>OBE</title>

		<!-- CSS -->
		<?php include($baseDir . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "std-css.php"); ?>
		
	</head>

	<body class="with-top-navbar obe-background1">

		<!-- header -->
		<?php include($baseDir . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "header.php"); ?>

		<div class="obecontainer container-fluid with-nav-fixed">
	
			<div class="row">
				
				<div class="offset-md-2 col-md-8" style="padding-top: 20px; padding-bottom: 20px; text-align: center">
					<p>
						Pour faire du basket sur le temps scolaire, 2 solutions s'offrent à vous :
					</p>
				</div>
				
			</div>
			
			<div class="row">
				
				<div class="offset-md-2 col-md-3">
				
					<div class="card card-obeinfo card-color1">
						<div class="card-header">
							OPERATION BASKET ECOLE
						</div>
					  <div class="card-body">
						<p class="card-text">Vous souhaitez faire un cycle basket avec vos élèves.</p>
						<div style="text-align: center" ><a href="http://www.basketecole.com/" target="_blank" class="btn btn-primary" >Cliquez ici</a></div>
					  </div>
					</div>

				</div>
				
				<div class="col-md-2" style="text-align: center">
					<i class="fas fa-exchange-alt obe-exchange"></i>
				</div>
				
				<div class="col-md-3">
				
					<div class="card card-obeinfo card-color2">
						<div class="card-header">
							DEFI RÉCRÉ BASKET
						</div>
					  <div class="card-body">
						<p class="card-text">Vous souhaitez que vos élèves découvrent le basket sur le temps des récréations.</p>
						<div style="text-align: center" ><a href="#" class="btn btn-primary btn-gotoinfo" >Cliquez ici</a></div>
					  </div>
					</div>

				</div>
				
			</div>
			
			<div class="row">
				
				<div class="offset-md-2 col-md-8" style="padding-top: 20px; text-align: center">
					<p>
						FACILITÉ, SIMPLICITÉ, RICHESSE PÉDAGOGIQUE... <span style="font-weight: bold;">ET C’EST GRATUIT</span>
					</p>
					Seulement 30 secondes pour s’inscrire.
				</div>
				
			</div>
			
			<div class="row obeinfodefibasket" style="display:none">
				
				<div class="offset-md-2 col-md-8" style="padding-top: 20px">

					<a name="obeinfodefibasketinfo"></a>
				
					<div class="card card-obeinfo card-color2">
						<div class="card-header">
							Anime ta récré avec le basket
						</div>
					  <div class="card-body obeinfodefibasketitem">
						<div>
							<ul>
								<li>
									<u><b>Quand et où</b></u><br>
									Au choix des élèves, à la récréation du matin ou durant celle de l’après-midi ; sur la cour, pas besoin d’un terrain particulier !
								</li>
								<li>
									<u><b>Pour qui ?</b></u><br>
									Pour tous les élèves de cycle 2 et 3 qui veulent soit :
									<ul>
										<li>découvrir l’activité</li>
										<li>renforcer leur pratique du basket (en lien avec un cycle d’apprentissage en EPS, en amont comme en aval, ou en prévision d’un projet de tournoi inter-classe ou de match inter-école)</li>
										<li>juste s’amuser</li>
									</ul>
								</li>
								<li>
									<u><b>Pourquoi ?</b></u><br>
									Pour donner le goût de se dépenser durant les récréations (objectif sport-santé)<br>
									Pour vivre d’autres sensations sportives, parallèlement à l’opération nationale « le Basket à l’école »
								</li>
								<li>
									<u><b>Comment ?</b></u><br>
									De manière ludique : c’est quand même la récréation !<br>
									De manière autonome : les élèves sont entre eux et ont l’initiative de l’atelier basket qu’ils choisissent parmi une sélection de fiches à télécharger
								</li>
								<li>
									<u><b>Avec quel matériel ?</b></u><br>
									Un kit matériel est fourni aux classes inscrites (un panier, 4 ballons, des chasubles, des craies, une toise, un chronomètre, 6 coupelles et des fiches-ateliers).
									Si vous le souhaitez, une aide humaine est possible pour vous épauler lors de la première récréation.
								</li>
								<li>
									<u><b>Visionner le clip</b></u><br>
									<br>
									<div style="text-align: center; padding-top: 10px;">
										<video controls width="80%">
											<source src="./images/DEFI RECRE BASKET1.mp4" type="video/mp4">
											Désolé votre navigateur n'est pas capable de lire la vidéo
										</video>
									<div>
								</li>
							</ul>
							<div class="alert alert-primary" role="alert">
								<b>Récompense</b> : chaque classe se verra attribuer un diplôme basket (bronze, argent ou or) en fonction des compétences obtenues ansi qu'une invitation pour aller voir un match de basket professionnel.
							</div>
						</div>
						<div style="text-align: center" ><a href="#" class="btn btn-primary btn-signin" data-info="subscribe">Je m'inscris maintenant</a></div>
					  </div>
					</div>

				</div>				
				
			</div>
			
		</div> <!-- container-fluid -->
		
		<!-- FOOTER -->
		<?php include($baseDir . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "footer.php"); ?>
		
		<!-- JS -->
		<?php include($baseDir . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "std-js.php"); ?>		
	
		<script type="text/javascript">
	
			// execute/clear BS loaders for docs
			$(function(){
		
				$(".btn-gotoinfo").on("click", function() {
					
					$(".obeinfodefibasket").css("display", "block");
					
					var aTag = $("a[name='obeinfodefibasketinfo']");
					 
					$('html,body').animate({scrollTop: aTag.offset().top - $(".obecontainer").offset().top },'slow');
					
				});
				
			});
			
		</script>

	</body>
	
</html>