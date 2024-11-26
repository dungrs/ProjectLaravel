    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
               Schema::create('product_language', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('language_id');
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        $table->foreign('language_id')->references('id')->on('language')->onDelete('cascade');
        $table->string('name');
        $table->text('description')->nullable();
        $table->longText('content')->nullable();
        $table->string('meta_title')->nullable();
        $table->string('meta_keyword')->nullable();
        $table->text('meta_description')->nullable();
        $table->string('canonical')->nullable();
        $table->timestamps();
    });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {   
            Schema::dropIfExists('product_language');
        }
    };