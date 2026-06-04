<?php

	$baseDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	define("_REF_TO_ROOT_DIR_", ".");

	include_once($baseDir . "config.php");
	include_once($baseDir . "common.php");
	include_once($baseDir . "/code/school.php");
	
	$userSchools = School::getUserSchools(array("userids" => array($userConnected->UserId), "activatefordate" => new DateTime("now")));
	$noSchools = count($userSchools) == 0;
	
	$selectSchool = isset($_GET["pus"]) ? $_GET["pus"] : null;
	$firstSchool = null;
	$findSchool = false;
	foreach($userSchools[0]["Schools"] as $school) {
		$id = $school["UserSchoolId"];
		if (!$firstSchool) $firstSchool = $id;
		if ($id == $selectSchool) $findSchool = true;
	}
	if (!$findSchool) $selectSchool = $firstSchool;
	
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
		<?php include($baseDir . DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "std-css.php"); ?>
		
	</head>

	<body class="with-top-navbar">

		<!-- header -->
		<?php include($baseDir . DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "header.php"); ?>

		<div class="container-fluid with-nav-fixed">
			
			<div class="row justify-content-center">
				
				<div class="col-md-2" style="margin-right:5px">

					<div class="form-group row">
							<input type="text" class="form-control" id="sessionDate" value="">
					</div>
  
				</div>
				
			</div>	

			<div class="row justify-content-center">
				
				<div class="col-md-8" style="margin-left:5px">
					
<?php
	
	if ($noSchools) {
		echo '<div class="alert alert-danger" role="alert">Le sytème n\'a pas trouvé d\'école associée à votre compte. Veuillez vous rendre dans les paramètres et ajouter une nouvelle école</div>';
	}
	
?>
					
					<table class="table table-sm table-select">
						  <thead>
							<tr>
								<th scope="col">Ecole</th>
								<th scope="col">Professeur</th>
								<th scope="col">Classe</th>
								<th scope="col"># Elèves</th>
							</tr>
						  </thead>
						  <tbody>
						  
<?php
	
				foreach($userSchools[0]["Schools"] as $school) {
					$select = $selectSchool == $school["UserSchoolId"];
					echo '<tr class="' . ($select ? 'table-warning' : '') . '" datat-id="' . $school["UserSchoolId"] . '">';
					echo '<td>' . $school["School"]["SchoolName"] . ", " . $school["School"]["SchoolAddress"] . '<br>' . $school["School"]["SchoolZipCode"] . ' - ' . $school["School"]["SchoolCity"] . '</td>';
					echo '<td>' . ((isset($school["Teacher"]) && $school["Teacher"] != "") ? $school["Teacher"] : "") . '</td>';
					echo '<td>';
						for($i = 0; $i < count($school["Levels"]); $i++) {
							echo '<span class="badge badge-warning">' . $school["Levels"][$i]["SchoolClassLevelName"] . '</span> ' . $school["Levels"][$i]["SchoolClassLevelDescription"];
							if ($i+1 != count($school["Levels"])) echo '<br>';							
						}
					echo '</td>';
					echo '<td>' . $school["NbStudents"] . '</td>';
					echo '</tr>';
				}
				
?>						  
						  
						  </tbody>
						</table>
						
				</div>
				
			</div>
			
			<div class="card-deck justify-content-center">
		
<?php



	for($i = 0; $i < 5; $i++) {
		
		$img = "";
		$name = "";
		$classnum = "";
		$classcard = "";
		$btnvideo = ""; $videopath="";
		$dataname = "";
		switch($i) {
			case 0: $img="images/dribble.jpg"; $classcard="card-dribble"; $classnum="dribble-value"; $dataname = "d"; $name="DRIBBLE"; $btnvideo = "btn-outline-primary"; $videopath = "./images/DEFI RECRE BASKET DRIBBLE.mp4"; break;
			case 1: $img="images/shoot.jpg"; $classcard="card-shoot"; $classnum="shoot-value"; $dataname = "s"; $name="TIR"; $btnvideo = "btn-outline-primary"; $videopath = "./images/DEFI RECRE BASKET TIR.mp4"; break;
			case 2: $img="images/pass.png"; $classcard="card-pass"; $classnum="pass-value"; $dataname = "p"; $name="PASSE"; $btnvideo = "btn-outline-primary" ;$videopath = "./images/DEFI RECRE BASKET PASSE.mp4"; break;
			case 3: $img="images/dribbletir.png"; $classcard="card-dribbletir"; $classnum="dribleshoot-value"; $dataname = "ds"; $name="DRIBBLE-TIR"; $btnvideo = "btn-outline-primary" ;$videopath = "./images/DEFI RECRE BASKET DRIBBLETIR.mp4"; break;
			case 4: $img="images/pass.png"; $classcard="card-dribbleshootpass"; $classnum="dribbleshootpass-value"; $dataname = "dsp"; $name="DRIBBLE-TIR-PASSE"; $btnvideo = "btn-outline-primary" ;$videopath = "./images/DEFI RECRE BASKET DRIBBLETIRPASSE.mp4"; break;
		}
		
?>

<div class="card card-play minus-plus-parent text-center <?php echo $classcard; ?>">
	<div class="card-header" style="text-align:center"><?php echo $name; ?></div>
	<div class="card-body">
		<img class="card-img-playinfo" src="<?php echo $img; ?>" alt="image dribbles">
		<button class="btn <?php echo $btnvideo; ?> btn-video" data-title="ATELIER <?php echo $name; ?>" data-video="<?php echo $videopath; ?>"><i class="fas fa-film"></i> vidéo</button>
		<input type="text" class="form-control minus-plus-value <?php echo $classnum; ?>" data-name="<?php echo $dataname; ?>" value="0">
	</div>
	<div class="card-footer">
		<div class="btn-group d-flex" role="group">
			<button type="button" class="btn btn-sm btn-danger minus-button w-100" disabled="disabled"><i class="fa fa-minus"></i></button>
			<button type="button" class="btn btn-sm btn-success plus-button w-100"><i class="fa fa-plus"></i></button>
		</div>
	</div>
</div>
			
<?php
	}
