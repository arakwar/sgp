<?php
	if($dataDispo !==FALSE){
		$row = $dataDispo->read();
		$dispos = array();$pageBreak = 0;
		if($groupe == '0'){
			$max = 0;
			//On crée les tableaux qui serviront pour tous les graphiques
			$arrHeure = ['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00'];
			$arrEquipe = array();
			//On lit le curseur SQL et on sort le data
			$i=0;
			do{
				$caserne = $row['Caserne'];
				do{
					$temps = $row['Temps'];
					$heure = substr($temps,11,5);
					do{					
						$dispos[$caserne][$temps][$row['Equipe']]['equipe']=$row['Equipe'];	
						$dispos[$caserne][$temps][$row['Equipe']]['temps']=$heure;						
						$dispos[$caserne][$temps][$row['Equipe']]['dispo']=$row['Dispo'];			
						$dispos[$caserne][$temps][$row['Equipe']]['couleur']=$row['Couleur'];
						if($max < $row['Dispo']){$max = $row['Dispo'];}
						if(!isset($arrEquipe[$caserne])){
							$arrEquipe[$caserne] = array();
						}
						if(!in_array($row['Equipe'],$arrEquipe[$caserne])){
							$arrEquipe[$caserne][] = $row['Equipe'];
						}
						$row = $dataDispo->read();				 
					}while($temps == $row['Temps']);
				}while($caserne = $row['Caserne']);
			}while($row!==FALSE);
			//echo '<pre>';print_r($dispos);echo '</pre>';
			//On classe le data 			
			foreach($dispos as $nom=>$caserne){		
				echo '<h2>'.$nom.'</h2>';
				$label = array();
				$labeltxt = '';
				$dataset = array();
				foreach($caserne as $labeldate=>$date){
					$arrdata = array();
					$lblDate = substr($labeldate,0,10);
					if(substr($lblDate,0,10)!=$labeltxt){
						$label[] = $lblDate;
						$labeltxt = $lblDate;
					}
					if(is_array($date)){
						foreach($date as $equipe){
							if(is_array($equipe)){
								$i=1;
								foreach($equipe as $data){
									switch($i){
										case 1:
											$equ = $data;
											break;
										case 2:
											$heure = $data;
											break;
										case 3:
											$arrdata[$equ][$heure]=$data;
											break;
										case 4:
											$arrdata[$equ]['couleur'] = $data;
											break;
									}
									$i++;							
								}
							}
						}
						if(!empty($arrdata)){
							$dataset[$labeltxt][] = $arrdata;
						}
					}
				}
				//On crée les datasets pour les graphiques
				foreach($label as $jour){
					echo '<h3>'.$jour.'</h3>';
					$datasets = array();
					foreach($arrEquipe[$nom] as $arrEq){
						$datares = array();
						foreach($dataset[$jour] as $set){
							foreach($arrHeure as $arrH){
								if(isset($set[$arrEq][$arrH])){
									$datares[$arrH] = $set[$arrEq][$arrH];
								}
							}
						}
						foreach($arrHeure as $arrH){
							if(!isset($datares[$arrH])){
								$datares[$arrH] = 0;
							}
						}
						ksort($datares);
						$temps = array();
						$temps["strokeColor"] = "#".$set[$arrEq]['couleur'];
						$temps["pointColor"] = "#".$set[$arrEq]['couleur'];
						$temps["pointStrokeColor"] = "#".$set[$arrEq]['couleur'];
						$temps["data"] = $datares;
						$datasets[] = $temps;
					}
					//On affiche les graphiques
					$pageBreak++;
					$this->widget(
							'chartjs.widgets.ChLine',
							array(
									'width' => 1168,
									'height' => 300,
									'htmlOptions' => array(),
									'labels' => ['0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23'],
									'datasets' => $datasets,
									'options' => array(
											"datasetFill"=>false,
											"bezierCurve"=>false,
											"scaleOverride"=>true,
											"scaleSteps"=>(($max/5)+1),
											"scaleStepWidth"=>5,
											"scaleStartValue"=>0,
											"scaleGridLineColor"=> "#999",
											"responsive"=>true,
											"customTooltips"=> true,
											"tooltipTemplate"=> "<%if (label){%><%=label%>: <%}%><%= value %>",
											"multiTooltipTemplate"=> "<%if (label){%><%=label%>: <%}%><%= value %>",
											"multiTooltipKeyBackground"=> '#fff',
									),
							)
					);
					if($pageBreak==4){
						$pageBreak = 0;
						echo '<div class="pageBreak"></div>';
					}
				}
			}
		}else{
			do{
				$caserne = $row['Caserne'];
				$dispos[]=$caserne;
				do{
					$equipe = $row['Equipe'];
					do{
						$temps = $row['Temps'];
						$dispos[$caserne][substr($temps,0,10)]['date'] = substr($temps,0,10);
						$dispos[$caserne][substr($temps,0,10)][$equipe]['equipe'] = $equipe;
						do{
							$usager = $row['Usager'];
							do{
								$dispos[$caserne][substr($temps,0,10)][$equipe][substr($temps,11,5)][$usager]['heure'] = substr($temps,11,5);
								$dispos[$caserne][substr($temps,0,10)][$equipe][substr($temps,11,5)][$usager]['nom'] = $usager;
								$dispos[$caserne][substr($temps,0,10)][$equipe][substr($temps,11,5)][$usager]['dispo'] = $row['Dispo'];
								$row = $dataDispo->read();
							}while($usager==$row['Usager']);
						}while($temps == $row['Temps']);
					}while($equipe == $row['Equipe']);
				}while($caserne = $row['Caserne']);
			}while($row!==FALSE);
			//echo '<pre>';print_r($dispo);echo '</pre>';
			foreach($dispos as $caserne){
				if(!is_array($caserne)){
					echo '<h2>'.$caserne.'</h2>';
				}else{
					foreach($caserne as $date){	
						$j=0;				
						foreach($date as $equipe){
							if(!is_array($equipe)){
								$dateTitre = '<h3>'.$equipe.'</h3>';
							}else{
								$ligne = '';
								foreach($equipe as $heure){								
									$titre = '<tr><th>&nbsp;</th>';
									$i=0;$n=1;
									if(!is_array($heure)){
										$equipeTitre = $heure;
									}else{
										foreach($heure as $usager){
											$n++;
											$titre .= '<th>'.$usager['nom'].'</th>';
											if($i==0){
												$ligne .='<tr><th>'.$usager['heure'].'</th>';
												$i=1;
											}
											$ligne .= '<td>'.(($usager['dispo']==0)?'':'<img src="images/crochet.png" style="width:62px;height:24px;"/>').'</td>';
										}
										$ligne .= '</tr>';
										$titre .= '</tr>';
									}
								}
								if($j==0){
									echo $dateTitre;
								}
								$j++;
								echo '<table><tr><th colspan="'.$n.'" class="equipeTitre">'.$equipeTitre.'</th></tr>'.$titre.$ligne.'</table><div class="pageBreak"></div>';
							}
						}
					}				
				}
			}
		}
	}
?>