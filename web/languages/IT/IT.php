<?php
// file della lingua italiana
// ora ci definiamo tutte le costanti a caso, poi faremo uno skeleton cosi possano creare anche le altre 
define('LANG_FLAGFILE','languages/IT/flag.jpg');

define('LANG_index_WRONGMSG', 'Username e/o password errati');

define('LANG_menu_SETTINGS', 'Impostazioni');
define('LANG_menu_ADDMODULE', 'Nuovo Modulo');

define('LANG_home_SYSTEM', 'Sistema');
define('LANG_home_WEATHER', 'Meteo Attuale');
define('LANG_home_VERSION', 'Versione');
define('LANG_home_LOADING', 'Caricamento...');
define('LANG_home_DATETIME','Data/Ora di sistema');
define('LANG_home_DAEMON', 'Stato del demone');
define('LANG_home_DAEMON_OK', 'In esecuzione');
define('LANG_home_DAEMON_KO', 'Fermo');
define('LANG_home_NEXTRAIN', 'Prossima pioggia prevista');
define('LANG_home_TIME','Ore');

define('LANG_settings_LANGUAGE', 'Lingua');
define('LANG_settings_LOCATION', 'Località');
define('LANG_settings_CHANGE','Cambia');
define('LANG_settings_ALERTTTW',"<b>Attenzione: </b>questa è un'impostazione avanzata. Modificarla può causare il malfunzionamento di RPirrigate.");
define('LANG_settings_ALERTTTW2',"Cambialo solo se sai cosa stai facendo!");
define('LANG_settings_CURRENT','Attuale');
define('LANG_settings_NEW','Nuova');
define('LANG_settings_BACK','Indietro');
define('LANG_settings_NEXT','Avanti');
define('LANG_settings_LOCATION_MSG',"Qui selezioni la tua località. Questo dato viene utilizzato per il meteo");
define('LANG_settings_CONFIRM','Conferma');
define('LANG_settings_ERRORLOC','Errore. La località che hai inserito non è valida');
define('LANG_settings_ERRORTTW','TTW Invalido. Leggi la documentazione per più info');
define('LANG_settings_USERS','Utenti');
define('LANG_settings_GENERAL','Generali');
define('LANG_settings_CHANGEPASSWORD','Cambia Password');
define('LANG_settings_CHANGEPASSWORD_MSG',"Qui puoi cambiare la tua password");
define('LANG_settings_CHANGEPASSWORD_OLD',"Vecchia Password");
define('LANG_settings_CHANGEPASSWORD_NEW',"Nuova   Password");
define('LANG_settings_CHANGEPASSWORD_ERROR1',"Compila i campi password corrente e nuova");
define('LANG_settings_CHANGEPASSWORD_ERROR2',"Le due password inserite non coincidono");
define('LANG_settings_CHANGEPASSWORD_ERROR3',"La password corrente che hai inserito non è corretta");
define('LANG_settings_OTHERUSERS','Altri Utenti');
define('LANG_settings_ADDUSER','Aggiungi Utente');
define('LANG_settings_ADDUSER_MSG',"Da questa sezione puoi aggiungere un utente a RPirrigate");
define('LANG_settings_ADDUSER_ERROR1', 'Compila i campi username e password');
define('LANG_settings_ADDUSER_ERROR2', "L'username può contenere solo lettere e numeri");
define('LANG_settings_ADDUSER_ERROR3', 'Le due password non coincidono');
define('LANG_settings_USERS_MSG',"Qui puoi visualizzare gli altri utenti che possono accedere a questo sistema RPirrigate ed eliminarli");
define('LANG_settings_USERS_NOOTHERS',"Non ci sono altri utenti che possono accedere a questo sistema.");
define('LANG_settings_BANNER_CHANGEPASSWORD', "Password cambiata con successo");
define('LANG_settings_BANNER_ADDUSER', "Nuovo utente aggiunto con successo");
define('LANG_settings_BANNER_DELETEUSER', "Utente eliminato con successo");
define('LANG_settings_BANNER_LANGUAGE', "Lingua cambiata con successo");
define('LANG_settings_BANNER_LOCATION', "Località cambiata con successo");
define('LANG_settings_BANNER_WEATHER', "Preferenze previsioni meteo salvate con sucesso");
define('LANG_settings_RUSURE', "Sei sicuro/a?");
define('LANG_settings_WEATHER','Previsioni Meteo');
define('LANG_settings_WEATHER_MSG','Qui selezioni se abilitare le previsioni meteo.');

