<?php


class Cmsmart_Dailydeal_Block_Manage_Alldeal_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('dailydeal_form', array('legend' => Mage::helper('dailydeal')->__('Meta Data')));

        $fieldset->addField('meta_keywords', 'editor', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('dailydeal')->__('Keywords'),
            'title' => Mage::helper('dailydeal')->__('Meta Keywords'),
            'style' => 'width: 520px;',
        ));

        $fieldset->addField('meta_description', 'editor', array(
            'name' => 'meta_description',
            'label' => Mage::helper('dailydeal')->__('Description'),
            'title' => Mage::helper('dailydeal')->__('Meta Description'),
            'style' => 'width: 520px;',
        ));

        $fieldset = $form->addFieldset('dailydeal_options', array('legend' => Mage::helper('dailydeal')->__('Advanced Post Options')));

        $fieldset->addField('user', 'text', array(
            'label' => Mage::helper('dailydeal')->__('Poster'),
            'name' => 'user',
            'style' => 'width: 520px;',
            'after_element_html' => '<span class="hint">('.Mage::helper('dailydeal')->__('Leave blank to use current user name').')</span>',
        ));


        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $fieldset->addField('created_time', 'date', array(
            'name' => 'created_time',
            'label' => $this->__('Created on'),
            'title' => $this->__('Created on'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'time' => true,
        ));



        if (Mage::getSingleton('adminhtml/session')->getDailydealData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDailydealData());
            Mage::getSingleton('adminhtml/session')->setDailydealData(null);
        } elseif ($data = Mage::registry('dailydeal_data')) {

            $form->setValues(Mage::registry('dailydeal_data')->getData());

            if ($data->getData('created_time')) {
                $form->getElement('created_time')->setValue(
                        Mage::app()->getLocale()->date($data->getData('created_time'), Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }
        }


        return parent::_prepareForm();
    }

}
