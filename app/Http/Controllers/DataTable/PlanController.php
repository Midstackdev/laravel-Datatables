<?php

namespace App\Http\Controllers\DataTable;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataTable\DataTableController;
use App\Plan;
use Illuminate\Http\Request;

class PlanController extends DataTableController
{
    public function builder()
    {
    	return Plan::query();
    }

    // public function getDisplayableColumns()
    // {
    // 	return [
    // 		'id', 'name', 'email', 'created_at'
    // 	];
    // }

    public function getUpdatableColumns()
    {
    	return [
    		'stripe_id', 'price', 'active'
    	];
    }

    /**
     * create a record
     *
     * @param Request $request [<description>]
     * @return void 
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'stripe_id' => 'required',
            'price' => 'required',
        ]);

        if(!$this->allowCreation) {
            return;
        }

        $this->builder->create($request->only($this->getUpdatableColumns()));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
    		'stripe_id' => 'required',
            'price' => 'required',
    	]);
        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }
}
