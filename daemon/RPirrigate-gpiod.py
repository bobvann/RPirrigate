import socket
import os, os.path
import time,stat
import RPi.GPIO as GPIO

socketpath = "/run/rpirrigate-gpiod-socket"

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)

def set_high(gp):
	GPIO.setup(gp,GPIO.OUT)
	GPIO.output(gp,1)

def set_low(gp):
	GPIO.setup(gp,GPIO.OUT)
	GPIO.output(gp,0)


if os.path.exists(socketpath):
	os.remove( socketpath )

server = socket.socket( socket.AF_UNIX, socket.SOCK_DGRAM )
server.bind(socketpath)
os.chmod(socketpath,stat.S_IRWXO)

while True:
	datagram = server.recv( 1024 )
	d=datagram.split("#")

	if str(d[0])=="LOW":
		set_low(int(d[1]))
	elif str(d[0])=="HIGH":
		set_high(int(d[1]))  

server.close()
