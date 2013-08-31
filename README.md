acl-annotations
===================
[![Build Status](https://api.travis-ci.org/jjarekk/acl-annotations.png?branch=master)](https://travis-ci.org/jjarekk/acl-annotations)

(If code coverage is under 75% build is failing).


Annotations for ACL with Zend Framework 2.
This package allows you to write ACL rules just in your Zend controllers using annotations.

Installation
============
Currently there is no composer repository for this package. You should simply download
sources and configure your autoloader library.

Usage
============
Using ACL annotations is very simple. All you need to do is to use @Acl annotation
to define access rule for user roles in place where you are defining your controllers.

Adding rules in controllers
---------------

On the snippnet below I will show you how to use ACL annotations with AlbumController - example form Zend Framework 2 tutorial
[Getting Started with Zend Framework 2 - Create the controller](http://framework.zend.com/manual/2.0/en/user-guide/routing-and-controllers.html#create-the-controller)

```php
<?php
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use \jkardynia\Annotations\Permissions\Acl\Acl;

class AlbumController extends AbstractActionController
{

    /**
     * @Acl("Allow", roles="admin, guest") 
     */
    public function indexAction()
    {
    }

    /**
     * @Acl("Allow", roles="admin") 
     */
    public function addAction()
    {
    }

    /**
     * @Acl("Allow", roles="admin") 
     */
    public function editAction()
    {
    }

    /**
     * @Acl("Allow", roles="admin") 
     */
    public function deleteAction()
    {
    }
}
```
There is not many changes. I only imported ACL annotation by *use* keyword and used this annotations.
As you can see I gave access only to indexAction for guest, and to all actions for admin.

This was defining ACL rules but before this will start working your module must collect all rules.

Building Access Controll List
---------------
ACL should be filled just at the begining, when aplication is initialized. With this in mind you should preapare
your module by attaching new closures to MvcEvent. You can do it like that:

```php
<?php
namespace Album;

use Zend\Mvc\MvcEvent;
use \jkardynia\Zend\Permissions\Acl\AclItemsCollector;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements  AutoloaderProviderInterface, ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'loadAcl'), 2);
    }
    
    public function loadAcl(MvcEvent $e){
        $collector = new AclItemsCollector();
        
        // adding some roles
        $collector->getAcl()->addRole('guest');
        $collector->getAcl()->addRole('admin', 'guest');

        // filing Access Controll List
        $collector->addEntriesFromResourceClass('Album\Controller\AlbumController');
    }

    // other stuff
}
```

Now we have list of accesses and we can use it to check access for current user.

Checking access
---------------
Checking if user has access to our controller is main purpose for ACL system. We should check
access after ActionController dispach. To do that first we must add new event in Module.

```php
//some stuff

class Module implements  AutoloaderProviderInterface, ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'loadAcl'), 2);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl'), 2);
    }
//other stuff
}
```

And then create callback **checkAcl**.

```php
//some stuff

class Module implements  AutoloaderProviderInterface, ConfigProviderInterface
{
    /**
     * @var \Zend\Permissions\Acl\Acl 
     */
    private $acl = null;

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'loadAcl'), 2);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl'), 2);
    }

    public function loadAcl(MvcEvent $e){
        // already written implementation

        $this->acl = $collector->getAcl();
    }

    public function checkAcl(MvcEvent $e){
        
        $application   = $e->getApplication();
        $sm = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();
     
        $router = $sm->get('router');
        $request = $sm->get('request');
     
        $matchedRoute = $router->match($request);
        if (null !== $matchedRoute) {
            $acl = $this->acl;
            $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController',  
                MvcEvent::EVENT_DISPATCH,
                function($event) use ($sm, $acl) {
                    $userRole =  new \Zend\Permissions\Acl\Role\GenericRole('guest');// you can get it from session
                    try{
                        $sm->get('ControllerPluginManager')->get('Acl', $acl)->checkAccess($event, $userRole);
                    }catch(AccessDeniedException $e){
                        
                        $event->getTarget()->plugin('redirect')->toUrl('access-denied');
                        return false;
                    }
                },
                2
            );
        }
    }
//other stuff
}
```

Notice that I added private field \Zend\Permissions\Acl\Acl $acl which is initialized in *loadAcl* callback.
It will be used by *checkAcl* callback.

What happens there? We are just attaching callback to proper event. The most important thing is:

```php
$userRole =  new \Zend\Permissions\Acl\Role\GenericRole('guest'); // you can get it from session

try{
    $sm->get('ControllerPluginManager')->get('Acl', $acl)->checkAccess($event, $userRole);
}catch(AccessDeniedException $e){

    $event->getTarget()->plugin('redirect')->toUrl('access-denied');
    return false;
}
```

There we get Acl plugin from ControllerPluginManager and use it to check access. If access is denied exception will be throw so
I catch it and do redirect to access-denied info page.

There is last thing to do. We must register Acl Plugin.

Registering ACL plugin
---------------
To register any new plugin in your module you must just add a line to your *module.config.php* file:

```php
'controller_plugins' => array(
    'invokables' => array(
        'Acl' => '\jkardynia\Zend\Controller\Plugin\Acl',
    )
)
```

Now everything is set - you can use annotations to provide and check access to your controllers :).

Final thoughts
=========
This is very basic implementation of ACL annotations package and there is still a lot of thing to do
but it could be used in developemnt environment. Feel free to involve. :)


TODO
=========
There is a lot of things still to do. To mention the most important:
- caching?
- using more than one acl annotaion for one action
- more flexible addition of resource class
- defining privileges