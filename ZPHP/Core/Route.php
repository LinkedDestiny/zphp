<?php
/**
 * author: shenzhe
 * Date: 13-6-17
 * route处理类
 */
namespace ZPHP\Core;
use ZPHP\Controller\IController,
    ZPHP\Core\Factory,
    ZPHP\Core\Config,
    ZPHP\View\IView;
use ZPHP\ZPHP;

class Route
{
    public static function route($server)
    {
        $action = Config::get('ctrl_path', 'ctrl') . '\\' . $server->getAction();
        $class = Factory::getInstance($action);
        if (!($class instanceof IController)) {
            throw new \Exception("ctrl error");
        }
        $class->setServer($server);
        $before = $class->_before();
        $view = $exception = null;
        if ($before) {
            try {
                $method = $server->getMethod();
                if (\method_exists($class, $method)) {
                    $view = $class->$method();
                } else {
                    throw new \Exception("no method {$method}");
                }
            } catch (\Exception $e) {
                $exception = $e;
            }
        }
        $class->_after();
        if ($exception !== null) {
            throw $exception;
        }
        return $server->display($view);
    }
}
