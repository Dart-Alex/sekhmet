#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys
import re



import irc.bot
import irc.strings
from irc.client import ip_numstr_to_quad, ip_quad_to_numstr

import random
import os
import time
import traceback
import thread
from threading import Timer
from threading import Thread, RLock
import operator
import codecs
import urllib2
import requests
import datetime
from BeautifulSoup import BeautifulSoup
timerslurk = 0
sendLock = RLock()

def nm_to_n(s):
    """Get the nick part of a nickmask.

    (The source of an Event is a nickmask.)
    """
    return s.split("!")[0]

def nm_to_uh(s):
    """Get the userhost part of a nickmask.

    (The source of an Event is a nickmask.)
    """
    return s.split("!")[1]

def nm_to_h(s):
    """Get the host part of a nickmask.

    (The source of an Event is a nickmask.)
    """
    return s.split("@")[1]

def nm_to_u(s):
    """Get the user part of a nickmask.

    (The source of an Event is a nickmask.)
    """
    s = s.split("!")[1]
    return s.split("@")[0]

def get_time():
	"""
	Return time as a nice yummy string
	"""
	return time.strftime("%H:%M:%S", time.localtime(time.time()))

def noHL(nick):
	newNick = ''
	for letter in nick:
		newNick += letter + '\xE2\x80\x8B'
	return newNick

class FetchYoutube(Thread):
	def __init__(self, bot, target, source, yid):
		Thread.__init__(self)
		self.bot = bot
		self.target = target
		self.source = source
		self.yid = yid

	def run(self):
		request = requests.post(self.bot.baseAddress + 'bot/yt/fetch/' + self.target, data={'yid':self.yid,'name':self.source})
		video = request.json()
		if video['error']:
			message = '[yt] Erreur : '+video['message']
		else:
			if video['new']:
				message = '[yt] '
			else:
				message = '[yt(n°' + video['index'] + ')] le ' + video['date'] + ' par ' + noHL(video['name']) + ' - '
			message += video['title'] + ' [' + video['duration']+']'
		self.bot.msg(self.target, message)



