<?php
$mc = new Memcached();
$mc->addServer('memcached', 11211);

// 1) Stats générales
echo "<h2>Stats générales</h2>";
echo "<pre>";
print_r($mc->getStats());
echo "</pre>";

// 2) Tous les keys (si supporté)
if (method_exists($mc, 'getAllKeys')) {
    echo "<h2>Toutes les clés</h2><ul>";
    $keys = $mc->getAllKeys();
    foreach ($keys as $key) {
        $val = $mc->get($key);
        echo "<li><strong>{$key}</strong>: " . htmlspecialchars(var_export($val, true)) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>getAllKeys() non supporté par cette version de l’extension.</p>";
}
