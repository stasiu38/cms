<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initRequest() {echo '1';}
	protected function _initAcl()
	{
	/*
		$acl = new Zend_Acl();
		$aclOptions = $this->getOption('acl');

		foreach($aclOptions['roles'] as $role) {
			$acl->addRole(new Zend_Acl_Role($role));
		}

		foreach($aclOptions['resources'] as $resource) {
			$acl->addResource(new Zend_Acl_Resource($resource));
		}

		foreach($aclOptions['privileges'] as $role => $resource) {
			$acl->allow($role, $resource);
		}

		return $acl;
	*/}
}
