<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories');
    }
    /**
     * A basic feature test example.
     */
    public function testInsert(): void
    {
        DB::table("categories")->insert([
            'id' => 'GADGET',
            'name' => 'Gadget',
        ]);

        DB::table("categories")->insert([
            'id' => 'FOOD',
            'name' => 'Food',
        ]);

        $result = DB::select("select count(*) as total from categories");

        self::assertNotNull($result);
        self::assertEquals(2, $result[0]->total);
    }
}
