<?php namespace GromIT\Catalog\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateValuesTable Migration
 */
class CreateValuesTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id');
            $table
                ->foreign('property_id', 'property_value')
                ->references('id')
                ->on('gromit_catalog_properties')
                ->onDelete('CASCADE');
            $table->text('name');
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_values');
    }
}
