<?php

	$baseDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	define("_REF_TO_ROOT_DIR_", ".");

	include_once($baseDir . "config.php");
	include_once($baseDir . "common.php");
	include_once($baseDir . "/code/challenge.php");
	include_once($baseDir . "/code/gamesession.php");
	
	$currentChallenges = Challenge::getCurrentChallenges(null);
	$currentChallengeIds = Challenge::getIdsFromArray($currentChallenges);
	$userChallengeData = GameSession::getUserScore(array("challenges" => $currentChallengeIds, "user" => $userConnected->UserId));
	
	$severalChallenges = count($currentChallenges) > 1;
	
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

	<body class="with-top-navbar">

		<!-- header -->
		<?php include($baseDir . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "header.php"); ?>

		<div class="container-fluid with-nav-fixed">

				<div class="row">
					
					<div class="col-md-10" style="text-align:center">
						<button class="btn btn-primary btn-play"><i class="fas fa-basketball-ball" style="margin-right:5px"></i>Je joue</button>
					</div>
					<div class="col-md-2" style="text-align:right">
						<!--button class="btn btn-primary btn-play"><i class="fas fa-basketball-ball" style="margin-right:5px"></i>Je joue</button-->
						
						<div class="btn-group">
							<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Votre avis nous intéresse
							</button>
							  <div class="dropdown-menu">
								<a class="dropdown-item" target="_blank" href="https://docs.google.com/forms/d/185NnYdylXpU2hH3OdEyut9Yfn_0s1gQ56kzWFGW6Jr4/edit?ts=6050aa2b&gxids=7628">Je suis enseignant</a>
								<a class="dropdown-item" target="_blank" href="https://docs.google.com/forms/d/1TjH4TzmTdL6PEUkbVHccN2mhHILDY2zXAxFqVPbOO1s/edit?ts=6050aa15&gxids=7628">Je suis enfant</a>
							  </div>
							</div>

					</div>
					
				</div>
		
				<div class="row row-challengeselectcaption" style="margin-top: 10px; display:none">
					<div class="col-md-4"><b>Selectionner le challenge pour afficher les informations</b></div>
				</div>
				
				<div class="row" style="display:none">
					
				  <div class="col-md-4 col-challenge-selector" style="display:none">
					<div class="list-group" id="list-challenges" role="tablist" style="margin-top:10px">
<?php

	$first = true;
	foreach($currentChallenges as $challenge) {
		$id = "CHALLENGE" . $challenge["ChallengeId"];
		echo '<a class="list-group-item list-challenge-item list-group-item-action" data-id="' . $challenge["ChallengeId"] . '" id="' . $id . '-list" data-toggle="list" href="#' . $id . '" role="tab" aria-controls="' . $challenge["ChallengeName"] . '">' . $challenge["ChallengeName"] . '</a>';
		$first = false;
	}
	

?>					

					</div>
				  </div>
				  <div class="col-md-8 col-challenge-data">
					<div class="tab-content" id="nav-tabContent" style="margin-top:10px">
					
<?php
	
	$first = true;
	foreach($currentChallenges as $challenge) {
		$id = "CHALLENGE" . $challenge["ChallengeId"];
		echo '<div class="tab-pane fade" id="' . $id . '" role="tabpanel" aria-labelledby="' . $id . '-list">';

?>
	
	<div class="row" style="margin-left:0; margin-right:0">
		<b>Vos résultats</b>
		<table class="table table-striped table-sm table-challenge-user">
			<thead>
				<tr>
					<th>Dernière partie</th><th>Ecole</th><th>Professeur</th><th>Classe</th><th style="text-align: right">Score</th><th style="text-align: center">Coupe</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<div class="row row-challenge-top" style="margin-left:0; margin-right:0; display: none;">
		<b>Les dernières parties</b>
		<table class="table table-striped table-sm table-challenge-top">
			<thead>
				<tr>
					<th>Dernière partie</th><th>Ecole</th><th>Classe</th><th style="text-align: right">Score</th><th style="text-align: center">Coupe</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<div class="row row-wait-loading" style="margin-left:0; margin-right:0; display: none;">
		<div class="wait-screen-container wait-screen-container-relative"><div class="wait-screen-logo"><i class="fas fa-sync"></i></div></div>
	</div>

<?php
		
		echo '</div>';
		$first = false;
	}

