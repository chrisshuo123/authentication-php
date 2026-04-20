<?php

class SessionManager {
    public function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    public function destroy(): void {
        session_destroy();
    }
}