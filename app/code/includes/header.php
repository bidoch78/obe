<?php
	
	$userConnected = User::getUserConnected();
	
?>

<nav class="navbar navbar-expand-md navbar-light fixed-top bg-light">
  <a href="<?php echo (($userConnected) ? "overview.php" : "index.php"); ?>"><img class="logo-obe28" src="./images/obetitre_new_28.png" /></a>
  <a class="navbar-brand navbar-apptitle" href="<?php echo (($userConnected) ? "overview.php" : "index.php"); ?>"><?php echo _APPLICATION_NAME_; ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
		<!--li class="nav-item">
			<button class="btn btn-primary my-2 my-sm-0 px-3 btn-signin" data-info="playnow">Je Joue Maintenant</button>
		</li-->		
      <!--li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
      </li-->
    </ul>
    <ul class="navbar-nav justify-content-center">

<?php	
	
	if ($userConnected) {
?>
		
	  
<?php
	}
?>
      <!--li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Dropdown
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li-->
    </ul>
    <div class="form-inline my-2 my-lg-0 nav-right-part">

<?php	
	
	if ($userConnected) {

		echo '<button class="btn btn-outline-secondary my-2 my-sm-0 btn-settings"><i class="fa fa-cog"></i></button>';	
		echo '<button class="btn btn-outline-danger my-2 my-sm-0 btn-disconnect"><i class="fas fa-sign-out-alt"></i></button>';

	}
	else {
		echo '<button class="btn btn-outline-danger my-2 my-sm-0 btn-signin" data-info="subscribe">Inscription</button>';
		echo '<button class="btn btn-outline-primary my-2 my-sm-0 btn-signin" data-info="signin">Connexion</button>';
		
	}
	
?>		
		
    </div>
  </div>
</nav>

<div class="modal fade modal-login" id="modal-login" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<span class="modal-title"><?php echo _APPLICATION_NAME_; ?></span>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
		
		<div class="row">
			<div class="col">
				<div class="alert alert-danger" style="display:none" role="alert">
					This is a danger alert—check it out!
				</div>
			</div>
		</div>
				
		<div class="div-snewlink div-info">
		
			<input type="hidden" id="_suseremail" name="_suseremail" data-name="l" value="">
			<div class="row">
				<div class="col">
					<button type="button" disabled class="btn btn-danger btn-connect">Envoyer</button>
				</div>
			</div>			
		
		</div>
				
		<div class="div-login div-info">
		
			<div class="row">
				<div class="col">
					<input type="text" id="_userlogin" name="_userlogin" data-name="l" class="form-control app-auth app-login" value="" placeholder="E-mail">
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<input type="password" id="_userpwd" name="_userpwd" data-name="p" class="form-control app-auth app-password" placeholder="Mot de passe">
				</div>
			</div>					
	  
			<div class="row">
				<div class="col">
					<div class="form-check">
						<input type="checkbox" class="form-check-input app-auth" data-name="r" name="_userremimber" id="_userremimber">
						<label class="form-check-label" for="_userremimber">Se souvenir de moi</label>
					</div>
				</div>
			</div>					
		
			<div class="row">
				<div class="col">
					<button type="button" class="btn btn-danger btn-connect">Se connecter</button>
				</div>
			</div>
			<div class="row">
				<div class="col" style="text-align:center">
					<a href="#" class="link-forgot-mypassword a-link">Vous avez oublié votre mot de passe</a>
				</div>
			</div>
			<div class="row">
				<div class="col" style="text-align:center; margin-top:10px; padding-top: 10px; border-top: 1px #cecece solid;">
					Vous n'avez pas de compte ? <a href="#" class="switch-to-subscribe a-link">S'inscire</a>
				</div>
			</div>
					
		</div>
		
		<div class="div-vsubscribe div-info">
			
			<div class="row">
				<input type="hidden" id="_vusertoken" name="_vusertoken" data-name="t" value="">
				<div class="col">
					<label for="_vuserlogin">Merci d'entrer l'email qui a reçu le code d'activation</label>
					<input type="text" id="_vuserlogin" name="_vuserlogin" data-name="l" class="form-control app-login app-auth" value="" placeholder="E-mail">
				</div>
			</div>	
			
			<div class="row">
				<div class="col">
					<button type="button" disabled class="btn btn-danger btn-connect">Valider</button>
				</div>
			</div>				
		
		</div>
		
		<div class="div-subscribe div-info">
		
			<div class="row">
				<div class="col">
					<input type="text" id="_nuserfirstname" name="_nuserfirstname" data-name="fn" class="form-control app-auth" placeholder="Prénom">
				</div>
			</div>

			<div class="row">
				<div class="col">
					<input type="text" id="_nuserlastname" name="_nuserlastname" data-name="ln" class="form-control app-auth" placeholder="Nom">
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<input type="text" id="_nuserlogin" name="_nuserlogin" data-name="l" class="form-control app-login app-auth" value="" placeholder="E-mail">
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<input type="password" id="_nuserpwd" name="_nuserpwd" data-button="btn-connect" data-parent="div-subscribe" data-name="p" class="form-control password-check app-password app-auth" placeholder="Mot de passe (6 caractères minimum)">
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<button type="button" disabled class="btn btn-danger btn-connect">Soumettre</button>
				</div>
			</div>			
			
		</div>
		
		<div class="div-changepassword div-info">
		
			<div class="row">
				<div class="col">
					<input type="text" id="_cuserlogin" name="_nuserlogin" data-name="l" class="form-control app-login app-auth" value="" placeholder="Confirmation de votre E-mail">
				</div>
			</div>

			<div class="row">
				<div class="col">
					<input type="password" id="_cuserpwd1" name="_cuserpwd1" data-button="btn-connect" data-parent="div-changepassword" data-name="p1" class="form-control password-check app-password app-auth" placeholder="Mot de passe (6 caractères minimum)">
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<input type="password" id="_cuserpwd2" name="_cuserpwd2" data-button="btn-connect" data-parent="div-changepassword" data-name="p2" class="form-control app-password app-auth" placeholder="Confirmation du mot de passe">
				</div>
			</div>
			
			<div class="row">
				<div class="col">
					<button type="button" disabled class="btn btn-danger btn-connect">Soumettre</button>
				</div>
			</div>			
			
		</div>
		
		<div class="row">
			<div class="col">
				<div class="alert alert-success" style="display:none" role="alert">
					This is a success !!
				</div>
			</div>
		</div>		
			
	  </div>
	
	</div>
  </div>
</div>
