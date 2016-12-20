<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteCache extends Migration
{
    public function up()
    {
        $table = config('route_doc')['table_name'];
        if (Schema::hasTable($table)) {
            throw new \Exception('table exist');
        }
        Schema::create($table, function ($table) {
            $table->collation = 'utf8_general_ci';
            $table->increments('id');
            $table->string('domain');
            $table->string('uri');
            $table->string('method');
            $table->string('as');
            $table->string('uses');
            $table->string('controller');
            $table->string('namespace');
            $table->string('prefix');
            $table->string('group');
            $table->string('group2');
            $table->text('where');
            $table->text('test_data');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index(['domain', 'uri', 'method']);
            $table->index(['method']);
        });
    }

    public function down()
    {
        $table = config('route_doc')['table_name'];
        Schema::dropIfExists($table);
    }
}
