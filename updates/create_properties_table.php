<?php namespace GromIT\Catalog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePropertiesTable Migration
 */
class CreatePropertiesTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_properties', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('group_id')->nullable();
            $table
                ->foreign('group_id', 'property_group')
                ->references('id')
                ->on('gromit_catalog_groups')
                ->onDelete('SET NULL');

            $table->string('name');
            $table->string('alt_name')->nullable();
            $table->boolean('multiple')->default(0);
            $table->integer('sort_order')->default(0);
            $table->string('slug')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_properties');
    }
}
