<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RoleUpdate;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {
            $users = User::all();
            $roles = Role::all();
            return view('admin.users.index')->with(compact('users', 'roles'));
        }
        return redirect(route('login'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {

            $rolesArr = Role::get()->toArray();
            $roles = [];
            foreach ($rolesArr as $item) {
                $roles[$item['id']] = $item['name'];
            }
            $user = User::where('id', $id)->get();
            return view('admin.users.edit')->with(compact('user', 'roles'));
        }
        return redirect(route('login'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        if (\Auth::check() && \Auth::user()->hasRole('admin')) {
            $user = User::where('id', $id)->get();
            $rolesArr = Role::get()->toArray();
            $roles = [];
            $guestId = null;
            foreach ($rolesArr as $item) {
                $roles[$item['id']] = $item['name'];
                if ($item['name'] == 'guest') {
                    $guestId = $item['id'];
                }
            }
            if ($request['role_id'] == null) {
                $user[0]->syncRoles($roles[$guestId]);
            } else {
                $user[0]->syncRoles($roles[$request['role_id']]);
            }
            $this->sendNotificationEmail($user[0]);
            return redirect(route('user-index'));
        }
        return redirect('season-index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check() && \Auth::user()->hasRole(['admin','qa','project','developer'])) {
            User::where('id', $id)->delete();
            return response('success', 200);
        }
        return response('error', 401);
    }

    private function sendNotificationEmail($user)
    {
        $details = [
            'greeting' => 'Hi '.$user->name,
            'body' => 'your request been approved, follow this link',
            'action' => 'Admin panel',
            'url' => url(RouteServiceProvider::HOME),
        ];

        Notification::send($user, new RoleUpdate($details));
    }
}
