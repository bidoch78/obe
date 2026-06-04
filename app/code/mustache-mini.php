<?php
	
	/**************************************************************************
	*
	* class : MustacheMini
	* Gestion des tags {{toto}}
	* version simplifié (la version officielle me parait un peu enorme pour des besoins relativement simples
	***************************************************************************
	*
	* Ybi : 15/10/2014
	*/
	class MustacheMini
	{

		/***********************
		* extract iter
		*
		***********************/	
		public static function extractIter($template) {
			
			$info = array("template" => $template, "iter" => array());
			
			if (strlen($template) < 1) return $info;
			
			$stop = false; $cpt = 0; $pos = 0;
			while(!$stop && $cpt < 500) {
				
				$debDeb = strpos($template, "%%deb:", $pos);
				$endDeb = strpos($template, "%%", $debDeb+2);
				
				//var_dump("deb - S" . $debDeb);
				//var_dump("deb - E" . $endDeb);
				
				if ($debDeb !== false && $endDeb !== false && ($debDeb < $endDeb)) {
					
					$tagStart = substr($template, $debDeb + 6, $endDeb - $debDeb - 6);
					
					$debEnd = strpos($template, "%%end:", $endDeb+2);
					$endEnd = strpos($template, "%%", $debEnd+2);

					//var_dump("end - S" . $debEnd);
					//var_dump("end - E" . $endEnd);
					
					if ($debEnd !== false && $endEnd !== false && ($debEnd < $endEnd)) {
						
						$tagEnd = substr($template, $debEnd + 6, $endEnd - $debEnd - 6);
						
						if ($tagEnd == $tagStart) {
							
							$txtIter = substr($template, $endDeb + 2, $debEnd - $endDeb - 2);
							$info["iter"][$tagEnd] = $txtIter;
							$template = substr($template, 0, $debDeb) . '{{' . $tagStart . '}}' . substr($template, $endEnd + 2);
							$pos = $debDeb + strlen($tagStart) + 4;
							//var_dump("CHAR".$template{$pos});
							
						}
						else {
							$stop = true;
						}
						
					}
					else {
						$stop = true;
					}
					
				}
				else {
					$stop = true;
				}
				
				$cpt++;
			
			}

			$info["template"] = $template;
			return $info;
			
		}
		
		/***********************
		* render
		*
		***********************/
		public static function render($template, $view) {
			
			$hasIter = false;
			foreach($view as $vKey => $v) {
				if (strpos($vKey, "iter-") === 0) { $hasIter = true; break; }
			}
			
			if ($hasIter) {
				
				$extractIter = self::extractIter($template);
				$template = $extractIter["template"];
				
				$newView = array();
				foreach($view as $vKey => $v) {
					if (strpos($vKey, "iter-") === 0) {						
						if (is_array($v) && isset($extractIter["iter"][$vKey])) {
							
							$tagText = "";
							foreach($v as $vitem) {
								$vinfoText = $extractIter["iter"][$vKey];
								foreach($vitem as $vitemkey => $vitemvalue) {
									$vinfoText = str_replace("{{" . $vitemkey . "}}", $vitemvalue, $vinfoText);
								}
								$tagText .= $vinfoText;
							}
							
							$template = str_replace("{{" . $vKey . "}}", $tagText, $template);
							
						}
					}
					else {
						$newView[$vKey] = $v;
					}
				}
				$view = $newView;
				
			}

			foreach($view as $vKey => $v) {
				$template = str_replace("{{{" . $vKey . "}}}", $v, $template);
				$template = str_replace("{{" . $vKey . "}}", $v, $template);
			}
			
			return $template;
		
		}

		public static function renderLoop($template, $viewLoop) {
			
		  $renderTemplate="";
		  foreach($viewLoop as $viewKey => $view) {
		  	$tmpTemplate=$template;
			  foreach($view as $vKey => $v) {
				  $tmpTemplate= str_replace("{{{" . $vKey . "}}}", $v, $tmpTemplate);
				  $tmpTemplate= str_replace("{{" . $vKey . "}}", $v, $tmpTemplate);
			  }
			  $renderTemplate.=$tmpTemplate;
		  }
		  return $renderTemplate;

		}		
		
		public static function getTags($template) {
			
			$tags = array();
			
			if (strlen($template) < 1) return $tags;
			
			$stop = false; $cpt = 0; $pos = 0;
			while(!$stop && $cpt < 500) {
				
				$deb = strpos($template, "{{", $pos);
				$end = strpos($template, "}}", $pos);
				//var_dump("check: " . $deb . " " . $end);
				
				if ($deb !== false && $end !== false && ($deb < $end)) {
					$tag = substr($template, $deb+2, $end-$deb-2);
					if (strlen($tag) > 0) $tags[$tag] = true;
					//var_dump(substr($template, $deb+2, $end-$deb-2));
					//var_dump($deb);
					//var_dump($end);
					$pos = $end+2;
					//exit();
				}
				else {
					$stop = true;
				}
				
				$cpt++;
			}
			
			return $tags;
			
		}
		
	}

?>