<?php

		namespace AclManager\Model\Table;

		use AclManager\Model\Entity\Role;
		use Cake\ORM\Query;
		use Cake\ORM\RulesChecker;
		use Cake\ORM\Table;
		use Cake\Validation\Validator;

		/**
		 * Roles Model
		 */
		class RolesTable extends Table
		{

				/**
				 * Initialize method
				 *
				 * @param array $config The configuration for the Table.
				 * @return void
				 */
				public function initialize(array $config)
				{
						$this->table('roles');
						$this->displayField('id');
						$this->primaryKey('id');
						$this->belongsToMany('Acos', ['foreignKey' => 'role_id', 'targetForeignKey' =>
								'aco_id', 'joinTable' => 'acos_roles', 'className' => 'AclManager.Acos']);

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

						$validator->requirePresence('role', 'create')->notEmpty('role');

						$validator //** add unique for validation role **//
								->add('role', ['unique' => ['rule' => 'validateUnique', 'provider' => 'table',
								'message' => 'Not unique']]);

						return $validator;
				}
		}
