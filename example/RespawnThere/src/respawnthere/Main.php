<?php

namespace respawnthere;

use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	private $respawnPosition;
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig(); // this function saves config.yml from resources to the plugin data folder
		$config = $this->getConfig(); // get the config object. PocketMine-MP will load config.yml and return it as a Config object when this function is called.
		$position = $config->get("respawn position");
		$x = $position["x"];
		$y = $position["y"];
		$z = $position["z"];
		$level = $position["level"];
		$levelObject = $this->getServer()->getLevelByName($level);
		$this->respawnPosition = new Position($x, $y, $z, $levelObject);
	}
	public function onRespawn(PlayerRespawnEvent $event){
		$event->setRespawnPosition($this->respawnPosition);
	}
}
