<?php namespace GromIT\Catalog\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateProductsTable Migration
 */
class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_products', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('category_id')->nullable();

            $table
                ->foreign('category_id', 'product_category')
                ->references('id')
                ->on('gromit_catalog_categories')
                ->onDelete('SET NULL');

            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('vendor_code')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_products');
    }
}
