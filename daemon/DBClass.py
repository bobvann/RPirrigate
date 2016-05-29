import sqlite3

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
	DB_PATH = "/srv/rpirrigate/data/database.sqlite"
	def __init__(self):
		pass

	def select1_setting(self, name):
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT Value FROM tbSettings WHERE Name='" + name + "'")
		results = cursor.fetchall()

		conn.commit()
		conn.close()
		return results[0][0]

	def select_module_ids(self): #returns list of IDs
		ids = []
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT ModuleID FROM tbModules;")
		results = cursor.fetchall()
		
		conn.commit()
		conn.close()

		for row in results:
			ids.append(row[0])
		return ids

	#returns array [0->Name, 1->Description, 2->GPIO, 3->Throughtput]
	def select_module_data(self, ModuleID):
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT GPIO, Throughtput FROM tbModules WHERE ModuleID = " + str(ModuleID))
		results = cursor.fetchall()
		
		conn.commit()
		conn.close()

		return [results[0][0], results[0][1] ]

	#returns array [0->ManualACT, 1->ManualVAL]
	def select_module_manual(self, ModuleID):
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT ManualACT, ManualVAL FROM tbModules WHERE ModuleID = " + str(ModuleID))
		results = cursor.fetchall()
		
		conn.commit()
		conn.close()

		return [results[0][0], results[0][1] ]

	def select_event_ids(self, ModuleID):
		ids = []
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT EventID FROM tbEvents WHERE ModuleID = " + str(ModuleID))
		results = cursor.fetchall()
		
		conn.commit()
		conn.close()

		for row in results:
			ids.append(row[0])
		return ids

	def select_logs(self):
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT LogID, Time, isRain, Liters, ModuleID, EventID FROM tbLogs;")
		results= cursor.fetchall()
		
		conn.commit()
		conn.close()

		return results

	#returns array [TimeInterval, Hour, Liters, FirstExecution]
	def select_event_data(self, EventID):
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT TimeInterval, strftime('%H',Hour), strftime('%M',Hour), Liters, FirstExecution FROM tbEvents WHERE EventID = " + str(EventID))
		results = cursor.fetchall()
		
		conn.commit()
		conn.close()

		return [results[0][0], results[0][1], results[0][2], results[0][3], results[0][4] ]

	def query_pid_save(self, pid):
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("UPDATE tbSettings SET Value = " +  str(pid) + " WHERE Name = 'LastPID';")
		
		conn.commit()
		conn.close()

		return

	def select1_willRainToday(self):
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("SELECT SUM(Liters) FROM tbRainForecasts WHERE strftime('%Y-%m-%d',Time) = strftime('%Y-%m-%d','now','localtime');")
		results = cursor.fetchall()
		
		conn.commit()
		conn.close()

		return bool(results[0][0])

	def query_log_open(self, M, evID):
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("INSERT INTO tbLogs(Time, isRain, Liters, ModuleID, EventID) VALUES (strftime('%Y-%m-%d %H:%M:%S','now','localtime'), 0, -1, " + str(M.id) + ", " + str(evID) + ");")
		
		conn.commit()
		conn.close()

		M.openDbRowID = cursor.lastrowid	

	def query_log_close(self, M):
		now = datetime.now()
		start = M.openTime
		diff = now - start
		liters = round( float(M.throughtput)*diff.total_seconds()/60/60 , 2)

		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute("UPDATE tbLogs SET Liters = "+ str(liters) +" WHERE LogID = "+ str(M.openDbRowID))
		
		conn.commit()
		conn.close()

	def query_insert_rainlog(self, logs):
		sql = "INSERT INTO tbLogs (Time, isRain, Liters, ModuleID, EventID) VALUES "
		for l in logs:
			sql = sql + "('"+str(l.time)+"', 1, "+str(l.mm)+", NULL, NULL), "
		sql = sql[:-2] + ";"
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute(sql)
		
		conn.commit()
		conn.close()

	def query_insert_forecasts(self, forecasts):
		sql = "INSERT INTO tbRainForecasts (Time, Liters) VALUES "
		for f in forecasts:
			sql = sql + "('"+str(f.time)+"', "+str(f.mm)+"), "
		sql = sql[:-2] + ";"
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()

		cursor.execute(sql)
		
		conn.commit()
		conn.close()

	def query_delete_old_forecasts(self):
		sql = "DELETE FROM tbRainForecasts WHERE DATE(Time) < strftime('%Y-%m-%d','now','localtime');"
		
		conn = sqlite3.connect(self.DB_PATH)
		cursor = conn.cursor()
		
		cursor.execute(sql)
		
		conn.commit()
		conn.close()
