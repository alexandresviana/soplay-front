<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Assinante;
use App\Models\App;
use App\Models\AssinanteDBComets;
use App\Models\Plan;
use App\Mail\ForgotPassword;
use App\Services\UserLoggedService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Mail\VerificationEmail;

use \Exception;

class AssinanteController extends Controller
{
  protected $userLoggedService;

  public function __construct(UserLoggedService $userLoggedService)
  {
    $this->userLoggedService = $userLoggedService;
  }

  public function login()
  {
    if (Auth::user()) {
      return redirect('/');
    }

    $appList = App::where('ativo', 1)->orderBy('app_nome')->get();
    $currentApp = $this->__app();

    return view('assinante.login', ['app_list' => $appList, 'current_app' => $currentApp]);
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $authLimite = $request->session()->get('auth_session_limite');
    if ($authLimite) {
      $this->userLoggedService->removeSession($authLimite);
    }
    $request->session()->flush();

    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
  }

  public function authenticate(Request $request)
  {
    $app = (int) $request->post('app');
    $runApp = $this->__app();
    $credentials = $request->only('email', 'password');
    $credentials['status'] = 1;

    if ($app > 0 && $runApp->tipo == 'multiprovedor') {
      $credentials['app'] = $app;
      $runApp = false;
    } else {
      if (!$runApp->getSettings('libera_login_outros')) {
        $credentials['app'] = $runApp->id;
      } else {
        $runApp = false;
      }
    }

    // verifica se existe usuario informado e esta inativo
    $email = $credentials['email'];
    $tmpUser = Assinante::whereRaw("(email = ? or documentacao_cpf = ?) and status = '0'", [$email, $email])->first();
    if ($tmpUser) {
      return back()->withErrors([
        'email' => 'Usuário inativo',
      ]);
    }

    // tenta logar com email e hash novo
    if (Auth::attempt($credentials)) {
      if ($this->userLoggedService->hasLogged(Auth::user())) {
        $request->session()->put('user_logged', true);
        $this->userLoggedService->saveLogged($request, Auth::id());
      };

      $request->session()->regenerate();
      $this->setCurrentTokenLogin();
      return redirect()->route('perfil.index');
    }
    // ===
    // tenta logar com documentacao_cpf e hash novo
    unset($credentials['email']);
    $credentials['documentacao_cpf'] = $request->post('email');
    if (Auth::attempt($credentials)) {
      if ($this->userLoggedService->hasLogged(Auth::user())) {
        $request->session()->put('user_logged', true);
        $this->userLoggedService->saveLogged($request, Auth::id());
      };
      $request->session()->regenerate();
      $this->setCurrentTokenLogin();
      return redirect()->route('perfil.index');
    }
    $cpf = $request->post('email');
    $cpfWithFormat = '-1';
    if (strlen($cpf) == 11 && !strpos($cpf, '@')) {
      $parts = str_split($cpf, 3);
      $cpfWithFormat = sprintf('%s.%s.%s-%s', $parts[0], $parts[1], $parts[2], $parts[3]);
      $credentials['documentacao_cpf'] = $cpfWithFormat;
      if (Auth::attempt($credentials)) {
        if ($this->userLoggedService->hasLogged(Auth::user())) {
          $request->session()->put('user_logged', true);
          $this->userLoggedService->saveLogged($request, Auth::id());
        };

        $request->session()->regenerate();
        $this->setCurrentTokenLogin();
        return redirect()->route('perfil.index');
      }
    }
    // ===

    // tenta logar usando email ou documentacao_cpf com hash md5(antigos)
    //  1 - email
    $params = [
      'email'    => $request->post('email'),
      'password' => md5($request->post('password')),
      'status'   => 1,
    ];
    if ($runApp) {
      $params['app'] = $runApp->id;
    }
    $user = Assinante::where($params)->first();

    //  2 - documentacao_cpf
    if (!$user) {
      $params = [
        'documentacao_cpf'  => $request->post('email'),
        'password'          => md5($request->post('password')),
        'status'            => 1,
      ];
      if ($runApp) {
        $params['app'] = $runApp->id;
      }
      $user = Assinante::where($params)->first();

      if (!$user) {
        $params['documentacao_cpf'] = $cpfWithFormat;
        $user = Assinante::where($params)->first();
      }
    }

    // caso tenha encontrado usuario significa que pode logar,
    // muda a senha para hash novo e loga o usuario
    if ($user) {
      $lUser = $user;
      $user->password = Hash::make($request->post('password'));
      $user->save();

      Auth::guard()->login($lUser);
      if ($this->userLoggedService->hasLogged(Auth::user())) {
        $request->session()->put('user_logged', true);
        $this->userLoggedService->saveLogged($request, Auth::id());
      };

      $request->session()->regenerate();
      $this->setCurrentTokenLogin();
      return redirect()->route('perfil.index');
    }

    return back()->withErrors([
      'email' => 'Usuário ou senha incorreto',
    ]);
  } //

