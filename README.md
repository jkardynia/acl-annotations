acl-annotations
===================
[![Build Status](https://api.travis-ci.org/jkardynia/acl-annotations.png?branch=master)](https://travis-ci.org/jjarekk/acl-annotations)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jkardynia/acl-annotations/badges/quality-score.png?s=96cf6db627fd33471fb56316fba96877ada46906)](https://scrutinizer-ci.com/g/jkardynia/acl-annotations/)
[![Code Coverage](https://scrutinizer-ci.com/g/jkardynia/acl-annotations/badges/coverage.png?s=c1fe16594af21a34a8256fdb93364760ddb37cb3)](https://scrutinizer-ci.com/g/jkardynia/acl-annotations/)


Annotations for ACL with Zend Framework 2.
This package allows you to write ACL rules just in your Zend controllers using annotations.

Installation
============
You can install it via composer. Just add this to your dependencies in composer.json:


    "jkardynia/acl-annot": "*"


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
There is not many changes. I only imported ACL annotation class by *use* keyword and use this annotations.
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
    /**
     * @var \Zend\Permissions\Acl\Acl 
     */
    private $acl = null;

    public function init(\Zend\ModuleManager\ModuleManager $m){
        $event = $m->getEventManager()->getSharedManager();
        
        $event->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP, function (MvcEvent $e){
            $collector = new AclItemsCollector();
        
            $collector->getAcl()->addRole('guest');
            $collector->getAcl()->addRole('admin', 'guest');
            $collector->addEntriesFromResourceClass('Album\Controller\AlbumController');
            $this->acl = $collector->getAcl();
        });
    }

    // other stuff
}
```

Now we have list of access controlls and we can use it to check access for current user.
Notice that I added private field \Zend\Permissions\Acl\Acl $acl which is initialized in ACL initialization callback.
We will need this later.

Checking access
---------------
Checking if user has access to our controller is main purpose for ACL system. We should check
access in ActionController dispach. To do that, first we must add new event subscriber (callback) in Module.

```php

class Module implements  AutoloaderProviderInterface, ConfigProviderInterface
{
    //some initialization

    public function onBootstrap(MvcEvent $e){
        $eventManager = $e->getApplication()->getEventManager();
        
        $eventManager->attach(MvcEvent::EVENT_ROUTE, function(MvcEvent $e){
        
            $application = $e->getApplication();
            $sm = $application->getServiceManager();
            $sharedManager = $application->getEventManager()->getSharedManager();
            $router = $sm->get('router');
            $request = $sm->get('request');
            $matchedRoute = $router->match($request);

            if (null !== $matchedRoute) {
                $acl = $this->acl;
                
                $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, function($event) use ($sm, $acl) {
                    $userRole =  new \Zend\Permissions\Acl\Role\GenericRole('guest');

                    try{
                        $sm->get('ControllerPluginManager')->get('Acl', $acl)->checkAccess($event, $userRole);
                    }catch(AccessDeniedException $e){

                        $event->getTarget()->plugin('redirect')->toUrl('access-denied');
                        return false;
                    }
                });
            }
        });
    }

    //other stuff
}
```


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

There we get Acl plugin from ControllerPluginManager and use it to check access. If access is denied exception will be thrown so
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

Performance
=========
To be honest annotations are slow and if you want to use them in real applications you should
turn on caching. Fortunately there is nice caching system proveded by Doctrine Annotations which
I use in this project. Thanks of Doctrine dvelopers you can use one of provided cache strategies, 
for example: APC, Memcache, Files etc.
You can easly configure cache in this ACL annotations package, right from your Module. All you need
to do is initialize AclItemsCollector with Reader that supports caching. This is example for filesystem cache:

```php
<?php
namespace Album;

use Zend\Mvc\MvcEvent;
use \jkardynia\Zend\Permissions\Acl\AclItemsCollector;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use \Zend\Permissions\Acl\Acl;
use \jkardynia\Annotations\Permissions\Acl\Parser\AclParser;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\CachedReader;
use \Doctrine\Common\Cache\FilesystemCache;

class Module implements  AutoloaderProviderInterface, ConfigProviderInterface
{
    /**
     * @var \Zend\Permissions\Acl\Acl 
     */
    private $acl = null;

    public function init(\Zend\ModuleManager\ModuleManager $m){
        $event = $m->getEventManager()->getSharedManager();
        
        $event->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP, function (MvcEvent $e){
            $parser = new AclParser(new CachedReader(
                new AnnotationReader(),
                new FilesystemCache("/path/to/cache"),
                $debug = true
            ));

            $collector = new AclItemsCollector(new Acl(), $parser);
        
            $collector->getAcl()->addRole('guest');
            $collector->getAcl()->addRole('admin', 'guest');
            $collector->addEntriesFromResourceClass('Album\Controller\AlbumController');
            $this->acl = $collector->getAcl();
        });
    }

    // other stuff
}
```

You can find more information about annotation cache in great [Doctrine Annotations documentation] (http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html#setup-and-configuration).

Final thoughts
=========
This is very basic implementation of ACL annotations package and there is still a lot of thing to do
but it could be used in developement environment. Feel free to involve. :)


TODO
=========
There is a lot of things still to do. To mention the most important:
- using more than one acl annotaion for one action
- more flexible addition of resource class
- defining privileges
