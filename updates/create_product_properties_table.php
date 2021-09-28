<?php namespace GromIT\Catalog\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateProductPropertiesTable Migration
 */
class CreateProductPropertiesTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_product_properties', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('product_id');
            $table
                ->foreign('product_id', 'product_properties_product')
                ->references('id')
                ->on('gromit_catalog_products')
                ->onDelete('CASCADE');

            $table->unsignedInteger('property_id');
            $table
                ->foreign('property_id', 'product_properties_property')
                ->references('id')
                ->on('gromit_catalog_properties')
                ->onDelete('CASCADE');

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_product_properties');
    }
}
