<?php
/**
* Frensh language file
*
* @author Gael Langlais
**/
// ora ci definiamo tutte le costanti a caso, poi faremo uno skeleton cosi possano creare anche le altre 
define('LANG_FLAGFILE','languages/FR/flag.jpg');

define('LANG_index_WRONGMSG', 'Nom d\'utilisateur ou mot de passe incorrect');

/**
* Menu section
**/
define('LANG_menu_SETTINGS', 'Parametres');
define('LANG_menu_ADDMODULE', 'Nouveau module');

define('LANG_home_SYSTEM', 'Système');
define('LANG_home_WEATHER', 'Météo');
define('LANG_home_VERSION', 'Version');
define('LANG_home_LOADING', 'Chargement...');
define('LANG_home_DATETIME','Date/heure Système');
define('LANG_home_DAEMON', 'Status du service');
define('LANG_home_DAEMON_OK', 'Démarré');
define('LANG_home_DAEMON_KO', 'Arrété');
define('LANG_home_NEXTRAIN', 'Prévision prochaine pluie');
define('LANG_home_TIME','Heure');

define('LANG_settings_LANGUAGE', 'Langue');
define('LANG_settings_LOCATION', 'Location');
define('LANG_settings_CHANGE','Changer');
define('LANG_settings_ALERTTTW',"<b>Attention: </b>Ceci est un parrametre avancé. Le modifier peu provoquer un mauvais fonctionnement de RPirrigate.");
define('LANG_settings_ALERTTTW2',"Changez le seulement si vous savez ce que vous faisez!");
define('LANG_settings_CURRENT','Actuel');
define('LANG_settings_NEW','Nouveau');
define('LANG_settings_BACK','Précédent');
define('LANG_settings_NEXT','Suivant');
define('LANG_settings_LOCATION_MSG',"Choisisez votre location. Cette information est necéssaire pour la météo");
define('LANG_settings_CONFIRM','Confirmer');
define('LANG_settings_ERRORLOC','Erreur. La localisation que vous avez entré est invalide');
define('LANG_settings_ERRORTTW','Invalid TTW. repotez vous à la documentation');
define('LANG_settings_USERS','Utilisateurs');
define('LANG_settings_GENERAL','Generale');
define('LANG_settings_CHANGEPASSWORD','Changer le Mot de passe');
define('LANG_settings_CHANGEPASSWORD_MSG',"Ici vous pouvez changer votre mot de passe");
define('LANG_settings_CHANGEPASSWORD_OLD',"Ancien mot de passe");
define('LANG_settings_CHANGEPASSWORD_NEW',"Nouveau mot de passe");
define('LANG_settings_CHANGEPASSWORD_ERROR1',"Votre mot de passe ancien et actuel doivent etre remplis");
define('LANG_settings_CHANGEPASSWORD_ERROR2',"Le nouveau mot de passe ne correspond pas");
define('LANG_settings_CHANGEPASSWORD_ERROR3',"Le mot de passe courrant ne coresspond pas a votre mot de passe");
define('LANG_settings_OTHERUSERS','Autre utilisateurs');
define('LANG_settings_ADDUSER','Ajouter un nouveau utilisateur');
define('LANG_settings_ADDUSER_MSG',"Here you can add new users to RPirrigate");
define('LANG_settings_ADDUSER_ERROR1', 'Le nom d\'utilisateur et mot de passe doivent etre remplis');
define('LANG_settings_ADDUSER_ERROR2', 'Username must contain only numbers and letters');
define('LANG_settings_ADDUSER_ERROR3', 'Les mots de passe ne correspondent pas');
define('LANG_settings_USERS_MSG',"Here you can other users who can access to this RPirrigate system and delete them");
define('LANG_settings_USERS_NOOTHERS',"Aucun autre utilisateur ne peu accéder a ceci.");
define('LANG_settings_BANNER_CHANGEPASSWORD', "Mot de passe changé");
define('LANG_settings_BANNER_ADDUSER', "Nouveau utilisateur ajouté");
define('LANG_settings_BANNER_DELETEUSER', "Utilisateur éffacé");
define('LANG_settings_BANNER_LANGUAGE', "Langue sauvagardée");
define('LANG_settings_BANNER_LOCATION', "Localisation sauvagardée");
define('LANG_settings_BANNER_WEATHER', "Préférance météo sauvegardée");
define('LANG_settings_RUSURE', "Etes-vous sure?");
define('LANG_settings_WEATHER','Prévision Météo');
define('LANG_settings_WEATHER_MSG','Choose if to enable the weather forecasts or not.');

