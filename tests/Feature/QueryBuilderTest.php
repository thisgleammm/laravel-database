<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function testSelect()
    {
        $this->testInsert();

        $result = DB::table("categories")->select("id", "name")->get();

        self::assertNotNull($result);
        $result->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function insertCategories()
    {
        DB::table("categories")->insert([
            'id' => 'SMARTPHONE',
            'name' => 'Smartphone',
            'created_at' => now(),
        ]);

        DB::table("categories")->insert([
            'id' => 'LAPTOP',
            'name' => 'Laptop',
            'created_at' => now(),
        ]);
        DB::table("categories")->insert([
            'id' => 'FASHION',
            'name' => 'Fashion',
            'created_at' => now(),
        ]);

        DB::table("categories")->insert([
            'id' => 'BEAUTY',
            'name' => 'Beauty',
            'created_at' => now(),
        ]);
    }

    public function testWhere()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->where(function ($builder) {
            $builder->where("id", "=", "SMARTPHONE");
            $builder->orWhere("id", "=", "LAPTOP");
            // SELECT * FROM categories WHERE (id = 'SMARTPHONE' AND id = 'LAPTOP')
        })->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereBetweenMethod()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereBetween("id", ["BEAUTY", "SMARTPHONE"])
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereInMethod()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereIn("id", ["SMARTPHONE", "LAPTOP"])
            ->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
    public function testWhereNullMethod()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereNull("description")
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereDateMethod()
    {
        $this->insertCategories();

        $collection = DB::table("categories")
            ->whereDate("created_at", "2026-01-25")
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }
}
