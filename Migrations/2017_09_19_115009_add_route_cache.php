<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRouteCache extends Migration
{

    public function up()
    {
        $this->connection = config('route_doc.table_connection', config('database.default'));
        $table = config('route_doc.table_name');
        if (!Schema::connection($this->connection)->hasTable($table)) {
            throw new \Exception('table not exist');
        }
        Schema::connection($this->connection)->table($table, function ($table) {
            $table->text('test_result')->after('test_data');
            $table->text('return')->after('test_result');
            $table->text('param_types')->after('params');
        });
    }

    public function down()
    {
        $table = config('route_doc.table_name');
        $this->connection = config('route_doc.table_connection', config('database.default'));
        Schema::connection($this->connection)->table($table)->dropColumn('param_types');
        Schema::connection($this->connection)->table($table)->dropColumn('return');
        Schema::connection($this->connection)->table($table)->dropColumn('test_result');
    }
}
