<?php

namespace FirstPlugin;

use pocketmine\plugin\PluginBase;

class FirstPlugin extends PluginBase{
	public function onEnable(){
		$this->getLogger()->info("Hello world! It works! I am enabled!");
	}
}
