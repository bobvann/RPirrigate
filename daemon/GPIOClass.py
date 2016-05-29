import socket, struct

class GPIOClass:
	#DEFINITIONS ON HOW TO SET THE GPIO OUT IN ORDER TO IRRIGATE
	#STATE_ON MEANS IRRIGATING, AND IS HOW GPIO NEEDS TO BE SET UP TO IRRIGATE
	#MOST TIME 1=HIGH AND RELAY=CLOSE -> IRRIGATING
	#SOMETIMES IN ORDER TO CLOSE THE RELAY YOU NEED TO SET IT UP AS 0

	STATE_ON = "LOW"
	STATE_OFF = "HIGH"

	socketpath="/run/rpirrigate-gpiod-socket"

	def __init__(self):
		self.client = socket.socket( socket.AF_UNIX, socket.SOCK_DGRAM )
		self.client.connect( self.socketpath )

	def openWater(self, pin):
		pin = str(pin)
		self.client.send(self.STATE_ON+"#"+pin)

	def closeWater(self, pin):
		pin = str(pin)
		self.client.send(self.STATE_OFF+"#"+pin)

