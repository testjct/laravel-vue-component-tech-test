<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Auth;

class ProductTest extends TestCase
{
    /**
     * A product test.
     *
     * @return void
     */
    public function test_product_list()
    {
        // Create and Login User

        $user = User::where('email', 'test@user.com')->first();
        if($user){
            Product::where('user_id', $user->id)->delete();
            $user->delete();
        }

        $new_user = User::create([
            'name' => 'John Doe',
            'email' => 'test@user.com',
            'password' => bcrypt('test@user'),
        ]);

        $login_response = $this->post('/login', [
            'email' => 'test@user.com',
            'password' => 'test@user',
        ]);

        // Get All products
        $response = $this->get('/products/get_all');
        $response->assertStatus(200);
    }


    /**
     * A product Create.
     *
     * @return void
     */
    public function test_product_create()
    {
        // Create and Login User

        $login_response = $this->post('/login', [
            'email' => 'test@user.com',
            'password' => 'test@user',
        ]);

        // Get error from creating product
        $create_response = $this->post('/products', ['name' => 'Test', 'year' => 123]);
        $create_response->assertStatus(302);

        // Creating product
        $file = UploadedFile::fake()->image('file.png', 150, 150);
        $response = $this->post('/products', ['name' => 'Test', 'year' => 1990, 'image'=> $file]);
        $response->assertStatus(200);
    }

    /**
     * A product update.
     *
     * @return void
     */
    public function test_product_update()
    {
        
        $login_response = $this->post('/login', [
            'email' => 'test@user.com',
            'password' => 'test@user',
        ]);

        // Get All products
        $response = $this->get('/products/get_all');
        $response->assertStatus(200);

        // Update product
        $product = Product::where('user_id', Auth::id())->first();
        if($product){
            $response = $this->post('/product/update/'.$product->id, ['name' => 'Test Updated', 'year' => 1995]);
            $response->assertStatus(200);
        }

    }

    /**
     * A product Delete.
     *
     * @return void
     */
    public function test_product_delete()
    {
        // Create and Login User

        $login_response = $this->post('/login', [
            'email' => 'test@user.com',
            'password' => 'test@user',
        ]);

        // Delete product
        $product = Product::where('user_id', Auth::id())->first();
        if($product){
            $response = $this->delete('/products/'.$product->id);
            $response->assertStatus(200);
        }
    }
    
}
