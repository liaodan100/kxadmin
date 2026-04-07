<?php

namespace KxAdmin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Jiannei\Response\Laravel\Support\Facades\Response;
use KxAdmin\Response\ApiResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class Authenticate extends BaseMiddleware
{
    use ApiResponse;
    public function handle($request, Closure $next)
    {
        try {
            $this->checkForToken($request);
            if (Auth::guard('admin')->parseToken()->check()) {
                $request->attributes->add([
                    'user' => Auth::guard('admin')->user()
                ]);
                return $next($request);
            }
            $token = Auth::guard('admin')->parseToken()->refresh();
        } catch (UnauthorizedHttpException $e) {
            return $this->error([], '您未登陆:' . $e->getMessage(), 401, 401);
        } catch (JWTException $e) {
            return $this->error([], '请登陆:' . $e->getMessage(), 401, 401);

        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 401, 401);
        }
        return $this->setAuthenticationHeader($next($request), $token);
    }
}
