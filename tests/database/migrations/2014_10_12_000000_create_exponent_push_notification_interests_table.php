<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExponentPushNotificationInterestsTable extends Migration
{
    public function up()
    {
        Schema::create('exponent_push_notification_interests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->index();
            $table->string('value');

            $table->unique(['key','value']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exponent_push_notification_interests');
    }
}
