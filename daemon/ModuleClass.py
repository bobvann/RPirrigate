from EventClass import EventClass
from datetime import datetime, timedelta
### CLASS Module
##
##  Properties
##     - id
##     - gpio
##     - throughtput
##     - manualACT
##     - manualVAL
##     - Events = Event object
##     - isOpen  (bool if is irrigating)
##		     - openEventID  (which event is irrigating)
##		     - openTime  (when did it open)
##		     - openDbRowID (record LogID on the Database)
##		     - openLogID (LogID on the class)
##		     - openLiters (total liters of the current open event)
##
##  Methods
##     - __init__
##     - reloadSettings(DataBase)
##     - reloadManuals(DataBase)

class ModuleClass:
	def __init__(self, DataBase, initID):
		self.id = initID
		self.isOpen = False
		self.reloadSettings(DataBase)
		self.reloadManuals(DataBase)

	def reloadSettings(self, DataBase):
		info = DataBase.select_module_data(self.id)
		self.gpio = info[0]
		self.throughtput = info[1]

		self.Events = []
		ev_ids = DataBase.select_event_ids(self.id)
		for ev_id in ev_ids:
			self.Events.append(EventClass(DataBase, ev_id))
		for ev in self.Events:
			ev.duration = int( float(ev.liters)/float(self.throughtput) * 60 )
			#ev.duration = int(ev.liters) #Daemon runs on more than 1 min, so no sense of decimal minutes

		#DEBUG
		#print "MODULE SETTINGS " + str(self.id) + " RELOADED"

	def reloadManuals(self, DataBase):
		manual = DataBase.select_module_manual(self.id)
		self.manualACT = bool(manual[0])
		self.manualVAL = bool(manual[1])

		#DEBUG
		#print "MODULE MANUALS " + str(self.id) + " RELOADED"

	def open(self, GPIO, evID):
		#DEBUG, CHANGE TO REAL GPIO
		#print "GPIO "+str(self.gpio)+" SET UP AS IRRIGATING"
		GPIO.openWater(self.gpio)
		
		self.isOpen = True
		self.openEventID = evID
		self.openTime = datetime.now()

		#evID=1 means Manual
		if evID != -1:
			self.openLiters = next((x for x in self.Events if x.id == evID), None).liters

	def close(self, GPIO):
		#DEBUG, CHANGE TO REAL GPIO
		#print "GPIO "+str(self.gpio)+" SET UP AS NOT IRRIGATING ANYMORE"
		GPIO.closeWater(self.gpio)

		self.isOpen = False
		self.openEventID = None
		self.openTime = None
		self.openLiters = None
		self.openDbRowID = None

	#Logs = array of Log classes
	def shouldOpenNow(self, Logs, Weather):
		# checks if there is any event to do now
		# if today will not rain, checks events
		if self.manualACT:
			if self.manualVAL:
				return -1 #return -1 which means manual (note that bool(-1)->True so ok for if(..) check)
			else:
				return False
		else:
			now = datetime.now()
			if Weather.willRainToday < 2:
				for ev in self.Events:
					if now > ev.nextExec(Logs):
						return ev.id

			return False

	def shouldCloseNow(self):
		# returns true if the event in self.openEventID has "expired"
		now = datetime.now()
		
		if self.openEventID==-1 and (not self.manualACT or (self.manualACT and not self.manualVAL)):
			return True
		else:
			if self.manualACT:
				return not self.manualVAL
			else:
				try:
					for ev in self.Events:
						if ev.id == self.openEventID:

							if (self.openTime + timedelta(minutes=ev.duration)) < now:
								return True
							else:
								return False
				except AttributeError:
					return False
