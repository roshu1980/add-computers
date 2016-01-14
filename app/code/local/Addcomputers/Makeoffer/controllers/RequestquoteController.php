<?php
class Addcomputers_Makeoffer_RequestquoteController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    }

    public function sendAction()
    {
		$postParams = $this->getRequest()->getParams();

		try 
		{
            $postObject = new Varien_Object();
            $postObject->setData($postParams);

            // validating customer data
            if (
            	!Zend_Validate::is($postObject->getCompanyName(), 'NotEmpty') ||
            	!Zend_Validate::is($postObject->getContactName(), 'NotEmpty') ||
            	!Zend_Validate::is($postObject->getTelephone(), 'NotEmpty') ||
            	!Zend_Validate::is($postObject->getEmail(), 'EmailAddress')
            ) 
            {
                throw new Exception();
            }

			// adding it to the template vars
			$emailTemplateVariables = array();
			$emailTemplateVariables['company_name'] = $postParams['company_name'];
			$emailTemplateVariables['contact_name'] = $postParams['contact_name'];
			$emailTemplateVariables['telephone'] = $postParams['telephone'];
			$emailTemplateVariables['email'] = $postParams['email'];

			foreach ($postParams['product_code'] as $key => $value) 
			{
				$products[] = array(
					'Product Code' => $postParams['product_code'][$key],
					'Description' => $postParams['description'][$key],
					'Quantity' => $postParams['quantity'][$key],
					'Condition' => $postParams['condition'][$key]
				);
			}

			$emailTemplateVariables['products'] = $products;

			$name_from = Mage::getStoreConfig('trans_email/ident_general/name');
			$email_from = Mage::getStoreConfig('trans_email/ident_general/email');
			$name_to = Mage::getStoreConfig('trans_email/ident_sales/name');
			$email_to = Mage::getStoreConfig('trans_email/ident_sales/email');
   			
   			$mail = Mage::getModel('core/email_template');
   			$mail->addBcc('vali@addcomputers.com');

			try{
				//Send email
                $mail->sendTransactional('requestquote', array('name' => $name_from, 'email' => $email_from), $email_to, $name_to, $emailTemplateVariables);
			}
			catch(Exception $error)
			{
			 	// die silently
				return false;
			}


            Mage::getSingleton('customer/session')->addSuccess(__('Your data was submitted successfully!'));
            $this->_redirectUrl('/request-quote');

        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError(__('Unable to submit your request. Please revise your data and try again.'));
            $this->_redirectUrl('/request-quote');
        }

        return;
    }
}