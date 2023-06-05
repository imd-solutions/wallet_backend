<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('user_id')->nullable();
            $table->string('username', 50)->after('lastname')->unique()->nullable();
            $table->timestamp('last_login')->after('username')->nullable();
            $table->bigInteger('country_id')->after('last_login')->nullable();
            $table->char('language', 5)->after('country_id')->nullable();
            $table->string('scopes', 50)->after('language')->nullable();
            $table->boolean('active')->after('scopes')->unsigned()->default(true)->nullable();
            $table->timestamp('deactivated_at')->after('active')->nullable()->useCurrent();
            $table->string('deactivated_reason', 1000)->after('deactivated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('username');
            $table->dropColumn('last_login');
            $table->dropColumn('country_id');
            $table->dropColumn('language');
            $table->dropColumn('scopes');
            $table->dropColumn('active');
            $table->dropColumn('deactivated_at');
            $table->dropColumn('deactivated_reason');
        });
    }
}
