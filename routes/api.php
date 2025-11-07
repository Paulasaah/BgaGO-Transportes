use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TelemetryController;

Route::post('/telemetria', [TelemetryController::class, 'recibir']);
