<?php
class Marktfeed_Api_ConfigController extends Mage_Adminhtml_Controller_Action
{
    public function connectAction()
    {
		//create role
		Mage::getModel('marktfeed_api/access')->createRole();
	
		//create user
		$credentials = Mage::getModel('marktfeed_api/access')->createUser();
		
		$params = array();
		$params["username"] = $credentials["username"];
		$params["api_key"] = $credentials["api_key"];
		$params["mage_version"] = Mage::getVersion();
		$params["plugin_version"] = Mage::helper('marktfeed_api')->getExtensionVersion();
		$params["api"] = Mage::getBaseUrl() . "api/xmlrpc/";
		
		$this->_redirectUrl("https://www.marktfeed.nl/link/magento?" . http_build_query($params));
    }
	
	public function disconnectAction() {
	
		$params = array();
		$params["guid"] = Mage::getStoreConfig("marktfeed/api/register_guid", 0);
		
		$this->_redirectUrl("https://www.marktfeed.nl/unlink/magento?" . http_build_query($params));
	}
}