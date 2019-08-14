<?php
class Marktfeed_Api_Model_Api extends Mage_Api_Model_Resource_Abstract {

    public function register($name, $guid) {
	
        try {
		
			$config = new Mage_Core_Model_Config();
			$config ->saveConfig("marktfeed/api/register_name", $name, 'default', 0);		
			$config ->saveConfig("marktfeed/api/register_guid", $guid, 'default', 0);
			Mage::app()->cleanCache();
		}
        catch (Exception $e) {
            $this->_fault('general_error', $e->getMessage());
        }
		
		return true;
    }

    public function unregister() {

        try {
		
			//remove from config
			$config = new Mage_Core_Model_Config();
			$config ->saveConfig("marktfeed/api/register_name", '', 'default', 0);		
			$config ->saveConfig("marktfeed/api/register_guid", '', 'default', 0);
			
			//remove api user
			Mage::getModel('marktfeed_api/access')->removeUserAndRole();
			Mage::app()->cleanCache();
		}
        catch (Exception $e) {
            $this->_fault('general_error', $e->getMessage());
        }

		return true;
    }

    public function productlist($store_id, $limit, $page) {
    	
		//result object
		$productResult = new StdClass();
		$productResult->total = 0;
		$productResult->products = array();
		

		//set collection
		$productCollection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*')
			// removing attribute
			->removeAttributeToSelect('stock_item')
			->removeAttributeToSelect('custom_layout_update')
			->removeAttributeToSelect('request_path')
			->removeAttributeToSelect('media_gallery')
			// visibility not equil "Not Visible Individually"
			->addAttributeToFilter('visibility', array('neq' => 1))
			->addAttributeToFilter('status', 1)
            ->setPageSize($limit)
            ->setCurPage($page);
            
		if ($store_id != 0) {
			$productCollection->setStoreId($store_id);
		}
		
		$imageBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product'; 
		    
		//loop products
		foreach ($productCollection as $product) {
			
			//get product data and remove un necessary data
			$productData = $product->getData();
			
			//add product categories
			$productData["category_ids"] = $product->getCategoryIds();
			
			//add product images
			$productData["images"] = array();
			$productData["images_excluded"] = array();
			
			// load the Media Images
		    $product->load('media_gallery');
		    foreach ($product->getMediaGallery('images') as $image) {
		    
				// check if image is disabled
		    	if($image["disabled"] == 1) {
		    		$productData["images_excluded"][] = $imageBaseUrl.$image['file'];
		    	} else {
		    		$productData["images"][] = $imageBaseUrl.$image['file'];
		    	}
		    }
		    
		    
			//add to result
			$productResult->products[] = $productData;
			
		}
		
		//set total
		$productResult->total = $productCollection->getSize();
		
		return $productResult;
			
	}
}