#DEFAULT LIBRARIES
import os, signal, subprocess, sys
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
#ON SIGUSR2 UPDATES MANUAL VALS
#ON SIGHUP RELOADS LOGS AND WEATHER (gets this signal from weather daemon)
def handUSR1(signum, frame):
	Settings.reloadSettingsOnNext = True

def handUSR2(signum, frame):
	Settings.reloadManualsOnNext = True

def handHUP(signum, frame):
	Settings.reloadWeatherLogsOnNext = True


#LOG ON LOGFILE
def logStatus(text):
	with open("/var/log/rpirrigate/status.log","a") as f: 
		f.write(str(datetime.now())+" "+text+"\n")

def logError(text):
	with open("/var/log/rpirrigate/error.log","a") as f: 
		f.write(str(datetime.now())+" "+text+"\n")

signal.signal(signal.SIGUSR1, handUSR1)
signal.signal(signal.SIGUSR2, handUSR2)
signal.signal(signal.SIGHUP, handHUP)

try:
	#DATABASE CLASS
	DataBase = DBClass()

	# Settings get Setting object
	# Only One! This would be a static class in Java
	Settings = SettingClass(DataBase)

	#Weather (pseudo-static) class
	Weather = WeatherClass(DataBase)

	#GPIO (pseudo-static) class
	GPIO = GPIOClass()

	#Create Modules[] array of Modules
	#Every Module create its Events as its .Events
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

	#NOW WE HAVE TO SAVE THE PID OF THIS PROCESS IN THE DATABASE 
	#SO THE PHP CAN SEND SIGUSR1 /SIGUSR2
	#AND THE WEATHER DAEMON/CRONJOB CAN SEND SIGHUP
	pid = os.getpid()
	DataBase.query_pid_save(pid)


	#DEBUG
	#print "PID:" + str(pid)
	#print ""
	#print "Location: " + str(Settings.location)+
	#print "WillRainToday: " + str(DataBase.select1_willRainToday())
	#print ""

	weatherdExecutedToday = False

	while(True):
		#EXECUTE WEATHERD AT 3 AM
		now = datetime.now()
		if now.hour==2:
			weatherdExecutedToday = False
		if now.hour==3 and not weatherdExecutedToday:
			wd = ""
			#ATTEMPTS UNTIL GETS OK RESPONSE FROM WEATHERD (should always go fine btw)
			while wd!="OK\n":
				logStatus("FETCH WEATHER")
				wd = subprocess.check_output(["python", "/srv/rpirrigate/daemon/RPirrigate-weatherd.py"])
				print wd
				if wd!="OK\n":
					sleep((datetime.now().second % 12)+3) #waits pseudo-random seconds 3-15 before attempting again
					logStatus("FETCH WEATHER FAILED")

			weatherdExecutedToday = True
			Settings.ReloadWeatherLogsOnNext = True

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
			Weather.reload(DataBase)

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
except:
	logError(str(sys.exc_info()[0]))
	
