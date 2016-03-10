<?php

		/**
		 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
		 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
		 *
		 * Licensed under The MIT License
		 * For full copyright and license information, please see the LICENSE.txt
		 * Redistributions of files must retain the above copyright notice.
		 *
		 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
		 * @link          http://cakephp.org CakePHP(tm) Project
		 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
		 */
		namespace AclManager\Shell;
		use Acl\Controller\Component\AclComponent;
		use Cake\Console\Shell;
		use Cake\Controller\ComponentRegistry;
		use Cake\Controller\Controller;
		use Cake\Core\App;
		use Cake\Core\Exception\Exception;
		use Cake\Core\Configure;
		use Cake\Utility\Hash;
		use AclManager\Shell\ConsoleOptionParser;

		class AclShell extends Shell
		{
				public function startup()
				{
						parent::startup();
				}
				/*************************************************
				*  initialize method
				*  
				*  this method is behaiver cakephp this always run before run other mehtods 
				*
				*  goals : 1. load models
				*         
				**************************************************/
				public function initialize()
				{
						parent::initialize();
						$Roles = $this->loadModel('AclManager.Roles');
						$Acos = $this->loadModel('AclManager.Acos');
						$AcosRoles = $this->loadModel('AclManager.AcosRoles');
				}
                /*************************************************
				*  show method
				*  
				*  goals : 1. show all children
				*         
				**************************************************/
				public function show($input)
				{
						$children_type = $this->Acos->findByAlias($input)->where(['parent_id IS NULL'])->
								find('all')->first();

						if (empty($children_type))
						{
								return $this->out('dosent Find acos.');
						}                       
						$node = $children_type->toArray();
						$parent = $this->Acos->find('children', ['for' => $node['id'], 'spacer' => ' ',
								'valuePath' => 'id', ]);


						foreach ($parent as $categoryName)
						{
								echo $categoryName['alias'] . "\n";
						}
				}
				/*************************************************
				*  acos_save_controller_plugin method
				*  
				*  goals : 1. save alias controller for a plugin
				*         
				**************************************************/
				private function acos_save_controller_plugin($alias, $parent_id = null, $message)
				{
						$acos = $this->Acos->newEntity();

						$data = array('alias' => $alias, 'parent_id' => $parent_id);

						$acos = $this->Acos->patchEntity($acos, $data, ['validate' => 'Uniquechildren']); //** i add a validation just for this method **//

						if ($this->Acos->save($acos))
						{
								return $this->out($message);
						} else
						{
								$error = $acos->errors()['alias']['uniquechildren'];

								return $this->out("dosent Create new acos becuase of $error .");
						}
				}
				/*************************************************
				*  _get_Node method
				*  
				*  goals : 1. find root plugin or controller
				*         
				**************************************************/
				protected function _get_Node($alias, $type = null)
				{
						if ($type === 'plugin' || $type === 'controller')
						{
								$node = $this->Acos->findByAlias($alias)->where(['parent_id IS NULL'])->find('all')->
										first();

						} else
						{
								$node = $this->Acos->findByAlias($alias)->find('all')->first();
						}
						if (isset($node))
								$node = $node->toArray();

						return $node;
				}
				/*************************************************
				*  _get_Node method
				*  
				*  goals : 1. find type
				*           
				*           2.find all children
				*           
				*           3.check level 2 (controller )& eqqual with alias
				*         
				**************************************************/
				protected function _get_Node_children_controller_for_root($type, $alias)
				{
						$children_type = $this->Acos->findByAlias($type)->where(['parent_id IS NULL'])->
								find('all');

						$node = $children_type->first()->toArray();

						$parent = $this->Acos->find('children', ['for' => $node['id']]);

						$controller_name = null;

						foreach ($parent as $parent): //** check level 2 & eqqual with alias **//
								if ($parent['alias'] == $alias && $parent['parent_id'] == $node['id'])
								{
										$controller_name = $parent;
								}
						endforeach;

						return $controller_name;
				}
				/*************************************************
				*  _get_Node_children_plugin_for_root method
				*  
				*  goals : 1. find type
				*           
				*           2.find all children
				*           
				*           3.check level 2 ( plugin)& eqqual with alias
				*         
				**************************************************/
				protected function _get_Node_children_plugin_for_root($type, $alias)
				{
						$children_type = $this->Acos->findByAlias($type)->where(['parent_id IS NULL'])->
								find('all');

						$node = $children_type->first()->toArray();

						$parent = $this->Acos->find('children', ['for' => $node['id']]);

						$plugin_name = null;

						foreach ($parent as $parent): //** check level 2 & eqqual with alias **//

								if ($parent['alias'] == $alias && $parent['parent_id'] == $node['id'])
								{
										$plugin_name = $parent;
								}

						endforeach;

						return $plugin_name;
				}
				/*************************************************
				*  _get_Node_children_plugin_for_controller method
				*  
				*  goals : 1. find type
				*           
				*           2.find all children
				*           
				*           3.check level 2 ( plugin)& eqqual with alias
				*
				*           4.check level 3 ( controller)& eqqual with alias
				*         
				**************************************************/
				protected function _get_Node_children_plugin_for_controller($type, $plugin, $controller)
				{
						$children_type = $this->Acos->findByAlias($type)->where(['parent_id IS NULL'])->
								find('all');

						$node = $children_type->first()->toArray();

						$parent = $this->Acos->find('children', ['for' => $node['id']]);

						$plugin_name = $controller_root_of_plugin = null;

						foreach ($parent as $parents): //** check level 2 & eqqual with alias **//
								if ($parents['alias'] == $plugin && $parents['parent_id'] == $node['id'])
								{
										$plugin_name = $parents;
								}
						endforeach;

						foreach ($parent as $parent1): //** check level 3 & eqqual with alias **//
								if ($parent1['alias'] == 'controller' && $parent1['parent_id'] == $plugin_name['id'])
								{
										$controller_root_of_plugin = $parent1;
								}
						endforeach;

						return $controller_root_of_plugin;
				}
				/*************************************************
				*  _get_Node_children_plugin_for_action method
				*  
				*  goals : 1. find type
				*           
				*           2.find all children
				*           
				*           3.check level 2 ( plugin)& eqqual with alias
				*
				*           4.check level 3 ( controller)& eqqual with alias
				*
				*           5.check level 4 ( action)& eqqual with alias
				*         
				**************************************************/
				protected function _get_Node_children_plugin_for_action($type, $plugin, $controller)
				{
						$children_type = $this->Acos->findByAlias($type)->where(['parent_id IS NULL'])->
								find('all');

						$node = $children_type->first()->toArray();

						$parent = $this->Acos->find('children', ['for' => $node['id']]);

						$plugin_name = $controller_root_of_plugin = null;

						foreach ($parent as $parents): //** check level 2 & eqqual with alias **//
								if ($parents['alias'] == $plugin && $parents['parent_id'] == $node['id'])
								{
										$plugin_name = $parents;
								}
						endforeach;

						foreach ($parent as $parent1): //** check level 3 & eqqual with alias **//
								if ($parent1['alias'] == 'controller' && $parent1['parent_id'] == $plugin_name['id'])
								{
										$controller_root_of_plugin = $parent1;
								}
						endforeach;

						foreach ($parent as $parent2): //** check level 4 & eqqual with alias **//
								if ($parent2['alias'] == $controller && $parent2['parent_id'] == $controller_root_of_plugin['id'])
								{
										$controller_root_of_plugin = $parent2;
								}
						endforeach;

						return $controller_root_of_plugin;
				}
				/*************************************************
				*  acos_save_root method
				*  
				*  goals : 1. save root
				*         
				**************************************************/
				private function acos_save_root($alias, $parent_id, $message)
				{
						$acos = $this->Acos->newEntity();

						$data = array('alias' => $alias);

						$acos = $this->Acos->patchEntity($acos, $data, ['validate' => 'update']); //** i add anouther validation just for this method **//

						if ($this->Acos->save($acos))
						{
								return $this->out($message);
						} else
						{
								$error = $acos->errors()['alias']['unique'];

								return $this->out("dosent Create new acos becuase of $error .");
						}
				}
				/*************************************************
				*  role method
				*  
				*  goals : 1. save role for example :create Admin admin
				*         
				**************************************************/
				public function role($role_input, $alias)
				{
						$role = $this->Roles->newEntity();

						$data = array('role' => $role_input, 'alias' => $alias);

						$role = $this->Roles->patchEntity($role, $data);

						if ($this->Roles->save($role))
						{
								return $this->out('Create new role.');
						} else
						{
								$s = $role->errors()['role']['unique'];

								return $this->out("dosen't Create new role becuase of $s.");
						}
				}

				/*************************************************
				*  acos method 
				*  
				*  goals : 1.this method is for create root for example : create root plugin || controller 
				*
				*          2.this method is for create root (name of plugin) for plugin as such as : create plugin AclManager 
				*
				*          3.this method is for create root (name of controller) for plugin as such as : create controller users 
				*
				*          4.this method is for create root (action of controller) for plugin as such as : create controller users index
				*         
				**************************************************/
				public function acos($create, $parent, $name_of_root, $action = null)
				{
						if ($create === 'create' && $parent === 'root' && $name_of_root === 'controller' &&
								empty($action))
						{
								/*
								for example : create root controller
								*/
								//** when we are going to  create new root for example controller , plugins //**
								$message = "Create new root by name controller";

								$this->acos_save_root($name_of_root, null, $message);

						} elseif ($create === 'create' && $parent === 'controller' && $name_of_root !==
						'controller' && $name_of_root !== 'plugin' && empty($action))
						{
								/*
								for example : create controller users
								*/
								$node = $this->_get_Node($parent);

								$message = "Create new childe by name $name_of_root for parent $parent";

								$this->acos_save_controller_plugin($name_of_root, $node['id'], $message);

						} elseif ($create === 'create' && $parent === 'controller' && $name_of_root !==
						'controller' && $name_of_root !== 'plugin' && !empty($action))
						{
								/*
								for example : create controller users index
								*/
								$node = $this->_get_Node_children_controller_for_root('controller', $name_of_root);
                                
                                if(empty($node))
                                return $this->out('Dont found ');

								$message = "Create new childe by name $action for parent $name_of_root";

								$this->acos_save_controller_plugin($action, $node['id'], $message);
						} elseif ($create === 'create' && $parent === 'root' && $name_of_root === 'plugin' && empty
						($action))
						{
								/*
								for example : create root plugin
								*/
								$message = "Create new root by name plugin";

								$this->acos_save_root($name_of_root, null, $message);

						} elseif ($create === 'create' && $parent === 'plugin' && $name_of_root !== 'controller' &&
						$name_of_root !== 'plugin' && empty($action))
						{
								/*
								for example : create plugin AclManager
								*/
								$node = $this->_get_Node($parent);                              

								$message = "Create new childe by name $name_of_root for parent $parent";

								$this->acos_save_controller_plugin($name_of_root, $node['id'], $message);

						} else
						{
								return $this->out('Your command is wrong.You can see help of Shell');
						}
                        
				}
				/*************************************************
				*  acosplugin method
				*  
				*  goals : 1.this method is for create node for example : create plugin AclManager root users 
				*
				*          2.this method is for create node for example : create plugin AclManager controller users
				*
				*         
				**************************************************/
				public function acosplugin($create, $plugin, $name_of_plugin, $root_controller, $name_of_controller)
				{ //** create controller for plugin **//

						if ($create === 'create' && $plugin === 'plugin' && !empty($name_of_plugin) && $root_controller
								=== 'root' && !empty($name_of_controller))
								//** create plugin AclManager root users  **//
						{

								$plugin_parent = $this->_get_Node_children_plugin_for_root('plugin', $name_of_plugin);

								if (empty($plugin_parent))
										return $this->out('Dont found ');

								$message = "Create new root by name controller";

								$this->acos_save_controller_plugin($name_of_controller, $plugin_parent['id'], $message);

						} elseif ($create === 'create' && $plugin === 'plugin' && !empty($name_of_plugin) && $root_controller
						=== 'controller' && !empty($name_of_controller))
						{ //** create plugin AclManager controller users  **//

								$controller_parent = $this->_get_Node_children_plugin_for_controller('plugin', $name_of_plugin,
										$name_of_controller);

								if (empty($controller_parent))
										return $this->out('Dont found ');

								$message = "Create new controller of plugin by name controller $name_of_controller";

								$this->acos_save_controller_plugin($name_of_controller, $controller_parent['id'],
										$message);

						} elseif ($create === 'create' && $plugin === 'plugin' && !empty($name_of_plugin) && $root_controller
						=== 'action' && !empty($name_of_controller))
						{ //** create plugin AclManager action users/index  **//

								$Controller_Action = explode("/", $name_of_controller);

								$controller_parent = $this->_get_Node_children_plugin_for_action('plugin', $name_of_plugin,
										$Controller_Action[0]);

								if (empty($controller_parent))
										return $this->out('Dont found ');

								$message = "Create new controller of plugin by name controller $name_of_controller";

								$this->acos_save_controller_plugin($Controller_Action[1], $controller_parent['id'],
										$message);
						} else
						{
								return $this->out('Your command is wrong.You can see help of Shell');
						}
				}
				/*************************************************
				*  gradecontroller method
				*  
				*  goals : 1.this method is for create grade on table acosroles for example :gradecontroller user users index   
				*         
				**************************************************/
				public function gradecontroller($role = null, $controller = null, $action = null, $create =
						1, $read = 1, $update = 1, $delete = 1)
				{
						$parent_controller = $this->_get_Node('controller', 'controller');

						$parent_name_controller = $this->Acos->find('all', ['conditions' => ['Acos.parent_id' =>
								$parent_controller['id'], 'Acos.alias' => $controller]])->first();

						//	$selection = $this->in('Red or Green?', ['R', 'G'], 'R');<br />
						$aco = $this->Acos->find('all', ['contain' => ['ParentAcos'], 'conditions' => ['Acos.alias' =>
								$action, 'Acos.parent_id' => $parent_name_controller['id']]]);

						$aco->matching('ParentAcos', function ($q)use ($controller, $parent_controller)
						{
								return $q->where(['ParentAcos.alias' => $controller, 'ParentAcos.parent_id' => $parent_controller['id']]); }
						);

						$aco = $aco->first();

						if (empty($aco))
								return $this->out('dosent Find acos.');

						$role = $this->Roles->find('all', ['conditions' => ['Roles.role' => $role]]);

						$role = $role->first();

						if (empty($role))
								return $this->out('dosent Find role.');

						$AcosRoles = $this->AcosRoles->newEntity();

						$data = array(
								'role_id' => $role['id'],
								'aco_id' => $aco['id'],
								'_create' => $create,
								'_read' => $read,
								'_update' => $update,
								'_delete' => $delete);

						$AcosRoles = $this->AcosRoles->patchEntity($AcosRoles, $data);

						if ($this->AcosRoles->save($AcosRoles))
						{
								return $this->out('Create new acos.');
						} else
						{
								$errors = $AcosRoles->errors()['aco_id']['unique'];

								return $this->out("dosent Create new acos becuase of $errors .");
						}
				}
				/*************************************************
				*  gradeplugin method
				*  
				*  goals : 1.this method is for create grade to plugin on table acosroles for example :gradeplugin user AclManager users index   
				*         
				**************************************************/
				public function gradeplugin($role = null, $plugin = null, $controller = null, $action = null,
						$create = 1, $read = 1, $update = 1, $delete = 1)
				{

						$children_type = $this->Acos->findByAlias('plugin')->where(['parent_id IS NULL'])->
								find('all');

						$node = $children_type->first()->toArray();

						$parent = $this->Acos->find('children', ['for' => $node['id']]);

						$plugin_name = null;

						foreach ($parent as $parents):

								if ($parents['alias'] == $plugin && $parents['parent_id'] == $node['id'])
								{ //** check level 2 & eqqual with alias **//
										$plugin_name = $parents;
								}
						endforeach;

						if (empty($plugin_name))
								return $this->out("dosent Find $plugin_name.");

						foreach ($parent as $parent1): //** check level 3 & eqqual with alias **//

								if ($parent1['alias'] == 'controller' && $parent1['parent_id'] == $plugin_name['id'])
								{
										$check_controller = $parent1;
								}
						endforeach;

						if (empty($check_controller))
								return $this->out("dosent Find $check_controller.");

						foreach ($parent as $parent2): //** check level 4 & eqqual with alias **//

								if ($parent2['alias'] == $controller && $parent2['parent_id'] == $check_controller['id'])
								{
										$check_controller_name = $parent2;
								}
						endforeach;

						if (empty($check_controller_name))
								return $this->out("dosent Find $check_controller_name.");

						foreach ($parent as $parent3): //** check level 5 & eqqual with alias **//

								if ($parent3['alias'] == $action && $parent3['parent_id'] == $check_controller_name['id'])
								{
										$check_action = $parent2;
								}
						endforeach;

						if (empty($check_action))
								return $this->out("dosen't Find $check_action.");

						$role = $this->Roles->find('all', ['conditions' => ['Roles.role' => $role]]);

						$role = $role->first();

						if (empty($role))
								return $this->out("dosen't Find $role.");

						$AcosRoles = $this->AcosRoles->newEntity();

						$data = array(
								'role_id' => $role['id'],
								'aco_id' => $check_action['id'],
								'_create' => $create,
								'_read' => $read,
								'_update' => $update,
								'_delete' => $delete);

						$AcosRoles = $this->AcosRoles->patchEntity($AcosRoles, $data);

						if ($this->AcosRoles->save($AcosRoles))
						{
								return $this->out('Create new grade.');
						} else
						{
								$errors = $AcosRoles->errors()['aco_id']['unique'];

								return $this->out("dosen't Create new acos becuase of $errors .");
						}
				}
				/*
				
				mehtod getOptionParser
				
				this method is for view help for user.we have to write all command my shell this here.
				
				*/
				public function getOptionParser()
				{
						$parser = parent::getOptionParser();
						$type = ['choices' => ['aro', 'acoscontroller'], 'required' => true, 'help' => __d('cake_acl',
								'Type of node to create.')];
						$parser->description(__d('cake_acl', 'A console tool for managing the DbAcl'))->
								addSubcommand('role', ['help' => __d('cake_acl', 'Create a new role'), 'parser' => ['description' =>
								__d('cake_acl', 'Creates a new Role object '), ]])->addSubcommand('acos', ['help' =>
								__d('cake_acl', 'Create a new acos '), 'parser' => ['description' => __d('cake_acl',
								'You can create a new Acos object with command for example you can will :
                                
                                 1. for create root for example : create root plugin || controller 
				
				                 2. For create root (name of plugin) for plugin as such as : create plugin AclManager 
				    
				                 3. For create root (name of controller) for plugin as such as : create controller users 
				    
				                 4. For create root (action of controller) for plugin as such as : create controller users index
                                
                                '), ]])->addSubcommand('acosplugin', ['help' => __d('cake_acl',
								'Create a new acos'), 'parser' => ['description' => [__d('cake_acl',
								'1.For create node for example : create plugin AclManager root users 
				
				                 2.For create node for example : create plugin AclManager action users/index')], ]])->
								addSubcommand('gradecontroller', ['help' => __d('cake_acl',
								'Creates a new acos_roles for example : role controller action'), 'parser' => ['description' =>
								__d('cake_acl',
								'For create grade to Controller on table acosroles for example : user users index'), ]])->
								addSubcommand('gradeplugin', ['help' => __d('cake_acl',
								'Creates a new acos_roles for example : role plugin controller action '), 'parser' => ['description' =>
								__d('cake_acl',
								'For create grade to plugin on table acosroles for example : user AclManager users index '), ]])->
								addSubcommand('show', ['help' => __d('cake_acl',
								'Show all children'), 'parser' => ['description' =>
								__d('cake_acl',
								'For Show all children for example :show controller  '), ]]);

						return $parser;
				}
				public function main()
				{
						$this->out($this->OptionParser->help());
				}
		}
