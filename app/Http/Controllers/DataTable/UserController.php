<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataTable\DataTableController;
use App\User;
use Illuminate\Http\Request;

class UserController extends DataTableController
{
	protected $allowCreation = true;
	protected $allowDeletion = true;

    public function builder()
    {
    	return User::query();
    }

    public function getDisplayableColumns()
    {
    	return [
    		'id', 'name', 'email', 'created_at'
    	];
    }

    public function getCustomColumnNames()
    {
        return [
        	'name' => 'Full name',
        	'email' => 'Email address'
        ];
    }

    public function getUpdatableColumns()
    {
    	return [
    		'name', 'email', 'created_at'
    	];
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
    		'name' => 'required',
    		'email' => 'required|email|unique:users,email'
    	]);
        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }
}
