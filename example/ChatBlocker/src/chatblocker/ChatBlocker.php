<?php

namespace chatblocker;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class ChatBlocker extends PluginBase implements Listener{
	public function onEnable(){
		$this->saveDefaultConfig(); // saves config.yml if it doesn't already exist
		$this->reloadConfig(); // pre-load the config so that the file is loaded in startup time rather than runtime, improving server performance
		$this->getPluginManager()->registerEvents($this, $this);
	}
}
