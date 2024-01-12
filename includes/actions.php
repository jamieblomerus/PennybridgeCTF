<?php
namespace PBCTF;

/**
 * Class Actions
 *
 * @package PBCTF
 */
class Actions {

    /**
     * En array med alla actions som ska köras
     */
    private static array $actions = [];

    /**
     * Lägger till en action som ska köras
     * 
     * @param string $action Namnet på action som ska köras
     * @param callable $callback Callback-funktionen som ska köras
     * @return void
     */
    public static function add_action(string $action, callable $callback): void {
        self::$actions[$action][] = $callback;
    }

    /**
     * Kör alla inlagda actions för en viss action
     * 
     * @param string $action Namnet på action som ska köras
     * @param mixed ...$args Argument som ska skickas till callback-funktionen
     * @return void
     */
    public static function do_action(string $action, ...$args): void {
        if (!isset(self::$actions[$action]) || !is_array(self::$actions[$action])) {
            return;
        }

        foreach (self::$actions[$action] as $callback) {
            call_user_func_array($callback, $args);
        }
    }
}