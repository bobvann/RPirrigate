import MySQLdb

from datetime import datetime
### CLASS DataBase
##
##  Properties
##     - host
##     - username
##     - password
##     - name
##
##  Methods
##     - __init__
##     - select1_setting(self, name)
##     - select_module_ids(self)
##     - select_module_data(self, ModuleID)
##     - select_module_manual(self, ModuleID)
##     - select_event_ids(self, ModuleID)
##     - select_event_data(self, EventID)
##     - query_pid_save(self, pid)

class DBClass:
	host = "127.0.0.1"   #METTI QUI "bobvann.noip.me" per usarlo in remoto
	username = "rpirrigate"
	password = "rpirrigate"
	name = "dbRpirrigate" 

	def __init__(self):
		self.db = MySQLdb.connect(self.host, self.username, self.password, self.name)
		self.db.ping(True)

	def select1_setting(self, name):
		
		cursor = self.db.cursor()
		cursor.execute("SELECT Value FROM tbSettings WHERE Name='" + name + "'")
		results = cursor.fetchall()
		self.db.commit()
		return results[0][0]

	def select_module_ids(self): #returns list of IDs
		ids = []
		cursor = self.db.cursor()
		cursor.execute("SELECT ModuleID FROM tbModules;")
		results = cursor.fetchall()
		self.db.commit()
		for row in results:
			ids.append(row[0])
		return ids

	#returns array [0->Name, 1->Description, 2->GPIO, 3->Throughtput]
	def select_module_data(self, ModuleID):
		cursor = self.db.cursor()
		cursor.execute("SELECT GPIO, Throughtput FROM tbModules WHERE ModuleID = " + str(ModuleID))
		results = cursor.fetchall()
		self.db.commit()
		return [results[0][0], results[0][1] ]

	#returns array [0->ManualACT, 1->ManualVAL]
	def select_module_manual(self, ModuleID):
		cursor = self.db.cursor()
		cursor.execute("SELECT ManualACT, ManualVAL FROM tbModules WHERE ModuleID = " + str(ModuleID))
		results = cursor.fetchall()
		self.db.commit()
		return [results[0][0], results[0][1] ]

	def select_event_ids(self, ModuleID):
		ids = []
		cursor = self.db.cursor()
		cursor.execute("SELECT EventID FROM tbEvents WHERE ModuleID = " + str(ModuleID))
		results = cursor.fetchall()
		self.db.commit()
		for row in results:
			ids.append(row[0])
		return ids

	def select_logs(self):
		cursor = self.db.cursor()
		cursor.execute("SELECT LogID, Time, isRain, Liters, ModuleID, EventID FROM tbLogs;")
		results= cursor.fetchall()
		self.db.commit()
		return results

	#returns array [TimeInterval, Hour, Liters, FirstExecution]
	def select_event_data(self, EventID):
		cursor = self.db.cursor()
		cursor.execute("SELECT TimeInterval, HOUR(Hour), MINUTE(Hour), Liters, FirstExecution FROM tbEvents WHERE EventID = " + str(EventID))
		results = cursor.fetchall()
		self.db.commit()
		return [results[0][0], results[0][1], results[0][2], results[0][3], results[0][4] ]

	def query_pid_save(self, pid):
		cursor = self.db.cursor()
		cursor.execute("UPDATE tbSettings SET Value = %s WHERE Name = 'LastPID';", str(pid))
		self.db.commit()
		return

	def select1_willRainToday(self):
		cursor = self.db.cursor()
		cursor.execute("SELECT SUM(Liters) FROM tbRainForecasts WHERE Day(Time) = CURDATE();")
		results = cursor.fetchall()
		self.db.commit()
		return bool(results[0][0])

	def query_log_open(self, M, evID):
		cursor = self.db.cursor()
		cursor.execute("INSERT INTO tbLogs(Time, isRain, Liters, ModuleID, EventID) VALUES (NOW(), 0, -1, " + str(M.id) + ", " + str(evID) + ");")
		self.db.commit()

		M.openDbRowID = cursor.lastrowid

	def query_log_close(self, M):
		now = datetime.now()
		start = M.openTime
		diff = now - start
		liters = float(M.throughtput)*diff.total_seconds()/60/60

		cursor = self.db.cursor()
		cursor.execute("UPDATE tbLogs SET Liters = "+ str(liters) +" WHERE LogID = "+ str(M.openDbRowID))
		self.db.commit()

	def query_insert_rainlog(self, logs):
		sql = "INSERT INTO tbLogs (Time, isRain, Liters, ModuleID, EventID) VALUES "
		for l in logs:
			sql = sql + "('"+str(l.time)+"', 1, "+str(l.mm)+", NULL, NULL), "
		sql = sql[:-2] + ";"
		cursor = self.db.cursor()
		cursor.execute(sql)
		self.db.commit()

	def query_insert_forecasts(self, forecasts):
		sql = "INSERT INTO tbRainForecasts (Time, Liters) VALUES "
		for f in forecasts:
			sql = sql + "('"+str(f.time)+"', "+str(f.mm)+"), "
		sql = sql[:-2] + ";"
		cursor = self.db.cursor()
		cursor.execute(sql)
		self.db.commit()

	def query_delete_old_forecasts(self):
		sql = "DELETE FROM tbRainForecasts WHERE DATE(Time) < CURDATE();"
		cursor = self.db.cursor()
		cursor.execute(sql)
		self.db.commit()
