<?php

namespace App\Helpers;

use Flight;

class RequestHelper {
    public static function getBaseUrl(): string {
        $base = Flight::request()->base;
        if ($base === '.' || $base === '/' || empty($base)) {
            return '';
        }
        return rtrim($base, '/');
    }

    public static function getJsonBody(): array {
        $raw = Flight::request()->getBody();
        if (!empty($raw)) {
            $data = json_decode($raw, true);
            if (is_array($data)) {
                return $data;
            }
        }
        $formData = Flight::request()->data->getData();
        return is_array($formData) ? $formData : [];
    }
}
