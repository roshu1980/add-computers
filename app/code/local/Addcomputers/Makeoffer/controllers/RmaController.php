<?php
class Addcomputers_Makeoffer_RmaController extends Mage_Core_Controller_Front_Action
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
            $dateValidator = new Zend_Validate_Date(array('format' => 'dd/mm/yyyy'));
            if (
            	!Zend_Validate::is($postObject->getCompanyName(), 'NotEmpty') ||
            	!Zend_Validate::is($postObject->getContactName(), 'NotEmpty') ||
            	!Zend_Validate::is($postObject->getContactPhone(), 'NotEmpty') ||
            	!Zend_Validate::is($postObject->getContactEmail(), 'EmailAddress') ||
            	!Zend_Validate::is($postObject->getRefNo(), 'NotEmpty') ||
            	!$dateValidator->isValid($postObject->getDateRma()) ||
            	!$dateValidator->isValid($postObject->getDatePurchase())
            ) 
            {
                throw new Exception();
            }

			// adding it to the template vars
			$emailTemplateVariables = array();
			$emailTemplateVariables['company_name'] = $postParams['company_name'];
			$emailTemplateVariables['contact_name'] = $postParams['contact_name'];
			$emailTemplateVariables['contact_phone'] = $postParams['contact_phone'];
			$emailTemplateVariables['contact_email'] = $postParams['contact_email'];
			$emailTemplateVariables['ref_no'] = $postParams['ref_no'];
			$emailTemplateVariables['invoice_no'] = $postParams['invoice_no'];
			$emailTemplateVariables['date_rma'] = $postParams['date_rma'];
			$emailTemplateVariables['date_purchase'] = $postParams['date_purchase'];

			//validating product data
			$digitValidator = new Zend_Validate_Digits();
			$files = array();
			for ($i = 1; $i < count($postParams['part_number']) + 1; $i++)
			{
				if (
					!Zend_Validate::is($postParams['part_number'][$i], 'NotEmpty') ||
					!Zend_Validate::is($postParams['quantity'][$i], 'NotEmpty') ||
					!($digitValidator->isValid($postParams['warranty_sticker'][$i]) && strlen($postParams['warranty_sticker'][$i]) == 6)
				)
				{
                	throw new Exception();
            	}

				$products[] = array(
					'Part Number' => $postParams['part_number'][$i],
					'Quantity' => $postParams['quantity'][$i],
					'Serial Number' => $postParams['serial_number'][$i],
					'Warranty Sticker' => $postParams['warranty_sticker'][$i],
					'Reason' => $postParams['reason'][$i]
				);

				if ($_FILES['upload_img']['name'][$i])
				{
					$files[] = array(
						'name' => $_FILES['upload_img']['name'][$i],
						'path' => $_FILES['upload_img']['tmp_name'][$i]
					);
				}	
			}
			$emailTemplateVariables['products'] = $products;

			$name_from = Mage::getStoreConfig('trans_email/ident_general/name');
			$email_from = Mage::getStoreConfig('trans_email/ident_general/email');
			$name_to = Mage::getStoreConfig('trans_email/ident_sales/name');
			$email_to = Mage::getStoreConfig('trans_email/ident_sales/email');

   			$mail = Mage::getModel('core/email_template');
   			$mail->addBcc('vali@addcomputers.com');
			// attach the uploaded files
			if (count($files) > 0)
			{
				foreach ($files as $file) {
					try {
					$mail->getMail()->createAttachment(
				        file_get_contents($file['path']),
				        Zend_Mime::TYPE_OCTETSTREAM,
				        Zend_Mime::DISPOSITION_ATTACHMENT,
				        Zend_Mime::ENCODING_BASE64,
				        $file['name']
				    );	
				    } catch (Exception $e) {
				    	print_r($e);
				    }			
				}
			}

			try{
				//Send email
                $mail->sendTransactional('rma', array('name' => $name_from, 'email' => $email_from), $email_to, $name_to, $emailTemplateVariables);
			}
			catch(Exception $error)
			{
			 	// die silently
				return false;
			}


            Mage::getSingleton('customer/session')->addSuccess(__('Your RMA form was submitted successfully!'));
            $this->_redirectUrl('/rma');

        } catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError(__('Unable to submit your request. Please revise your data and try again.'));
            $this->_redirectUrl('/rma');
        }

        return;
    }
}