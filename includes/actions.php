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

class Filters {
    private static array $filters = [];

    public static function add_filter(string $filter, callable $callback, int $priority = 10) {
        self::$filters[$filter][$priority][] = $callback;
    }

    public static function apply_filters(string $filter, $value, ...$args) {
        if (!isset(self::$filters[$filter]) || !is_array(self::$filters[$filter])) {
            return $value;
        }

        ksort(self::$filters[$filter]);

        foreach (self::$filters[$filter] as $callbacks) {
            foreach ($callbacks as $callback) {
                $value = call_user_func_array($callback, array_merge([$value], $args));
            }
        }

        return $value;
    }
}