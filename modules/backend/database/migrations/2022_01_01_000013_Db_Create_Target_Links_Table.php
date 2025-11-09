<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class DbCreateTargetLinksTable extends Migration
{
    public function up()
    {
        Schema::create('backend_target_links', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('target_type', 70);
            $table->string('main_value');
            $table->enum('open_type', ['internal', 'external'])->default('internal');
            $table->string('description');
            $table->text('extra_data');
        });
    }

    public function down()
    {
        Schema::dropIfExists('backend_target_links');
    }

}
