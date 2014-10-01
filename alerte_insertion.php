<?php
require_once("db.php");
$proprietaire = 1;
$action= $_POST["action"];

	if ($action == null) {
		header('Location: alerte_ajouter.html');	
	}

    elseif($action === 'types_alertes_notifications_afficher'){
        $results = $db->query("SELECT alrt_id, alrt_libelle FROM alerte");
        $data ='';
        while($row = $results->fetch_assoc()) {
            echo '<option value="'.$row["alrt_id"].'">'. $row["alrt_libelle"] .'</option>';
        }
        $results->free();
        $db->close();
    }

	elseif ($action === 'afficher_survielle') {
		$results = $db->query("SELECT surv_id, surv_libelle FROM survielle_quoi");
		while($row = $results->fetch_assoc()) {
			echo '<option value="'.$row["surv_id"].'">'. $row["surv_libelle"] .'</option>';
		}
		$results->free();
		$db->close();
	}
	
	elseif ($action === 'ajouter_alerte') {
        if($_POST){
            $sql_champs = "";
            $sql_values = " ";
            $alerte_id = null;

            if(isset($_POST['typealerte']) && !empty($_POST['typealerte'])){
                $type_alerte = filter_var($_POST['typealerte'], FILTER_SANITIZE_STRING);
                $sql_champs.="alrt_libelle";
                $sql_values.="'$type_alerte'";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => 'Le champ type alerte est obligatoir!'));
                die($output);
            }

            if(isset($_POST['personne_radio'])){
                $alrt_personne = filter_var($_POST['personne_radio'], FILTER_SANITIZE_NUMBER_INT);
                $sql_champs.=",alrt_personne";
                $sql_values.=",$alrt_personne";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => "Personne Erreur !!!!! "));
                die($output);
            }

            if(isset($_POST['groupe_radio'])){
                $alrt_grp_personne = filter_var($_POST['groupe_radio'], FILTER_SANITIZE_NUMBER_INT);
                $sql_champs.=",alrt_grp_personne";
                $sql_values.=",$alrt_grp_personne";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => "Groupe Erreur !!!!!"));
                die($output);
            }

            if(isset($_POST['niveaualerte']) && !empty($_POST['niveaualerte'])){
                $niveau_alerte = filter_var($_POST['niveaualerte'], FILTER_SANITIZE_STRING);
                $sql_champs.=", alrt_niveau";
                $sql_values.=",'$niveau_alerte'";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => 'Le champ niveau d\'alerte est obligatoir!'));
                die($output);
            }

            if(isset($_POST['typeenvoi']) && !empty($_POST['typeenvoi'])){
                $typeenvoi_filtered = filter_var_array($_POST['typeenvoi'], FILTER_SANITIZE_STRING);
                $typeenvoi = (count($typeenvoi_filtered)>1) ? implode(",", $typeenvoi_filtered) : $typeenvoi_filtered[0];
                $sql_champs.=", alrt_type_envoi";
                $sql_values.=",'$typeenvoi'";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => 'Select ou moins un type d’envoi!'));
                die($output);
            }

            if($_POST['jrse'] === 'oui') {
                if(isset($_POST['joursemaine']) && !empty($_POST['joursemaine'])){
                    $joursemaine_filtered = filter_var_array($_POST['joursemaine'], FILTER_SANITIZE_STRING);
                    $joursemaine = (count( $joursemaine_filtered)>1) ? implode(",",  $joursemaine_filtered) :  $joursemaine_filtered[0];
                    $sql_champs.=", alrt_jour_semaine";
                    $sql_values.=",'$joursemaine'";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Select ou moins une journée de la semaine!'));
                    die($output);
                }
            }

            if($_POST['trhr'] === 'oui') {
                if(isset($_POST['matin_t1'], $_POST['matin_t2'], $_POST['soir_t1'], $_POST['soir_t2']) && !empty($_POST['matin_t1']) && !empty($_POST['matin_t2']) && !empty($_POST['soir_t1']) && !empty($_POST['soir_t2'])){
                    $alrt_matin_t1 = filter_var($_POST['matin_t1'], FILTER_SANITIZE_STRING);
                    $alrt_matin_t2 = filter_var($_POST['matin_t2'], FILTER_SANITIZE_STRING);
                    $alrt_soir_t1 = filter_var($_POST['soir_t1'], FILTER_SANITIZE_STRING);
                    $alrt_soir_t2 = filter_var($_POST['soir_t2'], FILTER_SANITIZE_STRING);
                    //^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$
                    $sql_champs.=", alrt_matin_t1, alrt_matin_t2,alrt_soir_t1, alrt_soir_t2";
                    $sql_values.=",Concat('$alrt_matin_t1',':59'),Concat('$alrt_matin_t2',':59'),Concat('$alrt_soir_t1',':59'),Concat('$alrt_soir_t2',':59')";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Les 4 champs de tranche horaire sont obligatoir!'));
                    die($output);
                }
            }

            if($_POST['valmin'] === 'oui') {
                if(isset($_POST['valeurminimum']) && !empty($_POST['valeurminimum'])){
                    $valmin = filter_var($_POST['valeurminimum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_champs.=", alrt_val_min";
                    $sql_values.=",$valmin";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ valeur Minimum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['valmax'] === 'oui') {
                if(isset($_POST['valeurmaximum']) && !empty($_POST['valeurmaximum'])){
                    $valmax = filter_var($_POST['valeurmaximum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_champs.=", alrt_val_max";
                    $sql_values.=",$valmax";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ valeur Maximum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['tolervalmin'] === 'oui') {
                if(isset($_POST['tolervaleurminimum']) && !empty($_POST['tolervaleurminimum'])){
                    $tolervaleurminimum = filter_var($_POST['tolervaleurminimum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_champs.=", alrt_tolerance_val_min";
                    $sql_values.=",$tolervaleurminimum";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ tolérance  valeur mianimum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['tolervalmax'] === 'oui') {
                if(isset($_POST['tolervaleurmaximum']) && !empty($_POST['tolervaleurmaximum'])){
                    $tolervaleurmaximum = filter_var($_POST['tolervaleurmaximum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_champs.=", alrt_tolerance_val_max";
                    $sql_values.=",$tolervaleurmaximum";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ tolérance valeur Maximum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['frappel'] === 'oui') {
                if(isset($_POST['frequencerappel']) && !empty($_POST['frequencerappel'])){
                    $frequencerappel_filtered = filter_var($_POST['frequencerappel'], FILTER_SANITIZE_STRING);
                    $frequencerappel =(count($frequencerappel_filtered)>1) ? implode(",",  $frequencerappel_filtered) :  $frequencerappel_filtered[0];
                    $sql_champs.=", alrt_freq_rappel";
                    $sql_values.=",'$frequencerappel'";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Select ou moins une fréquence de rappel!'));
                    die($output);
                }
            }
            $db->autocommit(false);
            $sql_insert_alerte = "INSERT INTO alerte (" .$sql_champs .") VALUES (" .$sql_values .")";
            if ($db->query($sql_insert_alerte)) {
                $alerte_id = mysqli_insert_id($db);            //echo $alerte_id;
                if (isset($_POST['associe']) && !empty($_POST['associe'])) {
                    $associe = filter_var($_POST['associe'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_associe = "INSERT INTO survielle_quoi_alerte (surv_id, alrt_id) VALUES($associe, $alerte_id)";
                    $db->query($sql_associe);
                } else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ associe à est obligatoir!'));
                    die($output);
                }
                $db->commit();
                $db->close();
                $output = json_encode(array('type' => 'message', 'msg' => "le type d'alerte <b>$type_alerte<b> a été enregistrer  $frequencerappel_filtered "));
                die($output);
            } else {
                $output = json_encode(array('type' => 'error', 'msg' => " L'alerte <b>$type_alerte<b> n'est pas enregistrer !! "));
                die($output);
            }
        }
	}

    elseif($action === 'alerte_a_update'){
        $id = $_POST['id'];
        $results = $db->query("SELECT alrt_id, alrt_libelle, alrt_personne, alrt_grp_personne, alrt_niveau, alrt_type_envoi, alrt_jour_semaine, alrt_matin_t1, alrt_matin_t2, alrt_soir_t1,alrt_soir_t2,alrt_val_min,alrt_val_max,alrt_tolerance_val_min, alrt_tolerance_val_max, alrt_freq_rappel FROM alerte WHERE alrt_id= $id");
        echo json_encode($results->fetch_assoc());
        $results->free();
        $db->close();
    }

    elseif ($action === 'update_alerte') {
        if($_POST){
            $sql_values_set = "";
            $alrt_id = filter_var($_POST['alrt_id'], FILTER_SANITIZE_NUMBER_INT);;

            if(isset($_POST['typealerte']) && !empty($_POST['typealerte'])){
                $type_alerte_update = filter_var($_POST['typealerte'], FILTER_SANITIZE_STRING);
                $sql_values_set.="alrt_libelle = '$type_alerte_update'";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => 'Le champ type alerte est obligatoir!'));
                die($output);
            }

            if(isset($_POST['personne_radio'])){
                $alrt_personne_update = filter_var($_POST['personne_radio'], FILTER_SANITIZE_NUMBER_INT);
                $sql_values_set.=",alrt_personne = $alrt_personne_update";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => "Personne Erreur !!!!! "));
                die($output);
            }

            if(isset($_POST['groupe_radio'])){
                $alrt_grp_personne_update = filter_var($_POST['groupe_radio'], FILTER_SANITIZE_NUMBER_INT);
                $sql_values_set.=",alrt_grp_personne = $alrt_grp_personne_update";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => "Groupe Erreur !!!!!"));
                die($output);
            }

            if(isset($_POST['niveaualerte']) && !empty($_POST['niveaualerte'])){
                $niveau_alerte_update = filter_var($_POST['niveaualerte'], FILTER_SANITIZE_STRING);
                $sql_values_set.=", alrt_niveau = '$niveau_alerte_update'";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => 'Le champ niveau d\'alerte est obligatoir!'));
                die($output);
            }

            if(isset($_POST['typeenvoi']) && !empty($_POST['typeenvoi'])){
                $typeenvoi_filtered_update = filter_var_array($_POST['typeenvoi'], FILTER_SANITIZE_STRING);
                $typeenvoi_update = (count($typeenvoi_filtered_update)>1) ? implode(",", $typeenvoi_filtered_update) : $typeenvoi_filtered_update[0];
                $sql_values_set.=", alrt_type_envoi = '$typeenvoi_update'";
            }else{
                $output = json_encode(array('type'=>'error', 'msg' => 'Select ou moins un type d’envoi!'));
                die($output);
            }

            if($_POST['jrse'] === 'oui') {
                if(isset($_POST['joursemaine']) && !empty($_POST['joursemaine'])){
                    $joursemaine_filtered_update = filter_var_array($_POST['joursemaine'], FILTER_SANITIZE_STRING);
                    $joursemaine_update = (count( $joursemaine_filtered_update)>1) ? implode(",",  $joursemaine_filtered_update) :  $joursemaine_filtered_update[0];
                    $sql_values_set.=", alrt_jour_semaine = '$joursemaine_update'";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Select ou moins une journée de la semaine!'));
                    die($output);
                }
            }

            if($_POST['trhr'] === 'oui') {
                if(isset($_POST['matin_t1'], $_POST['matin_t2'], $_POST['soir_t1'], $_POST['soir_t2']) && !empty($_POST['matin_t1']) && !empty($_POST['matin_t2']) && !empty($_POST['soir_t1']) && !empty($_POST['soir_t2'])){
                    $alrt_matin_t1_update = filter_var($_POST['matin_t1'], FILTER_SANITIZE_STRING);
                    $alrt_matin_t2_update = filter_var($_POST['matin_t2'], FILTER_SANITIZE_STRING);
                    $alrt_soir_t1_update = filter_var($_POST['soir_t1'], FILTER_SANITIZE_STRING);
                    $alrt_soir_t2_update = filter_var($_POST['soir_t2'], FILTER_SANITIZE_STRING);
                    //^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$
                    $sql_values_set.=", alrt_matin_t1 = Concat('$alrt_matin_t1_update',':59'), alrt_matin_t2 = Concat('$alrt_matin_t2_update',':59'), alrt_soir_t1=Concat('$alrt_soir_t1_update',':59'),alrt_soir_t2 = Concat('$alrt_soir_t2_update',':59')";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Les 4 champs de tranche horaire sont obligatoir!'));
                    die($output);
                }
            }

            if($_POST['valmin'] === 'oui') {
                if(isset($_POST['valeurminimum']) && !empty($_POST['valeurminimum'])){
                    $valmin_update = filter_var($_POST['valeurminimum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_values_set.=", alrt_val_min = $valmin_update";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ valeur Minimum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['valmax'] === 'oui') {
                if(isset($_POST['valeurmaximum']) && !empty($_POST['valeurmaximum'])){
                    $valmax_update = filter_var($_POST['valeurmaximum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_values_set.=", alrt_val_max = $valmax_update";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ valeur Maximum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['tolervalmin'] === 'oui') {
                if(isset($_POST['tolervaleurminimum']) && !empty($_POST['tolervaleurminimum'])){
                    $tolervaleurminimum_update = filter_var($_POST['tolervaleurminimum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_values_set.=",alrt_tolerance_val_min = $tolervaleurminimum_update";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ tolérance  valeur mianimum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['tolervalmax'] === 'oui') {
                if(isset($_POST['tolervaleurmaximum']) && !empty($_POST['tolervaleurmaximum'])){
                    $tolervaleurmaximum_update = filter_var($_POST['tolervaleurmaximum'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_values_set.=", alrt_tolerance_val_max = $tolervaleurmaximum_update";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ tolérance valeur Maximum est obligatoir!'));
                    die($output);
                }
            }

            if($_POST['frappel'] === 'oui') {
                if(isset($_POST['frequencerappel']) && !empty($_POST['frequencerappel'])){
                    $frequencerappel_filtered_update = filter_var($_POST['frequencerappel'], FILTER_SANITIZE_STRING);
                    $frequencerappel_update =(count($frequencerappel_filtered_update)>1) ? implode(",",  $frequencerappel_filtered_update) :  $frequencerappel_filtered_update[0];
                    $sql_values_set.=",alrt_freq_rappel = '$frequencerappel_update'";
                }else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Select ou moins une fréquence de rappel!'));
                    die($output);
                }
            }
            $db->autocommit(false);
            $sql_update_alerte = "UPDATE alerte SET $sql_values_set WHERE alrt_id = $alrt_id ";
            if ($db->query($sql_update_alerte)) {
                if (isset($_POST['associe']) && !empty($_POST['associe'])) {
                    $associe = filter_var($_POST['associe'], FILTER_SANITIZE_NUMBER_INT);
                    $sql_associe = "UPDATE survielle_quoi_alerte SET surv_id = $associe WHERE alrt_id = $alrt_id";
                    $db->query($sql_associe);
                } else {
                    $output = json_encode(array('type' => 'error', 'msg' => 'Le champ associe à est obligatoir!'));
                    die($output);
                }
                $db->commit();
                $db->close();
                $output = json_encode(array('type' => 'message', 'msg' => "le type d'alerte <b>$type_alerte_update <b> a été mettre à jour"));
                die($output);
            } else {
                $output = json_encode(array('type' => 'error', 'msg' => " L'alerte <b> $type_alerte_update <b> n'est pas à jour !!"));
                die($output);
            }
        }
    }














