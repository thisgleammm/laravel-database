<?php

namespace Tests\Feature;

use Error;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionTest extends TestCase
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
    public function testTransactionSuccess(): void
    {
        DB::transaction(function () {
            DB::insert('insert into categories (id, name, description, created_at, updated_at) values (:id, :name, :description, :created_at, :updated_at)', [
                'id' => 'GADGET',
                'name' => 'Gadget',
                'description' => 'Gadget Category',
                'created_at' => now(),
            'updated_at' => now(),
            ]);

            DB::insert('insert into categories (id, name, description, created_at, updated_at) values (:id, :name, :description, :created_at, :updated_at)', [
                'id' => 'FOOD',
                'name' => 'Food',
                'description' => 'Food Category',
                'created_at' => now(),
            'updated_at' => now(),
            ]);

        });

        $results = DB::select('select * from categories');

        self::assertCount(2, $results);
    }
    public function testTransactionFailed(): void
    {
        try {
            DB::transaction(function () {
            DB::insert('insert into categories (id, name, description, created_at, updated_at) values (:id, :name, :description, :created_at, :updated_at)', [
                'id' => 'GADGET',
                'name' => 'Gadget',
                'description' => 'Gadget Category',
                'created_at' => now(),
            'updated_at' => now(),
            ]);

            DB::insert('insert into categories (id, name, description, created_at, updated_at) values (:id, :name, :description, :created_at, :updated_at)', [
                'id' => 'GADGET',
                'name' => 'Gadget',
                'description' => 'Gadget Category',
                'created_at' => now(),
            'updated_at' => now(),
            ]);
        });
        } catch (QueryException $error) {
            //
        }


        $results = DB::select('select * from categories');

        self::assertCount(0, $results);
    }
}
