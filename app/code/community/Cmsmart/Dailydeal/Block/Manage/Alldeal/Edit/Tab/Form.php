<?php


class Cmsmart_Dailydeal_Block_Manage_Alldeal_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('dailydeal_form', array('legend' => Mage::helper('dailydeal')->__('Post information')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('dailydeal')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('productid', 'hidden', array(
            // 'class' => 'required-entry',
            'name' => 'productid',
        ));

        $fieldset->addField('quantity', 'text', array(
            'label' => Mage::helper('dailydeal')->__('Quantity'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'quantity',
			//'after_element_html' => '<span class="hint">' . Mage::helper('dailydeal')->__('Default as 1000') . '</span>',
        ));

        // $fieldset->addField('identifier', 'text', array(
            // 'label' => Mage::helper('dailydeal')->__('Identifier'),
            // 'class' => 'required-entry',
            // 'required' => true,
            // 'name' => 'identifier',
            // 'class' => 'aw-dailydeal-validate-identifier',
            // 'after_element_html' => '<span class="hint">' . Mage::helper('dailydeal')->__('(eg: domain.com/dailydeal/identifier)') . '</span>'
            // . "<script>
                    // Validation.add(
                    // 'aw-dailydeal-validate-identifier', 
                    // '" . addslashes(Mage::helper('dailydeal')->__("Please use only letters (a-z or A-Z), numbers (0-9) or symbols '-' and '_' in this field")) . "',
                    // function(v, elm) {
                                    // var regex = new RegExp(/^[a-zA-Z0-9_-]+$/);
                                    // return v.match(regex);
                         // }
                    // );
                    // </script>",)
        // );

		$fieldset->addField('save', 'text', array(
			'name' => 'save',
			'label' => Mage::helper('dailydeal')->__('Save'),
			'title' => Mage::helper('dailydeal')->__('Save'),
			'required' => true,
		));


        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('stores', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }

        $categories = array();
        // $collection = Mage::getModel('dailydeal/cat')->getCollection()->setOrder('dealid', 'asc');
        // foreach ($collection as $cat) {
            // $categories[] = ( array(
                // 'label' => (string) $cat->getTitle(),
                // 'value' => $cat->getCatId()
                    // ));
        // }

        // $fieldset->addField('cat_id', 'multiselect', array(
            // 'name' => 'cats[]',
            // 'label' => Mage::helper('dailydeal')->__('Category'),
            // 'title' => Mage::helper('dailydeal')->__('Category'),
            // 'required' => true,
            // 'style' => 'height:100px',
            // 'values' => $categories,
        // ));

        $fieldset->addField('image', 'image', array(
            'name' => 'image',
            'label' => Mage::helper('dailydeal')->__('Image'),
            'title' => Mage::helper('dailydeal')->__('Image'),
			'src' => "media/cmsmart/dailydeal/",
            'required' => false
			//'class' => '',
            //'style' => 'height:100px',
            //'values' => '',
        ));

        $fieldset->addField('ishome', 'select', array(
            'label' => Mage::helper('dailydeal')->__('Featured'),
            'name' => 'ishome',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('dailydeal')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('dailydeal')->__('No'),
                ),
            ),
            'after_element_html' => '<span class="hint">' . Mage::helper('dailydeal')->__('(Will be Show This Image on home page)') . '</span>',
        ));

        // $fieldset->addField('inbox1', 'select', array(
            // 'label' => Mage::helper('dailydeal')->__('Show in block 1'),
            // 'name' => 'inbox1',
            // 'values' => array(
			    // array(
                    // 'value' => 0,
                    // 'label' => Mage::helper('dailydeal')->__('No'),
                // ),
				
                // array(
                    // 'value' => 1,
                    // 'label' => Mage::helper('dailydeal')->__('Yes'),
                // )

            // ),
            // 'after_element_html' => '<span class="hint">' . Mage::helper('dailydeal')->__('(Show This Post on block 1)') . '</span>',
        // ));



        // $fieldset->addField('comments', 'select', array(
            // 'label' => Mage::helper('dailydeal')->__('Enable Comments'),
            // 'name' => 'comments',
            // 'values' => array(
                // array(
                    // 'value' => 0,
                    // 'label' => Mage::helper('dailydeal')->__('Enabled'),
                // ),
                // array(
                    // 'value' => 1,
                    // 'label' => Mage::helper('dailydeal')->__('Disabled'),
                // ),
            // ),
            // 'after_element_html' => '<span class="hint">' . Mage::helper('dailydeal')->__('Disabling will close the post to new comments') . '</span>',
        // ));

        // $fieldset->addField('tags', 'text', array(
            // 'name' => 'tags',
            // 'label' => Mage::helper('dailydeal')->__('Tags'),
            // 'title' => Mage::helper('dailydeal')->__('tags'),
            // 'style' => 'width:700px;',
            // 'after_element_html' => Mage::helper('dailydeal')->__('Use space or comma as separators'),
        // ));
		
		
		$config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        // try {
            // $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
            // $config->setData(Mage::helper('dailydeal')->recursiveReplace(
                            // '/dailydeal_admin/', '/' . (string) Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '/', $config->getData()
                    // )
            // );
        // } catch (Exception $ex) {
            // $config = null;
        // }

        // if (Mage::getStoreConfig('dailydeal/dailydeal/useshortcontent')) {
            // $fieldset->addField('short_content', 'editor', array(
                // 'name' => 'short_content',
                // 'label' => Mage::helper('dailydeal')->__('Short Content'),
                // 'title' => Mage::helper('dailydeal')->__('Short Content'),
                // 'style' => 'width:700px; height:100px;',
                // 'config' => $config,
            // ));
        // }
		
		$outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);


        $fieldset->addField('starttime', 'date', array(
            'name' => 'starttime',
            'label' => $this->__('Start time'),
            'title' => $this->__('Start time'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
			'input_format'  => Varien_Date::DATETIME_INTERNAL_FORMAT,
			'required'  => true,
            'time' => true,
        ));

        $fieldset->addField('closetime', 'date', array(
            'name' => 'closetime',
            'label' => $this->__('Close time'),
            'title' => $this->__('Close time'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
			'input_format'  => Varien_Date::DATETIME_INTERNAL_FORMAT,
			'required'  => true,
            'time' => true,
        ));
        // $fieldset->addField('post_content', 'editor', array(
            // 'name' => 'post_content',
            // 'label' => Mage::helper('dailydeal')->__('Content'),
            // 'title' => Mage::helper('dailydeal')->__('Content'),
            // 'style' => 'width:700px; height:500px;',
            // 'config' => $config
        // ));

		$statuses = Mage::getSingleton('dailydeal/status')->getOptionArray();
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('dailydeal')->__('Status'),
            'name' => 'status',
            'values' => $statuses,
            'after_element_html' => '<span class="hint">' . Mage::helper('dailydeal')->__('(Hidden Pages will not show in the dailydeal but can still be accessed directly)') . '</span>',
        ));
		
        if (Mage::getSingleton('adminhtml/session')->getDailydealData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDailydealData());
            Mage::getSingleton('adminhtml/session')->setDailydealData(null);
        } elseif ($data = Mage::registry('dailydeal_data')) {
           // Mage::registry('dailydeal_data')->setTags(Mage::helper('dailydeal')->convertSlashes(Mage::registry('dailydeal_data')->getTags()));
            $form->setValues(Mage::registry('dailydeal_data')->getData());
			
			if ($data->getData('closetime')) {
                $form->getElement('closetime')->setValue(
                        Mage::app()->getLocale()->date($data->getData('closetime'), Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }
			
			 if ($data->getData('starttime')) {
                $form->getElement('starttime')->setValue(
                        Mage::app()->getLocale()->date($data->getData('starttime'), Varien_Date::DATETIME_INTERNAL_FORMAT)
                );
            }
        }
        return parent::_prepareForm();
    }

}
