<?php

	$baseDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	define("_REF_TO_ROOT_DIR_", ".");

	include_once($baseDir . "config.php");
	include_once($baseDir . "common.php");
	
	$needSchools = !School::UserHasDefinedSchool($userConnected->UserId);

	$schoolsLevel = School::getSchoolLevels();
	$colSchoolLevel1 = array();
	$colSchoolLevel2 = array();
	$pivot = floor(count($schoolsLevel) / 2) + 1;
	$i = 0;
	foreach($schoolsLevel as $sl) {
		if ($i < $pivot) {
			$colSchoolLevel1[] = $sl;
		}
		else {
			$colSchoolLevel2[] = $sl;
		}
		$i++;
	}
	
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
			
			<div class="row" style="margin-bottom: 10px">
			
				<div class="col">
					<button class="btn btn-primary btn-back">Retour</button>
				</div>
			
			</div>

			<div id="settingItems">
			
			  <div class="card">
				<div class="card-header" id="heading_setting_schools">
				  <h5 class="mb-0">
					<button class="btn btn-link" data-toggle="collapse" data-target="#collapse_setting_schools" aria-expanded="true" aria-controls="collapse_setting_schools">
					  Informations personnelles
					</button>
				  </h5>
				</div>
				<div id="collapse_setting_schools" class="collapse show" aria-labelledby="heading_setting_schools" data-parent="#settingItems">
				  <div class="card-body">
						
					<div class="form-row">
						<div class="form-group col-md-6">
						  <label for="inputEmail4">Email</label>
						  <input type="email" class="form-control" id="inputEmail4" placeholder="Entrez votre Email" value="<?php echo $userConnected->UserEmail; ?>">
						</div>
					</div>
	
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="inputLastName">Nom</label>
							<input type="text" class="form-control" id="inputLastName" placeholder="Entrez votre Nom" value="<?php echo $userConnected->UserLastName; ?>">
						</div>
						<div class="form-group col-md-6">
							<label for="inputFirstName">Prénom</label>
							<input type="text" class="form-control" id="inputFirstName" placeholder="Entrez votre Prénom" value="<?php echo $userConnected->UserFirstName; ?>">
						</div>
					</div>
					
					<div class="form-group">
						<button class="btn btn-primary">Changer votre mot de passe</button>
					</div>
  					
				  </div>
				</div>
			  </div>
			  
			  <div class="card">
				<div class="card-header" id="schoolcard">
				  <h5 class="mb-0">
					<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSchool" aria-expanded="false" aria-controls="collapseSchool">
					  Mes affectations
					</button>
				  </h5>
				</div>
				<div id="collapseSchool" class="collapse" aria-labelledby="schoolcard" data-parent="#settingItems">
				  <div class="card-body">
						
						<div style="margin-bottom:20px" class="listschoolsassigned">Liste de mes affectations :</div>
						
						<div class="row listschoolsassigned">
							
							<table class="table table-sm table-userschools">
								<thead class="thead-light">
									<tr>
										<th></th>
										<th>Date</th>
										<th>Ecole</th>
										<th>Classe</th>
										<th>Professeur</th>
										<th># Elèves</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							
						</div>
						
						<div class="row listschoolsassigned">
							<button class="btn btn-primary btn-addschool">Ajouter une affectation</button>
						</div>
						
						<div class="row selectmyschoolbuttons selectmyschool" style="margin-top: 10px;">
						
								<div class="btn-group btn-group-toggle btn-group-toggle-selectschool" data-toggle="buttons">
								
									<label class="btn btn-optionselectschool">
										<input type="radio" name="options" id="optionselectschool" autocomplete="off" checked>Sélectionner votre école
									</label>
									<label class="btn btn-optionselectschoollevel">
										<input type="radio" name="options" id="optionselectschoollevel" autocomplete="off">Sélectionner votre classe
									</label>
		
								</div>
														
						</div> <!-- selectmyschoolbuttons -->
							
						<div class="row selectmyschoolcontainer selectmyschool">
							
							<div id="carouselAddSchools" class="carousel slide" data-interval="false">
							  <div class="carousel-inner">
								<div class="carousel-item active">
	
									<div class="col-md-12 selectmyschoolcurrentselected">
										<div class="alert alert-success" role="alert">
											<span><b>Ecole séléctionée :</b> <span class="selectmyschoolcurrentselectedtxt"></span>
										</div>
									</div>
									
									<div class="col-md-12 selectmyschoolfiltercontainer">
										
										<input type="text" class="form-control" style="display: inline; width: 150px" id="filtercp" placeholder="Code Postal">
										<button class="btn btn-primary btn-filterCP">Filtrer</button>
										
									</div>
									
									<div class="col-md-12 selectmyschooltablecontainer"></div>
								
								</div>
								<div class="carousel-item">
								
									<div class="col-md-12 selectmyschoollevelcontainer">
										<div class="addschoolcurrentselectcontainer"><span class="addschoolcurrentselectcontainertitle">Votre école : </span><span class="addschoolcurrentselect"></span></div>
										<span class="addschoolcaption">Vous pouvez selectionner plusieurs niveaux</span>
										<div class="row" style="margin-top: 10px;">
											<div class="col-md-6">
	<?php

		foreach($colSchoolLevel1 as $sl) {
			$id = "sl" . $sl["SchoolClassLevelId"];
			$caption = $sl["SchoolClassLevelName"];
			if ($sl["SchoolClassLevelDescription"]) $caption .= " (" . $sl["SchoolClassLevelDescription"] . ")";		
			echo '<div class="form-check"><input type="checkbox" class="form-check-input schoollevel" data-id="' . $sl["SchoolClassLevelId"] . '" id="' . $id . '"><label class="form-check-label" for="' . $id . '">' . $caption . '</label></div>';
		}

	?>															
											</div>
											<div class="col-md-6">
	<?php

		foreach($colSchoolLevel2 as $sl) {
			$id = "sl" . $sl["SchoolClassLevelId"];
			$caption = $sl["SchoolClassLevelName"];
			if ($sl["SchoolClassLevelDescription"]) $caption .= " (" . $sl["SchoolClassLevelDescription"] . ")";		
			echo '<div class="form-check"><input type="checkbox" class="form-check-input schoollevel" data-id="' . $sl["SchoolClassLevelId"] . '" id="' . $id . '"><label class="form-check-label" for="' . $id . '">' . $caption . '</label></div>';
		}

	?>														
											</div>											
										</div>
										<div class="row" style="margin-top: 10px;">
											
											<label class="lbladdschoolnbstudent col-md-2" for="addschoolnbstudent">Nombre d'élèves</label>
											<div class="col-md-3">
												<input type="text" class="form-control" id="addschoolnbstudent" placeholder="# élèves">
											</div>
										
										</div>
										<div class="row" style="margin-top: 10px;">
											
											<label class="lbladdschoolnbstudent col-md-2" for="addschoolteacher">Nom du professeur</label>
											<div class="col-md-3">
												<input type="text" class="form-control" id="addschoolteacher" placeholder="Nom du professeur">
											</div>
										
										</div>										
										
									</div>
									
								</div> <!-- carrousel-item -->										
								
								<div class="row" style="margin-top: 10px;">
								
									<div class="alert alert-danger alert-addschool" role="alert" style="width:100%">
										This is a danger alert—check it out!
									</div>
								
								</div>
										
								<div class="row">
									<div style="margin-left: auto; margin-right: auto">
										<button class="btn btn-default btn-canceladdschool">Annuler</button>								
										<button class="btn btn-primary btn-addschoolsave">Sauvegarder</button>
									</div>
								</div>
								
							  </div>
							</div>
							
						</div> <!-- selectmyschoolcontainer -->
						
				  </div>
				</div>
			  </div>
			  
			  <div class="card">
				<div class="card-header" id="headingThree">
				  <h5 class="mb-0">
					<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
					  Historique
					</button>
				  </h5>
				</div>
				<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#settingItems">
				  <div class="card-body">

						<div style="margin-bottom:20px">Liste de mes écoles :</div>
						
						<!--table class="table table-sm">
						  <thead>
							<tr>
							  <th scope="col"></th>
							  <th scope="col">Date</th>
							  <th scope="col">Ecole</th>
							  <th scope="col">Information</th>
							  <th scope="col"></th>
							</tr>
						  </thead>
						  <tbody>
							<tr>
							  <td><i class="far fa-eye icon-action icon-action-view"></i></td>
							  <td>20/09/2018</td>
							  <td>Ecole élémentaire - Yann Arthus-Bertrand - BARJOUVILLE</td>
							  <td>Passes : 30 - Dribbles : 10 - Tirs : 30</td>
							  <td><i class="far fa-trash-alt icon-action icon-action-delete"></i></td>
							</tr>
							<tr>
							  <td><i class="far fa-eye icon-action icon-action-view"></i></td>
							  <td>25/09/2018</td>
							  <td>Ecole élémentaire - Yann Arthus-Bertrand - BARJOUVILLE</td>
							  <td>Passes : 10 - Dribbles : 15 - Tirs : 20</td>
							  <td><i class="far fa-trash-alt icon-action icon-action-delete"></i></td>
							</tr>
							<tr>
							  <td><i class="far fa-eye icon-action icon-action-view"></i></td>
							  <td>05/10/2018</td>
							  <td>Ecole élémentaire - Maurice Fanon - AUNEAU</td>
							  <td>Passes : 20 - Dribbles : 40 - Tirs : 20</td>
							  <td><i class="far fa-trash-alt icon-action icon-action-delete"></i></td>
							</tr>
							<tr>
							  <td><i class="far fa-eye icon-action icon-action-view"></i></td>
							  <td>20/19/2018</td>
							  <td>Ecole élémentaire - Yann Arthus-Bertrand - BARJOUVILLE</td>
							  <td>Passes : 30 - Dribbles : 30 - Tirs : 30</td>
							  <td><i class="far fa-trash-alt icon-action icon-action-delete"></i></td>
							</tr>							
						  </tbody>
						</table-->				  
				  
				  </div>
				</div>
			  </div>
			  
			</div>
			
			<div class="row" style="text-align: center">
			
				<div class="col" style="text-align:center">
					<button class="btn btn-primary btn-back">Retour</button>
				</div>
			
			</div>
			
			<!--div class="table-schools">
			</div-->
			
		</div> <!-- container-fluid -->
		
		<!-- FOOTER -->
		<?php include($baseDir . DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "footer.php"); ?>
		
		<!-- JS -->
		<?php include($baseDir . DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "std-js.php"); ?>		
	
		<script type="text/javascript">
			
			var tableSchools = null;
			
			function addUserSchool(data, options) {
				
				var findTR = $('.table-userschools tr[data-id="' + data["UserSchoolId"] + '"]');
				
				var schoolname = data["School"]["SchoolName"] + ", " + data["School"]["SchoolAddress"] + '<br>' + data["School"]["SchoolZipCode"] + ' - ' + data["School"]["SchoolCity"];
				var schoolteacher = (data["Teacher"] ? data["Teacher"] : "");
				var schoolnbstudents = data["NbStudents"];
				var schoollevels = "";

				for(var i = 0; i < data["Levels"].length; i++) {
					schoollevels += '<span class="badge badge-warning">' + data["Levels"][i]["SchoolClassLevelName"] + '</span> ' + data["Levels"][i]["SchoolClassLevelDescription"];
					if (i+1 != data["Levels"].length) schoollevels += '<br>';
				}
					
				if (findTR.length > 0) {
					
					findTR.find(".userschoolname").html(schoolname);
					findTR.find(".userschoolteacher").html(schoolteacher);
					findTR.find(".userschoolnbstudents").html(schoolnbstudents);
					findTR.find(".userschoollevels").html(schoollevels);
					
					return;
				}
				
				var html = '<tr data-id="' + data["UserSchoolId"] + '">';
				
				var dt = moment(data["DateFrom"]);
				
				html += '<td class="userschoolplay"><button class="btn btn-primary btn-play"><i class="fas fa-basketball-ball" style="margin-right: 10px"></i>Je joue</button></td>';
				html += '<td class="userschooldate">' + dt.format("LL") + '</td>';
				html += '<td class="userschoolname">' + schoolname + '</td>';
				html += '<td class="userschoollevels">' + schoollevels + '</td>';
				
				html += '<td class="userschoolteacher">' + schoolteacher + '</td>';
				html += '<td class="userschoolnbstudents">' + schoolnbstudents + '</td>';
				html += '<td><button class="btn btn-primary btn-edit"><i class="fas fa-edit"></i></button> <button class="btn btn-danger btn-delete"><i class="far fa-trash-alt"></i></button></td>';
				
				html += '</tr>';
				
				var $html = $(html);
				
				$html.find(".btn-play").on("click", function() {
					
					var $this = $(this);
					var $tr = $this.closest("tr");
					
					location.href = "play.php?pus=" + $tr.attr("data-id");
					
				});
				
				$html.find(".btn-edit").on("click", $.proxy(function(e) {
					
					$(".selectmyschoolcontainer").attr("data-id", this["UserSchoolId"]);
					addSchool(this);
					
				}, data));
				
				$html.find(".btn-delete").on("click", $.proxy(function(e) {
					
					var info = "<b>Ecole :</b> " + this["School"]["SchoolName"] + ", " + this["School"]["SchoolAddress"] + ' - ' + this["School"]["SchoolZipCode"] + ' - ' + this["School"]["SchoolCity"];
					for(var i = 0; i < this["Levels"].length; i++) {
						info += ' <span class="badge badge-warning">' + this["Levels"][i]["SchoolClassLevelName"] + '</span>';
					}					
										
					info += "<br><b>Professeur :</b> " + (this["Teacher"] ? this["Teacher"] : "");
					info += "<br><b># Elèves :</b> " + this["NbStudents"] + "<br>";
					
					var options = {
						'title': 'Confirmation suppression',
						'text': 'Vous aller supprimer l`\'affectation suivante :<br>' + info + '<br><br><b><i class="fas fa-exclamation-triangle"></i> Attention, toutes les parties associées à cette affectation seront également supprimées.</b>',
						'save': {
							'visible': true,
							'class': 'btn-danger',
							'text': 'Supprimer',
							'fct': $.proxy(function(m) {
								
								m(); //Close Modal
								
								WaitSplash(true);
														
								$.ajax({
									type: 'POST',
									url: "code/ws/save.php?fct=delschool",
									async: true,
									dataType: 'json',
									data: { 'user': <?php echo $userConnected->UserId; ?>, 'data': this },
								}).done(function(json) {
									
									if (json.err) {
										formatErrorOnModal(json, $("#modal_message"), {'qryError': true});
										$("#modal_message").modal();
									}
									else {
										$('.table-userschools tr[data-id="' + json.data.UserSchoolIdDeleted + '"]').remove();
									}
									
								}).fail(function(e) {
									formatErrorOnModal(e, $("#modal_message"));
									$("#modal_message").modal();						
								}).always(function() {
									WaitSplash(false);
								});										
							
								return false;
								
							}, this)
						},
						'cancel': {
							'text': 'Annuler'
						}
					};
					
					displayMessageModal(options);
					
				}, data));				
				
				if (options && options.addfirst === true) {
					$(".table-userschools tbody").prepend($html);
					return;
				}
				$(".table-userschools tbody").append($html);
				
			};
			
			function addSchoolButtonStates(btn1, btn2, focus) {
				
				$(".btn-optionselectschool, .btn-optionselectschoollevel").removeClass("btn-primary btn-outline-primary disabled");
				$("#optionselectschool, #optionselectschoollevel").prop({"checked": false, "disabled": true});

				if (btn1 === true) {
					$(".btn-optionselectschool").addClass("btn-primary");
					$(".btn-optionselectschool input").prop("disabled", false);
				}
				else if (btn1 === false) {
					$(".btn-optionselectschool").addClass("btn-outline btn-outline-primary disabled");
				}

				if (btn2 === true) {
					$(".btn-optionselectschoollevel").addClass("btn-primary");
					$(".btn-optionselectschoollevel input").prop("disabled", false);					
				}
				else if (btn2 === false) {
					$(".btn-optionselectschoollevel").addClass("btn-outline btn-outline-primary disabled");
				}
				
				if (focus) { 
					$(focus).addClass("active");
					$(focus).find("input").prop("checked", true);
				}

			}
			
			function addSchool(data) {
				
				$(".btn-addschool").prop("disabled", true);
				$(".selectmyschool").css("display", "flex");
				$(".alert-addschool, .listschoolsassigned").css("display", "none");
				$("#addschoolnbstudent, #addschoolteacher").val("");
				$(".addschoolcurrentselect").attr("data-id", (data ? data.School.SchoolId : ""));
				$(".selectmyschoolcurrentselectedtxt").html("");
				
				$(".btn-canceladdschool").off("off").on("click", function() {
					
					$(".btn-addschool").prop("disabled", false);
					$(".selectmyschool").css("display", "none");
					$(".listschoolsassigned").css("display", "");
					$(".selectmyschoolcontainer").removeAttr("data-id");
					
				});
				
				$(".btn-addschoolsave").off("click").on("click", function() {
					
					var nbStudents = $("#addschoolnbstudent").val();
					var schoolLevels = [];
					
					if (!$.isNumeric($(".addschoolcurrentselect").attr("data-id"))) {
						$(".alert-addschool").html("Sélectionner une école.");
						$(".alert-addschool").css("display", "");
						return;
					}
					
					$(".schoollevel:checked").each($.proxy(function(index, item) {
						var $this = $(item);
						this.push($this.attr("data-id"));
					}, schoolLevels));
					
					if (schoolLevels.length == 0) {
						$(".alert-addschool").html("Vous devez sélectionner au moins 1 niveau.");
						$(".alert-addschool").css("display", "");
						return;
					}
					else if (!$.isNumeric(nbStudents)) {
						$(".alert-addschool").html("Vous devez renseigner le nombre d'élèves.");
						$(".alert-addschool").css("display", "");
						return;						
					}
										
					WaitSplash(true);
					
					$.ajax({
						type: 'POST',
						url: "code/ws/save.php?fct=addschool",
						async: true,
						dataType: 'json',
						data: { 'user': <?php echo $userConnected->UserId; ?>, 'userschool': $(".selectmyschoolcontainer").attr("data-id"), 'school': $(".addschoolcurrentselect").attr("data-id"), 'levels': schoolLevels, 'nbstudents': nbStudents, 'teacher': $("#addschoolteacher").val() },
					}).done(function(json) {
					
						if (json.data.length > 0) {
							var udata = json.data[0];
							for(var i = 0; i < udata.Schools.length; i++) {
								addUserSchool(udata.Schools[i], { 'addfirst': true });
							}
						}
				
						$(".btn-addschool").prop("disabled", false);
						$(".selectmyschool").css("display", "none");
						$(".listschoolsassigned").css("display", "");
						$(".selectmyschoolcontainer").removeAttr("data-id");
					
					}).fail(function(e) {
						formatErrorOnModal(e, $("#modal_message"));
						$("#modal_message").modal();						
					}).always(function() {
						WaitSplash(false);
					});			
							
				});
				
				if (data) {
					$(".addschoolcurrentselect").html(data.School.SchoolName + ' / ' + data.School.SchoolAddress + ' / ' + data.School.SchoolZipCode + ' / ' + data.School.SchoolCity);
					addSchoolButtonStates(true, true);
				}
				else {
					//Reset
					addSchoolButtonStates(true, false, ".btn-optionselectschool");					
				}

				$(".btn-filterCP").off("click").on("click", function() {
					if (tableSchools) tableSchools.load({ 'urldata': { 'filtercp': $("#filtercp").val() }});
				});
				
				$(".schoollevel").prop("checked", false);
				
				$(".btn-optionselectschool").off("click").on("click", function() {
					$("#carouselAddSchools").carousel(0);
				});
				$(".btn-optionselectschoollevel").off("click").on("click", function() {
					$("#carouselAddSchools").carousel(1);
				});
				
				$("#carouselAddSchools").carousel(0);
				$("#filtercp").val("");
				
				if (data) {
					for(var ilevel = 0; ilevel < data.Levels.length; ilevel++) $('.schoollevel[data-id="' + data.Levels[ilevel].SchoolClassLevelId + '"]').prop("checked", true);
					$("#addschoolteacher").val(data.Teacher ? data.Teacher : "");
					$("#addschoolnbstudent").val(data.NbStudents ? data.NbStudents : "");
					var schooltxt = data.School.SchoolName + ' / ' + data.School.SchoolAddress + ' / ' + data.School.SchoolZipCode + ' / ' + data.School.SchoolCity;
					$(".selectmyschoolcurrentselectedtxt").html(schooltxt);
				}
				
				if (!tableSchools) {
					
					tableSchools = new ytable({
						'template': new ytable_template_bootstrap4(),
						'container': $(".selectmyschooltablecontainer"),
						'fields': [ 
							{ 'key': 'SchoolId', 'caption': 'Id', 'visible': false },
							{ 'key': 'SchoolName', 'caption': 'Nom', 'sortable': true },
							{ 'key': 'SchoolAddress', 'caption': 'Adresse', 'sortable': true  },
							{ 'key': 'SchoolZipCode', 'caption': 'Code Postal', 'sortable': true },
							{ 'key': 'SchoolCity', 'caption': 'Ville', 'sortable': true }
						],
						'url': {
							'get': 'code/ws/getdata.php?get=schools'
						},
						'defaultrecordsperpage': 10,
						'selectable': true,
						'selectall': false,
						'recordId': 'SchoolId',
						'events': {
							'selectionchange': function(e, selected, unselected) {
								
								if (selected.length == 0) {
									$(".selectmyschoolcurrentselectedtxt").html("");
									addSchoolButtonStates(true, false);
								}
								else {
									$(".addschoolcurrentselect").attr("data-id", selected[0].record.SchoolId);
									var schooltxt = selected[0].record.SchoolName + ' / ' + selected[0].record.SchoolAddress + ' / ' + selected[0].record.SchoolZipCode + ' / ' + selected[0].record.SchoolCity;
									$(".addschoolcurrentselect").html(schooltxt);
									$(".selectmyschoolcurrentselectedtxt").html(schooltxt);
									addSchoolButtonStates(true, true);
								}
								
							}
						}
					});					
					
				}
				
				tableSchools.load({ 'reset': true });
				
			};
			
			// execute/clear BS loaders for docs
			$(function(){
		
				$(".icon-action-view").on("click", function() {
					location.href = "play.php";
				});
				
				$(".icon-action-delete").on("click", function() {
					$(this).closest("tr").remove();
				});				
		
				$(".btn-back").on("click", function() {
					location.href = "overview.php";
				});
				
<?php if ($needSchools) { ?>
		
		var options = {
			'title': 'Information',
			'text': 'Pour jouer, vous devez au moins renseigner une affectation (école / classe).<br>Merci'
		};
		
		displayMessageModal(options);		
				
		$('#schoolcard .btn[data-target="#collapseSchool"]').click();
		
<?php } else { ?>
	
		$('#collapseSchool').on('shown.bs.collapse', function () {
			
			if ($("#collapseSchool").attr("data-call") != "1") {
			
				WaitSplash(true);
			
				$.ajax({
					type: 'POST',
					url: "code/ws/getdata.php?get=userschools",
					async: true,
					dataType: 'json',
					data: { 'user': <?php echo $userConnected->UserId; ?> },
				}).done(function(json) {
					
					if (json.err) {
						formatErrorOnModal(json, $("#modal_message"), {'qryError': true});
						$("#modal_message").modal();
					}
					else {
						if (json.records.length > 0) {
							var udata = json.records[0];
							for(var i = 0; i < udata.Schools.length; i++) {
								addUserSchool(udata.Schools[i]);
							}
						}
					}
					
					$("#collapseSchool").attr("data-call", "1");
					WaitSplash(false);
					
				}).fail(function(e) {
					formatErrorOnModal(e, $("#modal_message"));
					$("#modal_message").modal();						
					WaitSplash(false);
				});		
					
			}
			
		});			
	
<?php } ?>				

				$(".btn-addschool").on("click", function() { addSchool(); });
				
			});
			
		</script>

	</body>
	
</html>