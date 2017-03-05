import socket
import os, os.path
import time,stat
import RPi.GPIO as GPIO

#importing for constraints
from GPIOClass import GPIOClass

socketpath = "/run/rpirrigate-gpiod-socket"

os.chmod(socketpath,stat.S_IRWXO)

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

def set_high(gp):
	GPIO.setup(gp,GPIO.OUT)
	GPIO.output(gp,1)

def set_low(gp):
	GPIO.setup(gp,GPIO.OUT)
	GPIO.output(gp,0)

def get_value(gp):
	val = 1
	if val==0:
		return GPIOClass.STATE_ON+"#"
	elif val==1:
		return GPIOClass.STATE_OFF+"#"
	else:
		return GPIOClass.STATE_ERR+"#"


if os.path.exists(socketpath):
	os.remove( socketpath )

server = socket.socket( socket.AF_UNIX, socket.SOCK_STREAM )
server.bind(socketpath)


server.listen(5)

while True:
	conn, addr  = server.accept()

	while True:

		datagram = conn.recv( 1024 )


		if not datagram:
			break
		
		d=datagram.split("#")

		print d

		if(len(d)<2):
			break

		if str(d[0]) == GPIOClass.STATE_ON :
			set_low(int(d[1]))
		elif str(d[0]) == GPIOClass.STATE_OFF:
			set_high(int(d[1]))
		elif str(d[0]) == GPIOClass.STR_GETVAL:
			val = get_value(int(d[1]))

			conn.send( str(val) )

server.close()
