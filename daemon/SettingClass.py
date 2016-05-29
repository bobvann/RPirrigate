
### CLASS Module
##
##  Properties
##     - location
##     - timeToWait
##     - reloadSettingsOnNext
##     - reloadManualOnNext
##
##  Methods
##     - __init__
##     - reload

class SettingClass:
	def __init__(self, DataBase):
		self.reload(DataBase)

		self.reloadSettingsOnNext = False
		self.reloadManualsOnNext = False
		self.reloadWeatherLogsOnNext = False

	def reload(self, DataBase):	
		self.location = DataBase.select1_setting("Location")
		self.weatherEnabled = DataBase.select1_setting("WeatherEnabled")=="1"