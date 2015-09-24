<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Netresearch_OPS_Block_Form_Other
    extends Mage_Payment_Block_Form
{

    /**
     * Init OPS payment form
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ops/form/other.phtml');
    }
} 