?>				

					</div>
				  </div>
  
				</div>
				
				<div class="row justify-content-center">
					
					<div class="col-lg-2">
						
						<div class="card card-info card-dribble text-center">
							<div class="card-header">DRIBBLE</div>
							<div class="card-body">
								<img class="card-img-info" src="images/dribble.jpg" alt="image dribbles">
								<p class="card-text" style="padding-top:10px"><button class="btn btn-outline-primary btn-video" data-title="Atelier Dribble" data-video="./images/DEFI RECRE BASKET DRIBBLE.mp4"><i class="fas fa-film"></i> vidéo</button></p>
								<p class="card-text">Découvrez les différents ateliers</p>
								<a href="docs/atelier-basketrecre-dribble-cycle1.pdf" target="_blank" class="btn btn-primary btn-download-pdf disabled"><i class="fas fa-file-download"></i> Télécharger cycle 1</a>
								<a href="docs/atelier-basketrecre-dribble-cycle2.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 2</a>
								<a href="docs/atelier-basketrecre-dribble-cycle3.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 3</a>
							</div>
						</div>
						
					</div>
					
					<div class="col-lg-2">
					
						<div class="card card-info card-shoot text-center">
							<div class="card-header">TIR</div>
							<div class="card-body">
								<img class="card-img-info" src="images/shoot.jpg" alt="image shoots">
								<p class="card-text" style="padding-top:10px"><button class="btn btn-outline-primary btn-video" data-title="Atelier Tir" data-video="./images/DEFI RECRE BASKET TIR.mp4"><i class="fas fa-film"></i> vidéo</button></p>
								<p class="card-text">Découvrez les différents ateliers</p>
								<a href="docs/atelier-basketrecre-tir-cycle1.pdf" target="_blank" class="btn btn-primary btn-download-pdf disabled"><i class="fas fa-file-download"></i> Télécharger cycle 1</a>
								<a href="docs/atelier-basketrecre-tir-cycle2.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 2</a>
								<a href="docs/atelier-basketrecre-tir-cycle3.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 3</a>
							</div>
						</div>	
						
					</div>

					<div class="col-lg-2">
					
						<div class="card card-info card-pass text-center">
							<div class="card-header">PASSE</div>
							<div class="card-body">
								<img class="card-img-info" src="images/pass.png" alt="image tirs">
								<p class="card-text" style="padding-top:10px"><button class="btn btn-outline-primary btn-video" data-title="Atelier Passe" data-video="./images/DEFI RECRE BASKET PASSE.mp4"><i class="fas fa-film"></i> vidéo</button></p>
								<p class="card-text">Découvrez les différents ateliers</p>
								<a href="docs/atelier-basketrecre-passe-cycle1.pdf" target="_blank" class="btn btn-primary btn-download-pdf disabled"><i class="fas fa-file-download"></i> Télécharger cycle 1</a>
								<a href="docs/atelier-basketrecre-passe-cycle2.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 2</a>
								<a href="docs/atelier-basketrecre-passe-cycle3.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 3</a>
							</div>
						</div>	
						
					</div>

					<div class="col-lg-2">
					
						<div class="card card-info card-dribbletir text-center">
							<div class="card-header">DRIBBLE-TIR</div>
							<div class="card-body">
								<img class="card-img-info" src="images/dribbletir.png" alt="image tirs">
								<p class="card-text" style="padding-top:10px"><button class="btn btn-outline-primary btn-video" data-title="Atelier Dribble-Tir" data-video="./images/DEFI RECRE BASKET DRIBBLETIR.mp4"><i class="fas fa-film"></i> vidéo</button></p>
								<p class="card-text">Découvrez les différents ateliers</p>
								<a href="docs/atelier-basketrecre-dribbletir-cycle1.pdf" target="_blank" class="btn btn-primary btn-download-pdf disabled"><i class="fas fa-file-download"></i> Télécharger cycle 1</a>
								<a href="docs/atelier-basketrecre-dribbletir-cycle2.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 2</a>
								<a href="docs/atelier-basketrecre-dribbletir-cycle3.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 3</a>
							</div>
						</div>	
						
					</div>

					<div class="col-lg-2">
					
						<div class="card card-info card-dribbleshootpass text-center">
							<div class="card-header">DRIBBLE-TIR-PASSE</div>
							<div class="card-body">
								<img class="card-img-info" src="images/pass.png" alt="image tirs">
								<p class="card-text" style="padding-top:10px"><button class="btn btn-outline-primary btn-video" data-title="Atelier Dribble-Tir-Passe" data-video="./images/DEFI RECRE BASKET DRIBBLETIRPASSE.mp4"><i class="fas fa-film"></i> vidéo</button></p>
								<p class="card-text">Découvrez les différents ateliers</p>
								<a href="docs/atelier-basketrecre-dribbletirpasse-cycle1.pdf" target="_blank" class="btn btn-primary btn-download-pdf disabled"><i class="fas fa-file-download"></i> Télécharger cycle 1</a>
								<a href="docs/atelier-basketrecre-dribbletirpasse-cycle2.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 2</a>
								<a href="docs/atelier-basketrecre-dribbletirpasse-cycle3.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Télécharger cycle 3</a>
							</div>
						</div>	
						
					</div>
					
					
				</div>
				
				<div class="row justify-content-center">
					
					<div class="col-lg-2">
						
						<div class="card card-info card-gamebox text-center">
							<div class="card-header">BOITE DE JEUX</div>
							<div class="card-body">
								<img class="card-img-info" src="images/gamesbox.png" alt="image dribbles">
								<p class="card-text">Des jeux ludiques autour du basket</p>
								<a href="docs/jeu-des-7-familles.pdf" target="_blank" class="btn btn-primary btn-download-pdf"><i class="fas fa-file-download"></i> Jeu des 7 familles</a>
							</div>
						</div>
						
					</div>				

					<div class="col-lg-2">
						
						<div class="card card-info card-tuto text-center">
							<div class="card-header">EXERCICES</div>
							<div class="card-body">
								<img class="card-img-info" src="images/tutos.png" alt="image tutos">
								<p class="card-text">Découvrez des exercices, tutos pour bien se préparer</p>
								<a href="images/obe28_appuis.mp4" target="_blank" class="btn btn-primary btn-tutos"><i class="fas fa-film"></i> Les appuis</a>
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
			
			var userData = <?php echo json_encode($userChallengeData); ?>;
			
			function addNewGameInTable(dataschool, $tbody, isyourresult) {
				
				var html = '<tr>';
				
					var school = dataschool.School.School;
					var levels = dataschool.School.Levels;
					
					var dt = moment(dataschool.LastPlayDate);
		
					html += '<td>' + dt.format("LL") + '</td>';
					html += '<td>' + school["SchoolName"] + ", " + school["SchoolAddress"] + '<br>' + school["SchoolZipCode"] + ' - ' + school["SchoolCity"] + '</td>';
					
					if (isyourresult) {
						html += '<td>' + (dataschool.School["Teacher"] ? dataschool.School["Teacher"] : '') + '</td>';
					}
					
					html += '<td>';
		
						for(var ilevel = 0; ilevel < levels.length; ilevel++) {
							html += '<span class="badge badge-warning" title="' + levels[ilevel]["SchoolClassLevelDescription"] + '">' + levels[ilevel]["SchoolClassLevelName"] + '</span> ';
						}
			
					html += '</td>';
					html += '<td style="text-align: right">' + format("# ### ##0.#", dataschool.score) + '</td>';
					
					html += '<td style="text-align:center">';
					
					switch(dataschool.cup){
						case "bronze": html += '<img class="" src="images/ranking-4.png">'; break;
						case "argent": html += '<img class="" src="images/ranking-3.png">'; break;
						case "or": html += '<img class="" src="images/ranking-2.png">'; break;
						case "platinium": html += '<img class="" src="images/ranking-1.png">'; break;
						default:
							html += "-";
							break;
					}
					html += '</td>';
				
				html += '</tr>';
				
				$tbody.append(html);
				
			}
			
			// execute/clear BS loaders for docs
			$(function(){
				
<?php if (!$severalChallenges) { ?>
				
				$(".row-challengeselectcaption").remove();
				$(".col-challenge-data").removeClass("col-md-8").addClass("col-md-12");
				$(".col-challenge-data").closest(".row").css({'margin-left': 0, 'margin-right': 0});
				
<?php } else { ?>
				
				$(".row-challengeselectcaption, .col-challenge-selector").css("display", "");
	
<?php } ?>
				$(".col-challenge-data").closest(".row").css("display", "");
				
				$(".btn-play").on("click", function() {
					
					location.href = "play.php";
					
				});
				
				$('a.list-challenge-item').on('shown.bs.tab', function (e) {
					
					var newTarget = $(e.target);
					var oldTarget = $(e.relatedTarget);
					var newContent = $(newTarget.attr("href"));
					var oldContent = $(oldTarget.attr("href"));
					
					newContent.find(".row-wait-loading").css("display", "");
					newContent.find(".row-challenge-top").css("display", "none");
					
					oldContent.find(".table-challenge-top, .table-challenge-user").find("tbody").html("");
					
					var challengeData = userData[newTarget.attr("data-id")];
					if (challengeData) {
						
						var userTabBody = newContent.find(".table-challenge-user > tbody");
						for(var ischool = 0; ischool < challengeData.Schools.length; ischool++) {
							addNewGameInTable(challengeData.Schools[ischool], userTabBody, true);
						}
						
					}

					/* load last 10 games */
					var ajaxData = {
						'challenge': newTarget.attr("data-id"),
						'user': <?php echo $userConnected->UserId; ?>
					};
										
					$.ajax({
						type: 'POST',
						url: "code/ws/getdata.php?get=lastgames",
						async: true,
						dataType: 'json',
						data: ajaxData,
						newContent: newContent,
						lastTabBody: newContent.find(".table-challenge-top > tbody"),
						currentChallenge: ajaxData.challenge,
					}).done(function(json) {

						if (json.err) {
							formatErrorOnModal(json, $("#modal_message"), { qryError: true, ajaxRetrieveData: true });
							$("#modal_message").modal();
							return;
						}
						
						var cdata = json.records[this.currentChallenge];
						if (cdata) {
							for(var ischool = 0; ischool < cdata.Schools.length; ischool++) {
								addNewGameInTable(cdata.Schools[ischool], this.lastTabBody);
							}
						}
						
					}).fail(function(e) {
						formatErrorOnModal(e, $("#modal_message"), { ajaxRetrieveData: true });
						$("#modal_message").modal();
					}).always(function() {
						this.newContent.find(".row-wait-loading").css("display", "none");
						this.newContent.find(".row-challenge-top").css("display", "");
					});						
										
				  
				});
				
				$('a.list-challenge-item:first').tab('show');
				
				//$(".card.card-info .card-body").on("click", function(e) { 
				//	//e.preventDefault();
				//	if (!$(e.currentTarget).hasClass("card-body")) return;
				//	$(e.currentTarget).find("a.btn-download-pdf").trigger("click"); 
				//});
				
			});
			
		</script>

	</body>
	
</html>