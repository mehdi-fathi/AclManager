<?php

		namespace AclManager\Controller\Component;

		use Cake\Controller\Component;
		use Cake\Controller\ComponentRegistry;
		use Cake\Event\Event;
		use Cake\ORM\TableRegistry;

		/**
		 * Grade component
		 */
		class GradeComponent extends Component
		{

				public function initialize(array $config)
				{

						$this->AcosRoles = TableRegistry::get('AclManager.AcosRoles');

						$this->Acos = TableRegistry::get('AclManager.Acos');

						$this->Roles = TableRegistry::get('AclManager.Roles');
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

						$aco = $this->Acos->find('all', ['contain' => ['ParentAcos'], 'conditions' => ['Acos.alias' =>
								$action, 'Acos.parent_id' => $parent_name_controller['id']]]);

						$aco->matching('ParentAcos', function ($q)use ($controller, $parent_controller)
						{
								return $q->where(['ParentAcos.alias' => $controller, 'ParentAcos.parent_id' => $parent_controller['id']]); }
						);

						$aco = $aco->first();

						if (empty($aco))
								return false;

						$role = $this->Roles->find('all', ['conditions' => ['Roles.role' => $role]]);

						$role = $role->first();

						if (empty($role))
								return false;

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
								return true;
						} else
						{
								$errors = $AcosRoles->errors()['aco_id']['unique'];

								return $errors;
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

						foreach ($parent as $parents)://** check level 2 & eqqual with alias **//

								if ($parents['alias'] == $plugin && $parents['parent_id'] == $node['id'])
								{ 
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

						foreach ($parent as $parent2)://** check level 4 & eqqual with alias **//

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
								return false;//dosent find//

						$role = $this->Roles->find('all', ['conditions' => ['Roles.role' => $role]]);

						$role = $role->first();

						if (empty($role))
								return false;//dosent find//

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
									return true;
						} else
						{
								$errors = $AcosRoles->errors()['aco_id']['unique'];

							     return $errors;
						}
				}

		}
