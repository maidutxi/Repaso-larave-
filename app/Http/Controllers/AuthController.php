namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Crear un token para el usuario
        $token = $user->createToken('YourAppName')->plainTextToken;

        // Devolver la respuesta
        return response()->json(['token' => $token], 201);
    }

    // Iniciar sesión
    public function login(Request $request)
    {
        // Validar las credenciales
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Crear un token de acceso
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        // Revocar todos los tokens del usuario
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
