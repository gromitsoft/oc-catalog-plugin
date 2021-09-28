<?php namespace GromIT\Catalog\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateProductPropertyValuesTable Migration
 */
class CreateProductPropertyValuesTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_product_property_values', function (Blueprint $table) {

            $table->unsignedInteger('product_property_id');
            $table
                ->foreign('product_property_id', 'product_property_value')
                ->references('id')
                ->on('gromit_catalog_product_properties')
                ->onDelete('CASCADE');

            $table->unsignedInteger('value_id');
            $table
                ->foreign('value_id', 'product_property_value_value')
                ->references('id')
                ->on('gromit_catalog_values')
                ->onDelete('CASCADE');

            $table->primary(['product_property_id', 'value_id'], 'product_property_value_primary_id');

        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_product_property_values');
    }
}
