<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->string('number')->nullable()->after('id');
            $table->string('name')->nullable()->after('number');
            $table->string('client')->nullable()->after('name');
            $table->string('client_org')->nullable()->after('client');
            $table->string('client_initials')->nullable()->after('client_org');
            $table->string('type_label')->nullable()->after('client_initials');
            $table->string('type_badge')->nullable()->after('type_label');
            $table->string('status')->nullable()->after('type_badge');
            $table->date('hearing_date')->nullable()->after('status');
            $table->string('hearing_time')->nullable()->after('hearing_date');
            $table->index('number');
        });
    }

    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropIndex(['number']);
            $table->dropColumn([
                'number','name','client','client_org','client_initials','type_label','type_badge','status','hearing_date','hearing_time'
            ]);
        });
    }
};
