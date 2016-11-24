<?php
/**
 *  Author: Omar Faruk Sharif
 *  Github: https://github.com/omarfaruksharif/Magento-View-Product-by-Attribute-Value
 *  Project: Magento-View-Product-by-Attribute-Value
 */

require_once Mage::getModuleDir('controllers', 'Mage_Catalog').DS.'ProductController.php';

/**
 * Product controller
 *
 * @category   Omar
 * @package    Omar_Prroductviewby
 */
class Omar_Prroductviewby_Catalog_ProductController extends Mage_Catalog_ProductController
{


    /**
     * Product view action
     */
    public function viewbyAction()
    {
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $params = $this->getRequest()->getParams();

        if( !empty($params) ) {
            $param_keys = array_keys($params);
            if( !empty($param_keys[0]) ) {
                $param_attr_key = $param_keys[0];
                $param_attr_val = $params[ $param_attr_key ];
            }
        }

        if( empty($param_attr_key) && empty($param_attr_val) ) {
            $this->_forward('noRoute');
        } else {
            $product = Mage::getModel('catalog/product')->loadByAttribute( $param_attr_key , $param_attr_val);
            if( !empty($product->getId()) )
            {
                $productId = $product->getId();
            }
            else {
                $this->_forward('noRoute');
            }
        }

        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $this->getRequest()->setActionName('view')->setParam('id', $productId);;
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

}