class ModIRC(irc.bot.SingleServerIRCBot):
	"""
	Module to interface IRC input and output with the PyBorg learn
	and reply modules.
	"""
	# The bot recieves a standard message on join. The standard part
	# message is only used if the user doesn't have a part message.
	join_msg = "%s"# is here"
	part_msg = "%s"# has left"

	# Command list for this module
	commandlist = "Commandes utilisateur : !yt, !ytcount"
	commandlistowner = "Commandes admin : !nick, !join, !part, !chans, !start, !stop, !timer, !quit, !owners, !hostwl, !nickwl, !quietchan, !ytchan, !ytdel"
	# Detailed command description dictionary
	commanddict = {
		"join": "Rejoint un ou plusieurs canaux. Syntaxe : !join <#chan1> (<#chan2> <#chan3> ...)",
		"part": "Part d'un ou plusieurs canaux. Syntaxe : !part <#chan1> (<#chan2> <#chan3> ...)",
		"chans": "Affiche la liste des canaux ou je suis présent",
		"start": "Démarre le spam",
		"stop": "Arrête le spam",
		"timer": "Affiche ou change la durée du timer (en secondes). Syntaxe : !timer <temps>",
		"owners": "Affiche ou modifie la liste des propriétaires. Syntaxe : !owners (add/remove) <username> (<username2> <username3> ...) ",
		"hostwl": "Affiche ou modifie la whitelist d'hosts. Syntaxe : !hostwl (add/remove) <host> (<host2> <host3> ...)",
		"nickwl": "Affiche ou modifie la whitelist de pseudos. Syntaxe : !nickwl (add/remove) <username> (<username2> <username3> ...)",
		"quietchan": "Affiche ou modifie la liste de canaux ou slurk sera quiet et non ban. Syntaxe : !quietchan (add/remove) <chan> (<chan2> <chan3> ...)",
		"ytchan": "Affiche ou modifie la liste des canaux ou la fonction youtube est activée. Syntaxe : !ytchan (add/remove) <chan> (<chan2> <chan3> ...)",
		"yt": "Affiche une vidéo youtube (aléatoire si aucun argument). Syntaxe : !yt (pseudo/numéro de vidéo/recherche)",
		"ytcount": "Affiche le nombre de vidéos partagées sur le canal, ou par un utilisateur. Syntaxe : !ytcount (<pseudo> (<pseudo2> <pseudo3> ...))",
		"ytdel": "Supprime une vidéo de la liste. Syntaxe : !ytdel <numéro de la vidéo>"
	}

	def __init__(self, args):
		"""
		Args will be sys.argv (command prompt arguments)
		"""
		if len(sys.argv) != 2:
			print('Usage: sekhmet <address>')
			sys.exit(1)
		self.baseAddress = args[1]
		# load settings
		request = requests.get(self.baseAddress + 'bot/config')
		try:
			self.config = request.json()
		except:
			print('No config returned')
			sys.exit(1)

		self.lastMsg = {}
		self.whois = {}
		self.bans = {}
		self.lastWhois = {}
		for chan in self.config["chans"].keys():
			self.lastMsg[chan] = []

	def our_start(self):
		try:
			server = [(self.config["server"]["address"],self.config["server"]["port"],self.config["server"]["password"])]
		except:
			server = [(self.config["server"]["address"],self.config["server"]["port"])]
		irc.bot.SingleServerIRCBot.__init__(self,server , self.config["myname"], self.config["realname"], 2)
		self.start()

	def msg(self, target, message):
		c = self.connection
		with sendLock:
			c.privmsg(target, message)

	def notice(self, target, message):
		c = self.connection
		with sendLock:
			c.notice(target, message)

	def on_welcome(self, c, e):
		for i in self.config["chans"].keys():
			c.join('#'+i)

	def on_nick(self, c, e):
		pass

	def on_join(self,c,e):
		pass

	def shutdown(self):
		try:
			self.die() # disconnect from server
		except AttributeError, e:
			# already disconnected probably (pingout or whatever)
			pass

	def get_version(self):
		return self.baseAddress

	def on_kick(self, c, e):
		"""
		Process leaving
		"""
		# Parse Nickname!username@host.mask.net to Nickname
		kicked = e.arguments()[0]
		kicker = nm_to_n(e.source())
		target = e.target() #channel
		if len(e.arguments()) >= 2:
			reason = e.arguments()[1]
		else:
			reason = ""
		if kicked == self.config["myname"]:
			print "[%s] <-- %s was kicked off %s by %s (%s)" % (get_time(), kicked, target, kicker, reason)
			try:
				c.join(target)
			finally:
				pass
	def on_privnotice(self,c,e):
		self.on_msg(c,e)
	def on_pubnotice(self,c,e):
		self.on_msg(c,e)
	def on_privmsg(self, c, e):
		self.on_msg(c, e)

	def on_pubmsg(self, c, e):
		self.on_msg(c, e)

	def on_ctcp(self, c, e):
		ctcptype = e.arguments()[0]
		if ctcptype == "ACTION":
			self.on_msg(c, e)
		else:
			irc.bot.SingleServerIRCBot.on_ctcp(self, c, e)

	def _on_disconnect(self, c, e):
		print "Disconnected"
		self.connection.execute_delayed(self.reconnection_interval, self._connected_checker)
	def on_whoischannels(self, c, e):
		pass

	def on_msg(self, c, e):
		"""
		Process messages.
		"""
		# Parse Nickname!username@host.mask.net to Nickname
		source = nm_to_n(e.source())
		target = e.target().replace('#', '').lower()

		# Message text
		if len(e.arguments()) == 1:
			# Normal message
			body = e.arguments()[0]
		else:
			# A CTCP thing
			if e.arguments()[0] == "ACTION":
				body = "+"+e.arguments()[1]
			else:
				# Ignore all the other CTCPs
				return
		# Ignore self.
		if source == self.config["myname"]: return

		if e.eventtype() == "pubmsg" or e.eventtype() == "privmsg":

			if (body.find("youtu") != -1) and (self.config['chans'][target]['youtube']['active']):
				isVideo = False
				if body.find("youtube.com") != -1:
					try:
						ylen = body.find("v=") + 2
						yid = body[ylen:ylen+11]
						isVideo = True
					except:
						pass
				elif body.find("youtu.be") != -1:
					try:
						ylen = body.find("youtu.be") + 9
						yid = body[ylen:ylen+11]
						isVideo = True
					except:
						pass
				if isVideo:
					FetchYoutube(self, target, source, yid).start()

			if body[0] == "!":
				if self.irc_commands(body, source, target, c, e) == 1: return

		if body == "": return

	def irc_commands(self, body, source, target, c, e):
		"""
		Special IRC commands.
		"""
		msg = ""

		command_list = body.split()
		command_list[0] = command_list[0].lower()
		### Owner commands
		if (source.lower() in self.config['owners']):

			# Change nick
			if command_list[0] == "!nick":
				try:
					self.connection.nick(command_list[1])
					self.config["myname"] = command_list[1]
				except:
					pass


			elif command_list[0] == "!spam":
				if len(command_list) == 1:
					self.output(self.message, ("<none>", source, target, c, e))
				else:
					message = ""
					for x in range(1,len(command_list)):
						message += command_list[x]+" "
					message = message[:-1]
					msg = 'Le nouveau message de spam est : "'+message+'"'
					self.message = message
			elif command_list[0] == "!nickwl":
				for nick in self.nickWhitelist:
					msg += nick + " "
				msg += "in the nick whitelist."
				try:
					if command_list[1].lower() == "add":
						msg = ""
						for x in range(2,len(command_list)):
							if not(command_list[x].lower() in self.config["nickWhitelist"]):
								self.nickWhitelist.append(command_list[x].lower())
								self.config["nickWhitelist"].append(command_list[x].lower())
								msg += command_list[x]+" "
						msg += "added to nick whitelist."
					elif command_list[1].lower() == "remove":
						msg = ""
						for x in range(2,len(command_list)):
							if command_list[x].lower() in self.config["nickWhitelist"]:
								self.nickWhitelist.remove(command_list[x].lower())
								self.config["nickWhitelist"].remove(command_list[x].lower())
								msg += command_list[x]+" "
						msg += "removed from nick whitelist"
				except:
					pass

			elif command_list[0] == "!ytchan":
				for chan in self.config["ytChans"]:
					msg += chan + " "
				msg += "in the youtube chan list."
				try:
					if command_list[1].lower() == "add":
						msg = ""
						for x in range(2,len(command_list)):
							if not(command_list[x].lower() in self.config["ytChans"]):
								self.config["ytChans"].append(command_list[x].lower())
								msg += command_list[x]+" "
								chan = command_list[x].lower()
								self.activateYt(chan)
						msg += "added to youtube chans list."
					elif command_list[1].lower() == "remove":
						msg = ""
						try:
							self.timerYt[target.lower()].cancel()
							del self.timerYT[target.lower()]
						except:
							pass
						for x in range(2,len(command_list)):
							if command_list[x].lower() in self.config["ytChans"]:
								self.config["ytChans"].remove(command_list[x].lower())
								msg += command_list[x]+" "
						msg += "removed from youtube chans list."
				except:
					pass

			elif command_list[0] == "!userbl":
				for user in self.config["usersBlacklist"]:
					msg += user + " "
				msg += "in the user blacklist."
				try:
					if command_list[1].lower() == "add":
						msg = ""
						for x in range(2,len(command_list)):
							if not(command_list[x].lower() in self.config["usersBlacklist"]):
								self.config["usersBlacklist"].append(command_list[x].lower())
								msg += command_list[x].lower()+" "
						msg += "added to user blacklist."
					elif command_list[1].lower() == "remove":
						msg = ""
						for x in range(2,len(command_list)):
							if command_list[x].lower() in self.config["usersBlacklist"]:
								self.config["usersBlacklist"].remove(command_list[x].lower())
								msg += command_list[x]+" "
						msg += "removed from user blacklist."
				except:
					pass

			elif command_list[0] == "!quietchan":
				for chan in self.config["quietChans"]:
					msg += chan + " "
				msg += "in the quiet chan list."
				try:
					if command_list[1].lower() == "add":
						msg = ""
						for x in range(2,len(command_list)):
							if not(command_list[x].lower() in self.config["quietChans"]):
								self.config["quietChans"].append(command_list[x].lower())
								msg += command_list[x].lower()+" "
						msg += "added to quiet chan list."
					elif command_list[1].lower() == "remove":
						msg = ""
						for x in range(2,len(command_list)):
							if command_list[x].lower() in self.config["quietChans"]:
								self.config["quietChans"].remove(command_list[x].lower())
								msg += command_list[x].lower()+" "
						msg += "removed from quiet chan list"
				except:
					pass

			elif command_list[0] == "!hostwl":
				for host in self.hostWhitelist:
					msg += host + " "
				msg += "in the host whitelist."
				try:
					if command_list[1].lower() == "add":
						msg = ""
						for x in range(2,len(command_list)):
							if not(command_list[x] in self.config["hostWhitelist"]):
								self.hostWhitelist.append(command_list[x])
								self.config["hostWhitelist"].append(command_list[x])
								msg += command_list[x]+" "
						msg += "added to host whitelist."
					elif command_list[1].lower() == "remove":
						msg = ""
						for x in range(2,len(command_list)):
							if command_list[x] in self.config["hostWhitelist"]:
								self.hostWhitelist.remove(command_list[x])
								self.config["hostWhitelist"].remove(command_list[x])
								msg += command_list[x]+" "
						msg += "removed from host whitelist"
				except:
					pass

			elif command_list[0] == "!owners":
				for owner in self.owners:
					msg += owner + " "
				msg += "in the owner list."
				try:
					if command_list[1].lower() == "add":
						msg = ""
						for x in range(2,len(command_list)):
							if not(command_list[x] in self.config["owners"]):
								self.owners.append(command_list[x])
								self.config["owners"].append(command_list[x])
								msg += command_list[x]+" "
						msg += "added to owners list."
					elif command_list[1].lower() == "remove":
						msg = ""
						for x in range(2,len(command_list)):
							if command_list[x] in self.config["owners"]:
								self.owners.remove(command_list[x])
								self.config["owners"].remove(command_list[x])
								msg += command_list[x]+" "
						msg += "removed from owners list"
				except:
					pass
			elif command_list[0] == "!badwords":
				for badword in self.config["badwords"]:
					msg += badword + " "
				msg += "in the badwords list."
				try:
					if command_list[1].lower() == "add":
						msg = ""
						for x in range(2,len(command_list)):
							if not(command_list[x].lower() in self.config["badwords"]):
								self.config["badwords"].append(command_list[x].lower())
								msg += command_list[x]+" "
						msg += "added to badwords list."
					elif command_list[1].lower() == "remove":
						msg = ""
						for x in range(2,len(command_list)):
							if command_list[x].lower() in self.config["badwords"]:
								self.config["badwords"].remove(command_list[x].lower())
								msg += command_list[x]+" "
						msg += "removed from badwords list"
				except:
					pass




			# Join a channel or list of channels
			elif command_list[0] == "!join":
				for x in range(1, len(command_list)):
					msg = "J'essaye de rejoindre "+command_list[x].lower()
					if not(command_list[x].lower() in self.chans):
						c.join(command_list[x].lower())
						self.chans.append(command_list[x].lower())
						self.config["chans"].append(command_list[x].lower())
			elif command_list[0] == "!slurk":
				try:
					self.kickbanall(command_list[1].lower(), "Tu es banni du serveur slurk. Comportement à revoir.", c, False)
					msg = command_list[1] + " banni."
				except:
					msg = "Je n'arrive pas à trouver "+ command_list[1]
			# Part a channel or list of channels
			elif command_list[0] == "!part":
				for x in range(1, len(command_list)):
					msg = "Je pars de "+command_list[x]
					c.part(command_list[x])
					if command_list[x].lower() in self.chans:
						self.chans.remove(command_list[x].lower())
						self.config["chans"].remove(command_list[x].lower())
			# List channels currently on
			elif command_list[0] == "!chans":
				if len(self.chans)==0:
					msg = "Je ne suis sur aucun canal."
				else:
					msg = "Je suis sur "
					channels = self.chans
					for x in range(0, len(channels)):
						msg = msg+channels[x]+" "
			# start game
			elif command_list[0] == "!start":
				chan = target.lower()
				try:
					self.startedSpam[chan]
				except:
					self.startedSpam[chan] = False
				if not self.startedSpam[chan]:
					try:
						self.config["spamChans"].append(chan)
					except:
						pass
					self.output(self.message, ("<none>", source, target, c, e))
					self.startedSpam[chan] = True
					self.spamTimer[chan] = Timer(self.config["timer"], self.spam, (body, source, target, c, e))
					self.spamTimer[chan].start()
			# stop game
			elif command_list[0] == "!stop":
				try:
					if self.startedSpam[target.lower()]:
						self.output(source +" a arrêté le spam !", ("<none>", source, target, c, e))
						self.startedSpam[target.lower()] = False
						self.spamTimer[target.lower()].cancel()
						try:
							self.config["spamChans"].remove(target.lower())
						except:
							pass
				except:
					self.output("Mais je suis pas en train de spammer !", ("<none>", source, target, c, e))

			# Change timer
			elif command_list[0] == "!timer":
				try:
					self.config["timer"] = int(command_list[1])
					msg = "Le timer est maintenant de "+command_list[1]+" secondes."
				except:
					msg = "Le timer est de "+str(self.config["timer"])+" secondes."
			elif command_list[0] == "!quit":
				self.saveConfig()
				self.saveData()
				sys.exit()
			elif command_list[0] == "!ytdel":
				chan = target.lower()
				try:
					index = int(command_list[1]) - 1
					yid = self.ytSorted[chan][index]
					nick = self.youtube[chan][yid]["nick"].lower()
					self.ytSorted[chan].remove(yid)
					self.ytUsers[chan][nick].remove(yid)
					del self.youtube[chan][yid]
					self.saveYoutube()
					msg = "Vidéo n°"+command_list[1]+" supprimée (https://youtu.be/"+yid+" "+self.afficheyt(yid,False)+" par "+nick
				except:
					try:
						msg = "Vidéo n°"+command_list[1]+" non trouvée."
					except:
						msg = "Pas d'arguments."

		if command_list[0] == "!aide":
			if len(command_list) > 1:
				# Help for a specific command
				cmd = command_list[1].lower()
				dic = None
				if cmd in self.commanddict.keys():
					dic = self.commanddict
				if dic:
					for i in dic[cmd].split("\n"):
						if e.eventtype() == "pubmsg":
							c.notice(source, i)
						else:
							self.output(i, ("<none>", source, target, c, e))
				else:
					if e.eventtype() == "pubmsg":
						c.notice(source,"Pas d'aide pour la commande '%s'" % cmd)
					else:
						self.output("Pas d'aide pour la commande '%s'" % cmd, ("<none>", source, target, c, e))
			else:
				for i in self.commandlist.split("\n"):
					if e.eventtype() == "pubmsg":
						c.notice(source, i)
					else:
						self.output(i, ("<none>", source, target, c, e))
				for i in self.commandlistowner.split("\n"):
					if e.eventtype() == "pubmsg":
						c.notice(source, i)
					else:
						self.output(i, ("<none>", source, target, c, e))
		elif command_list[0] == "!err":
			chan = target.lower()
			if chan in self.config["ytChans"]:
				try:
					toReplace = body.replace('!err ','').split('/')[0]
					replaced = body.replace('!err','').split('/')[1]
					found = False
					for message in self.lastMsg[chan]:
						nick = message.split(' ')[0]
						message = message.replace(nick+' ','')
						if message.find(toReplace) != -1 and not found:
							retour = nick + ' voulait dire "' + message.replace(toReplace,replaced) + '"'
							found = True
					for badword in self.config["badwords"]:
						if retour.lower().find(badword) != -1:
							found = False
					if found:
						self.output(retour, ("<none>", source, target, c, e))
				except:
					pass

		elif command_list[0] == "!yt":
			chan = target.lower()
			if chan in self.config["ytChans"]:
				archive = False
				error = False
				if len(command_list) == 1:
					yid = self.ytSorted[chan][random.randint(0, len(self.ytSorted[chan])-1)]
					archive = True
				elif len(command_list) == 2:
					try:
						if ((int(command_list[1])) <= len(self.ytSorted[chan])):
							yid = self.ytSorted[chan][int(command_list[1])-1]
							archive = True
						else:
							error = True
							retour = '\x02'+"[yt]"+'\x02'+" La vidéo n°"+command_list[1]+" n'existe pas."
					except:
						try:
							yid = self.ytUsers[chan][command_list[1].lower()][random.randint(0, len(self.ytUsers[chan][command_list[1].lower()])-1)]
							archive = True
						except:
							query = command_list[1]
				else:
					query = ""
					for x in range(1, len(command_list)):
						query += command_list[x]+" "
					query = query[:-1]


				if archive and not error:
					nick = ""
					for letter in self.youtube[chan][yid]["nick"]:
						nick += letter + '\xE2\x80\x8B'
					ts = int(self.youtube[chan][yid]["timestamp"])
					date = time.strftime('%d/%m/%Y',time.localtime(ts))
					index = str(self.ytSorted[chan].index(yid) + 1)
					titre = self.afficheyt(yid,True)
					retour = '\x02'+"[yt(n°"+index+")]"+'\x02'+" le "+date+" par "+ '\x1D' + nick + '\x1D' +" - "+'\x1F'+"https://youtu.be/"+yid+'\x1F'+" - "+titre
					if titre == "Erreur":
						del self.youtube[chan][yid]
						self.saveYoutube()
				elif not error:
					yid = self.searchyt(query)
					try:
						self.youtube[chan]
					except:
						self.youtube[chan] = {}
					if not(yid in self.youtube[chan]):
						self.youtube[chan][yid] = {}
						self.youtube[chan][yid]["nick"] = source
						self.youtube[chan][yid]["timestamp"] = time.time()
						self.saveYoutube()
						try:
							self.ytUsers[chan][source.lower()].append(yid)
						except:
							self.ytUsers[chan][source.lower()] = [yid]
						try:
							self.ytSorted[chan].append(yid)
						except:
							self.ytSorted[chan] = [yid]
						retour = '\x02'+"[ytSearch]"+'\x02'+" "+'\x1F'+"https://youtu.be/"+yid+'\x1F'+" - "+self.afficheyt(yid,True)
					else:
						nick = ""
						for letter in self.youtube[chan][yid]["nick"]:
							nick += letter + '\xE2\x80\x8B'
						ts = int(self.youtube[chan][yid]["timestamp"])
						date = time.strftime('%d/%m/%Y',time.localtime(ts))
						index = str(self.ytSorted[chan].index(yid) + 1)
						titre = self.afficheyt(yid,True)
						retour = '\x02'+"[ytSearch(n°"+index+")]"+'\x02'+" le "+date+" par "+ '\x1D' + nick + '\x1D' +" - "+'\x1F'+"https://youtu.be/"+yid+'\x1F'+" - "+titre

				self.output(retour, ("<none>", source, target, c, e))
		elif command_list[0] == "!ytcount":
			chan = target.lower()
			if chan in self.config["ytChans"]:
				if len(command_list) == 1:
					try:
						ts = int(self.youtube[chan][self.ytSorted[chan][0]]["timestamp"])
						date = time.strftime('%d/%m/%Y',time.localtime(ts))
						retour = '\x02'+"[ytCount]"+'\x02'+ " "+str(len(self.ytSorted[chan])) + " vidéos Youtube ont été partagées sur "+chan+" depuis le "+date+"."
					except:
						retour = '\x02'+"[ytCount]"+'\x02'+ " Aucune vidéo Youtube n'a été partagée sur "+chan+"."
					self.output(retour, ("<none>", source, target, c, e))
				else:
					for nick in command_list[1:]:
						nickAff = ''
						for letter in nick:
							nickAff += letter + '\xE2\x80\x8B'
						try:
							ts = int(self.youtube[chan][self.ytUsers[chan][nick.lower()][0]]["timestamp"])
							date = time.strftime('%d/%m/%Y',time.localtime(ts))
							retour = '\x02'+"[ytCount]"+'\x02'+ " "+nickAff+" a partagé "+str(len(self.ytUsers[chan][nick.lower()])) + " vidéos Youtube depuis le "+date+" sur "+chan+"."
						except:
							retour = '\x02'+"[ytCount]"+'\x02'+ " "+nickAff+" n'a partagé aucune vidéo sur "+chan+"."
						self.output(retour, ("<none>", source, target, c, e))








		self.saveConfig()
		if msg == "":
			return 0
		else:
			#self.output(msg, ("<none>", source, target, c, e))
			c.notice(source,msg)
			return 1
	def spam(self, body, source, target, c, e):
		msg = self.message
		self.spamTimer[target.lower()] = Timer(self.config["timer"], self.spam, (body, source, target, c, e))
		self.spamTimer[target.lower()].start()
		self.output(msg, ("<none>", source, target, c, e))
		return
	def spamYt(self, body, source, target, c, e):
		chan = target.lower()
		yid = self.ytSorted[chan][random.randint(0, len(self.ytSorted[chan])-1)]
		nick = ""
		for letter in self.youtube[chan][yid]["nick"]:
			nick += letter + '\xE2\x80\x8B'
		ts = int(self.youtube[chan][yid]["timestamp"])
		date = time.strftime('%d/%m/%Y',time.localtime(ts))
		index = str(self.ytSorted[chan].index(yid) + 1)
		titre = self.afficheyt(yid, True)
		msg = '\x02'+"[ytAuto(n°"+index+")]"+'\x02'+" le "+date+" par "+'\x1D'+nick+'\x1D'+" - "+'\x1F'+"https://youtu.be/"+yid+'\x1F'+" - "+titre
		if titre == "Erreur":
			del self.youtube[chan][yid]
			self.saveYoutube()
		try:
			del self.timerYT[target.lower()]
		except:
			pass
		self.timerYt[target.lower()] = Timer(1200, self.spamYt, (body, source, target, c, e))
		self.timerYt[target.lower()].start()
		self.output(msg, ("<none>", source, target, c, e))
		return
	def kickbanall(self, nick, reason, c, force):
		source = self.whois[nick.lower()]
		host = nm_to_h(self.whois[nick.lower()])
		for chan in self.chans:
			try:
				self.bans[chan]
			except:
				self.bans[chan] = {}
			if not(source in self.bans[chan]):
				self.bans[chan][source] = Timer(864000, self.removeban, (chan,source))
				self.bans[chan][source].start()
				if (chan in self.config["quietChans"]) and not force:
					c.send_raw('MODE '+chan+' +b ~t:1440:~q:*!*@'+host)
					c.send_raw('PRIVMSG '+chan+' '+nick+' : '+reason)
				else:
					c.send_raw('MODE '+chan+' +b ~t:1440:*!*@'+host)
					c.send_raw('KICK '+chan+' '+nick+' '+reason)
		return
	def removeban(self,target,source):
		del self.bans[target][source]
		return

	def saveConfig(self):
		with codecs.open("settings.json","w", encoding="utf-8") as file:
			file.truncate()
			json.dump(self.config, file, ensure_ascii=False)
			file.close()
	def saveData(self):
		with codecs.open("data.json","w", encoding="utf-8") as file:
			file.truncate()
			json.dump(self.data, file)
			file.close()
	def saveYoutube(self):
		with codecs.open("youtube.json","w", encoding="utf-8", errors="replace") as file:
			file.truncate()
			json.dump(self.youtube, file)
			file.close()
	def afficheyt(self, yid, bold):
		try:
			video = urllib.urlopen('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id='+yid+'&key='+self.config["ytAPI"]).read()
			jsonyt = json_loads_byteified(video)
			titre = jsonyt['items'][0]['snippet']['title']
			temps = jsonyt['items'][0]['contentDetails']['duration'][2:].lower()
			if bold:
				b = '\x02'
			else:
				b = ''
			return b + titre + b + " [" + temps + "]"
		except:
			return "Erreur"
	def searchyt(self,query):
		try:
			q = urllib.quote(query, safe='')
			video = urllib.urlopen('https://www.googleapis.com/youtube/v3/search?part=id&type=video&key='+self.config["ytAPI"]+'&q='+q).read()
			jsonyt = json_loads_byteified(video)
			yid = jsonyt['items'][0]["id"]["videoId"]
			return yid
		except:
			return "Error"
	def activateYt(self,chan):
		self.ytUsers[chan] = {}
		self.ytSorted[chan] = []
		temp = {}
		toDelete = []

		modified = False
		try:
			self.youtube[chan]
		except:
			self.youtube[chan] = {}
			modified = True
		deleted = False
		for yid in self.youtube[chan]:
			if yid == "Erreur" or yid == "" or self.youtube[chan][yid]["nick"] == "":
				toDelete.append(yid)
			else:
				nick = self.youtube[chan][yid]["nick"].lower()
				botNicks = ["sekhmet","badrobot","melody","hal`9000","bastet","bowser"]
				if nick in botNicks:
					toDelete.append(yid)
					deleted = True
				else:
					timestamp = int(self.youtube[chan][yid]["timestamp"])
					temp[yid] = timestamp

		if deleted:
			for yid in toDelete:
				del self.youtube[chan][yid]
			modified = True
		if modified:
			self.saveYoutube()
		self.ytSorted[chan] = sorted(temp, key=temp.__getitem__)
		for yid in self.ytSorted[chan]:
			nick = self.youtube[chan][yid]["nick"].lower()
			try:
				self.ytUsers[chan][nick].append(yid)
			except:
				self.ytUsers[chan][nick] = [yid]
	def urlTitle(self,url):
		try:
			soup = BeautifulSoup(urllib2.urlopen(url),convertEntities=BeautifulSoup.HTML_ENTITIES)
			if str(soup.title.string) == "":
				retour = "Erreur"
			else:
				retour = '\x02'+"[url]"+'\x02'+" "+str(soup.title.string).replace('\n','').replace('  ','')
		except:
			retour = "Erreur"
		return retour

if __name__ == "__main__":

	# start the bot
	bot = ModIRC(sys.argv)
	try:
		bot.our_start()
	except:
		traceback.print_exc()
	bot.disconnect(bot.config["quitmsg"])
