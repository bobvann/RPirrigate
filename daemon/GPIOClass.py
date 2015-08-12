import socket, struct

class GPIOClass:
	#DEFINITIONS ON HOW TO SET THE GPIO OUT IN ORDER TO IRRIGATE
	#STATE_ON MEANS IRRIGATING, AND IS HOW GPIO NEEDS TO BE SET UP TO IRRIGATE
	#MOST TIME 1=HIGH AND RELAY=CLOSE -> IRRIGATING
	#SOMETIMES IN ORDER TO CLOSE THE RELAY YOU NEED TO SET IT UP AS 0

	STATE_ON = 0
	STATE_OFF = 1

	def __init__(self):

		self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		self.sock.connect(('127.0.0.1', 8888))

	def openWater(self, pin):
		pin = int(pin)
		self.sock.send(struct.pack('IIII', 4, pin, self.STATE_ON, 0))

	def closeWater(self, pin):
		pin = int(pin)
		self.sock.send(struct.pack('IIII', 4, pin, self.STATE_OFF, 0))
