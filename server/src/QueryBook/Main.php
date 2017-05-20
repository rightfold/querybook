<?php
namespace QueryBook;

use FastRoute\{Dispatcher, RouteCollector};
use QueryBook\Web;
use Symfony\Component\HttpFoundation\{Request, Response};

use function FastRoute\simpleDispatcher;

final class Main {
  private function __construct() {
  }

  public static function main(): void {
    $dispatcher = simpleDispatcher(function(RouteCollector $r) {
      self::setup($r);
    });
    $req = Request::createFromGlobals();
    $route = $dispatcher->dispatch($req->getMethod(), $req->getPathInfo());
    switch ($route[0]) {
      case Dispatcher::NOT_FOUND:
        $response = Response::create('Not Found', 404);
        break;
      case Dispatcher::METHOD_NOT_ALLOWED:
        $response = Response::create('Method Not Allowed', 405);
        break;
      case Dispatcher::FOUND:
        $response = $route[1]->handle($req, $route[2]);
        break;
    }
    $response->send();
  }

  private static function setup(RouteCollector $r) {
    $db = pg_connect('');

    $webQueryExecute = new Web\Queries\Execute($db);

    $r->addRoute('GET', '/queries/{id}/execute', $webQueryExecute);
  }
}
