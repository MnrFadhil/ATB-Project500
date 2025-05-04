<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    /* -------------------------------------------------------------------------- */
    /*                                  Form User                                 */
    /* -------------------------------------------------------------------------- */
    public $name = '';
    public $email = '';
    public $username = '';
    public $password = '';
    public $role = 'user';
    public $address = '';

    /* -------------------------------------------------------------------------- */
    /*                             Member Of Variabel                             */
    /* -------------------------------------------------------------------------- */
    public $userId = '';
    public $search = '';
    public $mode = 'create';
    public $isChangePassword = false;

    /* -------------------------------------------------------------------------- */
    /*                               Logical Methods                              */
    /* -------------------------------------------------------------------------- */
    public function updateUser($user)
    {
        $this->dispatch('show-modal-form');
        $this->mode = 'edit';

        $this->assignValueUser($user);
    }

    public function deleteUser($user)
    {
        $this->dispatch('show-modal-detail');
        $this->mode = 'delete';

        $this->assignValueUser($user);
    }

    public function detailUser($user)
    {
        $this->dispatch('show-modal-detail');
        $this->mode = 'detail';

        $this->assignValueUser($user);
    }

    public function createUser()
    {
        $this->dispatch('show-modal-form');
        $this->mode = 'create';

        $this->name     = '';
        $this->email    = '';
        $this->role     = 'user';
        $this->username = '';
        $this->address  = '';
        $this->password  = '';
    }


    public function assignValueUser($user)
    {
        $this->userId   = $user['id'];
        $this->name     = $user['name'];
        $this->email    = $user['email'];
        $this->role     = $user['role'];
        $this->username = $user['username'];
        $this->address  = $user['address'];
    }

    public function submit()
    {

        $validData = $this->validate([
            'name'      => ['required', 'string'],
            'username'  => ['required', 'string',  Rule::unique('users', 'username')->ignore($this->userId)],
            'email'     => ['required', 'email', 'string',  Rule::unique('users', 'email')->ignore($this->userId)],
            'password'  => ['string', $this->mode == 'edit' ? 'nullable' : 'required'],
            'role'      => ['required', 'string'],
            'address'   => ['string'],
        ]);

        if (!empty($validData['password'])) {
            $validData['password'] = Hash::make($validData['password']);
        } else {
            unset($validData['password']);
        }

        if ($this->mode == 'edit') {
            $userUpdate = User::findOrFail($this->userId);
            $isSuccess = $userUpdate->update($validData);

            if ($isSuccess) Session::flash('success', 'Success Edit User');
            else Session::flash('eror', 'Eror Edit User');
        }
        if ($this->mode == 'create') {
            $isSuccess = User::create($validData);

            if ($isSuccess) Session::flash('success', 'Success Create User');
            else Session::flash('eror', 'Eror Create User');
        }
        if ($this->mode == 'delete') {
            $userUpdate = User::findOrFail($this->userId);
            $isSuccess = $userUpdate->delete();

            if ($isSuccess) Session::flash('success', 'Success Create User');
            else Session::flash('eror', 'Eror Create User');
        }

        if ($this->mode !== 'delete') {
            $this->dispatch('close-modal-form');
        } else {
            $this->dispatch('close-modal-detail');
        }

        return redirect('/user-list');
    }

    public function showChangePassword()
    {
        $this->isChangePassword = !$this->isChangePassword;
        $this->password = '';
    }

    /* -------------------------------------------------------------------------- */
    /*                               Lifecycle Hooks                              */
    /* -------------------------------------------------------------------------- */
    public function render()
    {
        return view('livewire.user-list', [
            'users' => User::where('name', 'like', '%' . $this->search . '%')->paginate(15),
        ])->layout('layouts.app');
    }
}
