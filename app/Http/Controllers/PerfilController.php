<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerfilRequest;
use Illuminate\Http\Request;
use App\Services\PerfilService;
use App\Models\Arquivos;

class PerfilController extends Controller
{

  private $service;

  public function __construct(PerfilService $service)
  {
    $this->service = $service;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = $this->subscriber();

    $data = $this->service->all($user->id);
    return view('perfil.index', compact('data'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $avatarList = Arquivos::where('avatar', '1')->where('status', 1)->get();
    return view('perfil.add', ['avatarList' => $avatarList]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(PerfilRequest $request)
  {
    $user = $this->subscriber();

    $data = $request->all();
    $data['id_assinante'] = $user->id;
    $this->service->save($data);

    return redirect()->route('perfil.index')->with('success', 'Perfil cadastrado com sucesso!');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    $request->session()->put('id_perfil', $id);

    return redirect()->route('home');
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $perfil = $this->service->find($id);
    $avatarList = Arquivos::where('avatar', '1')->where('status', 1)->get();
    return view('perfil.add', compact('perfil') + ['avatarList' => $avatarList]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $data = $request->all();
    $this->service->update($data, $id);

    return redirect()->route('perfil.gerenciar')->with('success', 'Perfil atualizado com sucesso!');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    request()->session()->forget('id_perfil');
    $this->service->delete($id);

    $user = $this->subscriber();
    $data = $this->service->all($user->id);
    if (count($data) > 0) {
      return redirect()->route('perfil.gerenciar')->with('success', 'Perfil atualizado com sucesso!');
    }
    return redirect()->route('perfil.index')->with('success', 'Perfil atualizado com sucesso!');
  }
}
