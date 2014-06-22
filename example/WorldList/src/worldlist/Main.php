<?php

namespace worldlist;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	public function onCommand(CommandSender $sender, Command $command, $alias, array $args){
		if(!isset($args[0])){ // if there is no world name given
			if(!($sender instanceof Player)){ // if the sender is not a player (not in-game)
				return false; // request a world name by sending usage
			}
			// There is no need to put else here. It is because if the sender is not a player, the "return false" line already stops the function and this line will not be run.
			$level = $player->getLevel(); // store the player's level's value into $level
		}
		else{
			$name = $args[0]; // store the world name into $name. $args[0] is the 0th item in the arguments. Arguments are the words after the command, splited (explode()) into an array by whitespaces. A general array's key counts from 0.
			$levels = $this->getServer()->getLevels(); // $levels is an array storing all the level objects
			foreach($levels as $cLevel){ // run the following for each level. I used $cLevel because I can't duplicate the name $level, which will be used later.
				if(strtolower($name) === strtolower($level->getName())){ // if the level's name is case-insensitively equal to $name. The lowercase of two words equal means the two words are case-insensitively equal
					$level = $cLevel; // save the found level
					break; // stop searching. Stopping this could make the server a few microseconds faster maybe. If not stopped, it will continue searching, but we know it won't have new discoveries.
				}
			}
			if(!isset($level)){ // if nothing is stored into $level i.e. no such level is found
				$sender->sendMessage("Level $name not found!");
				return true; // don't send the usage
			}
		}
		$names = array(); // initialize an array to prepare a variablr to store the found players in that world (level)
		foreach($this->getServer()->getOnlinePlayers() as $player){ // for each online player on the server, run this code:
			if($player->getLevel()->getName() === $level->getName()){ // if player world name equals to level world name; $level is defined above in the two cases (world name given or not), otherwise the function will have already been ended by a line with "return".
				$names[] = $player->getName(); // add the player name into the array prepared
			}
		}
		$sender->sendMessage("The following players are in world ".$level->getName().":");
		$sender->sendMessage(implode(", ", $names)); // combine the names into a string with ", " linking them, and send this string
		return true;
	}
}
