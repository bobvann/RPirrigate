import socket, struct
import time

class GPIOClass:
	#DEFINITIONS ON HOW TO SET THE GPIO OUT IN ORDER TO IRRIGATE
	#STATE_ON MEANS IRRIGATING, AND IS HOW GPIO NEEDS TO BE SET UP TO IRRIGATE
	#MOST TIME ON=HIGH AND RELAY=CLOSE -> IRRIGATING
	#SOMETIMES IN ORDER TO CLOSE THE RELAY YOU NEED TO SET IT UP AS LOW

	STATE_ON = "LOW"
	STATE_OFF = "HIGH"
	STATE_ERR = "ERR"


	STR_GETVAL = "GET"

	socketpath="/run/rpirrigate-gpiod-socket"

	def __init__(self):
		self.client = socket.socket( socket.AF_UNIX, socket.SOCK_STREAM )
		self.client.connect( self.socketpath )

	def openWater(self, pin):
		pin = str(pin)
		self.client.send(self.STATE_ON+"#"+pin+"#")

	def closeWater(self, pin):
		pin = str(pin)
		self.client.send(self.STATE_OFF+"#"+pin+"#")

	def getState(self,pin):
		pin = str(pin)
		self.client.send(self.STR_GETVAL+"#"+pin+"#")
		datagram = self.client.recv( 1024 )
		d=datagram.split("#")
		if len(d)<1:
			return self.STATE_ERR
		else:
			return d[0]
