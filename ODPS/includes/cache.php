<?php
class Cache {
    private $cache = array();

    public function get($key) {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }
        return false;
    }

    public function set($key, $value, $expiration = 3600) {
        $this->cache[$key] = array(
            'value' => $value,
            'expiration' => time() + $expiration
        );
        return true;
    }

    public function delete($key) {
        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);
            return true;
        }
        return false;
    }

    public function flush() {
        $this->cache = array();
        return true;
    }
}
?>