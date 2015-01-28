<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("links", function($table){
			$table->increments("id");
			$table->text("title");
			$table->text("icon_url")->nullable();
			$table->text("url");
			$table->integer("user_id")->unsigned();
			$table->foreign("user_id")->references("id")->on("user")->onDelete("cascade");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("links");
	}
}
