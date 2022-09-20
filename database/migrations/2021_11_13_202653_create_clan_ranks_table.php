<?php
/**
 * MIT License
 *
 * Copyright (c) 2021-2022 FoxxoSnoot
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClanRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clan_ranks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clan_id')->unsigned();
            $table->string('name');
            $table->integer('rank');
            $table->boolean('can_post_wall')->default(false);
            $table->boolean('can_moderate_wall')->default(false);
            $table->boolean('can_invite_users')->default(false);
            $table->boolean('can_manage_relations')->default(false);
            $table->boolean('can_rank_members')->default(false);
            $table->boolean('can_manage_ranks')->default(false);
            $table->boolean('can_edit_description')->default(false);
            $table->boolean('can_post_shout')->default(false);
            $table->boolean('can_add_funds')->default(false);
            $table->boolean('can_take_funds')->default(false);
            $table->boolean('can_edit_clan')->default(false);
            $table->timestamps();

            $table->foreign('clan_id')->references('id')->on('clans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clan_ranks');
    }
}
