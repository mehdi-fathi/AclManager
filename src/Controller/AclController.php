<?php

		namespace AclManager\Controller;

		use AclManager\Controller\AppController;
		use App\Controller\ContestsController;
		use Cake\Controller\Controller;
		use Cake\Core\App;
		use Cake\Filesystem\Folder;
		use Cake\Filesystem\File;

		class AclController extends AppController
		{
			public function index(){

				$this->viewBuilder()->layout( 'Admin/admin');

				$this->loadModel('Roles');

				$roles=$this->Roles->find('all');

				$this->set(compact('roles'));

				$path = App::path('Controller' . (empty($prefix) ? '' : DS . Inflector::camelize($prefix)));
				$dir = new Folder($path[0]);
				$controllers = $dir->find('.*Controller\.php');

				debug($controllers);


				$contents = \App\Controller\ContestsController::$actions;

				foreach ($controllers as $file) {


					$nameController=str_replace('.php','',$file);
					if($nameController<>'AppController'){

					$controlller="\App\Controller".DS.$nameController  ;

						debug($controlller);


						if(property_exists($controlller, 'actions')){

					@$contents =[
						'controller'=>[$nameController=>['action' =>$controlller::$actions
							]]] ;
					// $file->write('I am overwriting the contents of this file');
					// $file->append('I am adding to the bottom of this file.');
					// $file->delete(); // I am deleting this file
					debug($contents);

						}


					}
				}


			}

		}
