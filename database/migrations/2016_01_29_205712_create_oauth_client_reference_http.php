<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthClientReferenceHttp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_client_reference_http', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('client_id', 40);
            $table->string('accept_from_url');

            $table->timestamps();

            $table->unique(['client_id', 'accept_from_url']);

            $table->foreign('client_id')
                ->references('id')->on('oauth_clients')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_client_reference_http', function (Blueprint $table) {
            $table->dropForeign('oauth_client_reference_http_client_id_foreign');
        });

        Schema::drop('oauth_client_reference_http');
    }
}
