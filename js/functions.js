$(document).ready(function(){
    $("#jrse_oui").change( (function () {
        $("#joursemaine input[type=checkbox]").each(function() {
           $(this).prop('disabled', false);
       });
    }));
    $("#jrse_non").change( (function () {
        $("#joursemaine input[type=checkbox]").each(function() {
           $(this).prop('disabled', true);
       });
    }));

    $("#trhr_oui").change( (function () {
        $("#trhr1 input[type=text], #trhr2 input[type=text]").each(function() {
           $(this).prop('disabled', false);
       });
    }));
    $("#trhr_non").change( (function () {
        $("#trhr1 input[type=text], #trhr2 input[type=text]").each(function() {
           $(this).prop('disabled', true);
       });
    }));

    $("#frappel_oui").change( (function () {
        $("#frequencerappel input[type=checkbox]").each(function() {
           $(this).prop('disabled', false);
       });
    }));
    $("#frappel_non").change( (function () {
        $("#frequencerappel input[type=checkbox]").each(function() {
           $(this).prop('disabled', true);
       });
    }));

    $("#valmin_oui").change( (function () {
        $("#valeurminimum").prop('disabled', false);
        $("#valeurminimum").val(120);
    }));
    $("#valmin_non").change( (function () {
        $("#valeurminimum").prop('disabled', true);
        $("#valeurminimum").val('');
    }));

    $("#valmax_oui").change( (function () {
        $("#valeurmaximum").prop('disabled', false);
        $("#valeurmaximum").val(140);
    }));
    $("#valmax_non").change( (function () {
        $("#valeurmaximum").prop('disabled', true);
        $("#valeurmaximum").val('');
    }));

    $("#tolervalmin_oui").change( (function () {
        $("#tolervaleurminimum").prop('disabled', false);
        $("#tolervaleurminimum").val(10);
    }));
    $("#tolervalmin_non").change( (function () {
        $("#tolervaleurminimum").prop('disabled', true);
        $("#tolervaleurminimum").val('');
    }));

    $("#tolervalmax_oui").change( (function () {
        $("#tolervaleurmaximum").prop('disabled', false);
        $("#tolervaleurmaximum").val(20);
    }));
    $("#tolervalmax_non").change( (function () {
        $("#tolervaleurmaximum").prop('disabled', true);
        $("#tolervaleurmaximum").val('');
    }));

    $('#matin_t1').datetimepicker({datepicker:false, format:'H:i',minTime:'00:00', maxTime:'12:00'});
    $('#matin_t2').datetimepicker({datepicker:false, format:'H:i', minTime:'01:00', maxTime:'13:00'});
    $('#soir_t1').datetimepicker({datepicker:false, format:'H:i', minTime:'13:00', maxTime:'22:00'});
    $('#soir_t2').datetimepicker({datepicker:false, format:'H:i', minTime:'14:00', maxTime:'23:00'});

    cmd_types_alertes_notifications_afficher();
    cmd_survielle_afficher();
    cmd_utilisateur_afficher();
    $("#enregistrer_alerte").click(function(event){
        cmd_alerte_ajouter(event);
    });

    $("#update_alerte").click(function(event){
        cmd_alerte_update(event);
    });

    $("#liste_types_alertes_notifications").change(function(event){
        cmd_alerte_a_update(event);
        $('#enregistrer_alerte').css('display', 'none');
        $('#update_alerte').css('display', 'inline');
    }); 
});

function cmd_types_alertes_notifications_afficher(){
    $.ajax({
        type:"post",
        url:"alerte_insertion.php",
        data:"action=types_alertes_notifications_afficher",
        success:function(data){
            $("#liste_types_alertes_notifications").html(data);
        }
    });
}

function cmd_survielle_afficher(){
    $.ajax({
	    type:"post",
	    url:"alerte_insertion.php",
	    data:"action=afficher_survielle",
	    success:function(data){
	        $("#associe").html(data);
	    }
    });
}

function cmd_utilisateur_afficher(){
    $.ajax({
	    type:"post",
	    url:"alerte_insertion.php",
	    data:"action=afficher_utilisateur",
	    success:function(resp){
	        $("#personne").html(resp);
	        $("#groupepersonne").html(resp);
	    }
    });
}

function cmd_alerte_ajouter(event){
	$.ajax({
	    type:"post",
	    url:"alerte_insertion.php",
	    data:$("#form_alerte").serialize()+"&action=ajouter_alerte",
	    success:function(data){
	    	cmd_types_alertes_notifications_afficher();
            var resp = JSON.parse(data);
            if(resp.type=='error'){
                $("#err").html(resp.msg).show().delay(9000).fadeOut();
            }else if(resp.type=='message'){
                $("#info").html(resp.msg).show().delay(5000).fadeOut();
                $( '#form_alerte' ).each(function(){
                    this.reset();
                });
            }
            cmd_survielle_afficher();
	    },
	    error:function(data){
	    	console.info(' ERROR :) '+data);
	    }
    });
    event.preventDefault();
}

