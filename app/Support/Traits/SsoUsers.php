<?php
/**
 * Created by PhpStorm.
 * User: liujun
 * Date: 2016/11/23
 * Time: 下午3:43
 */
namespace App\Support\Traits;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;
use Auth;

trait SsoUsers
{
    /**
     * 生成用于sso登录的cookie
     */
    protected function generateSsoToken()
    {
        $user = Auth::user();
        $token = JWTAuth::fromUser($user);
        setcookie(
            config('sso.cookie_name'), $token, time() + 60 * config('sso.cookie_expires'),
            '/',  config('sso.cookie_domain')
        );
    }

    /**
     * 清除sso的cookie
     */
    protected function clearSsoToken()
    {
        setcookie(config('sso.cookie_name'), null);
    }


    /**
     * @return \Tymon\JWTAuth\Payload
     */
    protected function getSSoClaims()
    {
        $sso_token = array_get($_COOKIE, config('sso.cookie_name'), '');
        $manager = app('tymon.jwt.manager');
        return $manager->decode(new Token($sso_token));
    }

    protected function checkSsoToken()
    {
        try {
            $claims =  $this->getSSoClaims();
            return true;
        } catch (JWTException $e) {
            return false;
        }
    }
}