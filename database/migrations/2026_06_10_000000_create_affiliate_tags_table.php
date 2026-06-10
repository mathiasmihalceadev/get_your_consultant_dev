<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('affiliate_tag_id')
                ->nullable()
                ->after('feedback_sent_at')
                ->constrained('affiliate_tags')
                ->nullOnDelete();
            $table->string('affiliate_ref')->nullable()->after('affiliate_tag_id')->index();
        });

        Schema::table('report_purchases', function (Blueprint $table) {
            $table->foreignId('affiliate_tag_id')
                ->nullable()
                ->after('metadata')
                ->constrained('affiliate_tags')
                ->nullOnDelete();
            $table->string('affiliate_ref')->nullable()->after('affiliate_tag_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('report_purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_tag_id');
            $table->dropColumn('affiliate_ref');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_tag_id');
            $table->dropColumn('affiliate_ref');
        });

        Schema::dropIfExists('affiliate_tags');
    }
};
