# AclManager
Plugin to create Acl for CakePHP 3

Note :

This is a stable plugin for CakePHP 3.0 at this time.This is like Acl on cakephp 2 but has a little different.

you can by use of AclManager create access level for all users.also this is for access level plugin and controller

# AclManager plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require acl-manager/acl
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
acos create controller Users index
```
You can see all commands on the help of shell.

## Note

 If you have prefix you must will enter prefix_action for example : admin_index that is like Cakephp 2
 
```php
acos create controller Resellers admin_index
```

## Step 3

### Controller

you must write this code on method isAuthorized Appcontroller :

```php
	public function isAuthorized($user)
	{
		if (empty($this->request->params['plugin']))
		{
			$AclManager = $this->loadComponent('AclManager.Check');
			if (empty($this->request->params['plugin']))
			{

				$check_action_curent = $AclManager->Check_request('controller', $this->Auth->
				allowedActions);

				if (!$check_action_curent)
				{
					return false;
				}else {
					return true;
				}

			}
		}
	}
```
### plugin

you must write this code on method isAuthorized Appcontroller on your plugin :

### Make Grade

Also you can create Grade via your controller for example :
 
 ```php
     $Grade = $this->loadComponent('AclManager.Grade');
                                
     $Grade->gradecontroller('user','users','admin_edit'); //** $Grade->gradecontroller(name of roll,controller,action);  **// 
  ```

# Note

You must change method beforefilter on other controller for example :

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


