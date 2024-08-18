<?php
namespace Yohns\Core;

/**
 * Class Plugable
 *
 * This class provides functionality to manage plugins, hooks, and filters. It allows loading plugins,
 * adding/removing hooks, and executing hooks and filters.
 */
class Plugable {

	/**
	 * @var array|null $events Stores the events and their associated hooks.
	 */
	public static ?array $events = [];

	/**
	 * @var array|null $filter Stores the filters and their associated functions.
	 */
	public static ?array $filter = [];

	/**
	 * @var array|null $plugin Stores the loaded plugins' configurations.
	 */
	public static ?array $plugin = [];

	/**
	 * Loads the specified plugins and their configurations.
	 *
	 * @param array $plugins List of plugin names to load.
	 * @param string $dir directory where each plugins has its own dircetory by name.
	 * @return array|null Returns the loaded plugins' configurations or null if no plugins were loaded.
	 */
	public static function loadPlugins(array $plugins, string $dir=''): ?array {
		if (!empty($plugins)) {
			foreach ($plugins as $v) {
				$configFile = $dir . $v . '/config.json';
				if (is_file($configFile)) {
					self::$plugin[$v] = json_decode(file_get_contents($configFile), true);
				}
			}
		}
		return self::$plugin ?: null;
	}

	/**
	 * Adds a hook to a specified event.
	 *
	 * @param string $event The event name to which the hook should be added.
	 * @param callable $func The function that will be executed when the event is triggered.
	 * @return void
	 */
	public static function addHook(string $event, callable $func): void {
		self::$events[$event][] = $func;
	}

	/**
	 * Adds multiple hooks to their respective events.
	 *
	 * @param array $hooks An associative array where the key is the event name and the value is the function.
	 * @return void
	 */
	public static function addHooks(array $hooks): void {
		foreach ($hooks as $event => $func) {
			self::addHook($event, $func);
		}
	}

	/**
	 * Removes a hook from a specified event.
	 *
	 * @param string $event The event name from which the hook should be removed.
	 * @param callable $func The function to be removed from the event.
	 * @return void
	 */
	public static function removeHook(string $event, callable $func): void {
		if (isset(self::$events[$event])) {
			$key = array_search($func, self::$events[$event], true);
			if ($key !== false) {
				unset(self::$events[$event][$key]);
			}
		}
	}

	/**
	 * Executes the hooks associated with a specific event.
	 *
	 * @param string $hook_name The event name whose hooks are to be executed.
	 * @param mixed ...$args Additional arguments passed to the hook functions.
	 * @return mixed Returns the result of the executed hook function or null if no function is found.
	 * @throws \Exception If the hook function does not exist.
	 */
	public static function doHook(string $hook_name, mixed ...$args): mixed {
		if (isset(self::$events[$hook_name])) {
			foreach (self::$events[$hook_name] as $func) {
				if (is_callable($func)) {
					return $func(...$args);
				} else {
					throw new \Exception("$func does not exist in plugable class for $hook_name");
				}
			}
		}
		return null;
	}

	/**
	 * Adds a filter to a specified event.
	 *
	 * @param string $event The event name to which the filter should be added.
	 * @param mixed $array The data to be filtered.
	 * @return void
	 */
	public static function addFilter(string $event, mixed $array): void {
		self::$filter[$event][] = $array;
	}

	/**
	 * Executes the filter function for a specified event.
	 *
	 * @param string $event The event name whose filters are to be executed.
	 * @param callable $func The function that processes the filters.
	 * @return mixed Returns the result of the filter function or null if the function does not exist.
	 */
	public static function doFilter(string $event, callable $func): mixed {
		return isset(self::$filter[$event]) ? $func(self::$filter[$event]) : null;
	}
}
