<?php

namespace respawnthere;

use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

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
		if(!($levelObject instanceof Level)){
			// level $level is not loaded. Let's try loading it ourselves :)
			$this->getServer()->loadLevel($level); // load a level called $level
			$levelObject = $this->getServer()->getLevelByName($level); // try to get the object again
			if(!($levelObject instanceof Level)){
				// uh-oh, the level is still not loaded
				$this->getLogger()->critical("Unable to load level ".TextFormat::YELLOW.$level.TextFormat::RED."!"); // send console a message that the level cannot be loaded
				$this->setEnabled(false); // commit suicide! Disable yoruself!
				return;
			}
		}
		$this->respawnPosition = new Position($x, $y, $z, $levelObject);
	}
	public function onRespawn(PlayerRespawnEvent $event){
		$event->setRespawnPosition($this->respawnPosition);
	}
}
