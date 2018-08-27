<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessTokenScopesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_access_tokens_scopes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('access_token_id');
            $table->text('scope_id');

            $table->foreign('access_token_id')->references('id')->on('oauth_access_tokens');
            $table->foreign('scope_id')->references('id')->on('oauth_scopes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_token_scopes');
    }
}
