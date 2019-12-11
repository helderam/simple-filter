<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    function login_teste(){ #esta funcioao deveria chamar so login() e acionar a parent apos analise, mas nao deu certo
        /*$email = request('email');
        $password = request('password');
        if ($email && $password) {
            // Host do servidor de ad
            #$servidor = "LDAP://vilanova.com.br";
            $servidor = "LDAP://192.168.250.8";
            // Tenta conectar ao servidor de AD
            if (!($ldap = ldap_connect($servidor))) {
                throw \Illuminate\Validation\ValidationException::withMessages(['LDAP' => ['Não foi possivel conectar ao servidor AD']]);
            }
            // Dominio - $dominio = "vilanova.com.br"; Digitado pelo usuário
            // DC = Converte o dominio para o formato de consulta no AD
            $dcs = "dc=vilanova,dc=com,dc=br";
            // Autentica a consulta com um usuário AD: teste sistema - vila@2018
            $usuario = $email;
            // Senha
            $senha = request('password');
            // Configurações de acesso ao servidor
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldap, LDAP_OPT_SIZELIMIT, 0);
            // Tenta autenticar a consulta no AD
            if (!$ldapbind = @ldap_bind($ldap, $usuario, $senha)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['LDAP' => ["Não foi possivel autenticar a consulta no AD com o usuário $usuario"]]);
            }

            // Verifica se usuario existe na tabela local
            $user = User::where('email', $email)->first();
            if (!$user){
                $user = User::Create([
                    'email' => $email,
                    'name' => $email,
                ]);
            }
            $user->ad_login_at = date('Y-m-d H:i:s');
            $user->save();

            #$this->login($user); # nao funciona pois exige REQUEST
            #$this->loginUsingId($user->id);
            session()->put('userData', $user); # nao funciona .... tem um formato doido 
            return redirect()->route('home');
            #return true;
            exit;
        }*/
        #parent::login();
        #$this->login();
    }
}
