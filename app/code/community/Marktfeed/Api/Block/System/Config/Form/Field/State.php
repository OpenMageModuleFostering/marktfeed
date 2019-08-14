<?php
	class Marktfeed_Api_Block_System_Config_Form_Field_State extends Mage_Adminhtml_Block_System_Config_Form_Field
	{
		protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
		{
			$guid = Mage::getStoreConfig("marktfeed/api/register_guid", 0);
			$name = Mage::getStoreConfig("marktfeed/api/register_name", 0);
			
			$style = '<style type="text/css">#row_marktfeed_api_state .scope-label {display:none;}</style>';
			
			if ($guid == "" || !Mage::getModel('marktfeed_api/access')->allExists()) {
			
				$url = Mage::helper('adminhtml')->getUrl('admin_marktfeedapi/config/connect');
				return '<img style="float:left;margin: 1px 5px 0 0;" src="'.$this->getSkinUrl('images/marktfeed/disconnected.png').'"/><a style="float:left;width:275px;" target="_blank" href="' . $url . '">' . $this->__('Disconnected, click to connect') . '</a><div style="clear:both;width:1px;height:1px;overflow:hidden;font-size:1px;">&nbsp;</div>'.$style;
			} else {
			
				$url = Mage::helper('adminhtml')->getUrl('admin_marktfeedapi/config/disconnect');
				return '<img style="float:left;margin: 1px 5px 0 0;" src="'.$this->getSkinUrl('images/marktfeed/connected.png').'"/> <a style="float:left;width:275px;" target="_blank" href="' . $url . '">' . $this->__("Connected with account '%s',<br/>click to disconnect", $name) . '</a><div style="clear:both;width:1px;height:1px;overflow:hidden;font-size:1px;">&nbsp;</div>'.$style;			
			}
		}
	}