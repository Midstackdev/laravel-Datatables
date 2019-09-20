<?php

namespace App\Http\Controllers\DataTable;


use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

abstract class DataTableController extends Controller
{
    /**
     * if an entity is allowed to be created
     * @var boolean
     */
    protected $allowCreation = true;

    /**
     * if a record is allowed to be deleted
     * @var boolean
     */
    protected $allowDeletion = true;

    /**
     * The entity builder
     * @var Illuminate\Database\Eleoquent\Builder
     */
	protected $builder;

    abstract public function builder();

    public function __construct()
    {
    	$builder = $this->builder();

    	if(!$builder instanceof Builder){
    		throw new Exception('Entity builder not an instance of builder');
    	}

    	$this->builder = $builder;
    }

    public function index(Request $request)
    {
    	return response()->json([
            'data' => [
                'table' => $this->getTableName(),
                'displayable' => array_values($this->getDisplayableColumns()),
                'updatable' => array_values($this->getUpdatableColumns()),
                'custom_columns' => $this->getCustomColumnNames(),
                'records' => $this->getRecords($request),
                'allow' => [
                    'creation' => $this->allowCreation,
                    'deletion' => $this->allowDeletion,
                ]
            ]
    	]);
    }

    public function update($id, Request $request)
    {
        $this->builder->find($id)->update($request->only($this->getUpdatableColumns()));
    }

    public function store(Request $request)
    {
        if(!$this->allowCreation) {
            return;
        }

        $this->builder->create($request->only($this->getUpdatableColumns()));
    }

    /**
     * Deletes a record
     * @param  id
     * @param  Request $request
     * @return Illluminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if(!$this->allowDeletion) {
            return;
        }
        $this->builder->find($id)->delete();
    }

    public function getDisplayableColumns()
    {
        return array_diff($this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden());
    }

    /**
     * Get Custom column names
     * @return array
     */
    public function getCustomColumnNames()
    {
        return [];
    }

    public function getUpdatableColumns()
    {
        return $this->getDisplayableColumns();
    }

    protected function getDatabaseColumnNames()
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
    }

    protected function getTableName()
    {
        return $this->builder->getModel()->getTable();
    }

    public function getRecords(Request $request)
    {
        $builder = $this->builder;

        if($this->hasSearchQuery($request)) {

            $builder = $this->buildSearch($builder, $request);
        }

        return $this->builder->limit($request->limit)->get($this->getDisplayableColumns());
    }

    protected function hasSearchQuery(Request $request) 
    {
        return count(array_filter($request->only(['column', 'operator', 'value']))) === 3;
    }

    protected function buildSearch(Builder $builder, Request $request)
    {
        $queyParts = $this->resolveQueryParts($request->operator, $request->value);

        return $builder->where($request->column, $queyParts['operator'], $queyParts['value']);
    }

    protected function resolveQueryParts($operator, $value)
    {
        return Arr::get([
            'equals' => [
                'operator' => '=',
                'value' => $value
            ],

            'contains' => [
                'operator' => 'LIKE',
                'value' => "%{$value}%"
            ],

            'starts_with' => [
                'operator' => 'LIKE',
                'value' => "{$value}%"
            ],

            'ends_with' => [
                'operator' => 'LIKE',
                'value' => "%{$value}"
            ],

            'greater_than' => [
                'operator' => '>',
                'value' => $value
            ],

            'less_than' => [
                'operator' => '<',
                'value' => $value
            ],
        ], $operator);
    }
}
