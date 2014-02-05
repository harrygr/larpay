<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePayerPaymentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payer_payment', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('payer_id');
			$table->integer('payment_id');
			$table->decimal('amount', 10, 2);
			$table->boolean('pays');
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
		Schema::drop('payer_payment');
	}

}