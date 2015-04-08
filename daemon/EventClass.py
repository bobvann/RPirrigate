from datetime import datetime, timedelta, date, time
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
		self.hour =  time( int( data[1] ), int( data[2] ) )
		self.liters = data[3]
		self.firstExecution = data[4]

	def nextExec(self, Logs):
		for log in Logs.elements:
			if log.eventID == self.id:
				last = log.time
				if type(last) is str:
					last = datetime.strptime(log.time, '%Y-%m-%d %H:%M:%S')
				next= last + timedelta(minutes=self.timeInterval)

				return datetime(next.year, next.month, next.day, self.hour.hour, self.hour.minute)

		#here if not found yet
		return datetime.strptime(str( str(self.firstExecution) + " " + str(self.hour)), '%Y-%m-%d %H:%M:%S')
