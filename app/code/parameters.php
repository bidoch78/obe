<?php
	
	/**************************************************************************
	*
	* objet : Parameters
	*
	* Gestion des parametres stock�s dans une chaine de caract�res
	***************************************************************************
	*
	* YBI le 21/10/2009
	* - 30/11/2009 : revue suite modification structure 1.2			
	*
	* ATTENTION LE CARACTERE {"} NE DOIT PAS ETRE UTILISE COMME CARACTERE DANS LA VALEUR DU PARAMETRE
	*
	* Syntaxe : myOpt1= "" ;myOpt2="popo ;et alors";myOpt3="ss"
	*
	*/
	final class Parameters
	{
	
		/***********************
		* GetParameters
		* $str = chaine listant les param�tres
		* $sep = caract�re de s�paration des param�tres (optionnel)
		* $assign = caract�re d'assignation (optionnel)
		* $valdelimiter = caract�re qui d�limite la chaine valeur (optionnel)
		* retourne un tableau contenant la liste des parametres + la valeur sans les "" si erreur de syntaxe retourne false
		***********************/
		public static function GetParameters($str, $sep=';', $assign='=' , $valdelimiter='"')
		{
			
			if ($str == null) return false;
			
			$str = trim($str);
			
			$Params = false;
			$ptC = 0; 
			$ptMax = strlen($str);
			$BeginAt = 0;

			while($ptC < $ptMax)
			{
				$c = $str[$ptC];

				if ($c == $assign)
				{
				
					$optName = substr($str, $BeginAt, $ptC - $BeginAt);
				
					//on zappe les blancs
					while($ptC++ < $ptMax)
					{
						$c = $str[$ptC];
						if ($c == ' ') {} //do nothing
						elseif ($c == $valdelimiter)
							break;
						else
							//erreur de syntaxe
							return false;
					}
				
					//Stock first
					$firstValDelimiter = $ptC;
				
					//erreur de syntawe
					if ($ptC == $ptMax) return false;
				
					//cherche le suivant
					$nextValDelimiter = strpos($str, $valdelimiter, $firstValDelimiter + 1);

					//erreur de syntaxe
					if ($nextValDelimiter === false) return false;
				
					$ptC = $nextValDelimiter;

					if ($ptC != $ptMax - 1)
					{
						
						while($ptC++ < $ptMax)
						{
							$c = $str[$ptC];		
							if ($c == ' ') {} //do nothing
							elseif ($c == $sep)
								break;
							else
								//erreur de syntaxe
								return false;
						}
						
					}

					//Trouv� on retourne la valeur
					if ($Params === false) $Params = array();
					$Params[$optName] = substr($str, $firstValDelimiter + 1, $nextValDelimiter - $firstValDelimiter - 1);
					
					//ici on est sur l'option suivante
					$BeginAt = $ptC + 1;

				}

				$ptC++;			
			}			
			
			return $Params;			
			
		}

		/***********************
		* AddParameter
		* $str = chaine listant les param�tres
		* $paramName = nom du param�tre � ajouter
		* $paramValue = valeur du param�tre
		* $sep = caract�re de s�paration des param�tres (optionnel)
		* $assign = caract�re d'assignation (optionnel)
		* $valdelimiter = caract�re qui d�limite la chaine valeur (optionnel)
		* Ajoute un param�tre dans une chaine, si il existe la valeur est remplac�e
		***********************/
		public static function AddParameter($str, $paramName, $paramValue, $sep=';', $assign='=' , $valdelimiter='"')
		{
			$tbParams = self::GetParameters($str, $sep, $assign, $valdelimiter);
			if ($tbParams === false)
			{
				return $paramName . $assign . $valdelimiter . $paramValue . $valdelimiter;
			}
			else
			{
				$strParam = "";
				$tbParams[$paramName] = $paramValue;
				foreach($tbParams as $k => $v)
				{
					$strParam .= $k . $assign . $valdelimiter . $v .  $valdelimiter . $sep;
				}
				return substr($strParam, 0, strlen($strParam)-1);
			}
			
		}

		/***********************
		* GetParameter
		* $str = chaine listant les param�tres
		* $paramName = nom du param�tre
		* $sep = caract�re de s�paration des param�tres (optionnel)
		* $assign = caract�re d'assignation (optionnel)
		* $valdelimiter = caract�re qui d�limite la chaine valeur (optionnel)
		* retourne la valeur sans les "" si erreur de syntaxe ou pas trouv� retourne false
		***********************/
		public static function GetParameter($str, $paramName, $sep=';', $assign='=' , $valdelimiter='"')
		{
			
			if ($str == null) return false;
			
			$str = trim($str);
			
			$ptC = 0; 
			$ptMax = strlen($str);
			$BeginAt = 0;

			while($ptC < $ptMax)
			{
				$c = $str[$ptC];

				if ($c == $assign)
				{
				
					$optName = substr($str, $BeginAt, $ptC - $BeginAt);
				
					//on recherche le caract�re suivant doit = "
					//on zappe les blancs
					while($ptC++ < $ptMax)
					{
						$c = $str[$ptC];
						if ($c == ' ') {} //do nothing
						elseif ($c == $valdelimiter)
							break;
						else
							//erreur de syntaxe
							return false;
					}
				
					//Stock first
					$firstValDelimiter = $ptC;
				
					//erreur de syntawe
					if ($ptC == $ptMax) return false;
				
					//cherche le suivant
					$nextValDelimiter = strpos($str,  $valdelimiter, $firstValDelimiter + 1);

					//erreur de syntaxe
					if ($nextValDelimiter === false) return false;
				
					$ptC = $nextValDelimiter;
					
					//cherche le caract�re ";" on zappe les blancs
					if ($ptC != $ptMax - 1)
					{
						
						while($ptC++ < $ptMax)
						{
							$c = $str[$ptC];		
							if ($c == ' ') {} //do nothing
							elseif ($c == $sep)
								break;
							else
								//erreur de syntaxe
								return false;
						}
						
					}

					//Trouv� on retourne la valeur
					if (strcasecmp($paramName, $optName) == 0)
						return substr($str, $firstValDelimiter + 1, $nextValDelimiter - $firstValDelimiter - 1);
				
					//ici on est sur l'option suivante
					$BeginAt = $ptC + 1;

				}

				$ptC++;			
			}			
			
			return false;
			
		}
		
		/***********************
		* GetFirstParameters
		* $params = chaine listant les param�tres
		* $paramName = nom du param�tre
		* $sep = caract�re de s�paration des param�tres (optionnel)
		* $assign = caract�re d'assignation (optionnel)
		* $valdelimiter = caract�re qui d�limite la chaine valeur (optionnel)
		* retourne la 1er valeur trouv�e sans les "" si erreur de syntaxe ou pas trouv� retourne false
		***********************/
		public static function GetFirstParameter(array $params, $paramName, $sep=';', $assign='=' , $valdelimiter='"')
		{
			if (count($params) == 0) return false;
			foreach($params	as $p)
			{
				$search = self::GetParameter($p, $paramName, $sep, $assign, $valdelimiter);
				if ($search !== false) return $search;
			}
			return false;
		}

		/***********************
		* GetFirstParameters
		* $params = tableau contenant les param�tres [name]=value
		* $sep = caract�re de s�paration des param�tres (optionnel)
		* $assign = caract�re d'assignation (optionnel)
		* $valdelimiter = caract�re qui d�limite la chaine valeur (optionnel)
		* retourne la 1er valeur trouv�e sans les "" si erreur de syntaxe ou pas trouv� retourne false
		***********************/		
		public static function WriteParameters(array $params, $sep=';', $assign='=' , $valdelimiter='"')
		{
			$pValue = "";
			foreach($params as $k => $v)
			{
				$pValue .= ($k . $assign . $valdelimiter . $v . $valdelimiter . $sep);
			}
			if (strlen($pValue) > 0) $pValue = substr($pValue, 0, strlen($pValue) - strlen($sep));
			return $pValue;
		}
		
	}
	
?>