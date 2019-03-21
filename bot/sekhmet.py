#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# Lookup antimixedutf8 if bot banned "Possible mixed character spam"
import sys
import re

import irc.bot
import irc.strings
from irc.client import ip_numstr_to_quad, ip_quad_to_numstr
from jaraco.stream import buffer
irc.client.ServerConnection.buffer_class = buffer.LenientDecodingLineBuffer
import random
import os
import time
import traceback
from multiprocessing import Process
from threading import Thread, RLock, Timer
import requests
from bs4 import BeautifulSoup
import datetime


def noHL(nick):
	newNick = ''
	for letter in nick:
		newNick += letter + u"\u200B"
	return newNick

def bold(string):
	return '\x02'+string+'\x02'

def underline(string):
	return '\x1F'+string+'\x1F'

def itallic(string):
	return '\x1D'+string+'\x1D'



class ModIRC(irc.bot.SingleServerIRCBot):

	# Detailed command description dictionary
	commandDictUser = {
		"yt": "Affiche une vidéo youtube (aléatoire si aucun argument). Syntaxe : !yt (pseudo/numéro de vidéo/recherche)",
		"ytcount": "Affiche le nombre de vidéos partagées sur le canal, ou par un utilisateur. Syntaxe : !ytcount (<pseudo> (<pseudo2> <pseudo3> ...))",
		"err": "Corrige ce qu'un utilisateur a dit. Syntaxe : !err <texte à remplacer>/<texte de remplacement>"
	}
	commandDictAdmin = {
		"admin": "Affiche ou modifie la liste des admins du bot sur le salon. Syntaxe : !admin (#channel si en privé) (add/remove) <pseudo1> (<pseudo2> ...)",
		"youtube": "Change les paramètres du module Youtube. Syntaxe : !youtube (#channel si en privé) start/stop / !youtube (#channel si en privé) timer <timer en secondes>",
		"spam": "Change les paramètres du module Spam. Syntaxe : !spam (#channel si en privé) start/stop / !spam (#channel si en privé) timer <timer en secondes>",
		"event": "Change les paramètres du module Event. Syntaxe : !event (#channel si en privé) start/stop / !event (#channel si en privé) timer <timer en secondes>"
	}
	commandDictOwner = {
		"owner": "Affiche ou modifie la liste des owners du bot. Syntaxe : !owner (add/remove) <pseudo1> (<pseudo2> ...)"
	}

	def __init__(self, args):
		"""
		Args will be sys.argv (command prompt arguments)
		"""
		if len(args) != 2:
			print('Usage: sekhmet <address>')
			sys.exit(1)
		self.baseAddress = args[1]
		# load settings
		request = requests.get(self.baseAddress + 'bot/config')
		try:
			self.config = request.json()
		except:
			print(request.url)
			print(request.text)
			print('No config returned')
			sys.exit(1)
		self.sendLock = RLock()
		self.updateLock = RLock()
		self.printLock = RLock()
		self.lastMsg = {}
		self.whois = {}
		self.bans = {}
		self.lastWhois = {}
		self.spamYtProcess = {}
		self.spamProcess = {}
		self.spamEventProcess = {}
		for chan in self.config["chans"].keys():
			self.lastMsg[chan] = []

	def our_start(self):
		try:
			server = [(self.config["server"]["address"],int(self.config["server"]["port"]),self.config["server"]["password"])]
		except:
			server = [(self.config["server"]["address"],self.config["server"]["port"])]
		self.print('Connecting to '+self.config['server']['address']+':'+str(self.config['server']['port']))
		irc.bot.SingleServerIRCBot.__init__(self,server , self.config["myname"], self.config["realname"], 2)
		self.start()

	def msg(self, target, message):
		c = self.connection
		with self.sendLock:
			self.print('(PRIVMSG)['+target+']<' + self.config['myname'] + '> => ' + message)
			c.privmsg(target, message)

	def notice(self, target, message):
		c = self.connection
		with self.sendLock:
			c.notice(target, message)

	def print(self, message):
		if(self.config['debug']):
			with self.printLock:
				print("["+datetime.datetime.now().strftime("%Y-%m-%d %H:%M")+"] "+message)

	def on_welcome(self, c, e):
		self.print('Connected')
		for i in self.config["chans"].keys():
			self.print('Joining #'+i)
			c.join('#'+i)
		self.checkConfigProcess = self.startProcess(target=self.checkConfig, args=(c,))

	def on_nicknameinuse(self, c, e):
		with self.updateLock:
			self.config['myname'] += "_"
			c.nick(self.config['myname'])

	def on_nick(self, c, e):
		pass

	def on_join(self,c,e):
		pass

	def shutdown(self):
		try:
			self.die() # disconnect from server
		except:
			# already disconnected probably (pingout or whatever)
			pass

	def get_version(self):
		return self.baseAddress

	def on_kick(self, c, e):
		"""
		Process leaving
		"""
		# Parse Nickname!username@host.mask.net to Nickname
		kicked = e.arguments[0]
		kicker = e.source.nick
		target = e.target #channel
		if len(e.arguments) >= 2:
			reason = e.arguments[1]
		else:
			reason = ""
		if kicked == self.config["myname"]:
			self.print('Kicked from '+target+' by '+kicker+' : '+reason)
			try:
				self.print('Rejoining '+target)
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
		ctcptype = e.arguments[0]
		if ctcptype == "ACTION":
			self.on_msg(c, e)
		else:
			irc.bot.SingleServerIRCBot.on_ctcp(self, c, e)
	def on_whoischannels(self, c, e):
		pass

	def on_msg(self, c, e):
		"""
		Process messages.
		"""
		with self.updateLock:

			source = e.source.nick
			target = e.target.replace('#', '').lower()

			# Message text
			if len(e.arguments) == 1:
				# Normal message
				body = e.arguments[0]
			else:
				# A CTCP thing
				if e.arguments[0] == "ACTION":
					body = "+"+e.arguments[1]
				else:
					# Ignore all the other CTCPs
					return
			# Ignore self.
			if source == self.config["myname"]: return
			self.print('('+e.type+')'+'['+e.target+']<'+source+'> <= '+body)
			if e.type == "privmsg" and body.split(' ')[0] == 'confirm':
				self.startProcess(target=self.confirmNick, args=(source.lower(), body.split(' ')[1]))
				return
			if body[0] == "!":
				if self.irc_commands(body, source, target, c, e) == 1: pass
			if e.type == "pubmsg":
				if target in self.config['chans'].keys():
					if self.config['chans'][target]['youtube']['active']:
						if self.config['chans'][target]['youtube']['timer'] > 0:
							try:
								self.print('Reseting Youtube timer for #'+target)
								self.spamYtProcess[target].terminate()
								self.spamYtProcess[target].join()
							except:
								pass
							self.spamYtProcess[target] = self.startProcess(target=self.spamYoutube, args=(target,))
						if (body.find("youtu") != -1):
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
								self.print('')
								self.startProcess(target=self.fetchYoutube, args=(target, source, yid,))

					if body[0] != "!":
						self.lastMsg[target] = [source + " " + body] + self.lastMsg[target][0:9]


			if body == "": return

	def startProcess(self, target, args):
		process = Process(target=target, args=args)
		process.daemon = True
		process.start()
		return process

	def irc_commands(self, body, source, target, c, e):
		"""
		Special IRC commands.
		"""
		self.print("Command received : "+body)
		command_list = body.split()
		command_list[0] = command_list[0].lower()
		sourceIsAdmin = False
		"""
		User commands
		"""
		if command_list[0] == "!ping":
			self.msg('#'+target, 'pong !')
		elif command_list[0] == "!ytcount":
			if self.config['chans'][target]['youtube']['active']:
				if len(command_list) == 1:
					self.startProcess(target=self.countYoutubeVideos, args=(target,))
					pass
				else:
					for nick in command_list[1:]:
						self.startProcess(target=self.countYoutubeVideosByName, args=(target, nick,))
		elif command_list[0] == "!yt":
			if self.config['chans'][target]['youtube']['active']:
				if len(command_list) == 1:
					self.startProcess(target=self.randomYoutube, args=(target,))
				else:
					self.startProcess(target=self.searchYoutube, args=(target, source, ' '.join(command_list[1:]),))
		elif command_list[0] == "!err":
			self.startProcess(target=self.errCommand, args=(target, body, self.lastMsg[target]))
		elif command_list[0] == "!aide":
			if len(command_list) == 1:
				message = "Commandes utilisateur: "+", ".join(self.commandDictUser.keys())
			else:
				command = command_list[1].lower()
				if command in self.commandDictUser.keys():
					message = "!"+command+": "+self.commandDictUser[command]
			try:
				if e.type == "privmsg":
					self.msg(source, message)
				elif e.type == "pubmsg":
					self.notice(source, message)
				del message
			except:
				pass
		"""
		privmsg admin commands
		"""
		if e.type == "privmsg":
			if command_list[0] in ['!youtube', '!spam', '!event', '!badwords', '!admin']:
				try:
					target = command_list[1].replace('#', '').lower()
					if target not in self.config['chans'].keys():
						self.msg(source, 'Je ne connais pas le canal '+command_list[1])
						return 1
					command_list.pop(1)
					self.print("Substituted command : "+" ".join(command_list)+" target="+target)
				except:
					self.msg(source, "Pas assez d'arguments.")
			elif command_list[0] == "!aide":

				for chan in self.config['chans'].keys():
					if source.lower() in self.config['chans'][chan]['admins']:
						sourceIsAdmin = True
		"""
		Admin commands
		"""
		try:
			if (source.lower() in self.config['owners']) or sourceIsAdmin or (source.lower() in self.config['chans'][target]['admins']):
				if command_list[0] in ['!youtube', '!spam', '!event']:
					try:
						command = command_list[0].replace('!','')
						subCommand = command_list[1].lower()
						if subCommand == 'timer':
							self.startProcess(target=self.sendConfig, args=(e.type, source, command, 'timer', [int(command_list[2])], target,))
						elif subCommand == 'start':
							self.startProcess(target=self.sendConfig, args=(e.type, source, command, 'active', [True], target,))
						elif subCommand == 'stop':
							self.startProcess(target=self.sendConfig, args=(e.type, source, command, 'active', [False], target,))
						else:
							self.msg(source, "Commande "+command_list[1]+" inconnue pour "+command_list[0]+".")
					except:
						self.msg(source, "Pas assez d'arguments.")
				elif command_list[0] == '!admin':
					if len(command_list) == 1:
						self.msg(source, "Administrateurs pour #"+target+": "+", ".join(self.config['chans'][target]['admins']))
					else:
						try:
							command = 'admin'
							subCommand = command_list[1].lower()
							if subCommand in ['add', 'remove']:
								self.startProcess(target=self.sendConfig, args=(e.type, source, command, subCommand, command_list[2:], target,))
							else:
								self.msg(source, "Commande "+subCommand+" inconnue pour "+command_list[0]+".")
						except:
							self.msg(source, "Pas assez d'arguments.")
				elif command_list[0] == "!aide":
					if len(command_list) == 1:
						message = "Commandes admin: "+", ".join(self.commandDictAdmin.keys())
					else:
						command = command_list[1].lower()
						if command in self.commandDictAdmin.keys():
							message = "!"+command+": "+self.commandDictAdmin[command]
					try:
						if e.type == "privmsg":
							self.msg(source, message)
						elif e.type == "pubmsg":
							self.notice(source, message)
						del message
					except:
						pass
		except:
			self.print("Error caught on admin commands")
			pass

		"""
		Owner commands
		"""
		if source.lower() in self.config['owners']:
			if command_list[0] == '!owner':
				if len(command_list) == 1:
					self.msg(source, "Owners: "+", ".join(self.config['owners']))
				else:
					try:
						command = 'owner'
						subCommand = command_list[1].lower()
						if subCommand in ['add', 'remove']:
							self.startProcess(target=self.sendConfig, args=(e.type, source, command, subCommand, command_list[2:],))
						else:
							self.msg(source, "Commande "+subCommand+" inconnue pour "+command_list[0]+".")
					except:
						self.msg(source, "Pas assez d'arguments.")
			elif command_list[0] == "!aide":
					if len(command_list) == 1:
						message = "Commandes owner: "+", ".join(self.commandDictOwner.keys())
					else:
						command = command_list[1].lower()
						if command in self.commandDictOwner.keys():
							message = "!"+command+": "+self.commandDictOwner[command]
					try:
						if e.type == "privmsg":
							self.msg(source, message)
						elif e.type == "pubmsg":
							self.notice(source, message)
						del message
					except:
						pass

		return 1

	def errCommand(self, target, body, lastMsgs):
		try:
			self.print('errCommand(self, target="'+target+'", body="'+body+'", lastMsgs='+str(lastMsgs)+')')
			toReplace = body.replace('!err ','').split('/')[0]
			replaced = body.replace('!err','').split('/')[1]
			found = False
			for message in lastMsgs:
				nick = message.split(' ')[0]
				message = message.replace(nick+' ','')
				if message.find(toReplace) != -1 and not found:
					retour = noHL(nick) + ' voulait dire "' + message.replace(toReplace,replaced) + '"'
					found = True
			for badword in self.config['chans'][target]['badwords']:
				if retour.lower().find(badword.lower()) != -1:
					found = False
			if found:
				self.msg('#'+target, retour)
		except:
			pass

	def confirmNick(self, source, token):
		self.print('confirmNick(self, source="'+source+'", token="'+token+'")')
		request = requests.get(self.baseAddress + 'bot/confirm/'+source+'/'+token)
		result = request.json()
		message = ""
		if result['error']:
			message += bold('Erreur : ')
		message += result['message']
		self.msg(source, message)

	def sendConfig(self,eventType, source, command, subCommand, dataSet, target = 'global'):
		source = source.lower()
		command = command.lower()
		subCommand = subCommand.lower()
		self.print('sendConfig(self, eventType="'+eventType+'", source="'+source+'", command="'+command+'", subCommand="'+subCommand+'", dataSet='+str(dataSet)+', target="'+target+'")')
		for data in dataSet:
			request = requests.post(self.baseAddress + 'bot/config', data={
				'command': command,
				'target': target,
				'subCommand': subCommand,
				'source': source,
				'data': data
			})
			response = request.json()
			try:
				for error in response['errors']:
					message = bold('Erreur: ')+error
			except:
				if response['error']:
					message = bold('Erreur: ')+response['message']
				else:
					message = response['message']
			try:
				if eventType == "pubmsg":
					self.notice(source, message)
				else:
					self.msg(source, message)
			except:
				pass

	def fetchYoutube(self, target, source, yid):
		self.print('fetchYoutube(self, target="'+target+'", source="'+source+'", yid="'+yid+'")')
		request = requests.post(self.baseAddress + 'bot/ytfetch/' + target, data={'yid':yid,'name':source})
		video = request.json()
		if video['error']:
			message = bold('[yt] ') + video['message']
		else:
			if video['new']:
				message = bold('[yt] ')+video['title']
			else:
				message = bold('[yt(n°' + str(video['index']) + ')]')+' le ' + video['date'] + ' par ' + itallic(noHL(video['name'])) + ' - ' + bold(video['title'])
			message += ' [' + video['duration']+']'
		self.msg('#' + target, message)

	def searchYoutube(self, target, source, search):
		self.print('searchYoutube(self, target="'+target+'", source="'+source+'", search="'+search+'")')
		request = requests.post(self.baseAddress + 'bot/ytsearch/' + target, data={'search_query':search, 'name':source})
		result = request.json()
		if result['error']:
			message = bold('[ytSearch] ') + result['message']
		else:
			if result['new']:
				message = bold('[ytSearch] ')
			else:
				message = bold('[ytSearch(n°' + str(result['index']) + ')]')+' le ' + result['date'] + ' par ' + itallic(noHL(result['name'])) + ' - '

			message += underline(result['url']) + ' - ' + bold(result['title']) + ' [' + result['duration'] + ']'
		self.msg('#' + target, message)

	def randomYoutube(self, target, auto=False):
		self.print('randomYoutube(self, target="'+target+'", auto='+str(auto)+')')
		request = requests.get(self.baseAddress + 'bot/yt/' + target)
		result = request.json()
		if auto:
			messageType = 'ytAuto'
		else:
			messageType = 'ytSearch'
		if result['error']:
			if auto: return
			message = bold('[' + messageType + '] ') + result['message']
		else:
			message = bold('['+ messageType + '(n°' + str(result['index']) + ')]')+' le ' + result['date'] + ' par ' + itallic(noHL(result['name'])) + ' - ' + underline(result['url']) + ' - ' + bold(result['title']) + ' [' + result['duration'] + ']'
		self.msg('#' + target, message)

	def countYoutubeVideos(self, target):
		self.print('countYoutubeVideos(self, target="'+target+'")')
		request = requests.get(self.baseAddress + 'bot/ytcount/' + target)
		result = request.json()
		if result['error']:
			message = bold('[ytCount] ') + result['message']
		else:
			message = bold('[ytCount] ') + bold(str(result['count'])) + ' vidéos ont été partagées sur '+bold('#'+target)+' depuis le '+underline(result['oldest'])
		self.msg('#' + target, message)

	def countYoutubeVideosByName(self, target, name):
		self.print('countYoutubeVideosByName(self, target="'+target+'", name="'+name+'")')
		request = requests.get(self.baseAddress + 'bot/ytcount/' + target + '/' + name)
		result = request.json()
		if result['error']:
			message = bold('[ytCount] ') + result['message']
		else:
			message = bold('[ytCount] ') + bold(str(result['count'])) + ' vidéos ont été partagées par '+ itallic(noHL(name)) + ' sur '+bold('#'+target)+' depuis le '+underline(result['oldest'])
		self.msg('#' + target, message)

	def spamYoutube(self, target):
		self.print('spamYoutube(self, target="'+target+'")')
		while True:
			time.sleep(self.config['chans'][target]['youtube']['timer'])
			self.randomYoutube(target, True)

	def checkConfig(self, c):
		self.print('checkConfig(self, c)')
		while True:
			time.sleep(60)
			request = requests.get(self.baseAddress + 'bot/config/check')
			result = request.json()
			self.print('Checking config')
			if((not result['error']) and (result['lastUpdate'] != self.config['lastUpdate'])):
				self.print('Getting new config')
				request = requests.get(self.baseAddress + 'bot/config')
				result = request.json()
				with self.updateLock:
					self.config['lastUpdate'] = result['lastUpdate']
					self.config['owners'] = result['owners']
					self.config['realname'] = result['realname']

					if(self.config['myname'] != result['myname']):
						self.connection.nick(result['myname'])
						self.config['myname'] = result['myname']

					for key in self.spamYtProcess.keys():
						self.spamYtProcess[key].terminate()
						self.spamYtProcess[key].join()
						del self.spamYtProcess[key]

					for key in self.spamProcess.keys():
						self.spamProcess[key].terminate()
						self.spamProcess[key].join()
						del self.spamProcess[key]

					for key in self.spamEventProcess.keys():
						self.spamEventProcess[key].terminate()
						self.spamEventProcess[key].join()
						del self.spamEventProcess[key]
					for chan in result['chans'].keys():
						if chan not in self.config['chans'].keys():
							self.lastMsg[chan] = []
							self.print('Joining #'+chan)
							c.join('#'+chan)

					for chan in self.config['chans'].keys():
						if chan not in result['chans'].keys():
							del self.lastMsg[chan]
							self.print('Leaving #'+chan)
							c.part('#'+chan, 'Leaving')
					self.config['chans'] = result['chans']



if __name__ == "__main__":

	# start the bot
	bot = ModIRC(sys.argv)
	try:
		bot.our_start()
	except:
		traceback.print_exc()
	bot.disconnect("Bye :(")
