<?php

		namespace AclManager\Controller\Component;

		use Cake\Controller\Component;
		use Cake\Controller\ComponentRegistry;

		use Cake\Event\Event;
		use Cake\ORM\TableRegistry;
		use \AclManager\Controller;
		use Cake\Core\Exception\Exception;

		/**
		 * Check component
		 */
		class CheckComponent extends Component
		{

				public function initialize(array $config)
				{

						$this->AcosRoles = TableRegistry::get('AclManager.AcosRoles');

						$this->Acos = TableRegistry::get('AclManager.Acos');
				}

				public function beforeFilter(Event $event)
				{

						debug($this->Auth->allowedActions);
				}

				public function Check_plugin()
				{
						$role_id_User = $this->request->session()->read('Auth.User.role_id');

						$plugin = $this->request->params['plugin'];

						$controller = $this->request->params['controller'];

						$action = $this->request->params['action'];

						$children_type = $this->Acos->findByAlias('plugin')->where(['parent_id IS NULL'])->
								find('all')->first();
						if (empty($children_type))
								return false;

						$node = $children_type->toArray();

						$parent = $this->Acos->find('children', ['for' => $node['id']]);
						$plugin_name = null;
						foreach ($parent as $parents):

								if ($parents['alias'] == $plugin && $parents['parent_id'] == $node['id'])
								{ //** check level 2 & eqqual with alias **//
										$plugin_name = $parents;
								}
						endforeach;

						if (empty($plugin_name))
								return false;

						foreach ($parent as $parent1):

								if ($parent1['alias'] == 'controller' && $parent1['parent_id'] == $plugin_name['id'])
								{ //** check level 2 & eqqual with alias **//
										$check_controller = $parent1;
								}
						endforeach;

						if (empty($check_controller))
								return false;

						foreach ($parent as $parent2):

								if ($parent2['alias'] == $controller && $parent2['parent_id'] == $check_controller['id'])
								{ //** check level 2 & eqqual with alias **//
										$check_controller_name = $parent2;
								}
						endforeach;

						if (empty($check_controller_name))
								return false;

						foreach ($parent as $parent3):

								if ($parent3['alias'] == $action && $parent3['parent_id'] == $check_controller_name['id'])
								{ //** check level 2 & eqqual with alias **//
										$check_action_name = $parent2;
								}
						endforeach;

						if (empty($check_action_name))
								return false;

						$AcosRoles = $this->AcosRoles->find('all', ['contain' => ['Roles', 'Acos' => ['ParentAcos']],
								'conditions' => ['Acos.alias' => $action, 'Acos.id' => $check_action_name['id'],
								'AcosRoles.role_id' => $role_id_User]]);

						$AcosRoles->matching('Acos.ParentAcos', function ($q)use ($controller)
						{
								return $q->where(['ParentAcos.alias' => $controller]); }
						);

						$AcosRoles = $AcosRoles->first();

						if (empty($AcosRoles))
								return false;

						return true;

				}
				public function Check_controller()
				{
						$Role_id_user = $this->request->session()->read('Auth.User.role_id');

						$Plugin = $this->request->params['plugin'];

						$Controller = $this->request->params['controller'];

						$Action = $this->request->params['action'];

						if (isset($this->request->params['prefix']))
								$Prefix = $this->request->params['prefix'];

						if (!empty($Role_id_user))
						{
								try
								{
										$curent_action = $Action;
										if (isset($Prefix))
												$curent_action = $Prefix . '_' . $Action;

										$AcosRoles = $this->AcosRoles->find('all', ['contain' => ['Roles', 'Acos' => ['ParentAcos']],
												'conditions' => ['Acos.alias' => $curent_action, 'AcosRoles.role_id' => $Role_id_user]]);

										$AcosRoles->matching('Acos.ParentAcos', function ($q)use ($Controller)
										{
												return $q->where(['ParentAcos.alias' => $Controller]); }
										);

										$query = $AcosRoles->first();

										if (empty($query['aco']['parent_aco']))
										{
												throw new Exception('false');
										}

										$id_parent = $query['aco']['parent_aco']->toArray()['parent_id'];

										$parent_acos = $this->Acos->findById($id_parent)->first();

										if (empty($parent_acos))
										{
												throw new Exception(false);
										}
										//debug($parent_acos->toArray());

										if (!empty($query))
										{
												//	debug($query->toArray());

												return $query->toArray();

										}
								}
								catch (exception $e)
								{

										return false;
								}
						}

				}

				public function Check_request($type, $allowed_actions)
				{

						if ($type == 'controller')
						{

								$check_action_curent = $this->Check_controller();

								if (isset($check_action_curent))
								{

										foreach ($allowed_actions as $allowed_actions):

												$current_action = strchr($check_action_curent['aco']['alias'], $this->request->
														params['action']);

												if (!$current_action)
												{

														$current_action = $this->request->params['action'];

												}

												if ($allowed_actions === $this->request->params['action'])
												{

														if ($check_action_curent['aco']['alias'] <> $allowed_actions)
														{

																return true;
														}
												}

										endforeach;

								}

								if (!$check_action_curent)
										return false;

								return true;

						} else
						{
								$check_action_plugin_curent = $this->Check_plugin();

								if (isset($check_action_plugin_curent))
								{

										foreach ($allowed_actions as $allowed_actions):

												$current_action = strchr($check_action_plugin_curent['aco']['alias'], $this->
														request->params['action']);

												if (!$current_action)
												{

														$current_action = $this->request->params['action'];

												}

												if ($allowed_actions === $this->request->params['action'])
												{

														if ($check_action_plugin_curent['aco']['alias'] <> $allowed_actions)
														{

																return false;
														}
												}

										endforeach;
								}
								if (!$check_action_plugin_curent)
										return false;

								return false;

						}

				}

		}
