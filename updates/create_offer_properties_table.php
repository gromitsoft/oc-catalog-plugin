<?php namespace GromIT\Catalog\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

/**
 * CreateOfferPropertiesTable Migration
 */
class CreateOfferPropertiesTable extends Migration
{
    public function up()
    {
        Schema::create('gromit_catalog_offer_properties', function (Blueprint $table) {

            $table->increments('id');

            $table->unsignedInteger('offer_id');
            $table
                ->foreign('offer_id', 'offer_properties_offer')
                ->references('id')
                ->on('gromit_catalog_offers')
                ->onDelete('CASCADE');

            $table->unsignedInteger('property_id');
            $table
                ->foreign('property_id', 'offer_properties_property')
                ->references('id')
                ->on('gromit_catalog_properties')
                ->onDelete('CASCADE');

            $table->integer('sort_order')->default(0);

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('gromit_catalog_offer_properties');
    }
}
