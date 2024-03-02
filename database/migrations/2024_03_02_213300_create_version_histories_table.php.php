<?php
namespace Laraversion\Laraversion\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('version_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('versionable');
            $table->uuid('commit_id')->unique();
            $table->enum('event_type', [
                'created',
                'updated',
                'deleted',
                'restored',
                'forceDeleted',
            ])->default('created');
            $table->text('data');
            $table->timestamps();
        });
    }
};
