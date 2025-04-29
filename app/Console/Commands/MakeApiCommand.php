<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD files: model, migration, controller, store request, update request';

    public function handle()
    {
        $this->info('Creating magic... 🪄');

        $this->createModel();
        $this->createController();
        $this->createRequests();
        $this->modifyMigration();
        $this->createResource();
        $this->createFactory();
        $this->createSeeder();
        $this->createTest();
        $this->addRoutes();

        $this->comment('Api Make Successful');
    }

    protected function createModel()
    {
        $name = $this->argument('name');
        $this->call('make:model', ['name' => $name, '-m' => true]);

        $modelPath = app_path("Models/{$name}.php");
        $modelContent = <<<EOT
            <?php

            namespace App\Models;

            use Illuminate\Database\Eloquent\Factories\HasFactory;
            use Illuminate\Database\Eloquent\Model;
            use Illuminate\Database\Eloquent\SoftDeletes;
            use App\Traits\UUID;

            class {$name} extends Model
            {
                use HasFactory, UUID, SoftDeletes;

                protected \$fillable = [
                    // Add your columns here
                ];
            }
            EOT;

        file_put_contents($modelPath, $modelContent);
    }

    protected function createRequests()
    {
        $name = $this->argument('name');
        $this->call('make:request', ['name' => "Store{$name}Request"]);
        $this->call('make:request', ['name' => "Update{$name}Request"]);

        $storeRequestPath = app_path("Http/Requests/Store{$name}Request.php");
        $storeRequestContent = <<<EOT
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class {$name}StoreRequest extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        // Add your validation rules here
                    ];
                }

                public function attributes()
                {
                    return [
                        // Add your attributes here
                    ];
                }

                public function messages()
                {
                    return [
                        // Add your messages here
                    ];
                }
            }
            EOT;

        file_put_contents($storeRequestPath, $storeRequestContent);

        $updateRequestPath = app_path("Http/Requests/Update{$name}Request.php");
        $updateRequestContent = <<<EOT
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class {$name}UpdateRequest extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        // Add your validation rules here
                    ];
                }

                public function attributes()
                {
                    return [
                        // Add your attributes here
                    ];
                }

                public function messages()
                {
                    return [
                        // Add your messages here
                    ];
                }
            }
            EOT;

        file_put_contents($updateRequestPath, $updateRequestContent);
    }

    protected function createController()
    {
        $name = $this->argument('name');

        $controllerPath = app_path("Http/Controllers/Api/{$name}Controller.php");

        $controllerContent =
            <<<'EOT'
            <?php

            namespace App\Http\Controllers\Api;

            use App\Models\__namePascalCase__;
            use Illuminate\Http\Request;
            use App\Http\Resources\__namePascalCase__Resource;
            use App\Http\Controllers\Controller;
            use App\Http\Requests\__namePascalCase__StoreRequest;
            use App\Http\Requests\__namePascalCase__UpdateRequest;
            use Illuminate\Support\Facades\Storage;
            use Illuminate\Support\Facades\Log;
            use App\Helpers\ResponseHelper;

            class __namePascalCase__Controller extends Controller
            {
                /**
                 * Display a listing of the resource.
                 */
                public function index(Request $request)
                {
                    try {
                        $query = __namePascalCase__::query();

                        if ($request->has('search')) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        }

                        $perPage = $request->get('per_page', 10);
                        $__nameCamelCasePlurals__ = $query->paginate($perPage);

                        return ResponseHelper::jsonResponse(
                            true,
                            'Success retrieve all __nameProperCase__',
                            __namePascalCase__Resource::collection($__nameCamelCasePlurals__),
                            200
                        );
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(
                            false,
                            'Failed retrieve all __nameProperCase__',
                            ['error' => $e->getMessage()],
                            500
                        );
                    }
                }

                /**
                 * Store a newly created resource in storage.
                 */
                public function store(__namePascalCase__StoreRequest $request)
                {
                    try {
                        $data = $request->validated();

                        $__nameCamelCase__ = __namePascalCase__::create($data);

                        return ResponseHelper::jsonResponse(
                            true,
                            '__nameProperCase__ created',
                            new __namePascalCase__Resource($__nameCamelCase__),
                            201
                        );
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(
                            false,
                            '__nameProperCase__ failed to create',
                            ['error' => $e->getMessage()],
                            500
                        );
                    }
                }

                /**
                 * Display the specified resource.
                 */
                public function show(string $id)
                {
                    try {
                        $__nameCamelCase__ = __namePascalCase__::find($id);

                        if (!$__nameCamelCase__) {
                            return ResponseHelper::jsonResponse(
                                false,
                                '__nameProperCase__ not found',
                                null,
                                404
                            );
                        }

                        return ResponseHelper::jsonResponse(
                            true,
                            'Success retrieve __nameProperCase__',
                            new __namePascalCase__Resource($__nameCamelCase__),
                            200
                        );
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(
                            false,
                            'Failed retrieve __nameProperCase__',
                            ['error' => $e->getMessage()],
                            500
                        );
                    }
                }

                /**
                 * Update the specified resource in storage.
                 */
                public function update(__namePascalCase__UpdateRequest $request, string $id)
                {
                    try {
                        $data = $request->validated();

                        $__nameCamelCase__ = __namePascalCase__::find($id);

                        if (!$__nameCamelCase__) {
                            return ResponseHelper::jsonResponse(
                                false,
                                '__nameProperCase__ not found',
                                null,
                                404
                            );
                        }

                        // Add your logic here

                        $__nameCamelCase__->update($data);

                        Log::info('Data setelah update: ', $__nameCamelCase__->refresh()->toArray());

                        return ResponseHelper::jsonResponse(
                            true,
                            '__nameProperCase__ updated',
                            new __namePascalCase__Resource($__nameCamelCase__),
                            200
                        );
                    } catch (\Exception $e) {
                        Log::error('Error saat update: ', ['error' => $e->getMessage()]);

                        return ResponseHelper::jsonResponse(
                            false,
                            '__nameProperCase__ failed to update',
                            ['error' => $e->getMessage()],
                            500
                        );
                    }
                }

                /**
                 * Remove the specified resource from storage.
                 */
                public function destroy($id)
                {
                    try {
                        $__nameCamelCase__ = __namePascalCase__::find($id);

                        if (!$__nameCamelCase__) {
                            return ResponseHelper::jsonResponse(
                                false,
                                '__nameProperCase__ not found',
                                null,
                                404
                            );
                        }

                        $__nameCamelCase__->delete();

                        return ResponseHelper::jsonResponse(
                            true,
                            '__nameProperCase__ deleted',
                            null,
                            200
                        );
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(
                            false,
                            '__nameProperCase__ failed to delete',
                            ['error' => $e->getMessage()],
                            500
                        );
                    }
                }
            }
            EOT;

        $controllerContent = str_replace('__namePascalCase__', $name, $controllerContent);
        $controllerContent = str_replace('__nameCamelCase__', Str::camel($name), $controllerContent);
        $controllerContent = str_replace('__nameSnakeCase__', Str::snake($name), $controllerContent);
        $controllerContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $controllerContent);
        $controllerContent = str_replace('__nameKebabCase__', Str::kebab($name), $controllerContent);
        $controllerContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $controllerContent);

        file_put_contents($controllerPath, $controllerContent);
    }

    protected function modifyMigration()
    {
        $name = $this->argument('name');
        $name = Str::snake($name);
        $name = Str::plural($name);
        $migration = database_path('migrations/'.date('Y_m_d_His').'_create_'.$name.'_table.php');

        $migrationContent = <<<EOT
            <?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                /**
                 * Run the migrations.
                 */
                public function up()
                {
                    Schema::create('{$name}', function (Blueprint \$table) {
                        \$table->id('');
                        // Add your columns here
                        \$table->softDeletes();
                        \$table->timestamps();
                    });
                }

                /**
                 * Reverse the migrations.
                 */
                public function down()
                {
                    Schema::dropIfExists('{$name}');
                }
            };
            EOT;

        file_put_contents($migration, $migrationContent);
    }

    protected function createResource()
    {
        $name = $this->argument('name');
        $resource = app_path("Http/Resources/{$name}Resource.php");

        $resourceContent = <<<EOT
            <?php

            namespace App\Http\Resources;

            use Illuminate\Http\Resources\Json\JsonResource;
            use Vinkla\Hashids\Facades\Hashids;

            class {$name}Resource extends JsonResource
            {
                /**
                 * Transform the resource into an array.
                 *
                 * @param  \Illuminate\Http\Request  \$request
                 * @return array<string, mixed>
                 */
                public function toArray(\$request)
                {
                    return [
                        'id' => Hashids::encode(\$this->id),
                        // Add your columns here
                        'created_at' => \$this->created_at,
                        'updated_at' => \$this->updated_at,
                    ];
                }
            }
            EOT;

        file_put_contents($resource, $resourceContent);
    }

    protected function createFactory()
    {
        $name = $this->argument('name');
        $factory = database_path("factories/{$name}Factory.php");

        $factoryContent = <<<EOT
            <?php

            namespace Database\Factories;

            use Illuminate\Database\Eloquent\Factories\Factory;
            use Illuminate\Support\Str;

            class {$name}Factory extends Factory
            {
                /**
                 * Define the model's default state.
                 *
                 * @return array<string, mixed>
                 */
                public function definition(): array
                {
                    return [
                        // Define your default state here
                    ];
                }
            }
            EOT;

        file_put_contents($factory, $factoryContent);
    }

    protected function createSeeder()
    {
        $name = $this->argument('name');
        $seederPath = database_path("seeders/{$name}Seeder.php");

        $seederContent = <<<EOT
<?php

namespace Database\Seeders;

use App\Models\\{$name};
use Illuminate\Database\Seeder;

class {$name}Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {$name}::factory()->count(10)->create();
    }
}
EOT;

        file_put_contents($seederPath, $seederContent);
    }

    protected function createTest()
    {
        $name = $this->argument('name');
        $testPath = base_path("tests/Feature/{$name}ControllerTest.php");

        $testContent = <<<EOT
<?php

namespace Tests\Feature;

use App\Models\\{$name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {$name}ControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->seed();
    }

    /** @test */
    public function it_can_list_all_{$name}s()
    {
        \$response = \$this->getJson('/api/{$name}s');

        \$response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'current_page',
                'per_page',
                'total',
                'last_page'
            ]);
    }

    /** @test */
    public function it_can_search_{$name}s()
    {
        \$response = \$this->getJson('/api/{$name}s?search=test');

        \$response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'current_page',
                'per_page',
                'total',
                'last_page'
            ]);
    }

    /** @test */
    public function it_can_create_a_{$name}()
    {
        \$data = [
            'name' => 'Test {$name}',
            'slug' => 'test-{$name}'
        ];

        \$response = \$this->postJson('/api/{$name}s', \$data);

        \$response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);

        \$this->assertDatabaseHas('{$name}s', \$data);
    }

    /** @test */
    public function it_can_show_a_{$name}()
    {
        \${$name} = {$name}::factory()->create();

        \$response = \$this->getJson('/api/{$name}s/' . \${$name}->id);

        \$response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);
    }

    /** @test */
    public function it_can_update_a_{$name}()
    {
        \${$name} = {$name}::factory()->create();
        \$data = [
            'name' => 'Updated {$name}',
            'slug' => 'updated-{$name}'
        ];

        \$response = \$this->putJson('/api/{$name}s/' . \${$name}->id, \$data);

        \$response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);

        \$this->assertDatabaseHas('{$name}s', \$data);
    }

    /** @test */
    public function it_can_delete_a_{$name}()
    {
        \${$name} = {$name}::factory()->create();

        \$response = \$this->deleteJson('/api/{$name}s/' . \${$name}->id);

        \$response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        \$this->assertSoftDeleted('{$name}s', ['id' => \${$name}->id]);
    }
}
EOT;

        file_put_contents($testPath, $testContent);
    }

    protected function addRoutes()
    {
        $name = $this->argument('name');

        $name = Str::kebab($name);
        $routes = base_path('routes/api.php');

        $routeContent = "\nRoute::apiresource('{$name}', App\Http\Controllers\Web\Api\\{$this->argument('name')}Controller::class);";

        file_put_contents($routes, $routeContent, FILE_APPEND);
    }
}
