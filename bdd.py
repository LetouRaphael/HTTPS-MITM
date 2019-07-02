#!/usr/bin/env python2
# -*- coding: utf-8 -*-

import psycopg2
dbHost="localhost"
dbName="projet_letournr"
dbUser="postgres"
dbPass="postgres"



def nbAttaquePrec():
	sql="SELECT count(*) from ATTAQUES"
	conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
	cur = conn.cursor()	
	cur.execute(sql)
	row = cur.fetchone()
	row=str(row).split('L')[0][1:]
	cur.close()
	conn.close()
	return int(row)


def newAttaque(attack):
	sql="INSERT INTO ATTAQUES(idAttaque) values("+str(attack)+");"
	conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
	cur = conn.cursor()	
	cur.execute(sql)
	conn.commit()	
	cur.close()
	conn.close()

def compteExist(user,passwd,site):
	sql="SELECT idcompte from comptes INNER JOIN sites on idSite=refSite where nom='"+site+"' and username='"+user+"' and pass='"+passwd+"' ;"
	conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
	cur = conn.cursor()	
	cur.execute(sql)
	row = cur.fetchone()
	if row == None:
		exist = False
	else:
		exist = (str(row).split(",")[0][1:])
	cur.close()
	conn.close()
	return exist


def newAccount(user,passwd,site,attack):
	sql="INSERT INTO COMPTES (username,pass,refSite,refAttaque) VALUES ('"+user+"','"+passwd+"',"+str(site)+","+str(attack)+") RETURNING idcompte;"
	conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
	cur = conn.cursor()	
	cur.execute(sql)
	idcompte=str(cur.fetchone()).split(",")[0][1:]
	conn.commit()	
	cur.close()
	conn.close()
	return idcompte

def newSite(site):
	sql="INSERT INTO SITES (nom) VALUES ('"+site+"') RETURNING idSite;"
	conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
	cur = conn.cursor()	
	cur.execute(sql)
	idS = cur.fetchone()[0]
	conn.commit()	
	cur.close()
	conn.close()
	return idS


def siteExist(site):
	sql="SELECT idSite from sites where nom='"+site+"';"
	conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
	cur = conn.cursor()	
	cur.execute(sql)
	row = cur.fetchone()
	if row == None:
		exist = False
	else:
		exist = (str(row).split(",")[0][1:])
	cur.close()
	conn.close()
	return exist

def siteBlack(site):
	sql="SELECT blacked from sites where nom='"+site+"';"
	conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
	cur = conn.cursor()	
	cur.execute(sql)
	row = cur.fetchone()
	bloquer = str(row)[1]
	if(bloquer == 'T'):
		bloquer = True
	else:
		bloquer = False
	cur.close()
	conn.close()
	return bloquer

def majCookie(idcompte,cookie):
	if cookie != "":
		sql="UPDATE comptes SET cookie = '"+cookie+"' WHERE idcompte="+idcompte+";"
		conn=psycopg2.connect(host=dbHost,database=dbName, user=dbUser, password=dbPass)
		cur = conn.cursor()	
		cur.execute(sql)
		conn.commit()	
		cur.close()
		conn.close()
		print("Cookie : "+cookie)
	else:
		print("Pas de cookie")
	return 

def mainDB(user,passwd,site,attack,cookie):
	siteEx = siteExist(site)
	if not siteEx:
		idcompte=newAccount(user,passwd,newSite(site),attack)
		print("Site : "+site)
		print("utilisateur : "+user)
		print("mot de passe : "+passw)
		print("Ajouter à la base de données")
		majCookie(idcompte,cookie)


	else:
		if not siteBlack(site):
			compteEx = compteExist(user,passwd,site)
			if not compteEx:
				idcompte=newAccount(user,passwd,siteEx,attack)	
				print("Site : "+site)
				print("utilisateur : "+user)
				print("mot de passe : "+passwd)
				print("Ajouter à la base de données")
			else:
				print("Ce compte est déjà connu !")	
				idcompte=compteEx
				print(idcompte)
			majCookie(idcompte,cookie)

		else:
			print("Le site "+site+" est blacklister, compte non ajouté à la base de données !")
