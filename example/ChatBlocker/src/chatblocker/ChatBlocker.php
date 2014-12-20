<?php

namespace chatblocker;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;

class ChatBlocker extends PluginBase implements Listener{
	public function onEnable(){
		$this->saveDefaultConfig(); // saves config.yml if it doesn't already exist
		$this->reloadConfig(); // pre-load the config so that the file is loaded in startup time rather than runtime, improving server performance
		$this->getPluginManager()->registerEvents($this, $this);
	}
	public function onChat(PlayerChatEvent $evt){
		$player = $evt->getPlayer();
		if($player->hasPermission("chatblocker.bypass")){
			return;
		}
		$message = $evt->getMessage();
		if($this->matches($message)){
			$this->executeOn($evt);
		}
	}
	public function matches($message){	
		foreach($this->getConfig()->get("phrases") as $phrase){
			if(stripos($message, $phrase) !== false){
				return true;
			}
		}
		foreach($this->getConfig()->get("regular expressions") as $regex){
			if(preg_match($regex, $message)){
				return true;
			}
		}
		return false;
	}
	public function executeOn(PlayerChatEvent $e){
		// TODO
	}
}
