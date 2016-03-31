<?php

use mysqli;
use mysqli_result;
use pocketmine\scheduler\AsyncTask;

abstract class MySQLTask extends AsyncTask{
	const STORE_KEY_MYSQLI = "your.plugin.name.async.mysqli";

	/** @type mysqli $cred */
	private $cred;

	public function __construct(MySQLCredentials $cred){
		$this->cred = $cred; // pthreads will internally serialize it, so don't let MySQLCredentials have any direct or indirect object reference to the main objects (e.g. plugin main, server, etc.)
	}

	public function getMysqli() : mysqli{
		$m = $this->getFromThreadStore(self::STORE_KEY_MYSQLI);
		if($m === null){
			$m = $this->cred->getMysqli();
			$this->saveToThreadStore(self::STORE_KEY_MYSQLI, $m);
		}
		return $m;
	}
}

class DirectMySQLTask extends MySQLTask{
	/** @type string $query */
	private $query;
	/** @type string $params serialized param array */
	private $params;

	public function __construct(MySQLCredentials $cred, string $query, ...$params){
		parent::__construct($cred);
		$this->query = $query;
		$this->params = serialize($params);
	}
	
	public function onRun(){
		$m = $this->getMysqli();
		$query = sprintf($this->query, ...unserialize($this->params));
		$result = $m->query($query);
		$output = new DirectMySQLTaskResult;
		if($result instanceof mysqli_result){
			$output->resultType = DirectMySQLTaskResult::TYPE_SELECT;
			$output->content = [];
			
			while(is_array($row = $result->fetch_assoc())){
				$output->content[] = $row;
			}
		}elseif($result === false){
			$output->resultType = DirectMySQLTaskResult::TYPE_ERROR;
			$output->content = $m->error;
		}elseif(in_array(strtoupper(substr(ltrim($query), 0 6)), ["INSERT", "UPDATE"])){
			$output->resultType = DirectMySQLTaskResult::TYPE_INSERT;
			$output->content = $m->insert_id;
		}
		
		$this->setResult($output);
	}
}

class DirectMySQLTaskResult{
	const TYPE_SELECT = 0;
	const TYPE_INSERT = 1;
	const TYPE_ERROR = 2;
	const TYPE_SUCCESS = 3;

	public $resultType;
	public $content;
}

class MySQLCredentials{
	public $ip;
	public $user;
	public $password;
	public $schema;
	public $port = 3306;
	public $socket = "";
	
	public function getMysqli() : mysqli{
		return new mysqli($this->ip, $this->user, $this->password, );
	}
	
	public function __debugInfo(){
		// prevents logging password in var_dump
		return [
			"ip" => $this->ip,
			"user" => $user,
			"password" => sha1($this->password),
			"schema' => $this->schema,
			"port" => $this->port,
			"socket" => $this->socket,
		];
	}
}
