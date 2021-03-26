<table>
<?php
/*foreach($tblDispo as $dispo){
	echo "<tr><td>".$dispo->Usager->prenomnom."</td><td>".$dispo->Quart->nom."</td>";//<td>".$dispo->Equipe->nom."</td>";
	foreach($dispo->Groupes as $groupe){
		echo "<td>".$groupe->nom."</td>";
	}
	echo "<td>";
	if($dispo->siDispo){
		echo "Disponible";
	}else{
		echo "Non disponible";
	}
	echo "</td></tr>";
}*/
foreach($dataReader as $row){
	echo "<tr>";
	foreach($row as $value){
		echo "<td>".$value."</td>";
	}
	echo "</tr>";
}
?>
</table>