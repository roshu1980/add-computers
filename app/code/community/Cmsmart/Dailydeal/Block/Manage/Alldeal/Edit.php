<?php


class Cmsmart_Dailydeal_Block_Manage_Alldeal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'dailydeal';
        $this->_controller = 'manage_alldeal';

        $this->_updateButton('save', 'label', Mage::helper('dailydeal')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('dailydeal')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        // if ($this->getRequest()->getParam('id')) {
            // $this->_addButton('diplicate', array(
                // 'label' => Mage::helper('dailydeal')->__('Duplicate Post'),
                // 'onclick' => 'duplicate()',
                // 'class' => 'save'
                    // ), 0);
        // }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('dailydeal_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function duplicate() {
                $(editForm.formId).action = '" . $this->getUrl('*/*/duplicate') . "';
                editForm.submit();
            }
        ";
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/alldeal/');
    }
	
	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/deletedeal', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
	}	
	// public function getFormActionUrl()
    // {
        // if ($this->hasFormActionUrl()) {
            // return $this->getData('form_action_url');
        // }
        // return $this->getUrl('*/' . $this->_controller . '/savedeal');
    // }
	
    public function getHeaderText() {
        if (Mage::registry('dailydeal_data') && Mage::registry('dailydeal_data')->getId()) {
            return Mage::helper('dailydeal')->__("Edit '%s'", $this->htmlEscape(Mage::registry('dailydeal_data')->getTitle()));
        } else {
            return Mage::helper('dailydeal')->__('Add New');
        }
    }

}
