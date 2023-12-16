<?php

namespace App\Http\Controllers;

use App\Models\User;
use Faker\Generator;
use Inertia\Inertia;
use App\Models\Entry;
use League\Csv\Reader;
use App\Models\CsvImport;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use App\Mail\SendAdminAlertMail;

use Illuminate\Routing\Pipeline;
use Laravel\Jetstream\Jetstream;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Jobs\SendEmailToActiveUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Http\Responses\LoginResponse;
use App\Mail\SendEmailToActiveUsersMail;
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

        for ($i = 1; $i <= 40; $i++) {
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

    public function send_mail()
    {
        return Inertia::render('Admin/SendMail');
    }
    public function send_mail_post(Request $request)
    {
        $message = $request->input('message');
        dispatch(new SendEmailToActiveUsers($message));

        //cd ~/nextpayday/ && php artisan queue:work --once doesn't seem to be running
        //so temporary work arround
        try {
            // Retrieve active users from the database
            $activeUsers = User::where('is_active', true)->get();

            // Loop through active users and send email
            foreach ($activeUsers as $user) {
                Mail::to($user->email)->send(new SendEmailToActiveUsersMail($user, $message));
            }

        } catch (\Exception $e) {
            Mail::to('tech@nextpayday.co')->send(new SendAdminAlertMail('Some troubles occurred'));
        }

        return response()->json(['success' => true, 'message' => 'Email queued for processing']);
    }

    public function admin_api_data()
    {
        return Inertia::render('Admin/APIData');
    }

    public function admin_data_entries()
    {
        $items = Entry::paginate(10);
        return response()->json($items);
    }

    private function keysToLower($obj)
    {
        if (!is_array($obj)) {
            $obj = json_decode(json_encode($obj), true);
        }
        return array_map(function ($item) {
            if (is_array($item))
                $item = $this->keysToLower($item);
            return $item;
        }, array_change_key_case($obj));
    }
    public function admin_api_data_load()
    {
        //call the api here;

        $apiUrl = 'https://api.publicapis.org/entries';
        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $data = $response->json();

            foreach ($data['entries'] as $entry) {

                $entry = $this->keysToLower($entry);
                // Check for duplicates based on some unique identifier (e.g., 'title')
                $existingEntry = Entry::where('api', $entry['api'])->where('link', $entry['link'])->first();

                if (!$existingEntry) {
                    // Save the non-duplicate entry to the database
                    Entry::create($entry);
                }
            }


        }
        return response()->json(['success' => true, 'message' => 'Entries Updated']);
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

