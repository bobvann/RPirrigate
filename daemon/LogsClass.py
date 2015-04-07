### CLASS Logs
##  Every element of Logs class is a Log, class here defined.
##  Logs contains all logs and provides reload method to reload all logs from the DB
##  pretty usefull when we have to reload logs on the morning due to weather daemon
from datetime import datetime

class Log:
	def __init__(self, p_LogID, p_Time, p_isRain, p_Liters, p_ModuleID, p_EventID):
		self.id = p_LogID
		self.time = p_Time
		self.isRain = p_isRain
		self.liters = p_Liters
		self.moduleID = p_ModuleID
		self.eventID = p_EventID


class LogsClass:

	def __init__(self, DB):
		self.reload(DB)

	def reload(self, DB):
		self.elements = []
		db_logs = DB.select_logs()
		for db_log in db_logs:
			self.elements.append(Log(db_log[0],db_log[1],db_log[2],db_log[3],db_log[4],db_log[5]))

		#DEBUG
		#print "LOGS RELOADED"

	def logOpen(self, M, evID):
		self.elements.append(Log(M.openDbRowID, M.openTime, 0, 0, M.id, evID))

	def logClose(self, M):
		#calculate how many liters have been given really
		now = datetime.now()
		start = M.openTime
		diff = now - start
		liters = float(M.throughtput)*diff.total_seconds()/60/60
		next((x for x in self.elements if x.id == M.openDbRowID), None).liters = liters
