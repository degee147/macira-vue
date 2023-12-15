<?php

namespace App\Http\Controllers;

use App\Models\User;
use Faker\Generator;
use Inertia\Inertia;
use League\Csv\Reader;
use App\Models\CsvImport;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use Illuminate\Routing\Pipeline;
use Laravel\Jetstream\Jetstream;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\LoginResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Actions\Fortify\AttemptToAuthenticate;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use App\Actions\Fortify\RedirectIfTwoFactorAuthenticatable;

class AdminController extends Controller
{

    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    public function loginForm(Request $request)
    {
        return Inertia::render('Admin/Login');
    }


    public function dashboard(Request $request)
    {
        return Inertia::render('Admin/Dashboard');
    }

    public function registered_users(Request $request)
    {
        return Inertia::render('Admin/RegisteredUsers');
    }


    public function admin_csv_import()
    {
        return Inertia::render('Admin/CSVImport');
    }

    public function admin_users_data()
    {
        $items = User::paginate(10);
        return response()->json($items);
    }
    public function csvData()
    {
        $items = CsvImport::paginate(10);
        return response()->json($items);
    }
    public function admin_csv_sample()
    {
        // Set the CSV file name
        // $csvFileName = 'output.csv';
        $csvFileName = public_path('output.csv');

        // Open the CSV file for writing
        $csvFile = fopen($csvFileName, 'w');

        // Write header row to the CSV file
        fputcsv($csvFile, ['id', 'name', 'email', 'phone', 'address']);

        // Generate and write data rows using Faker
        // $faker = Faker::create();
        // Resolve the Faker instance from the service container
        $faker = resolve(Generator::class);

        for ($i = 1; $i <= 150; $i++) {
            $rowData = [
                $i,
                $faker->name,
                $faker->email,
                $faker->phoneNumber,
                $faker->address('shortAddress'),
            ];
            fputcsv($csvFile, $rowData);
        }

        // Close the CSV file
        fclose($csvFile);
        // Provide the CSV file for download
        return response()->file($csvFileName)->deleteFileAfterSend(true);
        // Download the CSV file
        // return response()->download($csvFileName)->deleteFileAfterSend(true);
    }

    public function uploadCSV(Request $request)
    {

        $data = $request->all();
        $base64Content = $data["csv"];

        $base64Content = substr($base64Content, strpos($base64Content, ',') + 1);
        $b64 = base64_decode(preg_replace('#^data:text/w+;base64,#i', '', $base64Content));
        $csv = Reader::createFromString($b64);
        $csv->setHeaderOffset(0);
        $header = $csv->getHeader();
        $records = $csv->getRecords();
        foreach ($records as $record) {
            $csv = CsvImport::where('id', $record['id'])->first();
            if (empty($csv)) {
                $csv = CsvImport::create($record);
            }
        }


        return response()->json(['success' => true, 'message' => 'CSV file uploaded and processed']);
    }

    public function admin_api_data()
    {
        return Inertia::render('Admin/Dashboard');
    }


    /**
     * Show the login view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LoginViewResponse
     */
    public function create(Request $request): LoginViewResponse
    {
        return app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
    {
        return $this->loginPipeline($request)->then(function ($request) {
            return app(LoginResponse::class);
        });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }


        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LogoutResponse
     */
    public function destroy(Request $request): LogoutResponse
    {
        $this->guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return app(LogoutResponse::class);
    }
}