  public function new()
  {
    return view('assinante.new');
  }

  public function create(Request $request)
  {
    $currentApp = $this->__app();

    $signupPlanId = $currentApp->settingsSignupPlan();
    $signupPlan   = Plan::find($signupPlanId);

    if (!$currentApp->settingsSignupEnabled() || !$signupPlanId || !$signupPlan) {
      $request->session()->flash('alert', 'Cadastros desabilitados.');
      return redirect()->route('login');
    }


    $input = $request->all();
    $input['password'] = Hash::make($request->input('password'));
    $input['status']   = 1;

    $request->validate([
      'nome'          => 'required',
      'email'         => 'required',
      'password'      => 'required|min:3',
      'password'      => 'required|min:3',
      'accept_terms'  => 'required'
    ], [
      'nome.required' => 'O nome é obrigatório',
      'email.required' => 'O email é obrigatório',
      'email.unique' => 'O email já está em uso',
      'password.required' => 'A senha é obrigatória',
      'accept_terms.required' => 'Os termos de uso devem ser aceitos',
    ]);


    $defSubs = Assinante::find(Assinante::DEFAULT_SUBSCRIBER_ID);
    //$defPlan = $defSubs->getMainPlan();
    //$defPlanId = (string) $defPlan->id;
    $defPlan    = $signupPlan;
    $defPlanId  = (string) $signupPlan->id;

    // para habilitar cobrança no cadastro, retirar comentario
    //$defPlanId  = ""; // no cadastro nao seta mais o plano. O plano sera setado
    // ao adicionar informacoes de pagamento

    $input['app'] = $this->__app()->id;

    $setConteudo                                = new \stdclass;
    $setConteudo->settings_conteudos_planos                             = [$defPlanId];
    $setConteudo->settings_conteudos_password_parental                  = false;
    $setConteudo->settings_conteudos_livemode_copa_nordeste             = false;
    $setConteudo->settings_conteudos_livemode_copa_nordeste_exportar    = false;
    $setConteudo->settings_conteudos_livemode_copa_nordeste_exportacao  = false;

    try {
      $subs = Assinante::create($input);
      $subs->updateSettingsConteudos($setConteudo);
      $subs->save();
    } catch (Exception $e) {
      $request->session()->flash('alert', $e->getMessage());
      return redirect('/user/new');
    }

    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
      $request->session()->regenerate();
      //return redirect()->intended('home');

      $link = $this->_generateVerificationEmailLink();
      Mail::to($subs->email)
        ->send(new VerificationEmail($subs, $currentApp, $link));


      return redirect(route('verification.notice'))
        ->with('success', 'Cadastro efetuado com sucesso');
    }