function cmd_alerte_a_update(event){
    event.preventDefault();
    var id = $("#liste_types_alertes_notifications").val();
    $.ajax({
        type:"post",
        url:"alerte_insertion.php",
        data:"id="+id+"&action=alerte_a_update",
        success:function(data){
            var alerte = JSON.parse(data);
            var type_envoi = alerte.alrt_type_envoi.split(',');
            var jour_semaine = alerte.alrt_jour_semaine.split(',');
            var freq_rappel = alerte.alrt_freq_rappel.split(',');

            $("#alrt_id").val(alerte.alrt_id);
            $("#typealerte").val(alerte.alrt_libelle);
            (alerte.alrt_personne == 1)? $("#personne_oui").prop( "checked", true ): $("#personne_non").prop( "checked", true );
            (alerte.alrt_grp_personne == 1)? $("#groupe_oui").prop( "checked", true ): $("#groupe_non").prop( "checked", true );
            (alerte.alrt_niveau === 'Alerte')? $("#Alerte").attr('checked', true): $("#Notification").attr('checked', true);
            ($.inArray("E-mail", type_envoi)!==-1)? $("#Email").prop( "checked", true ): $("#Email").prop( "checked", false );
            ($.inArray("SMS", type_envoi)!==-1)? $("#SMS").prop( "checked", true ): $("#SMS").prop( "checked", false );
            ($.inArray("Flux", type_envoi)!==-1)? $("#Flux").prop( "checked", true ): $("#Flux").prop( "checked", false );
            ($.inArray("Tableau de bord", type_envoi)!==-1)? $("#Tableaudebord").prop( "checked", true ): $("#Tableaudebord").prop( "checked", false );
            ($.inArray("Lun", jour_semaine)!==-1)? $("#Lun").prop( "checked", true ): $("#Lun").prop( "checked", false );
            ($.inArray("Mar", jour_semaine)!==-1)? $("#Mar").prop( "checked", true ): $("#Mar").prop( "checked", false );
            ($.inArray("Mer", jour_semaine)!==-1)? $("#Mer").prop( "checked", true ): $("#Mer").prop( "checked", false );
            ($.inArray("Jeu", jour_semaine)!==-1)? $("#Jeu").prop( "checked", true ): $("#Jeu").prop( "checked", false );
            ($.inArray("Ven", jour_semaine)!==-1)? $("#Ven").prop( "checked", true ): $("#Ven").prop( "checked", false );
            ($.inArray("Sam", jour_semaine)!==-1)? $("#Sam").prop( "checked", true ): $("#Sam").prop( "checked", false );
            ($.inArray("Dim", jour_semaine)!==-1)? $("#Dim").prop( "checked", true ): $("#Dim").prop( "checked", false );
            $("#matin_t1").val(alerte.alrt_matin_t1);
            $("#matin_t2").val(alerte.alrt_matin_t2);
            $("#soir_t1").val(alerte.alrt_soir_t1);
            $("#soir_t2").val(alerte.alrt_soir_t2);
            ($.inArray("Immédiate", freq_rappel)!==-1)? $("#immediate").prop( "immediate", true ): $("#").prop( "checked", false );
            ($.inArray("Par Jour", freq_rappel)!==-1)? $("#parjour").prop( "checked", true ): $("#parjour").prop( "checked", false );
            ($.inArray("Par Mois", freq_rappel)!==-1)? $("#parmois").prop( "checked", true ): $("#parmois").prop( "checked", false );
        },
        error:function(data){
            console.info(' ERROR :) '+data);
        }
    });
}

function cmd_alerte_update(event){
    event.preventDefault();
    $.ajax({
        type:"post",
        url:"alerte_insertion.php",
        data:$("#form_alerte").serialize()+"&action=update_alerte",
        success:function(data){
        	cmd_types_alertes_notifications_afficher();
            var resp = JSON.parse(data);
            if(resp.type=='error'){
                $("#err"). css('display', 'inline');
                $("#err").html(resp.msg).show().delay(9000).fadeOut();
            }else if(resp.type=='message'){
                $('#info').css('display', 'inline');
                $("#info").html(resp.msg).show().delay(5000).fadeOut();
                $( '#form_alerte' ).each(function(){this.reset();});
                cmd_survielle_afficher();
                event.preventDefault();
            }
        },
        error:function(data){
            console.info(' ERROR :) '+data);
        }
    });

}