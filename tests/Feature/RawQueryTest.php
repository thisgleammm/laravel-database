<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RawQueryTest extends TestCase
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
    public function testCRUD(): void
    {
        DB::insert('insert into categories (id, name, description, created_at, updated_at) values (:id, :name, :description, :created_at, :updated_at)', [
            'id' => 'GADGET',
            'name' => 'Gadget',
            'description' => 'Gadget Category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $category = DB::select('select * from categories where id = ?', ['GADGET']);

        self::assertCount(1, $category);
        self::assertEquals('GADGET', $category[0]->id);
        self::assertEquals('Gadget', $category[0]->name);
        self::assertEquals('Gadget Category', $category[0]->description);
        self::assertEquals(now(), $category[0]->created_at);
        self::assertEquals(now(), $category[0]->updated_at);
    }
}
