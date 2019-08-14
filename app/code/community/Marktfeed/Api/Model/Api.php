<?php
class Marktfeed_Api_Model_Api extends Mage_Api_Model_Resource_Abstract {

    public function register($name, $guid) {
	
        try {
		
			$config = new Mage_Core_Model_Config();
			$config ->saveConfig("marktfeed/api/register_name", $name, 'default', 0);		
			$config ->saveConfig("marktfeed/api/register_guid", $guid, 'default', 0);
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
			->addAttributeToFilter('status', 1)
            ->setPageSize($limit)
            ->setCurPage($page);
			
		//
		if ($store_id != 0)
			$productCollection->setStoreId($store_id);

		//loop products
		foreach ($productCollection AS $_product) {
			
			$product = Mage::getModel('catalog/product')->load($_product->getId());
			
			//get product data and remove un necessary data
			$productData = $product->getData();
			unset($productData["stock_item"]);
			unset($productData["custom_layout_update"]);
			unset($productData["request_path"]);
			unset($productData["media_gallery"]);
			
			//add product categories
			$productData["category_ids"] = $product->getCategoryIds();
			
			//add product images
			$productData["images"] = array();
			foreach ($product->getMediaGalleryImages() as $image) {
				 $productData["images"][] = $image->getUrl();
			}

			//add to result
			$productResult->products[] = $productData;
		}
		
		//set total
		$productResult->total = $productCollection->getSize();
		
		//return products
		return $productResult;		
	}
}