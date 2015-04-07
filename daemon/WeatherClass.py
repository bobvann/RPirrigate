
class WeatherClass:
	def __init__(self, DB):
		self.reload(DB)

	def reload(self, DB):
		self.willRainToday = DB.select1_willRainToday()	

