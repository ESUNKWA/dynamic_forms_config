@extends('layouts.backend')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Modification du Workflow [ {{ $workflow->name }} ]</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
               <form action="{{ route('workflow.update', ['id' => $workflow->id]) }}" method="POST">
                   @csrf

                   <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Produits</label>
                        <select class="form-select" aria-label="Default select example" name="r_produit">

                            <option >---Sélectionnez un produit---</option>

                            @foreach($produits as $key => $produit)

                                <option value="{{ $produit->id }}" {{ $produit->id == $workflow->r_produit ? 'selected' : '' }}>{{ $produit->r_nom_produit }}</option>
                            @endforeach

                        </select>
                    </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Nom du workflow</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $workflow->name }}" aria-describedby="Name" placeholder="Name">
                </div>


                <div class="col-md-12 text-right">
                   <a href="{{config('workflows.prefix')}}/workflows" class="btn btn-danger">Annuler</a>
                   <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
               </form>
            </div>
        </div>
    </div>
@endsection