    return redirect(route('verification.notice'))
      ->with('success', 'Cadastro efetuado com sucesso');
  }

  public function terms()
  {
    return view('assinante.terms');
  }

  public function profile()
  {
    return view('assinante.profile');
  }

  public function profileParentalPassword(Request $request)
  {
    if ($request->method() != 'POST') {
      return view('assinante.profile_parental_password');
    }

    $request->validate([
      'password'          => 'required|min:3',
      'password_parental' => 'required|min:3',
    ]);

    $sub = $request->user();

    if (!Hash::check($request->password, $sub->password)) {
      return back()->withErrors([
        'password' => ['A senha atual informada não é válida.']
      ]);
    }

    $sub->updateSettingsPasswordParental($request->post('password_parental'));

    $request->session()->flash('success', 'Senha parental atualizada');

    return view('assinante.profile_parental_password');
  }

  public function profileParentalPasswordRequest(Request $request)
  {
    if ($request->method() != 'POST') {
      return view('assinante.profile_parental_password_request');
    }

    $sub = $request->user();

    if (!Hash::check($request->password_parental, $sub->settingsPasswordParental())) {
      return back()->withErrors([
        'password' => ['A senha parental informada não é válida.']
      ]);
    }

    $request->session()->put('parental_checked', time());
    $request->session()->flash('success', 'Senha parental verificada');

    return redirect($request->session()->get('parental_return_url'));
    //return view('assinante.profile_parental_password_request');
  }

  public function newPassword(Request $request)
  {
    if ($request->method() != 'POST') {
      return view('assinante.user_profile_new_password');
    }

    $request->validate([
      'password'                  => 'required',
      'new_password'              => 'min:3|required_with:new_password_confirmation|same:new_password_confirmation',
      'new_password_confirmation' => 'min:3',
    ]);

    if (!Hash::check($request->password, $request->user()->password)) {
      // tenta atualizar senha usando md5 - antigos
      if (md5($request->password) != $request->user()->password) {
        return back()->withErrors([
          'password' => ['A senha atual informada não é válida.']
        ]);
      }
    }

    $user = $request->user();
    $user->password = Hash::make($request->new_password);
    if ($user->save()) {
      $request->session()->flash('success', 'Senha atualizada');
    }

    return view('assinante.user_profile_new_password');
  }

  public function forgotPassword(Request $request)
  {
    $currentApp = $this->__app();

    if ($request->method() != 'POST') {
      return view('assinante.forgot_password', ['current_app' => $currentApp]);
    }

    $request->validate([
      'email'                  => 'required|email',
    ]);


    $email = $request->post('email');
    $sub   = Assinante::where('email', $email)->where('app', $currentApp->id)->first();
    if (!$sub) {
      $request->session()->flash('alert', 'Email não encontrado.');
      return view('assinante.forgot_password', ['current_app' => $currentApp]);
    }

    $token = $sub->resetPasswordToken();
    $link  = route('reset_password', ['id' => $sub->id, 'token' => $token, 'app' => $currentApp->id]);

    try {
      Mail::to($sub->email)
        ->send(new ForgotPassword($sub, $currentApp, $link));
    } catch (\Exception $e) {
      $request->session()->flash('alert', sprintf('Não foi possível enviar o email no momento. Tente novamente mais tarde (%s).', $e->getMessage()));
      return view('assinante.forgot_password', ['current_app' => $currentApp]);
    }

    $request->session()->flash('success', 'Email enviado. Consulte sua caixa de entrada.');
    return view('assinante.forgot_password', ['current_app' => $currentApp]);
  }

  public function resetPassword(Request $request, $id, $token)
  {
    $currentApp = $this->__app();

    $sub = Assinante::find($id);

    if ($request->method() != 'POST') {
      return view('assinante.reset_password', ['id' => $id, 'current_app' => $currentApp, 'token' => $token, 'app' => $currentApp->id]);
    }

    if (!$sub || $token != $sub->resetPasswordToken()) {
      $request->session()->flash('alert', 'Link inválido');
      return view('assinante.reset_password', ['id' => $id, 'current_app' => $currentApp, 'token' => $token, 'app' => $currentApp->id]);
    }

    $request->validate([
      'new_password'              => 'min:3|required_with:new_password_confirmation|same:new_password_confirmation',
      'new_password_confirmation' => 'min:3',
    ]);

    $sub->password = Hash::make($request->post('new_password'));

    if (!$sub->save()) {
      $request->session()->flash('alert', 'Ocorreu um erro ao atualizar a senha. Acesse novamente o link enviado.');
      return view('assinante.reset_password', ['id' => $id, 'current_app' => $currentApp, 'token' => $token, 'app' => $currentApp->id]);
    }

    $request->session()->flash('success', 'Senha atualizada');
    return redirect(route('login', ['app' => $currentApp->id]));
  }

  public function verificationEmail()
  {
    return view('assinante.verification_email');
  }

  private function _generateVerificationEmailLink()
  {
    $currentApp = $this->__app();
    $sub = $this->__subscriber();

    $exp = new \Datetime;
    $exp->add(new \Dateinterval(sprintf('PT%sH%sM', 1, 0)));
    $hash = base64_encode($exp->format('U'));

    $link = route('verification.verify', ['id' => $sub->id, 'hash' => $hash]);
    return $link;
  }

  public function verificationEmailRequest()
  {
    $currentApp = $this->__app();
    $sub = $this->__subscriber();

    $link = $this->_generateVerificationEmailLink();

    Mail::to($sub->email)
      ->send(new VerificationEmail($sub, $currentApp, $link));

    $request->session()->flash('success', 'Solicitação de confirmação enviada. Verifique se email');
    //auth()->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Email de verificação enviado');
  }

  public function verificationEmailVerify(Request $request, $id, $hash)
  {
    $sub     = $this->__subscriber();
    $now     = time();
    $timeout = base64_decode($hash);

    if ($sub->email_verified_at != null) {
      $request->session()->flash('alert', 'Email já verificado.');
      return view('assinante.verification_email');
    }

    if ($sub->id != $id) {
      $request->session()->flash('alert', 'Assinante inválido. Solicite um novo link.');
      return view('assinante.verification_email');
    }

    if ($now > $timeout) {
      $request->session()->flash('alert', 'Link expirou. Solicite um novo link.');
      return view('assinante.verification_email');
    }

    $sub->verifyEmail();

    $request->session()->flash('success', 'Email verificado.');

    return redirect()->to('/home');
  }

  public function listDevicesConnected()
  {
    $devices = $this->userLoggedService->all(Auth::id());
    $sesionActual = session()->get('auth_session_limite');

    return view('assinante.devices', ['devices' => $devices, 'sessionAtual' => $sesionActual]);
  }

  public function deleteDevice(Request $request, $id)
  {
    $session = session()->get('user_logged');
    $sessionFind = $this->userLoggedService->find($id);
    $this->userLoggedService->delete($id);

    if ($session == null) {
      $request->session()->put('user_logged', true);
      $this->userLoggedService->saveLogged($request, Auth::id());

      return redirect()->route('perfil.index');
    }

    $sesionActual = session()->get('auth_session_limite');
    if ($sessionFind->auth_session_limite == $sesionActual) {
      return $this->logout($request);
    }

    return redirect()->back();
  }

  public function updateInfo(Request $request)
  {
    $sub     = $this->__subscriber();

    if ($request->method() != 'POST') {
      return view('assinante.user_profile_update_info', ['subscriber' => $sub]);
    }

    $request->validate([
      'documentacao_cpf'  => 'required|cpf',
      'email'         => 'required',
    ], [
      'documentacao_cpf.required' => 'O cpf é obrigatório',
      'email.unique' => 'O email já está em uso',
    ]);

    if ((string) $sub->id_importacao != '') {
      return back()->withErrors([
        'email' => 'Entre em contato com o provedor para alterar o email',
      ]);
    }

    $sub->documentacao_cpf = $request->documentacao_cpf;
    $sub->email = $request->email;
    if ($sub->save()) {
      $request->session()->flash('success', 'Dados atualizados');
    }

    return view('assinante.user_profile_update_info', ['subscriber' => $sub]);
  }
}
