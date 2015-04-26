# phpTreeBasedRouter
The main objective for this Router is to organize the possible routes in a hierarchical way 
although you are free to implement a different organization/data structure such as arrays with one or multiple
dimensions.

# Brief summary of Objects/Classes
Here are some objects that will help you initialize your router:

##### Route
`use MinusFour\Router\Route;`

This object will hold the route, the name of the route and the actions per each method (which can be HTTP Methods, 
but there's really no restriction for that).

##### Action
`use MinusFour\Router\Action;`

This object holds the Class and Method to be called plus any fixed arguments to passed along.

##### RouteContainer
`use MinusFour\Router\TreeRouteContainer;`

You'll basically load your route objects into the router container. This object will match a given path to a route
object.

##### Router
`use MinusFour\Router\Router;`

Once you are finished done loading objects into the RouteContainer, the Router will pick up the route object and call
the action according to the supplied method. This object is also responsible for building a path for a given route,
which at the moment is unfinished.

# How to use

Start by including the autoloader and setting up the classes:
```php
include 'src/Autload.php'

use MinusFour\Router\Action;
use MinusFour\Router\Route;
use MinusFour\Router\TreeRouteContainer;
use MinusFour\Router\Router;
```

This is how you create a route object:
```php
$route = new Route('nameOfRoute', '/'); //Route for '/'
```

This is how you assign an action to said route object:
```php
$route->setMethodAction('GET', new Action('MyClass', 'MyMethod'));
//Router will call method `MyMethod` from Object `MyClass` when the '/' route is matched and GET method is called.
```

You must then initialize the RouteContainer object and add it.
```php
$routeContainer = new TreeRouteContainer();
$routeContainer->addRoute($route);
//Loads the route object into the tree.
```

Finall you'll initialize the Router Object with `$routeContainer` and at that point is up to you to supply a path
to match a Route object.

```php
$router = new Router($routeContainer);
$route->dispatch($_SERVER['REQUEST_METHOD'], strtok($_SERVER['REQUEST_URI'], '?'));
//This uses the HTTP method and the HTTP URI as arguments for the router.
//I.e. GET /home?section=1
//Will use GET as the Method and /home for path.
```

# Regular Expresions in Routes

It is possible to use regular expressions in some parts of the route. For example:

```php
$route = new Route('regex_route_example', '/section:(home|news)');
```

It will match /home or /news.

It's possible to mix in static elements as well.

```php
$route = new Route('regex_route_example_2', '/section/name:(home|news)');
```

Will match /section/home o /section/news.

# Warnings about path elements using regular expressions

#### General Note

You can have multiple expressions in one path, but keep in mind that the elements of the path are being tested
not the path itself.

#### No slash

You shouldn't use a slash in your expressions.

```php
$route = new Route('regex_route_bad_example', '/section:(home/news|home/forums)');
```

Will not work. It will create static childrens for news|home, forums) and the first element will be tested for (home.

#### Multiple matches restriction

If you have expressions that match the same thing only one of them will be matched. 
Let say for an instance that you have routes like this:

```php
$route = new Route('regex_common_route', '/route:\w+'); //Matches alphanumerical characters.
$route2 = new Route('regex_common_route_2', '/route2:\d+'); //Matches numbers.
```

And the path to be matched is: /123456. Only the first one will be matched, it will not even attempt to try out
the second expression as there's already one that fits plus there's no way the Router could know the other one
is the valid one, even though it's a more restrictive expression.

## HOWEVER

If the matched expresion fails to continue down its hierarchical path, it will attempt to use any other expression
available.

```php
$route = new Route('regex_common_route', '/route:\w+'); //Matches alphanumerical characters.
$route2 = new Route('regex_common_route_2', '/route2:\d+\static'); //Matches numbers.
```

It will behave like this:

```
/123456/static -> regex_common_route_2
/123456 -> regex_common_route
```

# About 404 Error Codes

When the router fails to match a Route, it will throw a `RouteNotFoundException`. If it fails to find an action for
the specified method, it will throw a `MethodNotFoundException`. Both can be caught under a `NotFoundException`.

`RouteNotFoundException` and `MethodNotFoundException` live under `MinusFour/Router` while `NotFoundException`
is under `MinusFour\Utils`.
