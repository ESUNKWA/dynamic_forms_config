@extends('layouts.backend')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>Saisie un nouveau workflow</h3>
            </div>
        </div>



                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

        <div class="row">
            <div class="col-md-12">
               <form action="{{ url('/workflows/store') }}" method="POST">
                   @csrf

                   <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Produits</label>
                        <select class="form-select" aria-label="Default select example" name="r_produit" >

                            <option value="" selected>---Sélectionnez un produit---</option>

                            @foreach($produits as $key => $produit)
                                <option value="{{ $produit->id }}">{{ $produit->r_nom_produit }}</option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Nom du workflow</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $workflows }}" aria-describedby="Name" placeholder="" required>
                    </div>



                   <div class="col-md-12 text-right">
                   <a href="{{url('/workflows')}}" class="btn btn-danger">Annuler</a>
                   <button type="submit" class="btn btn-primary">Enregistrer</button>
                   </div>
               </form>
            </div>
        </div>
    </div>
@endsection
