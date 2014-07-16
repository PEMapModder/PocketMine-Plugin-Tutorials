<?php

namespace respawnthere;

use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig(); // this function saves config.yml from resources to the plugin data folder
		$config = $this->getConfig(); // get the config object. PocketMine-MP will load config.yml and return it as a Config object when this function is called.
		$position = $config->get("respawn position");
		$x = $position["x"];
		$y = $position["y"];
		$z = $position["z"];
	}
	public function onRespawn(PlayerRespawnEvent $event){
		$event->setRespawnPosition(new Position($x, $y, $z, $level));
	}
}
