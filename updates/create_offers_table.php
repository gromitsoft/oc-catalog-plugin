<?php namespace GromIT\Catalog\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateOffersTable Migration
 */
class CreateOffersTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_offers', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('product_id')->nullable();

            $table
                ->foreign('product_id', 'offer_product')
                ->references('id')
                ->on('gromit_catalog_products')
                ->onDelete('CASCADE');

            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('vendor_code')->nullable();
            $table->longText('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_offers');
    }
}
