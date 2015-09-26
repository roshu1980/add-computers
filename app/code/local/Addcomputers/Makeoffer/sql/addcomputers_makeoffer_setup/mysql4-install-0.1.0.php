<?php

$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute("customer", "offers",  array(
    "type"      => "text",
    "backend"   => "",
    "label"     => "Offers",
    "input"     => "text",
    "source"    => "",
    "visible"   => true,
    "required"  => false,
    "default"   => "",
    "frontend"  => "",
    "unique"    => false,
    "note"      => "Customer Offers"
));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "offers");

$setup->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'offers',
    '999'  //sort_order
);

$installer->endSetup();