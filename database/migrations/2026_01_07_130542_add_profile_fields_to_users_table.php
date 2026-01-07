<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bio')->nullable()->after('email');
            $table->string('title')->nullable()->after('bio'); // e.g., "Editor in Chief"
            $table->string('avatar_path')->nullable()->after('title');
            $table->string('website_url')->nullable()->after('avatar_path');
            $table->string('twitter_handle')->nullable()->after('website_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'title', 'avatar_path', 'website_url', 'twitter_handle']);
        });
    }
};
