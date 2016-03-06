<?php
namespace voteme;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class Main extends PluginBase {
    
    public function onEnable() {	
	    @mkdir($this->getDataFolder());
	    @mkdir($this->getDataFolder() . "Data/");
	    $this->getServer()->getPluginManager()->registerEvents(new PluginListener($this), $this);
	    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array(
	    "question" => "Do you like this server?",
	    "yes.votes.amount" => 0,
	    "no.votes.amount" => 0
	    ));
	    $this->data = new Config($this->getDataFolder() . "Data/" . "last-question.yml", Config::YAML, array(
	    "last.question" => ""
	    ));
	    $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML, array(
	    "vote.success" => "Your vote has been submitted!",
	    "already.voted" => "You have already submitted a vote for this question"
	    ));
	
	    if($this->getQuestion() !== $this->getLastQuestion()) {
	    	$this->setNoVotes(0);
	    	$this->setYesVotes(0);
	    	$this->setLastQuestion($this->getQuestion());
	    }
	   
    }
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $array) {
		if(strtolower($cmd->getName()) == "voteme") {
			if(count($array) == 0) {
					$sender->sendMessage("Usage: /voteme <question | yes | no>");
					return true;
				}
			if(count($array) == 1) {
					if(strtolower($array[0]) == "question") {
						$sender->sendMessage("[VoteMe] " . $this->getQuestion());
						$sender->sendMessage("Yes: " . $this->getYesVotes() . " No: " . $this->getNoVotes());
						return true;
					}

					if(strtolower($array[0]) == "yes") {
						if($this->hasVoted($sender->getName())) {
							$sender->sendMessage($this->messages->get("already.voted"));
							return true;
						}
						else {
						$sender->sendMessage($this->messages->get("vote.success"));
						$this->saveYesVote();
						$this->setPlayerVoteStatus($sender->getName(), true);
						return true;
						}
					}
					
					if(strtolower($array[0]) == "no") {
						if($this->hasVoted($sender->getName())) {
							$sender->sendMessage($this->messages->get("already.voted"));
							return true;
						}
						else {
						$sender->sendMessage($this->messages->get("vote.success"));
						$this->saveNoVote();
						$this->setPlayerVoteStatus($sender->getName(), true);
						return true;
						}
					}
				}
			}
	}
	
	public function getLastQuestion() {
		return $this->data->get("last.question");
	}
	
	public function setLastQuestion($str) {
		$this->data->set("last.question", $str);
		$this->data->setAll($this->data->getAll());
		$this->data->save();
	}
	
	public function hasVoted($player) {
		return $this->getPlayerVoteStatus($player) == true;
	}
	
	public function setPlayerVoteStatus($player, $value) {
		$this->player = new config($this->getDataFolder() . "Data/" . strtolower($player) . ".yml");
		
		$this->player->set("vote.status", $value);
		$this->player->setAll($this->player->getAll());
		$this->player->save();
	}
	
	public function getPlayerVoteStatus($player) {
		$this->player = new config($this->getDataFolder() . "Data/" . strtolower($player) . ".yml");
		
		return $this->player->get("vote.status");
	}
	
	public function setPlayerLastQuestion($player, $question) {
		$this->player = new config($this->getDataFolder() . "Data/" . strtolower($player) . ".yml");
		
		$this->player->set("last.question", $question);
		$this->player->setAll($this->player->getAll());
		$this->player->save();
	}
	
	public function getPlayerLastQuestion($player) {
		$this->player = new config($this->getDataFolder() . "Data/" . strtolower($player) . ".yml");
		
		return $this->player->get("last.question");
	}
	
	public function generatePlayerData($player) {
		$this->player = new config($this->getDataFolder() . "Data/" . strtolower($player) . ".yml", Config::YAML, array(
		"vote.status" => "",
		"last.question" => ""
		));
		
		$this->player;
	}
	
	public function saveYesVote() {
		$this->config->set("yes.votes.amount", $this->getYesVotes() + 1);
		$this->config->setAll($this->config->getAll());
		$this->config->save();
	}
	
	public function saveNoVote() {
		$this->config->set("no.votes.amount", $this->getNoVotes() + 1);
		$this->config->setAll($this->config->getAll());
		$this->config->save();
	}
	
	public function getQuestion() {
		return $this->config->get("question");
	}
	
	public function getYesVotes() {
		return $this->config->get("yes.votes.amount");
	}
	
	public function getNoVotes() {
		return $this->config->get("no.votes.amount");
	}
	
	public function setYesVotes($amount) {
		return $this->config->set("yes.votes.amount", $amount);
		$this->config->setAll($this->config->getAll());
		$this->config->save();
	}
	
	public function setNoVotes($amount) {
		return $this->config->set("no.votes.amount", $amount);
		$this->config->setAll($this->config->getAll());
		$this->config->save();
	}
}
