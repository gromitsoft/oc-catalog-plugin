<?php namespace GromIT\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateGroupsTable Migration
 */
class CreateGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('alt_name')->nullable();
            $table->string('slug')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_groups');
    }
}
