<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image_path')->nullable()->after('password');
            $table->string('postcode')->nullable()->after('profile_image_path');
            $table->string('address')->nullable()->after('postcode');
            $table->string('building')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_image_path');
            $table->dropColumn('postcode');
            $table->dropColumn('address');
            $table->dropColumn('building');
        });
    }
}