?>
			
			</div>
			
			<div class="row" style="text-align: center">
			
				<div class="col" style="text-align:center">
					<button class="btn btn-primary btn-validate"><i class="far fa-save"></i> Enregistrer</button>
					<button class="btn btn-default btn-outline-secondary btn-exit" style="margin-left: 10px"><i class="far fa-times-circle"></i> Quitter</button>
				</div>
			
			</div>
		
		</div> <!-- container-fluid -->
		
		<!-- FOOTER -->
		<?php include($baseDir . DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "footer.php"); ?>
		
		<!-- JS -->
		<?php include($baseDir . DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "std-js.php"); ?>		
		
		<script type="text/javascript">
	
			function lockScreen(status) {
				$(".btn-validate, .minus-plus-value, .plus-button, .minus-button, #sessionDate").prop("disabled", status);
			}
	
			// execute/clear BS loaders for docs
			$(function(){
				
<?php if ($noSchools) { ?>
					
	lockScreen(true);
					
<?php } ?>
				
				$('#sessionDate').datepicker({
					language: 'fr'
				}).datepicker("setDate", new Date());
				
				$(".btn-exit").on("click", function() {
					location.href = "overview.php";
				});
				
				$(".btn-validate").on("click", function() { 
					
					lockScreen(true);
					WaitSplash(true);
					
					var dt = moment($("#sessionDate").datepicker("getDate"));
					var currentdt = moment();
					
					dt.hour(currentdt.hour());
					dt.minute(currentdt.minute());
					dt.second(currentdt.second());
					
					var ajaxData = {
						'user': <?php echo $userConnected->UserId ?>,
						'userschoolid' : $(".table-select tbody tr.table-warning").attr("datat-id"),
						'date': dt.format("YYYY-MM-DD HH:mm:ss"),
						'values': {}
					};
					
					$(".minus-plus-value").each($.proxy(function(index, item) {
						
						var $this = $(item);
						var dataName = $this.attr("data-name");
						if (dataName) {
							this.values[dataName] = $this.val();
						}
						
					}, ajaxData));
					
					$.ajax({
						type: 'POST',
						url: "code/ws/save.php?fct=addgamesession",
						async: true,
						dataType: 'json',
						data: ajaxData,
					}).done(function(json) {
						location.href = "overview.php";
					}).fail(function(e) {
						formatErrorOnModal(e, $("#modal_message"));
						$("#modal_message").modal();
					}).always(function() {
						WaitSplash(false);
						lockScreen(false);
					});
					
				}); 
	
				$(".table.table-select tr").on("click", function() {
					
					var $this = $(this);
					var $table = $this.closest("table");
					
					$table.find("tbody tr").removeClass("table-warning");
					$table.find("tbody .td-select").html("");
					$this.closest("tr").addClass("table-warning");
					$this.closest("tr").find(".td-select").html('<i class="fas fa-check"></i>');
					
				});
	
			  $('.minus-button').click(function(e) {
				
				// change this to whatever minimum you'd like
				const minValue = 0
				
				var $this = $(this);
				var $parent = $this.closest(".minus-plus-parent");
				var $input = $parent.find(".minus-plus-value");
				var currentInput = parseInt($input.val());
				
				if (currentInput > minValue) {
					currentInput--;
					$input.val(currentInput);
					if (currentInput <= minValue) $parent.find(".minus-button").prop("disabled", true);
					$parent.find(".plus-button").prop("disabled", false);
				}
				
			  });

			  $('.plus-button').click(function(e) {
				  
				const maxValue = 500;

				var $this = $(this);
				var $parent = $this.closest(".minus-plus-parent");
				var $input = $parent.find(".minus-plus-value");
				var currentInput = parseInt($input.val());
				
				if (currentInput < maxValue) {
					currentInput++;
					$input.val(currentInput);
					$parent.find(".minus-button").prop("disabled", false);
					if (currentInput >= maxValue) $parent.find(".plus-button").prop("disabled", true);
				}				
				
			  });				
				
			});
			
		</script>

	</body>
	
</html>