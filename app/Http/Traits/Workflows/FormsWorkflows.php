<?php
namespace App\Http\Traits\Workflows;

use App\Models\Workflow;
use App\Models\Workflows\Taches;

    /**
     *
     */
    trait FormsWorkflows
    {
        public function get_nbre_validation(int $idforms){
            return Taches::select('id')
            ->where('r_formulaire', $idforms)
            ->count();
        }

        public function get_validateur_tache($idforms){
            $rangValidateur = Taches::select('tasks.*','t_niveau_validations.r_utilisateur')
                                        ->join('t_niveau_validations', 'tasks.id', 't_niveau_validations.r_task')
                                        ->where('tasks.r_formulaire', $idforms)
                                        ->orderBy('tasks.r_rang', 'asc')
                                        ->limit(1)
                                        ->first();
            return $rangValidateur;
        }

        public function get_first_validateur_par_wkfl($produit){
            $rangValidateur = Workflow::select('t_niveau_validations.r_rang', 'users.email')
                                        ->join('tasks', 'workflows.id', 'tasks.workflow_id')
                                        ->join('t_niveau_validations', 'tasks.id', 't_niveau_validations.r_task')
                                        ->join('users', 'users.id', 't_niveau_validations.r_utilisateur')
                                        ->where('workflows.r_produit', $produit)
                                        ->where('t_niveau_validations.r_rang', 1)
                                        ->first();
            return $rangValidateur;
        }

        public function get_validateur_par_wkfl($produit, $idvalidateur){
            $rangValidateur = Workflow::select('t_niveau_validations.r_rang', 'users.email')
                                        ->join('tasks', 'workflows.id', 'tasks.workflow_id')
                                        ->join('t_niveau_validations', 'tasks.id', 't_niveau_validations.r_task')
                                        ->join('t_formulaires', 't_formulaires.id', 'tasks.r_formulaire')
                                        ->join('users', 'users.id', 't_niveau_validations.r_utilisateur')
                                        ->where('workflows.r_produit', $produit)
                                        ->where('t_niveau_validations.r_utilisateur', $idvalidateur)
                                        ->first();
            return $rangValidateur;
        }
    }

?>
