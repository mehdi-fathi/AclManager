<?php

		namespace AclManager\Controller;

		use App\Controller\AppController as BaseController;
		use Cake\Event\Event;
		class AppController extends BaseController
		{

				public function beforeFilter(Event $event)
				{

						$this->Auth->allow(['delete']);

						$this->layout = 'panel';

						$auth = $this->request->session()->read('Auth.User.role_id');

						if (!empty($auth))
						{

								$AclManager = $this->loadComponent('AclManager.Check');

								$check_action_plugin_curent = $AclManager->Check_plugin();

								$this->Auth->config(['loginRedirect' => ['controller' => 'roles', 'action' =>
										'index']]);

								if (!$check_action_plugin_curent)
								{
										return $this->redirect($this->Auth->redirectUrl());
								}
						}
				}

		}
