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
        DB::delete('delete from products');
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
            ->whereDate("created_at", \Illuminate\Support\Carbon::now()->toDateString())
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testQueryBuilderUpdate()
    {
        $this->insertCategories();

        DB::table("categories")
            ->where("id", "=", "SMARTPHONE")
            ->update([
                "name" => "HANDPHONE",
            ]);

        $collection = DB::table("categories")
            ->where("name", "=", "HANDPHONE")
            ->get();

        self::assertCount(1, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testQueryBuilderUpdateOrInsert()
    {
        DB::table("categories")
            ->updateOrInsert([
                "id" => "VOUCHER",
            ], [
                "name" => "VOUCHER",
                "description" => "Ticket and Voucher Category",
                "created_at" => now(),
                "updated_at" => now(),
            ]);

        $collection = DB::table("categories")
            ->where("id", "=", "VOUCHER")
            ->get();

        self::assertCount(1, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testQueryBuilderIncrement()
    {
        DB::table("counters")->insert([
            "id" => "sample",
            "counter" => 0,
        ]);

        DB::table("counters")
            ->where("id", "=", "sample")
            ->increment("counter", 1);

        $collection = DB::table("counters")
            ->where("id", "=", "sample")
            ->get();

        self::assertCount(1, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testQueryBuilderDelete()
    {
        $this->testWhere();

        DB::table("categories")
            ->where("id", "=", "SMARTPHONE")
            ->delete();

        $collection = DB::table("categories")
            ->where("id", "=", "SMARTPHONE")
            ->get();

        self::assertCount(0, $collection);
    }

    public function insertProducts()
    {
        $this->insertCategories();

        DB::table("products")->insert([
            "id" => "1",
            "name" => "Iphone 21",
            "description" => "Smartphone",
            "price" => 9909,
            "category_id" => "SMARTPHONE",
        ]);
        DB::table("products")->insert([
            "id" => "2",
            "name" => "Iphone 20",
            "price" => 12319,
            "category_id" => "SMARTPHONE",
        ]);
    }

    public function testQueryBuilderJoin() {
        $this->insertProducts();

        $collection = DB::table("products")
            ->join("categories", "products.category_id", "=", "categories.id")
            ->select("products.id", "products.name", "categories.name as category_name", "products.price")
            ->get();

        self::assertCount(2, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testQueryBuilderOrdering() {
        $this->insertProducts();

        $collection = DB::table("products")
            ->whereNotNull("id")
            ->orderBy("price", "desc")
            ->get();

        self::assertCount(2, $collection);
        self::assertEquals(12319, $collection[0]->price);
        self::assertEquals(9909, $collection[1]->price);

        $collection->each(function ($item) { 
            Log::info(json_encode($item));
        });
    }

    public function testOrdering() {
        $this->insertProducts();

        $collection = DB::table("products")->whereNotNull("id")->orderBy("price", "desc")->orderBy("name", "asc")->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function testQueryBuilderPaging() {
        $this->insertProducts();

        $collection = DB::table("categories")
            ->skip(2)
            ->take(2)
            ->get();

        self::assertCount(2, $collection);
        $collection->each(function ($item) {
            Log::info(json_encode($item));
        });
    }

    public function insertManyCategories()
    {
        for($i =0; $i < 100; $i++) {
            DB::table("categories")->insert([
                "id" => "Category-$i",
                "name" => "Category $i",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }

    public function testChunk() {
        $this->insertManyCategories();

        DB::table("categories")
            ->whereNotNull("id")
            ->orderBy("id")
            ->chunk(10, function ($collection) {
                self::assertNotNull($collection);
                Log::info("Start Chunk");
                $collection->each(function ($item) {
                    Log::info(json_encode($item));
                });
                Log::info("End Chunk");
            });
    }
}
