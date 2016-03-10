<?php

		namespace AclManager\Model\Table;

		use AclManager\Model\Entity\AcosRole;
		use Cake\ORM\Query;
		use Cake\ORM\RulesChecker;
		use Cake\ORM\Table;
		use Cake\Validation\Validator;

		/**
		 * AcosRoles Model
		 */
		class AcosRolesTable extends Table
		{

				/**
				 * Initialize method
				 *
				 * @param array $config The configuration for the Table.
				 * @return void
				 */
				public function initialize(array $config)
				{

						$this->table('acos_roles');
						$this->displayField('id');
						$this->primaryKey('id');
						$this->belongsTo('Acos', ['foreignKey' => 'aco_id', 'joinType' => 'INNER',
								'className' => 'AclManager.Acos']);
						$this->belongsTo('Roles', ['foreignKey' => 'role_id', 'joinType' => 'INNER',
								'className' => 'AclManager.Roles']);
						$this->hasMany('Users', ['foreignKey' => 'role_id', 'joinType' => 'INNER',
								'className' => 'AclManager.Users']);
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

						$validator->requirePresence('_create', 'create')->notEmpty('_create');

						$validator->requirePresence('_read', 'create')->notEmpty('_read');

						$validator->requirePresence('_update', 'create')->notEmpty('_update');

						$validator->requirePresence('_delete', 'create')->notEmpty('_delete');

						$validator //** add unique for validation role **//
								->add('role', ['unique' => ['rule' => 'validateUnique', 'provider' => 'table',
								'message' => 'Is Not unique']]);

						$validator //** add unique for validation role **//
								->add('aco_id', ['unique' => ['rule' => 'unique', 'provider' => 'table', 'message' =>
								'Is Not unique']]);

						return $validator;
				}
				public function unique($value, array $context)
				{
						$count = $this->findByAco_id($value)->where(['role_id' => $context['data']['role_id']])->
								count();

						if ($count > 0)
								return false;

						return true;
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

						$rules->add($rules->existsIn(['aco_id'], 'Acos'));
						$rules->add($rules->existsIn(['role_id'], 'Roles'));

						return $rules;
				}
		}
