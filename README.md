# AclManager
Plugin to create Acl for CakePHP 3

Note :

This is a stable plugin for CakePHP 3.0 at this time.This is like Acl on cakephp 2 but has a little different.

you can by use of AclManager create access level for all users.also this is for access level plugin and controller

## Installing via composer

You can install this plugin into your CakePHP application using
[composer](http://getcomposer.org). For existing applications you can add the
following to your `composer.json` file:

```javascript
"require": {
	"mehdi-fathi/AclManager": "dev-master"
}
```

# configrations

## Step 1

In your `config\bootstrap.php`:
```php
Plugin::load('AclManager', ['bootstrap' => false, 'routes' => true]);
```
Create tables

To create ACL related tables, run the following Migrations command:
```php
bin/cake migrations migrate -p AclManager
```
## Step 2

You must create values of acos,roles,acos_roles.Well you can use of Shell Acl of plugin.
for example :
```php
acos create root plugin
```
```php
acos create controller users index
```
You can see all commands on the help of shell.

## Step 3

### Controller

you must write this code on method beforfilter Appcontroller :

```php
public function beforeFilter(Event $event)
				{
            if (empty($this->request->params['plugin']))
						{

								$auth = $this->request->session()->read('Auth.User.role_id');

								if (!empty($auth))
								{
										$AclManager = $this->loadComponent('AclManager.Check');

									if (empty($this->request->params['plugin']))
									{

										$check_action_curent = $AclManager->Check_request('controller', $this->Auth->
														allowedActions);

										$this->Auth->config(['loginRedirect' => ['controller' => 'users', 'action' =>
														'index']]);
														
										if (!$check_action_curent)											
											return $this->redirect($this->Auth->redirectUrl());


									}
								}
						}
				}
```
### plugin

you must write this code on method beforfilter Appcontroller on your plugin :

```php
public function beforeFilter(Event $event)
				{

						$this->Auth->allow(['delete']);

						$auth = $this->request->session()->read('Auth.User.role_id');

						if (!empty($auth))
						{
								$AclManager = $this->loadComponent('AclManager.Check');

								$check_action_plugin_curent = $AclManager->Check_plugin();

								$this->Auth->config(['loginRedirect' => ['controller' => 'roles', 'action' =>
										'index']]);

								if (!$check_action_plugin_curent)
										return $this->redirect($this->Auth->redirectUrl());
						}
				}
```

# Note

 you must change method beforefilter on other controller for example :

```php
	class UsersController extends AppController
		{
	public function beforeFilter(Event $event = null)
				{
                         $this->Auth->allow(['edit']);
						 parent::beforeFilter($event);//** we must have parent beforfilter **//
				}
		}
```
This code is for dosen't check action edit in plugin Acl.Thats why you allow edit on Auth.This plugin needs develope for will be complete

Enjoy!


