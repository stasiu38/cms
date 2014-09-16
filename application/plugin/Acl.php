<?php
	class Application_Plugin_Acl extends Zend_Controller_Plugin_Abstract
	{
		public function preDispatch(Zend_Controller_Request_Abstract $request)
		{
		// rolę pobieramy z Zend_Auth lub dowolnego innego miejsca
		// dla uproszczenia wpisałem ją na sztywno

		$role = 'admin';
		$resource = $request->getControllerName() . '_' . $request->getActionName();

		/** @var Zend_Acl $acl */
		$acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Acl');

		if(!$acl->isAllowed($role, $resource)) {
		// tutaj powinno nastąpić rzucenie wyjątku lub przekierowanie
			$this->_redirect('/');
		}
	}
}