define('LANG_module_STATUS','Status');
define('LANG_module_MODE', 'Mode');
define('LANG_module_AUTO', 'Automatique');
define('LANG_module_MANUAL', 'Manuel');
define('LANG_module_DESCRIPTION', 'Module Description');
define('LANG_module_IRRIG_LAST', 'Dernier arrosage');
define('LANG_module_IRRIG_NEXT', 'Prochain arrosage');
define('LANG_module_IRRIG_PLAN', 'Irrigation Plan');
define('LANG_module_IRRIGS_LAST', 'Dernier arrosages');
define('LANG_module_IRRIGS', 'Arrosages');
define('LANG_module_VIEWALL', 'Voire tout');
define('LANG_module_SETTINGS', 'Configuration du module');
define('LANG_module_NAME', 'Nom');
define('LANG_module_EDIT', 'Edit');
define('LANG_module_BACK', 'Retour');
define('LANG_module_IRRIGATION','Arrosage');
define('LANG_module_MANUALIRRIGATION','Arrogage Man.');
define('LANG_module_PLANNEDIRRIGATION','Arrosage plannifié');
define('LANG_module_RAIN','Pluie');
define('LANG_module_DATE','Date');
define('LANG_module_TYPE','Type');
define('LANG_module_NOIRRIGYET', 'This module has never been irrigated yet');
define('LANG_module_SAVE', 'Sauver');
define('LANG_module_FORCEDVALUE', 'Valeur forcée');
define('LANG_module_BANNER_MANUAL', 'Mode manuel Activé/Désactivé avec succès');
define('LANG_module_BANNER_DESCRIPTION', 'Description mise à jour');
define('LANG_module_BANNER_SETTINGS', 'Configuration mise à jour');
define('LANG_module_BANNER_NEWEVENT', 'Nouvel arrosage ajouté');
define('LANG_module_BANNER_DELETEEVENT', 'Arrosage effacé');
define('LANG_module_THROUGHTPUT', 'Throughtput (lt/h)');
define('LANG_module_LITERS','Littres');
define('LANG_module_MINUTES','Minutes');
define('LANG_module_MINUTES_SHORT','Min');
define('LANG_module_LITERS_SHORT','L.');
define('LANG_module_INTERVAL','Interval');
define('LANG_module_ADDEVENT','Nouvel arrosage');
define('LANG_module_QUANTITYTOGIVE','Temp (en minutes) de chaque arrosage');
define('LANG_module_STARTINGFROM','Démarrer de');
define('LANG_module_IRRIGATEEVERY','Arrosez tous les');
define('LANG_module_INPROGRESS', "En cours");
define('LANG_module_ERR1', "Merci de remplir au moin un des champs Semaines/Jours/Heures");
define('LANG_module_ERR2', "Merci de remplir le champ de démarrage");
define('LANG_module_ERR3', "Merci de remplir l'heure de démarrage");
define('LANG_module_ERR4', "Merci de remplir le champ Littres");
define('LANG_module_DELETE_BTN', "Suprimmer le module");
define('LANG_module_DELETE_ALERT', "Etes-vous sur? Cette action ne peu pas etre annulée!");

define('LANG_modulenew_IMAGEFILE', 'Fichier image');
define('LANG_modulenew_IMAGE_LOGODEFAULT', "Si vous ne chargez pas d'image, celle par defaut sera utilisée");
define('LANG_modulenew_BANNER', "Nouveau module ajouté avec succès");
define('LANG_modulenew_ERR1', "Remplissez le champ Nom");
define('LANG_modulenew_ERR2', "Remplissez le champ de description");
define('LANG_modulenew_ERR3', "Sélèctionnez un PIN GPIO");
define('LANG_modulenew_ERR4', "Please fill up the Throughtput field");

define('LANG_timestring_WEEKS', 'Semaines');
define('LANG_timestring_DAYS', 'Jours');
define('LANG_timestring_HOURS', 'Heures');
define('LANG_timestring_HOURS_SHORT', 'H');
define('LANG_timestring_MINUTES', 'Minutes');
define('LANG_timestring_MINUTES_SHORT', 'Min');
define('LANG_timestring_SECONDS_SHORT', 's');
define('LANG_timestring_WEEK', 'Semaine');
define('LANG_timestring_DAY', 'Jour');
define('LANG_timestring_HOUR', 'Heure');
define('LANG_timestring_MINUTE', 'Minute');

?>
