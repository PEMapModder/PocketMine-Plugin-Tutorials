<?php

namespace chatblocker;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;

class ChatBlocker extends PluginBase implements Listener{
	public function onEnable(){
		$this->saveDefaultConfig(); // saves config.yml if it doesn't already exist
		$this->reloadConfig(); // pre-load the config so that the file is loaded in startup time rather than runtime, improving server performance
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onChat(PlayerChatEvent $evt){
		$player = $evt->getPlayer();
		if($player->hasPermission("chatblocker.bypass")){
			return; // don't execute if the player has bypasd permission
		}
		$message = $evt->getMessage();
		if($this->matches($message)){ // if the message matches the expressions
			$this->executeOn($evt); // execute the actions on the player
		}
	}
	public function matches($message){	
		foreach($this->getConfig()->get("phrases") as $phrase){ // for each phrase,
			if(stripos($message, $phrase) !== false){ // if the message case-insensitively contains the phrase,
				return true; // return a result of true; no need to check any other phrases or expressions because we are only interested in whether the message matches, not how many phrases/expressions it matches.
			}
		}
		foreach($this->getConfig()->get("regular expressions") as $regex){ // for each RegExp,
			if(preg_match($regex, $message)){ // execute a PCRE check on it. If the number of checks is not zero (i.e. matched),
				return true; // stop executing and return a result of true
			}
		}
		return false; // if the function goes to this point, it means that it never matched anything, or else it would have returned true. So, return false.
	}
	public function executeOn(PlayerChatEvent $e){
		foreach($this->getConfig()->get("actions") as $action){ // execute actions one by one
			switch($action){
				case "block":
					$e->setCancelled();
					break;
				case "send warning":
					$e->getPlayer()->sendMessage($this->getConfig()->get("warning message"));
					break;
				case "kick":
					$e->getPlayer()->kick($this->getConfig()->get("kick message"));
					break;
				case "ban ip":
					$list = $this->getServer()->getIPBans();
					$list->addBan($e->getPlayer()->getAddress(), $this->getConfig()->get("ban message"), null, "ChatBlocker");
				case "ban name":
					$list = $this->getServer()->getNameBans();
					$list->addBan($e->getPlayer()->getName(), $this->getConfig()->get("ban message"), null, "ChatBlocker");
				case "tell console":
					$this->getLogger()->info($this->format($this->getConfig()->get("console format"), $e));
					break;
				case "tell moderators":
					$this->getServer()->broadcast($this->format($this->getConfig()->get("moderator format"), $e), "chatblocker.moderate");
					break;
				default:
					$this->getLogger()->error("Unknown action from config.yml: '$action'");
					break;
			}
		}
	}
	public function format($message, PlayerChatEvent $e){
		return str_replace(
			[
				"@player",
				"@ip",
				"@message",
			],
			[
				$e->getPlayer()->getName(),
				$e->getPlayer()->getAddress(),
				$e->getMessage(),
			],
			$message
		);
	}
}
