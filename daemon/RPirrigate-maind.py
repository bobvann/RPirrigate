#DEFAULT LIBRARIES
import os, signal, subprocess, sys, linecache
from time import sleep
from datetime import datetime

#RPirrigate classes
from SettingClass import SettingClass
from DBClass import DBClass
from ModuleClass import ModuleClass
from LogsClass import LogsClass
from WeatherClass import WeatherClass
from GPIOClass import GPIOClass

#ON SIGUSR1 UPDATES SETTINGS + MODULE INFOS,
def handUSR1(signum, frame):
	Settings.reloadSettingsOnNext = True

#ON SIGUSR2 UPDATES MANUAL VALS
def handUSR2(signum, frame):
	Settings.reloadManualsOnNext = True

#ON SIGHUP RELOADS LOGS AND WEATHER (gets this signal from weather daemon)
#REALLY DON'T NEED THIS, AS WEATHER IS A SUBPROCESS
#GONNA KEEP IT HERE FOR NOW, maybe will change weather logic later
def handHUP(signum, frame):
	Settings.reloadWeatherLogsOnNext = True


#LOG ON LOGFILE status
def logStatus(text):
	with open("/var/log/rpirrigate/status.log","a+") as f: 
		f.write(str(datetime.now())+" "+text+"\n")

#LOG ON LOGFILE error
def logError():
	exc_type, exc_obj, tb = sys.exc_info()
	f = tb.tb_frame
	lineno = tb.tb_lineno
	filename = f.f_code.co_filename
	linecache.checkcache(filename)
	line = linecache.getline(filename, lineno, f.f_globals)

	text = "EXCEPTION IN (" + str(filename) + ", LINE " + str(lineno) + " '" + str(line.strip()) + "'):" + str(exc_obj);
	with open("/var/log/rpirrigate/error.log","a+") as f: 
		f.write(str(datetime.now())+" "+text+"\n")

#SIGNAL HANDLING DEFINITON
signal.signal(signal.SIGUSR1, handUSR1)
signal.signal(signal.SIGUSR2, handUSR2)
signal.signal(signal.SIGHUP, handHUP)

#WAIT FOR GPIO DAEMON TO CORRECTLY ACTIVATE
sleep(10)

