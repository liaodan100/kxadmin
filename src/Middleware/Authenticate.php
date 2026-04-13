<?php

namespace KxAdmin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use KxAdmin\Response\ApiResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class Authenticate extends BaseMiddleware
{
    use ApiResponse;

    public function handle($request, Closure $next)
    {
        try {
            $this->checkForToken($request);
            $guard = Auth::guard('admin');

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($request);
            }

            $user = $guard->user();

            if (!$user) {
                return $this->error([], '未登录或登录已失效', 401, 401);
            }

            $request->setUserResolver(static fn () => $user);

            return $next($request);
        } catch (TokenExpiredException $e) {
            return $this->error([], '登录已过期', 401, 401);
        } catch (TokenInvalidException $e) {
            return $this->error([], 'Token 无效', 401, 401);
        } catch (JWTException $e) {
            return $this->error([], 'Token 缺失', 401, 401);
        } catch (\Throwable $e) {
            return $this->error([], $e->getMessage(), 401, 401);
        }
    }
}
