<?php

namespace App;

class Auth {

    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool {
        self::init();
        return !empty($_SESSION['store_id']);
    }

    public static function id(): ?int {
        self::init();
        if (!empty($_SESSION['store_id'])) {
            return (int)$_SESSION['store_id'];
        }
        return null;
    }

    public static function store(): ?array {
        $id = self::id();
        if (!$id) {
            return null;
        }

        $store = Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$id]);
        if (!$store) {
            // Store was deleted or session invalid
            self::logout();
            return null;
        }

        return $store;
    }

    public static function login(int|array $store): void {
        self::init();
        $_SESSION['store_id'] = is_array($store) ? (int)($store['id'] ?? 1) : (int)$store;
    }

    public static function isSuperAdmin(): bool {
        self::init();
        return !empty($_SESSION['super_admin_id']);
    }

    public static function admin(): ?array {
        self::init();
        if (empty($_SESSION['super_admin_id'])) {
            return null;
        }
        return Database::fetchOne("SELECT id, username, name FROM super_admins WHERE id = ?", [(int)$_SESSION['super_admin_id']]);
    }

    public static function loginAdmin(int $adminId): void {
        self::init();
        $_SESSION['super_admin_id'] = $adminId;
    }

    public static function logoutAdmin(): void {
        self::init();
        unset($_SESSION['super_admin_id']);
    }

    public static function logout(): void {
        self::init();
        unset($_SESSION['store_id']);
        unset($_SESSION['super_admin_id']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
