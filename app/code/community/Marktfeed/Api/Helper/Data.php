<?php
class Marktfeed_Api_Helper_Data extends Mage_Core_Helper_Data
{
	public function getExtensionVersion()
	{
		return (string) Mage::getConfig()->getNode()->modules->Marktfeed_Api->version;
	}
}