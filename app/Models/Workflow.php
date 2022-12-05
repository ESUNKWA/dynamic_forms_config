<?php

//namespace the42coders\Workflows;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Utilisateurs;

class Workflow extends Model
{

    protected $table = 'workflows';

    protected $fillable = [
        'name',
        'r_produit',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('workflows.db_prefix').$this->table;
        parent::__construct($attributes);
    }

    public function tasks()
    {
        return $this->hasMany('the42coders\Workflows\Tasks\Task');
    }

    public function triggers()
    {
        return $this->hasMany('the42coders\Workflows\Triggers\Trigger');
    }

    public function logs()
    {
        return $this->hasMany('the42coders\Workflows\Loggers\WorkflowLog');
    }

    public function getTriggerByClass($class)
    {
        return $this->triggers()->where('type', $class)->first();
    }
}
