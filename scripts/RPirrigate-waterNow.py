#!/usr/bin/python

import os, signal, subprocess, sys, linecache, requests
from time import sleep
from datetime import datetime

#SECONDS
WATERNOWTIME = 20

#LOG ON LOGFILE error
def logError():
    exc_type, exc_obj, tb = sys.exc_info()
    f = tb.tb_frame
    lineno = tb.tb_lineno
    filename = f.f_code.co_filename
    linecache.checkcache(filename)
    line = linecache.getline(filename, lineno, f.f_globals)

    text = "WATERNOW EXCEPTION IN (" + str(filename) + ", LINE " + str(lineno) + " '" + str(line.strip()) + "'):" + str(exc_obj);
    with open("/var/log/rpirrigate/error.log","a") as f:
        f.write(str(datetime.now())+" "+text+"\n")


def openModule(url, moduleID):
    r = requests.post(url, data={'moduleID': moduleID, 'U_ManualACT': '1', 'U_ManualVAL': '1'})

def closeModule(url, moduleID):
    r = requests.post(url, data={'moduleID': moduleID, 'U_ManualACT': '0', 'U_ManualVAL': '0'})


try:

    if(len(sys.argv)>2):
        a = sys.argv
        a.pop(0)

        url = a.pop(0)

        print(a)
        print(url)

        for g in a:
            print("water now " + str(g))
            openModule(url, g)
            sleep(WATERNOWTIME)
            closeModule(url,g)


except Exception:
    logError()
