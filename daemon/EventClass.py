from datetime import datetime, timedelta
### CLASS Event (Module.Events are all Events)
##
## this file needs to be included only in the ModuleClass.py file
## not necessary in the "main" as Events are child of Module
##
##
##  Properties
##     - id
##     - hour
##     - liters
##     - firstExecution
##	   - timeInterval
##     - duration (minutes)
##
##  Methods
##     - __init__
##     - reload

class EventClass:
	def __init__(self, DataBase, initID):
		self.id = initID
		self.reload(DataBase)

	def reload(self, DataBase):
		#returns array [TimeInterval, Hour, Liters, FirstExecution]
		data = DataBase.select_event_data(self.id)

		self.timeInterval = data[0]
		self.hour =  data[1]
		self.liters = data[2]
		self.firstExecution = data[3]

	def nextExec(self, Logs):
		for log in Logs.elements:
			if log.eventID == self.id:
				last = log.time
				if type(last) is str:
					last = datetime.strptime(log.time, '%Y-%m-%d %H:%M:%S')
				return last + timedelta(minutes=self.timeInterval)

		#here if not found yet
		return datetime.strptime(str( str(self.firstExecution) + " " + str(self.hour)), '%Y-%m-%d %H:%M:%S')
