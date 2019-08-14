<?php
	class Marktfeed_Api_Model_Access {
	
		private $username = "Marktfeed";
		private $rolename = "Marktfeed";

		public function allExists() {

			return ($this->userExists() && $this->roleExists());
		}

		public function createUser() {
		
			//chars
			$chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
				. Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
				. Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS;
				
			//generate new API key
			$api_key = Mage::helper('core')->getRandomString(24, $chars);			

			//create user
			$userapi = $this->getUser();
			if (!$userapi->getId()) {
				$userapi = Mage::getModel('api/user')->setData(array(
					'username' => $this->username,
					'firstname' => $this->username,
					'lastname' => 'API',
					'email' => 'api@marktfeed.nl',
					'api_key' => $api_key,
					'api_key_confirmation' => $api_key,
					'is_active' => 1));
				$userapi->save();
			} else {
				$userapi->setApiKey($api_key);
				$userapi->setApiKeyConfirmation($api_key);
				$userapi->setIsActive(1);
				$userapi->save();
			}

			//
			$userapi->setRoleIds(array($this->getRole()->getId()))  // your created custom role
				->setRoleUserId($userapi->getUserId())
				->saveRelations();	

			//return credentials
			return array(
				"api_key" => $api_key,
				"username" => $this->username);
		}

		public function userExists() {
		
			return ($this->getUser()->getId() != false);
		}

		public function createRole() {

			//create role when not exists
			if (!$this->roleExists()) {
				$role = Mage::getModel('api/roles')
					->setName($this->rolename)
					->setPid(false)
					->setRoleType('G')
					->save();
			}

			//attach rule to role
			Mage::getModel("api/rules")
				->setRoleId($this->getRole()->getId())
				->setResources(array('all'))
				->saveRel();		
		}
	
		public function roleExists() {
		
			return ($this->getRole()->getId() != false);
		}
		
		public function removeUserAndRole() {
		
			//remove user
			$user = $this->getUser();
			if ($user->getId() != false)
				$user->delete();
		
			//remove role
			$role = $this->getRole();
			if ($role->getId() != false)
				$role->delete();
		}
	
		private function getUser() {
		
			return Mage::getModel('api/user')->load($this->username, 'username');
		}
	
		private function getRole() {

			return Mage::getModel('api/roles')->load($this->rolename, 'role_name');
		}		
	}