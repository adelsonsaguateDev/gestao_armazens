<?php
namespace App\Http\Controllers\Admin;
@session_start();
date_default_timezone_set('Africa/Maputo');
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{

    public function autenticar(Request $request)
    {
            session_unset();
            session_destroy();

            $_SESSION['permissao'] = null;



            $data = $request->validate([
                'username' => 'required|string',
                'password' => 'required'
            ]);

            if ((Auth::attempt(['username' => $data['username'], 'password' => $data['password'],'estado'=>'1']))) {
                $id = auth()->user()->id;
                $result = DB::selectOne("SELECT p.id, p.nome FROM users u INNER JOIN permissao_user pu  ON u.id = pu.user_id INNER JOIN permissaos p ON p.id = pu.permissao_id WHERE pu.user_id='$id'");
                session(['permissao_nome' => $result->nome]);
                session(['nome_utilizador' => auth()->user()->name]);
                return redirect('/home')->with('status', 'Login efectuado com sucesso.');

            }else{

                session_unset();
                return redirect()->back()->withErrors(['username'=>'Por favor verifique o seu utilizador.','password'=>'Por favor verifique a sua senha.']);

            }




    }

    public function logout(){

        session_unset();
        session_destroy();
        return view('auth.login');
    }


}
