<?php

		namespace AclManager\Model\Table;

		use AclManager\Model\Entity\Aco;
		use Cake\ORM\Query;
		use Cake\ORM\RulesChecker;
		use Cake\ORM\Table;
		use Cake\Validation\Validator;

		/**
		 * Acos Model
		 */
		class AcosTable extends Table
		{

				/**
				 * Initialize method
				 *
				 * @param array $config The configuration for the Table.
				 * @return void
				 */
				public function initialize(array $config)
				{
						$this->table('acos');
						$this->displayField('id');
						$this->primaryKey('id');
						$this->addBehavior('Tree');
						$this->belongsTo('ParentAcos', ['className' => 'AclManager.Acos', 'foreignKey' =>
								'parent_id']);
						$this->hasMany('ChildAcos', ['className' => 'AclManager.Acos', 'foreignKey' =>
								'parent_id']);
						$this->belongsToMany('Roles', ['foreignKey' => 'aco_id', 'targetForeignKey' =>
								'role_id', 'joinTable' => 'acos_roles', 'className' => 'AclManager.Roles']);

						$this->addBehavior('Tree');
				}

				/**
				 * Default validation rules.
				 *
				 * @param \Cake\Validation\Validator $validator Validator instance.
				 * @return \Cake\Validation\Validator
				 */
				public function validationDefault(Validator $validator)
				{
						$validator->add('id', 'valid', ['rule' => 'numeric'])->allowEmpty('id', 'create');

						$validator->allowEmpty('model');

						$validator->add('foreign_key', 'valid', ['rule' => 'numeric'])->allowEmpty('foreign_key');

						$validator->allowEmpty('alias');

						$validator->add('lft', 'valid', ['rule' => 'numeric'])->allowEmpty('lft');

						$validator->add('rght', 'valid', ['rule' => 'numeric'])->allowEmpty('rght');

						$validator //** add unique for validation role **//
								->add('role', ['unique' => ['rule' => 'validateUnique', 'provider' => 'table',
								'message' => 'Is Not unique']]);

						return $validator;
				}
				public function validationUpdate($validator)
				{
						$validator //** add unique for validation role **//
								->add('alias', ['unique' => ['rule' => 'validateUnique', 'provider' => 'table',
								'message' => 'Is Not unique']]);

						return $validator;
				}
				public function validationUniquechildren($validator)
				{
						$validator->add('alias', ['uniquechildren' => ['rule' => 'uniquechildren',
								'provider' => 'table', 'message' => 'Not unique']]);

						return $validator;
				}
				public function uniquechildren($value, array $context)
				{

						$descendants = $this->find('children', ['for' => $context['data']['parent_id']]);

						foreach ($descendants->toArray() as $key => $all_actions):

								if ($all_actions['alias'] == $value)
								{

										return false;

								}
						endforeach;

						return true;
				}

				public function uniquecontrollerplugin($value, array $context)
				{

						debug($context['data']['parent_id']);

						$descendants = $this->find('children', ['for' => $context['data']['parent_id']]);

						foreach ($descendants->toArray() as $key => $all_actions):

								debug($all_actions['alias']);

								if ($all_actions['alias'] == $value[1])
								{

										return false;

								}
						endforeach;

						return true;

				}
				public function isUnique($value, array $context)
				{

						// debug($value);

						if (is_array($value))
						{ //** if we have command :create  **//

								$node = $this->findByAlias($value[0])->where(['id' => $context['data']['parent_id']])->
										find('all');

								if (empty($node->first()['id']))
										return false;

								$node_controller = $node->first()->toArray();

								$descendants = $this->find('children', ['for' => $node_controller['id']]);

								foreach ($descendants->toArray() as $key => $all_actions):

										if ($all_actions['alias'] == $value[1])
										{

												return false;

										}
								endforeach;

								return true;

						} else //** if we have command :root **//
						{

								$node = $this->findByAlias($value)->where(['parent_id' => 0])->find('all');

								if (!empty($node->first()['id']))
								{

										return false;
								} else
								{
										return true;
								}

						}

				}
				/**
				 * Returns a rules checker object that will be used for validating
				 * application integrity.
				 *
				 * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
				 * @return \Cake\ORM\RulesChecker
				 */
				public function buildRules(RulesChecker $rules)
				{
						$rules->add($rules->existsIn(['parent_id'], 'ParentAcos'));
						return $rules;
				}
		}
