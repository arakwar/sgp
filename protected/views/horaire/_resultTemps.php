<?php
	if($dataDispo !==FALSE){
		$row = $dataDispo->read();
		$feuille = '<table><tr><th>Usager</th><th>Temps</th></tr>';
		do{
			$feuille.= '</tr><td>'.$row['Usager'].'</td><td>'.$row['HeureTotal'].'</td></tr>';
			$row = $dataDispo->read();
		}while($row!==FALSE);
		$feuille .= '</table>';
		echo $feuille;
	}
?>