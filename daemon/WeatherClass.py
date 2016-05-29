
class WeatherClass:
	def __init__(self, DB, Settings):
		self.minimumStopRainMM = 2
		self.reload(DB, Settings)

	def reload(self, DB, Settings):
		if Settings.weatherEnabled:
			self.willRainToday = DB.select1_willRainToday()	
		else:
			#if weather disabled, forces to "think" that today it's not gonna rain
			self.willRainToday = 0

