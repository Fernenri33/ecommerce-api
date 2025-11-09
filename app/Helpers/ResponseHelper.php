<?php
namespace App\Helpers;

class ResponseHelper
{
    public static function success(string $message, $data = null): array
    {
        return [
            'success' => true,
            'message' => $message,
            'content' => $data
        ];
    }

    public static function error(string $message, $data = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'content' => $data
        ];
    }

    public static function notFound(string $resource = 'Recurso'): array
    {
        return [
            'success' => false,
            'message' => "No se encontró {$resource}",
            'content' => null
        ];
    }

    public static function created(string $resource, $data): array
    {
        return [
            'success' => true,
            'message' => "{$resource} creado exitosamente",
            'content' => $data
        ];
    }

    public static function updated(string $resource, $data): array
    {
        return [
            'success' => true,
            'message' => "{$resource} actualizado exitosamente",
            'content' => $data
        ];
    }

    public static function deleted(string $resource): array
    {
        return [
            'success' => true,
            'message' => "{$resource} eliminado exitosamente",
            'content' => null
        ];
    }

    public static function exception(string $action, \Exception $e): array
    {
        return [
            'success' => false,
            'message' => "Error al {$action}: " . $e->getMessage(),
            'content' => null
        ];
    }
}