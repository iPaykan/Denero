<?php

/**
* Test task for for understanding of OOP principles from Denero
*
* @author Баянов Е.А. <biaqwerty@yandex.ru
* @version 1.0
*/

/** 
* Сlass for working with the object
*/
final class object 
{
	private $id;
	private $name;
	private $status;
	private $changed;
	private $pdo;

	/**
	* @param int $id current object id
	* @return void
	*/
	function __construct($id) {
		$this->pdo = new PDO('mysql:host=localhost;dbname=testdbname', 'root', '');
		$this->id = $id;
		$this->init();
	}

	/**
	* Get name, status of the current object from DB and set appropriate values in attribites
	* @return bool 
	*/	
	private function init() {
		try {
			$pdo = $this->pdo;
			$stm = $pdo->prepare("SELECT name, status FROM `testtable` WHERE id = ?");
			$stm->execute(array($this->id));
			$result = $stm->fetch();

			$this->name = $result['name'];
			$this->status = $result['status'];

			return true;
		}	
		catch(PDOException $e) {
			echo $e->getMessage();
			return false;
		}
	}

	/**
	* Magic method __get
	* @param string $property 
	* @return mixed
	*/
	public function __get($property) {
		if(property_exists($this, $property)) {
			return $this->$property;
		}
	}

	/**
	* Magic method __set with checking data types and fullness
	* @param string $property
	* @param mixed $value 
	*/
	public function __set($property, $value) {
		if($value && $property!='id') {
			switch ($property) {
				case 'name':
					if(gettype($value) == 'string') {
						$this->$property = $value;
						$this->changed = true;
					}
					break;		
				case 'status':
					if(gettype($value) == 'integer') {
						$this->$property = $value;
						$this->changed = true;
					}
					break;
				case 'changed':
					if(gettype($value) == 'boolean') {
						$this->$property = $value;
						$this->changed = true;
					}
					break;		
				default:
					break;
			}
		}
	}

	/**
	* Save information into DB if the change really happened
	* @return bool
	*/
	public function save() {
		if($this->changed) {
			try {
				$pdo = $this->pdo;
				$stm = $pdo->prepare("UPDATE `testtable` SET `name`='$this->name', `status`=$this->status WHERE id = ?");
				$stm->execute(array($this->id));

				return true;
			}	
			catch(PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		}
	}
}

?>