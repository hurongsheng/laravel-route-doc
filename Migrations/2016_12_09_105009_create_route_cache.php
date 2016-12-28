<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteCache extends Migration
{

    public function up()
    {
        $this->connection = config('route_doc.table_connection', config('database.default'));
        $table = config('route_doc.table_name');
        if (Schema::connection($this->connection)->hasTable($table)) {
            throw new \Exception('table exist');
        }
        Schema::connection($this->connection)->create($table, function ($table) {
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
            $table->text('params');
            $table->text('test_data');
            $table->integer('state');
            $table->integer('last_test');
            $table->string('author');
            $table->text('description');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->index(['domain', 'uri', 'method']);
            $table->index(['method']);
            $table->index(['state']);
            $table->index(['group']);
            $table->index(['group2']);
            $table->index(['author']);
            $table->index(['updated_at', 'state']);
        });
    }

    public function down()
    {
        $table = config('route_doc.table_name');
        $this->connection = config('route_doc.table_connection', config('database.default'));
        Schema::connection($this->connection)->dropIfExists($table);
    }
}
