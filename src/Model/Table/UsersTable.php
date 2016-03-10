<?php

		namespace AclManager\Model\Table;

		use AclManager\Model\Entity\User;
		use Cake\ORM\Query;
		use Cake\ORM\RulesChecker;
		use Cake\ORM\Table;
		use Cake\Validation\Validator;

		/**
		 * Users Model
		 */
		class UsersTable extends Table
		{

				/**
				 * Initialize method
				 *
				 * @param array $config The configuration for the Table.
				 * @return void
				 */
				public function initialize(array $config)
				{
						$this->table('users');
						$this->displayField('name');
						$this->primaryKey('id');
						$this->addBehavior('Timestamp');
						$this->belongsTo('Roles', ['foreignKey' => 'role_id', 'className' =>
								'AclManager.Roles']);
						$this->hasMany('Posts', ['foreignKey' => 'user_id', 'className' =>
								'AclManager.Posts']);
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

						$validator->allowEmpty('name');

						$validator->allowEmpty('username');

						$validator->allowEmpty('password');

						$validator->add('lastlogin', 'valid', ['rule' => 'datetime'])->allowEmpty('lastlogin');

						$validator->allowEmpty('image');

						return $validator;
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
						$rules->add($rules->isUnique(['username']));
						$rules->add($rules->existsIn(['role_id'], 'Roles', ['Role is wrong']));
						return $rules;
				}
		}
