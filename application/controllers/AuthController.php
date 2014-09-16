<?php
class AuthController extends Zend_Controller_Action
{
	function init()
	{
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();


	}

	function indexAction()
	{
		$this->_redirect('/');

	}
	function loginAction()
	{
		$this->view->message = '';
		if ($this->_request->isPost()) {
			// collect the data from the user
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$f = new Zend_Filter_StripTags();
			$email = $f->filter($this->_request->getPost('email'));
			$password =md5( $f->filter($this->_request->getPost('password')));

			if (empty($email)) {
				$this->view->message = 'Please provide a username.';
			} else {
				// setup Zend_Auth adapter for a database table
				Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
				$authAdapter = new Zend_Auth_Adapter_DbTable(
					Zend_Registry::get('user_accounts'),
					'users',
					'user_login',
					'user_password',
					'MD5(?) and is_active = 1'
				);

				$authAdapter->setTableName('user_accounts');
				$authAdapter->setIdentityColumn('email');
				$authAdapter->setCredentialColumn('password');

				// Set the input credential values to authenticate against
				$authAdapter->setIdentity($email);
				$authAdapter->setCredential($password);

				// do the authentication
				$auth = Zend_Auth::getInstance();
				$result = $auth->authenticate($authAdapter);

				if ($result->isValid()) {
					// success: store database row to auth's storage
					// system. (Not the password though!)
					$data = $authAdapter->getResultRowObject(null,
						'password');
					$auth->getStorage()->write($data);
					$this->_redirect('/admin/');
				} else {
					// failure: clear database row from session
					$this->view->message = 'Login failed.';
				}
			}
		}
		$this->view->title = "Log in";
	}
	function logoutAction()
	{
		$this->Session->delete('admin');
		$this->redirect('/');
	}

}