define('LANG_module_STATUS','Stato');
define('LANG_module_MODE', 'Modalità');
define('LANG_module_AUTO', 'Automatica');
define('LANG_module_MANUAL', 'Manuale');
define('LANG_module_DESCRIPTION', 'Descrizione Modulo');
define('LANG_module_IRRIG_LAST', 'Ultima irrigazione');
define('LANG_module_IRRIG_NEXT', 'Prossima irrigazione');
define('LANG_module_IRRIG_PLAN', 'Piano di irrigazione');
define('LANG_module_IRRIGS_LAST', 'Ultime irrigazioni');
define('LANG_module_IRRIGS', 'Irrigazioni');
define('LANG_module_VIEWALL', 'Vedi Tutte');
define('LANG_module_SETTINGS', 'Impostazioni modulo');
define('LANG_module_NAME', 'Nome');
define('LANG_module_EDIT', 'Modifica');
define('LANG_module_BACK', 'Indietro');
define('LANG_module_IRRIGATION','Irrigazione');
define('LANG_module_MANUALIRRIGATION','Irrig. Manuale');
define('LANG_module_PLANNEDIRRIGATION','Irrig. Programmata');
define('LANG_module_RAIN','Pioggia');
define('LANG_module_DATE','Data');
define('LANG_module_TYPE','Tipo');
define('LANG_module_NOIRRIGYET', 'Questo modulo non è mai stato irrigato');
define('LANG_module_SAVE', 'Salva');
define('LANG_module_FORCEDVALUE', 'Valore Forzato');
define('LANG_module_BANNER_MANUAL', 'Modalità manuale attivata/disattivata con succcesso');
define('LANG_module_BANNER_DESCRIPTION', 'Descrizione modificata con successo');
define('LANG_module_BANNER_SETTINGS', 'Impostazioni aggiornate con successo');
define('LANG_module_BANNER_NEWEVENT', 'Nuova irrigazione aggiunta al piano con successo');
define('LANG_module_BANNER_DELETEEVENT', 'Irrigazione eliminata con successo');
define('LANG_module_THROUGHTPUT', 'Portata (l/h)');
define('LANG_module_LITERS','Litri');
define('LANG_module_LITERS_SHORT','Lt.');
define('LANG_module_INTERVAL','Intervallo');
define('LANG_module_ADDEVENT','Nuova Irrigazione');
define('LANG_module_QUANTITYTOGIVE',"Litri d'acqua ad ogni irrigazione");
define('LANG_module_STARTINGFROM','A partire da');
define('LANG_module_IRRIGATEEVERY','Irriga ogni');
define('LANG_module_INPROGRESS', "In corso");
define('LANG_module_ERR1', "Compila almeno uno dei campi Settimane/Giorni/Ore");
define('LANG_module_ERR2', "Compila il campo Data");
define('LANG_module_ERR3', "Compila il campo Ora");
define('LANG_module_ERR4', "Compila il campo Litri");
define('LANG_module_DELETE_BTN', "Elimina Modulo");
define('LANG_module_DELETE_ALERT', "Sei sicuro? Questa azione non può essere annullata!");

define('LANG_modulenew_IMAGEFILE', 'File immagine');
define('LANG_modulenew_IMAGE_LOGODEFAULT', "Se non carichi alcun file, verr&agrave; usato quello di default");
define('LANG_modulenew_BANNER', "Nuovo modulo aggiunto con successo");
define('LANG_modulenew_ERR1', "Compila il campo Nome");
define('LANG_modulenew_ERR2', "Compila il campo Descrizione");
define('LANG_modulenew_ERR3', "Scegli un PIN GPIO");
define('LANG_modulenew_ERR4', "Compila il campo Portata");

define('LANG_timestring_WEEKS', 'Settimane');
define('LANG_timestring_DAYS', 'Giorni');
define('LANG_timestring_HOURS', 'Ore');
define('LANG_timestring_MINUTES', 'Minuti');
define('LANG_timestring_WEEK', 'Settimana');
define('LANG_timestring_DAY', 'Giorno');
define('LANG_timestring_HOUR', 'Ora');
define('LANG_timestring_MINUTE', 'Minuto');

?>
