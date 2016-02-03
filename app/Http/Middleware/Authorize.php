<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use Closure;
use Route;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        list($model, $action) = $this->getModelAndAction();

        $result = auth()->user()->can($action, $this->getArguments($model));
        
        if (!$result) {
            return ApiResponse::responseError(ErrorCode::NO_PERMISSION, 401);
        }

        return $next($request);
    }

    /**
     * Get arguments for policy checking - either model or route model binding
     * parameters
     *
     * @param string $model
     *
     * @return array
     */
    protected function getArguments($model)
    {
        $parameters = Route::getCurrentRoute()->parameters();

        if ($parameters) {
            return array_values($parameters);
        }

        return ['App\\Models\\' . $model];
    }

    /**
     * Get model and action name from current route
     *
     * @return array
     */
    protected function getModelAndAction()
    {
        $route = Route::getCurrentRoute();
        $namespace = $route->getAction()['namespace'];
        $name = $route->getActionName();

        // don't allow closures for routes protected by this middleware
        if (!str_contains($name, '@')) {
            throw new \LogicException('Closures for routes not allowed in this application');
        }

        $controller = ltrim(mb_substr($name, mb_strlen($namespace)), '\\');
        list($model, $action) = explode('@', $controller);

        if (ends_with($model, 'Controller')) {
            $model = mb_substr($model, 0, -mb_strlen('Controller'));
        }

        return [$model, $action];
    }
}
