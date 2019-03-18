#!/usr/bin/env python3
# -*- coding: utf-8 -*-
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
from threading import Thread, RLock, Timer
import requests

sendLock = RLock()

def noHL(nick):
	newNick = ''
	for letter in nick:
		newNick += letter + u"\u200B"
	return newNick

class FetchYoutube(Thread):
	def __init__(self, bot, target, source, yid):
		Thread.__init__(self)
		self.bot = bot
		self.target = target
		self.source = source
		self.yid = yid

	def run(self):
		request = requests.post(self.bot.baseAddress + 'bot/ytfetch/' + self.target, data={'yid':self.yid,'name':self.source})
		video = request.json()
		if video['error']:
			message = '[yt] ' + video['message']
		else:
			if video['new']:
				message = '[yt] '
			else:
				message = '[yt(n°' + str(video['index']) + ')] le ' + video['date'] + ' par ' + noHL(video['name']) + ' - '
			message += video['title'] + ' [' + video['duration']+']'
		self.bot.msg('#' + self.target, message)

class SearchYoutube(Thread):
	def __init__(self, bot, target, source, search):
		Thread.__init__(self)
		self.bot = bot
		self.target = target
		self.source = source
		self.search = search

	def run(self):
		request = requests.post(self.bot.baseAddress + 'bot/ytsearch/' + self.target, data={'search_query':self.search, 'name':self.source})
		result = request.json()
		if result['error']:
			message = '[ytSearch] ' + result['message']
		else:
			if result['new']:
				message = '[ytSearch] '
			else:
				message = '[ytSearch(n°' + str(result['index']) + ')] le ' + result['date'] + ' par ' + noHL(result['name']) + ' - '

			message += result['url'] + ' - ' + result['title'] + ' [' + result['duration'] + ']'
		self.bot.msg('#' + self.target, message)

class RandomYoutube(Thread):
	def __init__(self, bot, target):
		Thread.__init__(self)
		self.bot = bot
		self.target = target
	def run(self):
		request = requests.get(self.bot.baseAddress + 'bot/yt/' + self.target)
		result = request.json()
		if result['error']:
			message = '[ytSearch] ' + result['message']
		else:
			message = '[ytSearch(n°' + str(result['index']) + ')] le ' + result['date'] + ' par ' + noHL(result['name']) + ' - ' + result['url'] + ' - ' + result['title'] + ' [' + result['duration'] + ']'
		self.bot.msg('#' + self.target, message)

class CountYoutubeVideos(Thread):
	def __init__(self, bot, target):
		Thread.__init__(self)
		self.bot = bot
		self.target = target

	def run(self):
		request = requests.get(self.bot.baseAddress + 'bot/ytcount/' + self.target)
		result = request.json()
		if result['error']:
			message = '[ytCount] ' + result['message']
		else:
			message = '[ytCount] ' + str(result['count']) + ' vidéos ont été partagées sur #'+self.target+' depuis le '+result['oldest']
		self.bot.msg('#' + self.target, message)

class CountYoutubeVideosByName(Thread):
	def __init__(self, bot, target, name):
		Thread.__init__(self)
		self.bot = bot
		self.target = target
		self.name = name

	def run(self):
		request = requests.get(self.bot.baseAddress + 'bot/ytcount/' + self.target + '/' + self.name)
		result = request.json()
		if result['error']:
			message = '[ytCount] ' + result['message']
		else:
			message = '[ytCount] ' + str(result['count']) + ' vidéos ont été partagées par '+ noHL(self.name) + ' sur #'+self.target+' depuis le '+result['oldest']
		self.bot.msg('#' + self.target, message)


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
		# if len(sys.argv) != 2:
		# 	print('Usage: sekhmet <address>')
		# 	sys.exit(1)
		self.baseAddress = ''
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
			server = [(self.config["server"]["address"],int(self.config["server"]["port"]),self.config["server"]["password"])]
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

		if e.type == "pubmsg" or e.type == "privmsg":

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
		command_list = body.split()
		command_list[0] = command_list[0].lower()
		if command_list[0] == "!ping":
			self.msg('#'+target, 'pong !')
		elif command_list[0] == "!ytcount":
			if self.config['chans'][target]['youtube']['active']:
				if len(command_list) == 1:
					CountYoutubeVideos(self, target).start()
				else:
					for nick in command_list[1:]:
						CountYoutubeVideosByName(self, target, nick).start()
		elif command_list[0] == "!yt":
			if self.config['chans'][target]['youtube']['active']:
				if len(command_list) == 1:
					RandomYoutube(self, target).start()
				else:
					SearchYoutube(self, target, source, ' '.join(command_list[1:])).start()

		return 1


if __name__ == "__main__":

	# start the bot
	bot = ModIRC(sys.argv)
	try:
		bot.our_start()
	except:
		traceback.print_exc()
	bot.disconnect("Bye :(")
