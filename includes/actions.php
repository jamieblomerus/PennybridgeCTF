<?php
namespace PBCTF;

class Actions {
    private static array $actions = [];

    public static function add_action(string $action, callable $callback) {
        self::$actions[$action][] = $callback;
    }

    public static function do_action(string $action, ...$args) {
        if (!isset(self::$actions[$action]) || !is_array(self::$actions[$action])) {
            return;
        }

        foreach (self::$actions[$action] as $callback) {
            call_user_func_array($callback, $args);
        }
    }
}