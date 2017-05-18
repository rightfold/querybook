<?php
namespace QueryBook\Web;

use Symfony\Component\HttpFoundation\{Request, Response};

interface Handler {
  public function handle(Request $request, array $params): Response;
}
