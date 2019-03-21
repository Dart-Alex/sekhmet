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
	"""
	Module to interface IRC input and output with the PyBorg learn
	and reply modules.
	"""
	# The bot recieves a standard message on join. The standard part
	# message is only used if the user doesn't have a part message.
	join_msg = "%s"# is here"
	part_msg = "%s"# has left"

	# Detailed command description dictionary
	commandDict = {
		"yt": "Affiche une vidéo youtube (aléatoire si aucun argument). Syntaxe : !yt (pseudo/numéro de vidéo/recherche)",
		"ytcount": "Affiche le nombre de vidéos partagées sur le canal, ou par un utilisateur. Syntaxe : !ytcount (<pseudo> (<pseudo2> <pseudo3> ...))"
	}
	commandDictAdmin = {
		"admin": "Affiche ou modifie la liste des admins du bot sur le salon. Syntaxe : !admin (add/remove) <pseudo1> (<pseudo2> ...)",
		"youtube": "Change les paramètres du module Youtube. Syntaxe : !youtube start/stop / !youtube timer <timer en secondes>",
		"spam": "Change les paramètres du module Spam. Syntaxe : !spam start/stop / !spam timer <timer en secondes>",
		"event": "Change les paramètres du module Event. Syntaxe : !event start/stop / !event timer <timer en secondes>"
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
			if e.type == "pubmsg" or e.type == "privmsg":
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

					if body[0] == "!":
						if self.irc_commands(body, source, target, c, e) == 1: return
					else:
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
		command_list = body.split()
		command_list[0] = command_list[0].lower()
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

		return 1

	def errCommand(self, target, body, lastMsgs):
		try:
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
		request = requests.get(self.baseAddress + 'bot/confirm/'+source+'/'+token)
		result = request.json()
		message = ""
		if result['error']:
			message += bold('Erreur : ')
		message += result['message']
		self.msg(source, message)

	def fetchYoutube(self, target, source, yid):
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
		request = requests.get(self.baseAddress + 'bot/ytcount/' + target)
		result = request.json()
		if result['error']:
			message = bold('[ytCount] ') + result['message']
		else:
			message = bold('[ytCount] ') + bold(str(result['count'])) + ' vidéos ont été partagées sur '+bold('#'+target)+' depuis le '+underline(result['oldest'])
		self.msg('#' + target, message)

	def countYoutubeVideosByName(self, target, name):
		request = requests.get(self.baseAddress + 'bot/ytcount/' + target + '/' + name)
		result = request.json()
		if result['error']:
			message = bold('[ytCount] ') + result['message']
		else:
			message = bold('[ytCount] ') + bold(str(result['count'])) + ' vidéos ont été partagées par '+ itallic(noHL(name)) + ' sur '+bold('#'+target)+' depuis le '+underline(result['oldest'])
		self.msg('#' + target, message)

	def spamYoutube(self, target):
		while True:
			time.sleep(self.config['chans'][target]['youtube']['timer'])
			self.randomYoutube(target, True)

	def checkConfig(self, c):
		while True:
			time.sleep(10)
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
