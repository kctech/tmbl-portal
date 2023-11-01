<?php

namespace App\Http\Controllers;

use Validator;

use App\User;
use App\Role;
use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('anything', Auth::user());
        if ($request->isMethod('post')) {
            $user_role = $request->input('role_id');
            $user_account = $request->input('account_id');
            $user_surname = $request->input('user_surname');
            $user_email = $request->input('user_email');
            $deleted_status = $request->input('deleted_status');
            $sort = $request->input('sort');

            $users = User::filter($user_role, $user_account, $user_email, $user_surname, $deleted_status, $sort);

            if ($user_role == "") $user_role = '';
            if ($user_account == "") $user_account = '';
            if ($user_email == "") $user_email = '';
            if ($user_surname == "") $user_surname = '';
            if ($deleted_status == "") $deleted_status = 'default';
            if ($sort == "") $sort = 'newest_first';
        } else {
            $user_role = '';
            $user_account = '';
            $user_email = '';
            $user_surname = '';
            $deleted_status = 'default';
            $sort = 'newest_first';

            $users = User::select('users.*')->join('roles', 'roles.id', 'users.role_id')->where('users.id','>',0)->where('roles.level','>=', Auth::user()->role->level)->orderby('users.last_login_at', 'DESC')->orderby('users.id', 'DESC')->paginate(999); //config('database.pagination_size')
        }

        $roles = Role::where('level', '>=', auth()->user()->role->level)->get();
        $accounts = Account::all();
        return view('admin.users.index')->with(compact('users','roles','accounts'))->with('user_email', $user_email)->with('user_role', $user_role)->with('user_account', $user_account)->with('user_surname', $user_surname)->with('sort', $sort)->with('deleted_status', $deleted_status);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('anything', Auth::user());
        //$roles = Role::all();
        $roles = Role::where('level', '>=', auth()->user()->role->level)->get();
        $accounts = Account::all();
        return view('admin.users.create', compact('roles', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('anything', Auth::user());
        //blank vars
        $statusMsg = array();
        $status = 0;

        //Fields
        $validator = Validator::make($request->all(), User::VALIDATION_RULES, User::VALIDATION_LABELS);

        $insert = User::create($validator->validated());

        //user 1
        if (!empty($insert)) {
            $request->session()->flash('alert-success', 'Task was successful!');
        } else {
            $request->session()->flash('alert-danger', 'Task was unsuccessful!');
        }

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('anything', Auth::user());
        return view('admin.users.view')->with('user', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('anything', Auth::user());
        if (auth()->user()->role->level <= $user->role->level) {
            $roles = Role::where('level', '>=', auth()->user()->role->level)->get();
            $accounts = Account::all();
            return view('admin.users.edit', compact('roles', 'accounts'))->with('user', $user);
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->authorize('anything', Auth::user());
        $user = User::findOrFail($id);

        if (auth()->user()->role->level <= $user->role->level) {
        
            $validatedData = request()->validate([
                'role_id' => 'required',
                'account_id' => 'required',
                'tel' => ['required', 'string', 'max:191'],
                'first_name' => ['required', 'string', 'max:191'],
                'last_name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email,'.$user->id, 'regex:/@(tmblgroup\.co\.uk|gatewaymortgagesuk\.com)$/'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed']
            ], User::VALIDATION_LABELS);
            $validatedData['user_id'] = $user->id;

            $update = User::change($validatedData);

            if (!empty($update)) {
                Session::flash('alert-success', 'Task was successful!');
            } else {
                Session::flash('alert-danger', 'Task was unsuccessful!');
            }

            //dd(request());
            return redirect()->route('users.index');
            
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('anything', Auth::user());

        $action = User::remove($id);
        if ($action) {
            Session::flash('alert-success', 'User ID '.$id.' deleted');
        } else {
            Session::flash('alert-danger', 'User ID '.$id.' could not be deleted');
        }

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function reinstate($id)
    {
        $this->authorize('anything', Auth::user());

        $action = User::reinstate($id);
        if ($action) {
            Session::flash('alert-success', 'User ID '.$id.' restored');
        } else {
            Session::flash('alert-danger', 'User ID '.$id.' could not be restored');
        }

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public static function list()
    {
        //$this->authorize('anything');
        $users = User::all();
        if ($users) {
            return response()->json([
                'status' => 0,
                'users' => $users
            ]);
        } else {
            return response()->json(['status' => 1]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        //$this->authorize('anything');
        $user = User::where('email', $request->input('search'))->first();
        if ($user) {
            return response()->json([
                'status' => 0,
                'user' => $user
            ]);
        } else {
            return response()->json(['status' => 1]);
        }
    }


    /**
     * Check for exisiting user
     *
     * @return \Illuminate\Http\Response
     */
    public function checkDuplicate(Request $request)
    {
        //$this->authorize('anything');
        $count = 0;

        if ($request->has('email') && !empty($request->input('email'))) {
            $count = User::where('email', 'LIKE', $request->input('email'))->get()->count();
        }

        return response()->json([
            'count' => $count
        ]);
    }

     /**
     * Check for exisiting user
     *
     * @return \Illuminate\Http\Response
     */
    public function impersonate($id)
    {
        $this->authorize('anything', Auth::user());
        if (Gate::allows('impersonate', auth()->user())) {
            if (auth()->user()->id != $id) {
                $user = User::findOrFail($id);
                session([
                    'user_id' => $user->id,
                    'account_id' => $user->account_id,
                    'logo' => $user->account->logo,
                    'css' => $user->account->css,
                    'viewset' => $user->account->viewset,
                    'acronym' => $user->account->acronym,
                    'impersonate' => 'Impersonating '.$user->first_name.' '.$user->last_name.', ID:'.$id,
                    'modules' => getModules($user)
                ]);
            } else {
                Session::flash('alert-info', 'You can\'t impresonate yourself...');
            }

            return redirect(route('users.index'));

        } else {
            return redirect(route('admin.dashboard'));
        }
    }

    

    /**
     * Check for exisiting user
     *
     * @return \Illuminate\Http\Response
     */
    public function stopImpersonating()
    {
        $this->authorize('anything', Auth::user());
        if (Gate::allows('impersonate', auth()->user())) {
            $user = User::findOrFail(auth()->user()->id);
            session([
                'user_id' => $user->id,
                'account_id' => $user->account_id,
                'logo' => $user->account->logo,
                'css' => $user->account->css,
                'viewset' => $user->account->viewset,
                'acronym' => $user->account->acronym,
                'impersonate' => null,
                'modules' => getModules($user)
            ]);

            Session::flash('alert-success', 'Normal user session resumed.');

            return redirect(route('users.index'));
        }else{
            return redirect(route('admin.dashboard'));
        }
    }
    
}
