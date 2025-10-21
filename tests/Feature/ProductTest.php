<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Cualquiera puede ver productos
     * Cualquiera puede buscar un producto
     * Solo admins pueden editar, crear o eliminar productos
    */
}
