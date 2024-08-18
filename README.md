# Yohns\Core\Plugable

```php
<?php
use Yohns\Core\Plugable;

// Define the plugin directory
$pluginDir = __DIR__.'/pluggable/plugins/';

// Define the plugins to load
$plugins = ['Events', 'News', 'Pictures'];

// Load the plugins with the directory variable
$loadedPlugins = Plugable::loadPlugins($plugins, $pluginDir);

echo "<pre>Loaded Plugins:\n";
print_r($loadedPlugins);
echo "</pre>";

// Add hooks
Plugable::addHook('startup', function() {
	echo "Startup hook executed!<br>";
});

Plugable::addHook('shutdown', function() {
	echo "Shutdown hook executed!<br>";
});

// Execute the 'startup' event hooks
Plugable::doHook('startup');

// Add a filter
Plugable::addFilter('content_filter', ['content' => 'Original content']);

// Execute the filter for 'content_filter'
$result = Plugable::doFilter('content_filter', function(array $data) {
	// Modify the filtered data
	$data[0]['content'] = 'Filtered content';
	return $data;
});

echo "<pre>Filtered Data:\n";
print_r($result);
echo "</pre>";

// Execute the 'shutdown' event hooks
Plugable::doHook('shutdown');

```

```php
//--   loading plugins
foreach ($loadedPlugins as $k => $v) {
	if ($v['IncludeFiles'] != '') {
		if (is_array($v['IncludeFiles'])) {
			foreach ($v['IncludeFiles'] as $fi) {
				if (is_file($pluginDir . $k . '/' . $fi)) {
					include ($pluginDir . $k . '/' . $fi);
				} else {
					die($pluginDir . $k . '/' . $fi . ' file was not found');
				}
			}
		}
	}
	if ($v['Route'] != '') {
		$Routes[] = $v['Route'];
	}
}
```