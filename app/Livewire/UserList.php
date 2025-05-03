<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;
    public $search = '';

    public function render()
    {
        return view('livewire.user-list', [
            'users' => User::where('name', 'like', '%' . $this->search . '%')->paginate(15),
        ])->layout('layouts.app');
    }
}
