@extends('templates.emails.model')

@section('greetings')
    Cher Admin,
@endsection

@section('content')
    <p style="font-size: 14px; line-height: 200%;">
        <span style="font-size: 16px; line-height: 32px;">
            Une nouvelle maison d'hôtes a été ajoutée sur la plateforme et nécessite votre attention pour approbation.
        </span>
    </p>
    <p style="font-size: 14px; line-height: 200%;">
        <span style="font-size: 16px; line-height: 32px;">
            <strong>Action requise :</strong><br>
            Veuillez consulter les détails de cette nouvelle maison d'hôtes en vous connectant à votre espace.
        </span>
    </p>
@endsection
