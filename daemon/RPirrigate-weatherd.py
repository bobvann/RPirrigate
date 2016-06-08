import urllib2, json
import time
import datetime
import calendar

from DBClass import DBClass

#SHOULD BE SAFE TO EXECUTE MULTIPLE TIMES
#BUT IS NOT, WILL DUPLICATE ENTRIES IN DB


class Rain:
	def __init__(self, p_time, p_mm):
		self.time = p_time
		self.mm = p_mm

def local_to_utc(t):
    secs = time.mktime(t)
    return time.gmtime(secs)

def utc_to_local(t):
    secs = calendar.timegm(t)
    return time.localtime(secs)


DB = DBClass()

#minimum mm to be saved
MIN_MM = 1

city = DB.select1_setting("Location")

now = datetime.datetime.now()
yesterday = now - datetime.timedelta(days=1)

now_date = time.strftime("%Y-%m-%d", now.timetuple())
yesterday_date = time.strftime("%Y-%m-%d", yesterday.timetuple())


#1)   TODAY WEATHER FORECAST

url="http://api.worldweatheronline.com/free/v2/weather.ashx?q="+str(city)+"&date="+str(now_date)+"&key=dadb7eba889f53e8a61dd447cac39&format=json"
response = urllib2.urlopen(url)
j = json.loads(response.read())

forecastsDo = False
forecasts = []
for hourly in j['data']['weather'][0]['hourly']:
	if float(hourly['precipMM']) >= MIN_MM:
		timeS = "000" + hourly['time']
		timeS = now_date + " " + timeS[-4:-2] + ":" + timeS[-2:]
		forecastsDo = True
		forecasts.append(Rain(timeS, float(hourly['precipMM']) ))

if forecastsDo:
	DB.query_insert_forecasts(forecasts)


#2)   YESTERDAY WEATHER HISTORY (SAVE AS tbLogs)

url="http://api.worldweatheronline.com/free/v2/past-weather.ashx?q="+str(city)+"&date="+str(yesterday_date)+"&key=dadb7eba889f53e8a61dd447cac39&format=json"
response = urllib2.urlopen(url)
j = json.loads(response.read())


logsDo = False
rains = []
for hourly in j['data']['weather'][0]['hourly']:
	if float(hourly['precipMM']) >= MIN_MM:
		timeS = "000" + hourly['time']
		timeS = yesterday_date + " " + timeS[-4:-2] + ":" + timeS[-2:]
		logsDo = True
		rains.append(Rain(timeS, float(hourly['precipMM']) ))

if logsDo:
	DB.query_insert_rainlog(rains)


#DELETE FORECASTS OLDER THAN TODAY
DB.query_delete_old_forecasts()

print "OK"
