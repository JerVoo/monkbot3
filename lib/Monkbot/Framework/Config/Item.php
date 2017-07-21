<?php
namespace Monkbot\Framework\Config;

use Monkbot\Framework\Log;

class Item {
    private $data;

    public function __construct($data) {
        if(is_array($data)) {
            foreach($data as $key => $value) {
                $this->data[$key] = new Item($value);
            }
        }
        else if(is_string($data) || is_numeric($data)) {
            $this->data = $data;
        }
    }

    /**
     * @param string $path
     * @return int|string|Item
     */
    public function get($path) {
        Log::write(Log::DEBUG, 'Getting configuration value by path ' . $path);
        $pathParts = explode('/', $path);

        if(isset($this->data[$pathParts[0]])) {
            $data = $this->data[$pathParts[0]];
            array_shift($pathParts);

            if(count($pathParts)) {
                $data = $data->get(implode('/', $pathParts));
            }

            return $data;
        }

        return $this->data;
    }

    public function getKeys() {
        if(is_array($this->data)) {
            return array_keys($this->data);
        }

        return null;
    }

    public function __toString()
    {
        if(is_string($this->data) || is_numeric($data)) {
            return $this->data;
        }

        throw new \Exception('Could not convert object to string');
    }
}