#CATCH **ANY** ERROR AND WRITE ERROR FILE 
try:
	#DATABASE CLASS
	DataBase = DBClass()

	# Settings obj 
	Settings = SettingClass(DataBase)

	#Weather obj 
	Weather = WeatherClass(DataBase, Settings)

	#GPIO obj 
	GPIO = GPIOClass()

	#Create Modules[] array of Modules  objs
	#Every Module create its Events as its M.Events[] in its constructor
	Modules = []
	mods = DataBase.select_module_ids()
	for mod in mods:
		Modules.append(ModuleClass(DataBase, mod))

	#Create Logs[] array of Logs
	#as potentially they are many and I also don't care of ids,
	#use different way to create them
	#when reloading them I just need to add some, never to edit existents
	Logs = LogsClass(DataBase)
	#NOW WE HAVE 
	##  ONE SETTINGS CLASS ((i.e. Settings )) 
	##  ONE DATABASE CLASS ((i.e. DataBase  ))
	##  AN ARRAY OF MODULE CLASSES ((i.e. Modules[]  ))

	#SAVE THE PID ON THE DB SO PHP CAN SEND SIGNALS
	pid = os.getpid()
	DataBase.query_pid_save(pid)


	#CLOSING WATER ON ALL MODULES ON STARTUP
	for M in Modules:
		M.close(GPIO)

	logStatus("RPirrigate STARTED")
	#DEBUG
	#print "PID:" + str(pid)
	#print ""
	#print "Location: " + str(Settings.location)+
	#print "WillRainToday: " + str(DataBase.select1_willRainToday())
	#print ""

	#WEATHER LOGIC...if weatherd has been executed today
	weatherdExec = False
	while(True):
		#EXECUTE WEATHERD AT 3 AM
		now = datetime.now()
		if now.hour==0 and not weatherdExec and Settings.weatherEnabled:
			wd = ""
			#ATTEMPTS UNTIL GETS OK RESPONSE FROM WEATHERD (should always go fine btw)
			while wd!="OK\n":
				logStatus("FETCH WEATHER")
				wd = subprocess.check_output(["python", "/srv/rpirrigate/daemon/RPirrigate-weatherd.py"])
				if wd!="OK\n":
					sleep((datetime.now().second % 12)+3) #waits pseudo-random seconds 3-15 before attempting again
					logStatus("FETCH WEATHER FAILED")

			weatherdExec = True
			Settings.ReloadWeatherLogsOnNext = True

		if now.hour==1:
			weatherdExec = False

		#CHECK IF NEED TO RELOAD SETTINGS (SIGUSR1, sent by web)
		if Settings.reloadSettingsOnNext:
			logStatus("RELOAD SETTINGS")
			Settings.reload(DataBase)

			#1) check if need to add modules
			mods = DataBase.select_module_ids()
			for mod in mods:
				if next((x for x in Modules if x.id == mod), None) is None:
					Modules.append(ModuleClass(DataBase, mod))


			#2) check existing modules gpio and throughtput
			for mod in Modules:
				mod.reloadSettings(DataBase)

			Settings.reloadSettingsOnNext = False

		#CHECK IF NEED TO RELOAD MANUALS (SIGUSR2, sent by web)
		if Settings.reloadManualsOnNext:
			logStatus("RELOAD MANUALS")
			for mod in Modules:
				mod.reloadManuals(DataBase)

			Settings.reloadManualsOnNext = False

		##CHECK IF NEED TO RELOAD WEATHER & LOGS (SIGHUP, sent by weatherd)
		if Settings.reloadWeatherLogsOnNext:
			logStatus("RELOAD WEATHER")
			Logs.reload(DataBase)
			Weather.reload(DataBase, Settings)

			Settings.reloadWeatherLogsOnNext = False

		#DAEMON LOGIC
		for M in Modules:
			evID = M.shouldOpenNow(Logs, Weather)
			if (not M.isOpen) and evID:
				M.open(GPIO, evID)
				DataBase.query_log_open(M, evID)
				Logs.logOpen(M, evID)
				logStatus("MODULE OPEN " + str(M.id))
			if M.isOpen  and M.shouldCloseNow():
				DataBase.query_log_close(M)
				Logs.logClose(M)
				M.close(GPIO)
				logStatus("MODULE CLOSE " + str(M.id))
		
		#DEBUG
		#logStatus("WHILE ENDED")
		#print "WHILE ENDED " + str(datetime.now())
		#for mod in Modules:
			#print "MODULO " + str(mod.id)
			#print "   isOpen: " + str(mod.isOpen)
			#print "   shouldOpenNow: " + str(mod.shouldOpenNow(Logs, Weather))
			#print "   ManualACT: " + str(mod.manualACT)
			#print "   ManualVAL: " + str(mod.manualVAL)
			#print "   GPIO: " + str(mod.gpio)
			#print "   Throughtput: " + str(mod.throughtput)
			#if mod.isOpen:
				#print "   shouldCloseNow: " + str(mod.shouldCloseNow())
				#print "   openEventID: " + str(mod.openEventID)
				#print "   openDbRowID: " + str(mod.openDbRowID)
				#print "   openTime: " + str(mod.openTime)
				#print "   openDbRowID: " + str(mod.openDbRowID)
				#if mod.openEventID != -1:
					#print "   openLiters: " + str(mod.openLiters)
			#for ev in mod.Events:
				#print "   EVENTO: " + str(ev.id)
				#print "      Intervallo: " + str(ev.timeInterval)
				#print "      Ora: " + str(ev.hour)
				#print "      Litri: " + str(ev.liters)
				#print "      Durata: " + str(ev.duration)
				#print "      Prima Esecuzione: " + str(ev.firstExecution)
				#print "      Next Esecuzione: " + str(ev.nextExec(Logs))
		#print ""

		sleep(60)
except Exception:
	logError()
	



